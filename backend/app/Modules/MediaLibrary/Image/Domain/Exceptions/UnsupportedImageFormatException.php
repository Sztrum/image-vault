<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormat;

final class UnsupportedImageFormatException extends DomainException
{
    public function __construct(string $mimeType)
    {
        parent::__construct('image::exceptions.unsupported_format', [
            'mime' => $mimeType,
            'formats' => implode(', ', ImageFormat::values()),
        ]);
    }
}
