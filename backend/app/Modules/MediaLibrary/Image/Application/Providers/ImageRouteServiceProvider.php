<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Providers;

use App\Core\Application\Providers\Routes\ApiRouteServiceProvider;
use App\Modules\MediaLibrary\Image\UI\Http\Controllers\ImageController;
use Illuminate\Contracts\Routing\Registrar;

class ImageRouteServiceProvider extends ApiRouteServiceProvider
{
    protected function registerRoutes(Registrar $router): void
    {
        $router->post('/', [ImageController::class, 'store'])->name('store');
    }
}
