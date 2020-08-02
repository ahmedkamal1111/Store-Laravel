<?php

namespace App\Providers;

use App\User;
use App\Product;
use App\Mail\userCreated;
use App\Mail\userMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Product::updated(function($product){
            if($product->quantity== 0 && $product->isAvailable()){
                $product->status = Product::UNAVAILABLE_PRODUCT;
                $product->save();
            }
        });

        User::created(function($user){
            // Mail::to($user)->send(new userCreated($user));
        retry(5,function()use($user){
            Mail::to($user)->send(new userCreated($user));
        },100);
        });

        User::updated(function($user){
            if($user->isDirty('email')){
                // Mail::to($user)->send(new userMailChanged($user));
        retry(5,function()use($user){
            Mail::to($user)->send(new userMailChanged($user));
        },100);   
            }
        });


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
