<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\DTO;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormat;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;
use App\Shared\Domain\DTO\AbstractDTO;

final class NewImageDTO extends AbstractDTO
{
    public function __construct(
        public string $original_name,
        public ImageFormat $format,
        public FileSize $size,
        public ImageDimensions $dimensions,
        public Uploader $uploader,
    ) {}
}
