<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\DTO;

use App\Shared\Domain\DTO\AbstractDTO;

final class StoredImageFilesDTO extends AbstractDTO
{
    public function __construct(
        public string $original_path,
        public string $thumbnail_path,
    ) {}
}
