<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Queries;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\ImageRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

readonly class ListImagesQuery
{
    public function __construct(
        private ImageRepository $imageRepository,
    ) {}

    /**
     * @return CursorPaginator<int, Image>
     */
    public function execute(int $perPage, ?string $cursor): CursorPaginator
    {
        return $this->imageRepository->paginateByCursor(perPage: $perPage, cursor: $cursor);
    }
}
