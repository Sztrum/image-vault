<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Contracts;

use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormat;

interface ImageStorageInterface
{
    /**
     * @return string storage path of the original
     */
    public function storeOriginal(string $imageId, ImageFormat $format, string $sourcePath): string;

    /**
     * @return string storage path of the thumbnail
     */
    public function storeThumbnail(string $imageId, string $contents): string;

    public function absolutePath(string $storagePath): string;

    public function delete(string ...$storagePaths): void;
}
