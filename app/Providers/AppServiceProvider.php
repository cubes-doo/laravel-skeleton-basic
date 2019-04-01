<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;

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
        
        /* morphMap za Image model */
        Relation::morphMap([
            'examples' => \App\Models\Example::class
        ]);
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
