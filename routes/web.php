<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\NotificationController;

//home page 
    // Route::get('/', function () {
    // return view('/home');
    // });

   Route::get('/home', [ProductController::class, 'showProduct'])->name('home');

    //Show login Page

    Route::get('/login',[LoginController::class,'show'])->middleware(['throttle:login']);

//
    Route::middleware('auth')->group(function(){
        //Logout 
        Route::get('/logout',[LoginController::class,'logout'])->name('logout');
        //Notify Us button
        Route::post('/notify-me/{product}', [NotificationController::class, 'subscribe'])->name('notify.subscribe')->middleware('throttle:notify');
        //Restock Product Update Notification
        Route::get('/update', [NotificationController::class, 'update']);
    });


    //Login Post Request 

    Route::post('/auth/login',[LoginController::class,'login'])->name('login');
    //Show Register Page

    Route::get('/register',[RegisterController::class,'show']);
    //Register Post Request

    Route::post('/auth/register',[RegisterController::class,'register'])->name('register')->middleware(['throttle:register']);




    //Search Filter 

    Route::get('/products/filter', [ProductController::class, 'filter'])
        ->name('products.filter');

    // Live search during typing

    Route::get('/live-search', [ProductController::class, 'liveSearch'])
        ->name('products.live')->middleware('throttle:live-search');

    // Smart search after typing & pressing enter
    Route::get('/smart-search', [ProductController::class, 'smartSearch'])
        ->name('products.smart');

    //UNfound Product  Page 
    Route::get('/unfound', function (Request $request) {
    $query = $request->query('query'); //the word sent
    $suggest = $request->query('suggest');
        return view('unfound', compact('suggest','query'));
    })->name('products.unfound');


   //Show Product -Not Real & work ,just for example ok? ^_^

   Route::get('/products/{product}', [ProductController::class, 'show'])
     ->name('product.show');
