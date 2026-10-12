<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Queries;

use App\Modules\MediaLibrary\Image\Domain\Exceptions\ImageNotFoundException;
use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\ImageRepository;

readonly class GetImageQuery
{
    public function __construct(
        private ImageRepository $imageRepository,
    ) {}

    /**
     * @throws ImageNotFoundException
     */
    public function execute(string $id): Image
    {
        return $this->imageRepository->firstById($id) ?? throw new ImageNotFoundException;
    }
}
