<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands\Handlers;

use App\Core\Application\Command\Abstracts\BaseCommandHandler;
use App\Core\Application\Command\CommandInterface;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;
use App\Modules\MediaLibrary\Image\Domain\DTO\NewImageDTO;
use App\Modules\MediaLibrary\Image\Domain\Enums\ImageFormatEnum;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\UnsupportedImageFormatException;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\FileSize;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageDimensions;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\ImageName;
use App\Modules\MediaLibrary\Image\Domain\ValueObjects\Uploader;
use App\Modules\MediaLibrary\Image\Infrastructure\Imaging\InterventionThumbnailGenerator;
use App\Modules\MediaLibrary\Image\Infrastructure\Metadata\PhpExifIptcExtractor;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\EloquentImageRepository;
use App\Modules\MediaLibrary\Image\Infrastructure\Storage\LaravelImageStorage;
use App\Modules\Weather\Infrastructure\OpenMeteo\OpenMeteoWeatherProvider;
use App\Shared\Domain\ValueObjects\Email;
use Illuminate\Support\Str;
use Throwable;

readonly class UploadImageCommandHandler extends BaseCommandHandler
{
    public function __construct(
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
        $newImage = $this->newImage($command);
        // Files get their own random name; the record links to them through storage_path and thumbnail_path.
        $fileName = (string) Str::uuid7();
        $storedPaths = [];

        try {
            $storedPaths[] = $storagePath = $this->storage->storeOriginal($fileName, $newImage->format, $command->filePath);
            $storedPaths[] = $thumbnailPath = $this->storage->storeThumbnail(
                $fileName,
                $this->thumbnailGenerator->generate($command->filePath),
            );
            $metadata = $this->metadataExtractor->extract($command->filePath, $newImage->format);
            $temperature = $this->weatherProvider->currentTemperatureInKatowice();

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

        $command->image = $image;
    }

    /**
     * Reads format, size and dimensions from the file itself, not from what the client sent.
     */
    private function newImage(UploadImageCommand $command): NewImageDTO
    {
        $info = @getimagesize($command->filePath);
        $size = filesize($command->filePath);
        throw_if($info === false || $size === false, UnsupportedImageFormatException::class, null, [
            'mime' => 'unknown',
            'formats' => implode(', ', ImageFormatEnum::values()),
        ]);

        return new NewImageDTO(
            original_name: new ImageName($command->originalName),
            format: ImageFormatEnum::fromMimeType($info['mime']),
            size: new FileSize($size),
            dimensions: new ImageDimensions($info[0], $info[1]),
            uploader: new Uploader($command->uploaderName, new Email($command->uploaderEmail)),
        );
    }
}
