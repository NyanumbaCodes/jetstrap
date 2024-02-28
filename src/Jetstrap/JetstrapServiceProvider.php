<?php

namespace NascentAfrica\Jetstrap;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use NascentAfrica\Jetstrap\Console\SwapHandlers\BreezeHandler;
use NascentAfrica\Jetstrap\Console\SwapHandlers\JetstreamHandler;

class JetstrapServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('jetstrap', function () {
            return new Jetstrap;
        });

        $this->app->bind('jetstrap.jetstream.handler', function () {
            return new JetstreamHandler(new Filesystem);
        });

        $this->app->bind('jetstrap.breeze.handler', function () {
            return new BreezeHandler(new Filesystem);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
        $this->configureCommands();
    }

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/jetstream'),
        ], 'jetstrap-views');
    }

    /**
     * Configure the commands offered by the application.
     *
     * @return void
     */
    protected function configureCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }
}
