<?php

declare(strict_types=1);

use App\Core\Domain\Exceptions\ExceptionHandler;
use App\Core\UI\Http\Middleware\ForceJsonResponse;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        $middleware->throttleApi();
    })
    ->create();

$app->singleton(ExceptionHandlerContract::class, ExceptionHandler::class);

return $app;
