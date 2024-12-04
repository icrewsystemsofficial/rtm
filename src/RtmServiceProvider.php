<?php

namespace Icrewsystems\Rtm;


use Icrewsystems\Rtm\Commands\ExportRTMToCsv;
use Icrewsystems\Rtm\Commands\ExportRTMToMarkdown;
use IcrewSystems\Rtm\Commands\ExportRTMToZip;
use Icrewsystems\Rtm\Commands\GenerateDuskTestCase;
use Icrewsystems\Rtm\Commands\GenerateGifForTestCases;
use Illuminate\Support\ServiceProvider;

class RtmServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'rtm');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'rtm');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('rtm.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/rtm'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                 __DIR__.'/../resources/assets' => public_path('vendor/rtm'),
             ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/rtm'),
            ], 'lang');*/

            $this->commands(ExportRTMToCsv::class);
            $this->commands(ExportRTMToMarkdown::class);
            $this->commands(GenerateGifForTestCases::class);
            $this->commands(GenerateDuskTestCase::class);
            $this->commands(ExportRTMToZip::class);
            
        }

    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'rtm');

        // Register the main class to use with the facade
        $this->app->singleton('rtm', function () {
            return new Rtm;
        });
    }
}
