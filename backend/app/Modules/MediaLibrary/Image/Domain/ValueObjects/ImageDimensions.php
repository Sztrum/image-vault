<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\ValueObjects;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageTooSmallException;

final readonly class ImageDimensions
{
    public const int MIN_SIDE = 500;

    /**
     * @throws ImageTooSmallException
     */
    public function __construct(
        public int $width,
        public int $height,
    ) {
        throw_if($width < self::MIN_SIDE || $height < self::MIN_SIDE, ImageTooSmallException::class, $width, $height);
    }
}
