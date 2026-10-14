<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands\Handlers;

use App\Core\Application\Command\Abstracts\BaseCommandHandler;
use App\Core\Application\Command\CommandInterface;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;
use App\Modules\MediaLibrary\Image\Application\Services\ImageFileService;
use App\Modules\MediaLibrary\Image\Application\Services\ImageInspectionService;
use App\Modules\MediaLibrary\Image\Infrastructure\Metadata\PhpExifIptcExtractor;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\ImageRepository;
use App\Modules\Weather\Domain\Services\WeatherProviderInterface;
use Throwable;

readonly class UploadImageCommandHandler extends BaseCommandHandler
{
    public function __construct(
        private ImageInspectionService $imageInspectionService,
        private ImageFileService $imageFileService,
        private PhpExifIptcExtractor $metadataExtractor,
        private WeatherProviderInterface $weatherProvider,
        private ImageRepository $imageRepository,
    ) {}

    /**
     * @param  UploadImageCommand  $command
     *
     * @throws Throwable
     */
    public function handle(CommandInterface $command): void
    {
        $newImage = $this->imageInspectionService->toNewImageDTO($command->uploadImageDTO);
        $metadata = $this->metadataExtractor->extract(
            sourcePath: $command->uploadImageDTO->file_path,
            format: $newImage->format,
        );
        $temperature = $this->weatherProvider->currentTemperatureInKatowice();
        $storedFiles = $this->imageFileService->storeFiles(
            filePath: $command->uploadImageDTO->file_path,
            format: $newImage->format,
        );

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
