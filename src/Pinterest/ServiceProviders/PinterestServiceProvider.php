<?php

namespace WaleedAhmad\Pinterest\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use WaleedAhmad\Pinterest\Pinterest;

class PinterestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Pinterest', function(){
            return new Pinterest(
                env('PINTEREST_KEY'),
                env('PINTEREST_SECRET')
            );
        });
    }
}
