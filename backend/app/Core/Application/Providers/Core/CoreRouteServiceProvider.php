<?php

declare(strict_types=1);

namespace App\Core\Application\Providers\Core;

use App\Core\Application\Providers\Routes\ApiRouteServiceProvider;
use App\Core\UI\Http\Controllers\HealthController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class CoreRouteServiceProvider extends ApiRouteServiceProvider
{
    protected bool $prefix = false;

    protected bool $pluralRouteName = false;

    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function registerRoutes(Registrar $router): void
    {
        $router->get('health', HealthController::class)->name('health');
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(300)->by($request->ip()));
    }
}
