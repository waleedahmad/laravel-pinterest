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
        $this->publishes([
            __DIR__.'/../Config/pinterest.php' => config_path('pinterest.php'),
        ]);
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
                config('pinterest.config.client_id'),
                config('pinterest.config.client_secret')
            );
        });
    }
}
