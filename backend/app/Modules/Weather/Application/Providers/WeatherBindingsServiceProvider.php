<?php

declare(strict_types=1);

namespace App\Modules\Weather\Application\Providers;

use App\Core\Application\Providers\ServiceProvider;
use App\Modules\Weather\Domain\Contracts\WeatherProviderInterface;
use App\Modules\Weather\Infrastructure\OpenMeteo\OpenMeteoWeatherProvider;

class WeatherBindingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            abstract: WeatherProviderInterface::class,
            concrete: OpenMeteoWeatherProvider::class,
        );
    }
}
