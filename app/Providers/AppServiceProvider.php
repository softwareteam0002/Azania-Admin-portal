<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		Paginator::useBootstrap();

        Validator::extend('tanzanian_mobile', function ($attribute, $value, $parameters, $validator) {
            // Check if the value is a valid Tanzanian mobile number
            return preg_match('/^(\+255|255|0)[6-9]\d{8}$/', $value);
        });
//		$this->app['request']->server->set('HTTPS', true);
//		URL::forceScheme('https');

        $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);
    }
}
