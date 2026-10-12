<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Contracts;

interface ThumbnailGeneratorInterface
{
    /**
     * @return string binary WebP contents
     */
    public function generate(string $sourcePath): string;
}
