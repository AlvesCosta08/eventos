<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Ajuste temporario do Vite na hospedagem.
        if (app()->environment('production')) {
            Vite::createAssetPathsUsing(
                function (string $path, ?bool $secure = null): string {
                    $baseUrl = rtrim(
                        config('app.asset_url') ?: config('app.url'),
                        '/'
                    );

                    return $baseUrl . '/public/' . ltrim($path, '/');
                }
            );
        }
    }
}
