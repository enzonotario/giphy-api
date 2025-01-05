<?php

namespace App\Providers;

use App\Services\GiphyService;
use Illuminate\Support\ServiceProvider;

class GiphyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            GiphyService::class,
            fn (): GiphyService => new GiphyService(
                config('services.giphy.key'),
                config('services.giphy.base_url')
            )
        );
    }
}
