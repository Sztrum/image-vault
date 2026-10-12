<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands\Handlers;

use App\Core\Application\Command\Abstracts\BaseCommandHandler;
use App\Core\Application\Command\CommandInterface;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;
use App\Modules\MediaLibrary\Image\Application\Services\ImageFileService;
use App\Modules\MediaLibrary\Image\Infrastructure\Imaging\InterventionThumbnailGenerator;
use App\Modules\MediaLibrary\Image\Infrastructure\Metadata\PhpExifIptcExtractor;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\EloquentImageRepository;
use App\Modules\MediaLibrary\Image\Infrastructure\Storage\LaravelImageStorage;
use App\Modules\Weather\Infrastructure\OpenMeteo\OpenMeteoWeatherProvider;
use Illuminate\Support\Str;
use Throwable;

readonly class UploadImageCommandHandler extends BaseCommandHandler
{
    public function __construct(
        private ImageFileService $imageFileService,
        private LaravelImageStorage $storage,
        private InterventionThumbnailGenerator $thumbnailGenerator,
        private PhpExifIptcExtractor $metadataExtractor,
        private OpenMeteoWeatherProvider $weatherProvider,
        private EloquentImageRepository $imageRepository,
    ) {}

    /**
     * @param  UploadImageCommand  $command
     *
     * @throws Throwable
     */
    public function handle(CommandInterface $command): void
    {
        // Prepare everything first: nothing below writes, so nothing needs cleaning up on failure.
        $newImage = $this->imageFileService->toNewImageDTO(
            filePath: $command->filePath,
            originalName: $command->originalName,
            uploaderName: $command->uploaderName,
            uploaderEmail: $command->uploaderEmail,
        );
        $thumbnail = $this->thumbnailGenerator->generate($command->filePath);
        $metadata = $this->metadataExtractor->extract(sourcePath: $command->filePath, format: $newImage->format);
        $temperature = $this->weatherProvider->currentTemperatureInKatowice();

        // Files get their own random name; the record links to them through storage_path and thumbnail_path.
        $fileName = (string) Str::uuid7();
        $storedPaths = [];

        try {
            $storedPaths[] = $storagePath = $this->storage->storeOriginal(
                fileName: $fileName,
                format: $newImage->format,
                sourcePath: $command->filePath,
            );
            $storedPaths[] = $thumbnailPath = $this->storage->storeThumbnail(fileName: $fileName, contents: $thumbnail);

            $image = $this->imageRepository->model();
            $image->fill([
                'original_name' => $newImage->original_name->value,
                'extension' => $newImage->format->value,
                'mime_type' => $newImage->format->mimeType(),
                'size_bytes' => $newImage->size->bytes,
                'width' => $newImage->dimensions->width,
                'height' => $newImage->dimensions->height,
                'storage_path' => $storagePath,
                'thumbnail_path' => $thumbnailPath,
                'uploader_name' => $newImage->uploader->name,
                'uploader_email' => $newImage->uploader->email->value,
                'exif' => $metadata->exif,
                'iptc' => $metadata->iptc,
                'temperature_celsius' => $temperature?->celsius,
                'temperature_measured_at' => $temperature?->measuredAt,
            ]);
            $this->imageRepository->save($image);
        } catch (Throwable $exception) {
            // A DB rollback does not remove files, so clean them up by hand.
            $this->storage->delete(...$storedPaths);

            throw $exception;
        }
    }
}
