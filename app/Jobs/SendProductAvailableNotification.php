<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Product;
use App\Events\ProductBackInStock;
use App\Models\ProductNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendProductAvailableNotification implements ShouldQueue
{
    use Queueable, Dispatchable;

    public $productId;

    public function __construct($productId)
    {
        $this->productId = $productId;
    }

 // App\Jobs\SendProductAvailableNotification.php

public function handle(): void
{
    // 1. Load product
    $product = Product::find($this->productId);
    if (!$product) {
        Log::error("Product not found for ID: {$this->productId}");
        return;
    }
    
    // // 2. Load subscriber IDs from Redis
    // $userIds = ProductNotification::where('product_id', $this->productId)
    // ->pluck('user_id');
// 2. Load subscriber IDs from Redis (Switching back to Redis)
    $userIds = Redis::smembers("notify:product:{$this->productId}");
    
    // If you need the IDs as integers for the DB query, convert them:
    $userIds = array_map('intval', $userIds); // <--- Add this conversio
      if (empty($userIds)) { 
        Log::info("No subscribers found for Product ID: {$this->productId}");
        return;
    }
    
    // Fetch all users to be notified
    $users = User::whereIn('id', $userIds)->get();

    // 3. Send event + cleanup
    foreach ($users as $user) {
        
        $notification = ProductNotification::where('user_id', $user->id)
            ->where('product_id', $this->productId)
            ->first();
        
        if (!$notification) {
            // Safety check for missing DB record
            Log::warning("Notification record missing for User ID: {$user->id}");
            Redis::srem("notify:product:{$this->productId}", $user->id);
            continue; 
        }

        // --- FINAL LOGIC START ---

        // A. Check 1: Has the user already been notified (DB status)?
        if ($notification->notified) {
            Log::info("User ID {$user->id} already notified. Cleaning up Redis.");
            Redis::srem("notify:product:{$this->productId}", $user->id);
            continue;
        }

        // B. Check 2: Is the user's email verified? (Using email_verified_at column)
        // if (is_null($user->email_verified_at)) { // <-- FIXED CHECK
        //      Log::info("User ID {$user->id} is NOT verified (email_verified_at is null). Removing from Redis set.");
        //      Redis::srem("notify:product:{$this->productId}", $user->id);
        //      continue; // Skip notification
        // }
        
        // C. If NOT notified and IS verified, then proceed with the broadcast:
        
        // 1. Send event 
        event(new ProductBackInStock($product, $user->id)); 

        // 2. Update the database record (Mark as notified)
        $notification->update(['notified' => true]);

        // 3. Cleanup Redis (Remove user ID from set)
        Redis::srem("notify:product:{$this->productId}", $user->id);

        // --- FINAL LOGIC END ---
    }
}
}
