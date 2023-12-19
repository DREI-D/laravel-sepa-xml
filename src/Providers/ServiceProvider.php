<?php

namespace DREID\LaravelSepaXml\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'laravel-sepa-xml');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/sepa.php', 'sepa'
        );

        $this->publishes([
            __DIR__ . '/../../config/sepa.php' => config_path('sepa.php'),
        ]);
    }
}
