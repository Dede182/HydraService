<?php

namespace HydraService\Provider;
use HydraService\Commands\CustomServiceCreate;

class HydraServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/storagepath.php' => config_path('storagepath.php'),
        ], 'config');
    }

    public function register()
    {

        $this->app->singleton('storageProvider', function ($app) {
            return config('storagepath.provider');
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                CustomServiceCreate::class,
            ]);
        }
    }
}
