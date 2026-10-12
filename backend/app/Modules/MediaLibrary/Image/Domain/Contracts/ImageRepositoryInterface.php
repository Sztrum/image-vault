<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Domain\Contracts;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface ImageRepositoryInterface
{
    public function save(Image $image): void;

    public function firstById(string $id): ?Image;

    public function delete(Image $image): void;

    /**
     * Newest first: created_at DESC, id DESC.
     *
     * @return CursorPaginator<int, Image>
     */
    public function paginateByCursor(int $perPage, ?string $cursor): CursorPaginator;
}
