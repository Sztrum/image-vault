<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;

final class ImageTooSmallException extends DomainException
{
    public function __construct(int $width, int $height)
    {
        parent::__construct('image::exceptions.too_small', [
            'min' => ImageDimensions::MIN_SIDE,
            'width' => $width,
            'height' => $height,
        ]);
    }
}
