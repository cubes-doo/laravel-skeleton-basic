<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        
//        /* morphMap for Image model */
//        Relation::morphMap([
//            'users' => \App\Models\User::class
//        ]);
    }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
