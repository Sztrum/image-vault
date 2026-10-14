<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands;

use App\Core\Application\Command\CommandInterface;
use App\Modules\MediaLibrary\Image\Application\Commands\Handlers\UploadImageCommandHandler;
use App\Modules\MediaLibrary\Image\Domain\DTO\UploadImageDTO;

/**
 * @see UploadImageCommandHandler
 */
readonly class UploadImageCommand implements CommandInterface
{
    public function __construct(
        public UploadImageDTO $uploadImageDTO,
    ) {}
}
