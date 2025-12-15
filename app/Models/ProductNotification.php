<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductNotification extends Model
{
    protected $table = 'outstock_products'; 
   protected $guarded=[];

//get the user who notifies
     public function user()
    {
        return $this->belongsTo(User::class);
    }
//get the product which notified
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
