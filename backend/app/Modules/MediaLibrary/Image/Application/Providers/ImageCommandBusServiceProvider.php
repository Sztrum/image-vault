<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Providers;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Providers\CommandBusServiceProvider;
use App\Modules\MediaLibrary\Image\Application\Commands\DeleteImageCommand;
use App\Modules\MediaLibrary\Image\Application\Commands\Handlers\DeleteImageCommandHandler;
use App\Modules\MediaLibrary\Image\Application\Commands\Handlers\UploadImageCommandHandler;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;

class ImageCommandBusServiceProvider extends CommandBusServiceProvider
{
    protected function registerCommands(CommandBusInterface $commandBus): void
    {
        $commandBus->map([
            UploadImageCommand::class => UploadImageCommandHandler::class,
            DeleteImageCommand::class => DeleteImageCommandHandler::class,
        ]);
    }
}
