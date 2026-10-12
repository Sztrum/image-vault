<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands;

use App\Core\Application\Command\CommandTransactionalInterface;
use App\Modules\MediaLibrary\Image\Application\Commands\Handlers\UploadImageCommandHandler;
use App\Modules\MediaLibrary\Image\Domain\DTO\UploadImageDTO;

/**
 * @see UploadImageCommandHandler
 */
readonly class UploadImageCommand implements CommandTransactionalInterface
{
    public function __construct(
        public UploadImageDTO $uploadImageDTO,
    ) {}
}
