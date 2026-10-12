<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\DTO;

use App\Shared\Domain\DTO\AbstractDTO;

final class UploadImageDTO extends AbstractDTO
{
    public function __construct(
        public string $file_path,
        public string $original_name,
        public string $uploader_name,
        public string $uploader_email,
    ) {}
}
