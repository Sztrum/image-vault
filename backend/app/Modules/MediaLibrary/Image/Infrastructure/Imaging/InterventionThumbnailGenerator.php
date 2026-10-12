<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Infrastructure\Imaging;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Intervention\Image\ImageManager;

class InterventionThumbnailGenerator
{
    private readonly ImageManager $manager;

    public function __construct(
        private readonly ConfigContract $config,
    ) {
        // Imagick, not GD: GD cannot read TIFF.
        $this->manager = ImageManager::imagick();
    }

    /**
     * @return string binary WebP contents
     */
    public function generate(string $sourcePath): string
    {
        $maxSide = $this->config->integer('image::image.thumbnail_max_side');

        return $this->manager->read($sourcePath)
            ->scaleDown(width: $maxSide, height: $maxSide)
            ->toWebp()
            ->toString();
    }
}
