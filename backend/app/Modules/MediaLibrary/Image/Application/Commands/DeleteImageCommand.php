<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands;

use App\Core\Application\Command\CommandTransactionalInterface;
use App\Modules\MediaLibrary\Image\Application\Commands\Handlers\DeleteImageCommandHandler;

/**
 * @see DeleteImageCommandHandler
 */
readonly class DeleteImageCommand implements CommandTransactionalInterface
{
    public function __construct(
        public string $imageId,
    ) {}
}
