<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Contracts;

use App\Modules\MediaLibrary\Image\Domain\DTO\ImageMetadataDTO;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormat;

interface ImageMetadataExtractorInterface
{
    public function extract(string $sourcePath, ImageFormat $format): ImageMetadataDTO;
}
