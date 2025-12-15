<?php

namespace App\Http\Controllers;

use App\Models\Product;


use App\Models\Category;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
// use Illuminate\Http\Request;

class ProductController extends Controller
{

/**
 * Show  all products for home page
 */
    public function showProduct()
    {
    $categories = Cache::remember('categories', 3600, function()  {
        return Category::all();
    });
    
    $productLeaderboardKey = 'product:leaderboard'; // 1. Descriptive key
    $cacheKey = 'products:sorted:popularity';      // Added cache key for clarity

    $products = Cache::remember($cacheKey, 3600, function() use ($productLeaderboardKey) {

    // 1. Fetch sorted IDs from Redis (e.g., ['product:101', 'product:55', 'product:21'])
    $sortedProductWithPrefix = Redis::zrevrange($productLeaderboardKey, 0, -1);

    // 2. Clean the IDs and ensure they are integers (e.g., [101, 55, 21])
    $sortedProductIds = collect($sortedProductWithPrefix)
        ->map(fn($id) => (int)str_replace('product:', '', $id))
        ->filter(); // use filter() for robustness

    // 3. Fallback check: If the collection of IDs is empty
    if ($sortedProductIds->isEmpty()) { // use isEmpty() method
        return Product::all();
    }

    // 4. Fetch full product models and key them by their ID
    $productsKeyed = Product::with('pharmacy')
        ->whereIn('id', $sortedProductIds)
        ->get()
        ->keyBy('id');

    // 5. Reorder the products based on the exact Redis-defined sequence
    // We map over the correctly ordered IDs ($sortedProductIds)
    // and pull the corresponding product object from the keyed collection.
    $finalProducts = $sortedProductIds->map(fn($id) => $productsKeyed->get($id))->filter(); // Corrected logic

    return $finalProducts;
    });

    $pharmacies = Cache::remember('pharmacies', 3600, function() {
        return Pharmacy::where('id', '>=', 21)->get();
    });

    $locations = Cache::remember('locations', 3600, function() {
        return Pharmacy::distinct()->pluck('location');
    });


        return view('home',compact(['categories','products','pharmacies','locations']));
    }



    /**
     * Filter search (location + product name)
     */

public function filter(Request $request)
{
    // 1. Setup
    $search = $request->q ?? ''; 
    $location = $request->location ?? ''; 
    session(['last_location' => $location]);
    
    $cacheKey = 'Products:filter' . md5($search . '|' . $location);
    $productLeaderboardKey = 'product:leaderboard';

    $lockKey = 'lock:leaderboard:score_update';
    $lockDuration = 5;

    $products = Cache::remember($cacheKey, 2, function() use ($search, $location, $productLeaderboardKey, $lockKey, $lockDuration, $cacheKey) {
        
        // This is the actual result of the lock operation, which holds the final sorted products or null.
        $results = Cache::lock($lockKey, $lockDuration)->get(function () use ($search, $location, $productLeaderboardKey) {
            
            // --- Logic inside the protected lock runs here ---
            $query = Product::with('pharmacy');
            
            if ($location) {
                $query->whereHas('pharmacy', fn($q) => $q->where('location', $location));
            }
            if ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            $matchedProducts = $query->get();
            
            if ($matchedProducts->isEmpty()) {
                return collect();
            }

            $matchingProductIds = $matchedProducts->pluck('id');

            // --- CRITICAL REDIS WRITE SECTION ---
            foreach($matchedProducts as $product)
            {
                if($product->pharmacy){
                    Redis::zincrby('pharmacy:leaderboard', 1, 'pharmacy:'.$product->pharmacy->id);
                }
                Redis::zincrby($productLeaderboardKey, 1, 'product:'.$product->id);
            }
            
            // --- SORTING LOGIC ---
            $sortedProductIdsWithPrefix = Redis::zrevrange($productLeaderboardKey, 0, -1);
            $allSortedIds = collect($sortedProductIdsWithPrefix)->map(fn($id) => (int)str_replace('product:', '', $id));
            $finalSortedIds = $allSortedIds->intersect($matchingProductIds)->values();

            $productsKeyed = $matchedProducts->keyBy('id'); 
            $finalProducts = $finalSortedIds->map(fn($id) => $productsKeyed->get($id))
            ->filter();

            return $finalProducts;
            
        }); // END of Lock::get() closure (returns $results)

        if ($results === null) {
            // Returns the last valid cached result if the current process couldn't get the lock.
            return Cache::get( $cacheKey , collect()); 
        }

        // Returns the successfully sorted products to the Cache::remember block.
        return $results;
        
    }); // END of Cache::remember closure (returns $products)

    // ... (rest of the function is correct and outside the closure) ...
    $categories = Cache::remember('categories', 3600, fn() => Category::all());
    $pharmacies = Cache::remember('pharmacies', 2, fn() => Pharmacy::where('id', '>=', 21)->get());
    $locations = Cache::remember('locations', 3600, fn() => Pharmacy::distinct()->pluck('location'));

    if ($products->isEmpty()) {
        return redirect()->route('products.unfound', ['query' => $search]);
    }

    return view('home', compact(
        'products',
        'categories',
        'pharmacies',
        'locations'
    ));
}


/**
 * words like my typing start appearing
 */

    public function liveSearch(Request $request)
    {
  
        $query = $request->input('q');
    if (!$query) return response()->json([]);

    $cacheKey = 'live_search:' . strtolower($query);

    $products = Cache::remember($cacheKey, 300, function() use ($query) {
        return Product::where('name', 'LIKE', "%$query%")
                      ->take(10)
                      ->get(['id','name','image']);
    });
     

        return response()->json($products);
    }




    /**
     * Get Suggestions near the word typing
     */

   public function smartSearch(Request $request)
{
     $input = trim($request->q);
    if (!$input) return response()->json(["did_you_mean" => null]);

    // Cache all product names
    $names = Cache::remember('product_names', 3600, function() {
        return Product::pluck('name')->toArray();
    });

    $suggestions = [];
    foreach ($names as $name) {
        $distance = levenshtein(mb_strtolower($input), mb_strtolower($name));
        if ($distance <= 3) {
            $suggestions[] = $name;
        }
    }

    return response()->json([
        "did_you_mean" => $suggestions[0] ?? null,
        "all" => $suggestions
    ]);
}




}
