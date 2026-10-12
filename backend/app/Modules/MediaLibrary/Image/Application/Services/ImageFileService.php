<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Services;

use App\Modules\MediaLibrary\Image\Domain\DTO\NewImageDTO;
use App\Modules\MediaLibrary\Image\Domain\DTO\StoredImageFilesDTO;
use App\Modules\MediaLibrary\Image\Domain\DTO\UploadImageDTO;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\EmptyImageException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageTooLargeException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageTooSmallException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidImageNameException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\InvalidUploaderNameException;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\UnsupportedImageFormatException;
use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageName;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;
use App\Modules\MediaLibrary\Image\Infrastructure\Imaging\InterventionThumbnailGenerator;
use App\Modules\MediaLibrary\Image\Infrastructure\Storage\LaravelImageStorage;
use App\Shared\Domain\Exceptions\InvalidEmailException;
use App\Shared\Domain\ValueObjects\Email;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

readonly class ImageFileService
{
    public function __construct(
        private LaravelImageStorage $storage,
        private InterventionThumbnailGenerator $thumbnailGenerator,
    ) {}

    /**
     * @throws UnsupportedImageFormatException
     * @throws EmptyImageException
     * @throws ImageTooLargeException
     * @throws ImageTooSmallException
     * @throws InvalidImageNameException
     * @throws InvalidUploaderNameException
     * @throws InvalidEmailException
     */
    public function toNewImageDTO(UploadImageDTO $uploadImageDTO): NewImageDTO
    {
        $info = @getimagesize($uploadImageDTO->file_path);
        $size = filesize($uploadImageDTO->file_path);
        throw_if($info === false || $size === false, UnsupportedImageFormatException::class, null, [
            'mime' => 'unknown',
            'formats' => implode(', ', ImageFormatEnum::values()),
        ]);

        return new NewImageDTO(
            original_name: new ImageName($uploadImageDTO->original_name),
            format: ImageFormatEnum::fromMimeType($info['mime']),
            size: new FileSize($size),
            dimensions: new ImageDimensions(width: $info[0], height: $info[1]),
            uploader: new Uploader(
                name: $uploadImageDTO->uploader_name,
                email: new Email($uploadImageDTO->uploader_email),
            ),
        );
    }

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

    public function thumbnailResponse(Image $image): StreamedResponse
    {
        return $this->storage->response(storagePath: $image->thumbnail_path, headers: [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    public function downloadResponse(Image $image): StreamedResponse
    {
        return $this->storage->download(
            storagePath: $image->storage_path,
            name: $image->original_name,
            headers: ['Content-Type' => $image->mime_type],
        );
    }
}
