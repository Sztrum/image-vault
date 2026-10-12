<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Providers;

use App\Core\Application\Providers\ServiceProvider;
use App\Modules\MediaLibrary\Image\Domain\Contracts\ImageMetadataExtractorInterface;
use App\Modules\MediaLibrary\Image\Domain\Contracts\ImageRepositoryInterface;
use App\Modules\MediaLibrary\Image\Domain\Contracts\ImageStorageInterface;
use App\Modules\MediaLibrary\Image\Domain\Contracts\ThumbnailGeneratorInterface;
use App\Modules\MediaLibrary\Image\Infrastructure\Imaging\InterventionThumbnailGenerator;
use App\Modules\MediaLibrary\Image\Infrastructure\Metadata\PhpExifIptcExtractor;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\EloquentImageRepository;
use App\Modules\MediaLibrary\Image\Infrastructure\Storage\LaravelImageStorage;

class ImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            abstract: ImageRepositoryInterface::class,
            concrete: EloquentImageRepository::class,
        );

        $this->app->bind(
            abstract: ImageStorageInterface::class,
            concrete: LaravelImageStorage::class,
        );

        $this->app->bind(
            abstract: ThumbnailGeneratorInterface::class,
            concrete: InterventionThumbnailGenerator::class,
        );

        $this->app->bind(
            abstract: ImageMetadataExtractorInterface::class,
            concrete: PhpExifIptcExtractor::class,
        );
    }
}
