<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Aggregates;

use App\Modules\MediaLibrary\Image\Domain\DTO\NewImageDTO;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormat;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidImageNameException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;

final readonly class ImageAggregate
{
    public const int NAME_MAX_LENGTH = 255;

    /**
     * @throws InvalidImageNameException
     */
    public static function register(
        string $originalName,
        ImageFormat $format,
        FileSize $size,
        ImageDimensions $dimensions,
        Uploader $uploader,
    ): NewImageDTO {
        $originalName = trim($originalName);
        throw_if(
            $originalName === '' || mb_strlen($originalName) > self::NAME_MAX_LENGTH,
            InvalidImageNameException::class,
        );

        return new NewImageDTO(
            original_name: $originalName,
            format: $format,
            size: $size,
            dimensions: $dimensions,
            uploader: $uploader,
        );
    }
}
