<?php

declare(strict_types=1);

namespace DREID\LaravelSepaXml\Providers;

use DREID\LaravelSepaXml\Interfaces\Formatter;
use DREID\LaravelSepaXml\Interfaces\Sanitizer;
use DREID\LaravelSepaXml\Services\SepaFormatter;
use DREID\LaravelSepaXml\Services\SepaSanitizer;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laravel-sepa-xml');

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sepa.php', 'sepa'
        );

        $this->publishes([
            __DIR__ . '/../../config/sepa.php' => config_path('sepa.php'),
        ]);

        $this->app->bind(Sanitizer::class, SepaSanitizer::class);
        $this->app->bind(Formatter::class, SepaFormatter::class);
    }
}
