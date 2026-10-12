<?php

declare(strict_types=1);

namespace App\Modules\Weather\Application\Providers;

use App\Core\Application\Providers\ServiceProvider;
use App\Modules\Weather\Domain\Services\WeatherProviderInterface;
use App\Modules\Weather\Infrastructure\OpenMeteo\OpenMeteoWeatherProvider;

class WeatherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->app->singleton(WeatherProviderInterface::class, OpenMeteoWeatherProvider::class);
    }
}
