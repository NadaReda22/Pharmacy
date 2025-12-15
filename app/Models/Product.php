<?php

namespace App\Models;

use App\Models\Pharmacy;
use App\Models\ProductNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
        use HasFactory;

      protected $guarded=[];

  //get the pharmacy who sell  the product
      public function pharmacy()
      {
        return $this->belongsTo(Pharmacy::class);
      }


 //get all users who notified this      
      public function notifiedUsers()
      {
          return $this->belongsToMany(User::class, 'outstock_products');
      }
      


          protected static function booted()
    {
          static::saved(function ($product) {
        if ($product->wasChanged(['name', 'description', 'quantity'])) {
            // Clear all relevant caches
            Cache::forget('products');                        // general product list
            Cache::forget('products:sorted:popularity');      // sorted leaderboard
        }
    });


        static::deleted(function ($product) {
            Cache::forget('products');
        });
    }

}
