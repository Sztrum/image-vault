<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Contracts;

use App\Modules\MediaLibrary\Image\Domain\DTO\ImageMetadataDTO;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;

interface ImageMetadataExtractorInterface
{
    public function extract(string $sourcePath, ImageFormatEnum $format): ImageMetadataDTO;
}
