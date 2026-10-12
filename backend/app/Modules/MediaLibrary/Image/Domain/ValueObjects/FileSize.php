<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\ValueObjects;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\EmptyImageException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageTooLargeException;

final readonly class FileSize
{
    public const int MAX_BYTES = 5 * 1024 * 1024;

    /**
     * @throws EmptyImageException|ImageTooLargeException
     */
    public function __construct(
        public int $bytes,
    ) {
        throw_if($bytes < 1, EmptyImageException::class);
        throw_if($bytes > self::MAX_BYTES, ImageTooLargeException::class, null, ['max' => self::MAX_BYTES / 1024 / 1024]);
    }
}
