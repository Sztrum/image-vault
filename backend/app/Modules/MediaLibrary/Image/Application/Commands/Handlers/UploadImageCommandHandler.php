<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands\Handlers;

use App\Core\Application\Command\Abstracts\BaseCommandHandler;
use App\Core\Application\Command\CommandInterface;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;
use App\Modules\MediaLibrary\Image\Application\Services\ImageFileService;
use App\Modules\MediaLibrary\Image\Infrastructure\Metadata\PhpExifIptcExtractor;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\EloquentImageRepository;
use App\Modules\Weather\Infrastructure\OpenMeteo\OpenMeteoWeatherProvider;
use Throwable;

readonly class UploadImageCommandHandler extends BaseCommandHandler
{
    public function __construct(
        private ImageFileService $imageFileService,
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
        $newImage = $this->imageFileService->toNewImageDTO(
            filePath: $command->filePath,
            originalName: $command->originalName,
            uploaderName: $command->uploaderName,
            uploaderEmail: $command->uploaderEmail,
        );
        $metadata = $this->metadataExtractor->extract(sourcePath: $command->filePath, format: $newImage->format);
        $temperature = $this->weatherProvider->currentTemperatureInKatowice();

        $storedFiles = $this->imageFileService->storeFiles(filePath: $command->filePath, format: $newImage->format);

        try {
            $this->imageRepository->createFromUpload(
                newImage: $newImage,
                storedFiles: $storedFiles,
                metadata: $metadata,
                temperature: $temperature,
            );
        } catch (Throwable $exception) {
            $this->imageFileService->deleteFiles($storedFiles);

            throw $exception;
        }
    }
}
