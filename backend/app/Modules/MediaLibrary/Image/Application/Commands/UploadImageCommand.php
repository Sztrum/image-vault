<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands;

use App\Core\Application\Command\CommandTransactionalInterface;
use App\Modules\MediaLibrary\Image\Domain\Models\Image;

final class UploadImageCommand implements CommandTransactionalInterface
{
    public ?Image $image = null;

    public function __construct(
        public readonly string $filePath,
        public readonly string $originalName,
        public readonly string $uploaderName,
        public readonly string $uploaderEmail,
    ) {}
}
