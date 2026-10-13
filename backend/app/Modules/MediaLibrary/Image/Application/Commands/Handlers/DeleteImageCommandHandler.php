<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Commands\Handlers;

use App\Core\Application\Command\Abstracts\BaseCommandHandler;
use App\Core\Application\Command\CommandInterface;
use App\Modules\MediaLibrary\Image\Application\Commands\DeleteImageCommand;
use App\Modules\MediaLibrary\Image\Application\Queries\GetImageQuery;
use App\Modules\MediaLibrary\Image\Application\Services\ImageFileService;
use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageNotFoundException;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\ImageRepository;
use Illuminate\Database\DatabaseManager;

readonly class DeleteImageCommandHandler extends BaseCommandHandler
{
    public function __construct(
        private GetImageQuery $getImageQuery,
        private ImageRepository $imageRepository,
        private ImageFileService $imageFileService,
        private DatabaseManager $database,
    ) {}

    /**
     * @param  DeleteImageCommand  $command
     *
     * @throws ImageNotFoundException
     */
    public function handle(CommandInterface $command): void
    {
        $image = $this->getImageQuery->execute($command->imageId);

        $this->imageRepository->delete($image);
        $this->database->afterCommit(fn () => $this->imageFileService->deleteImageFiles($image));
    }
}
