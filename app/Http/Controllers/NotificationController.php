<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductNotification;
use Illuminate\Support\Facades\Redis;
use App\Jobs\SendProductAvailableNotification; // Assuming this job exists

class NotificationController extends Controller
{
    /**
     * Subscribes the user to an out-of-stock product.
     */
    public function subscribe(Request $request, Product $product)
{
    // 1. Initial Check
    if ($product->quantity > 0) {
        return response()->json(['message' => 'Product already in stock'], 400);
    }

        $userId = $request->user()->id;
        $message = 'You are already subscribed to this product.';
        $wasCreated = false;

    try {
        // --- START DATABASE TRANSACTION ---
        DB::transaction(function () use ($userId, $product, &$wasCreated) {

            $notification = ProductNotification::firstOrCreate([
                'user_id'    => $userId,
                'product_id' => $product->id,
            ]);

            // Correct way to know if it was created
            $wasCreated = $notification->wasRecentlyCreated;
        });
        // --- COMMIT ---

        // --- POST-COMMIT LOGIC ---
        if ($wasCreated) {
            Redis::sadd("notify:product:{$product->id}", (string) $userId);

            // Increment subscription counter (only once)
            Redis::incr("product:{$product->id}:subscribers");

            $message = 'You will be notified when the product is available.';
        }

        return response()->json([
            'message' => $message,
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'An internal error occurred during subscription. Please try again.'
        ], 500);
    }
}

    }
