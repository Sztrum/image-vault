<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Exceptions;

use App\Core\Domain\Exceptions\DomainException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;

final class ImageTooLargeException extends DomainException
{
    public function __construct(int $bytes)
    {
        parent::__construct('image::exceptions.too_large', [
            'max' => FileSize::MAX_BYTES / 1024 / 1024,
            'bytes' => $bytes,
        ]);
    }
}
