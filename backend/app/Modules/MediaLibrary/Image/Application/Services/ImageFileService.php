<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Services;

use App\Modules\MediaLibrary\Image\Domain\DTO\StoredImageFilesDTO;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Modules\MediaLibrary\Image\Infrastructure\Imaging\InterventionThumbnailGenerator;
use App\Modules\MediaLibrary\Image\Infrastructure\Storage\LaravelImageStorage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

readonly class ImageFileService
{
    public function __construct(
        private LaravelImageStorage $storage,
        private InterventionThumbnailGenerator $thumbnailGenerator,
    ) {}

    /**
     * @throws RuntimeException
     * @throws Throwable
     */
    public function storeFiles(string $filePath, ImageFormatEnum $format): StoredImageFilesDTO
    {
        $thumbnail = $this->thumbnailGenerator->generate($filePath);
        $fileName = (string) Str::uuid7();

        $originalPath = $this->storage->storeOriginal(fileName: $fileName, format: $format, sourcePath: $filePath);

        try {
            $thumbnailPath = $this->storage->storeThumbnail(fileName: $fileName, contents: $thumbnail);
        } catch (Throwable $exception) {
            $this->storage->delete($originalPath);

            throw $exception;
        }

        return new StoredImageFilesDTO(original_path: $originalPath, thumbnail_path: $thumbnailPath);
    }

    public function deleteFiles(StoredImageFilesDTO $storedFiles): void
    {
        $this->storage->delete($storedFiles->original_path, $storedFiles->thumbnail_path);
    }

    public function deleteImageFiles(Image $image): void
    {
        $this->storage->delete($image->storage_path, $image->thumbnail_path);
    }
}
