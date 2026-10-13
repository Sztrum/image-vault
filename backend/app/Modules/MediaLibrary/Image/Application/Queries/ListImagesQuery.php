<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Application\Queries;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Modules\MediaLibrary\Image\Infrastructure\Repositories\ImageRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Validation\ValidationException;
use UnexpectedValueException;

readonly class ListImagesQuery
{
    public function __construct(
        private ImageRepository $imageRepository,
    ) {}

    /**
     * @return CursorPaginator<int, Image>
     *
     * @throws ValidationException
     */
    public function execute(int $perPage, ?string $cursor): CursorPaginator
    {
        try {
            return $this->imageRepository->paginateByCursor(perPage: $perPage, cursor: $cursor);
        } catch (UnexpectedValueException) {
            throw ValidationException::withMessages(['cursor' => __('image::validation.invalid_cursor')]);
        }
    }
}
