<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Infrastructure\Repositories;

use App\Core\Infrastructure\Repositories\Eloquent\EloquentModelRepository;
use App\Modules\MediaLibrary\Image\Domain\Contracts\ImageRepositoryInterface;
use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use Illuminate\Contracts\Pagination\CursorPaginator;

/**
 * @extends EloquentModelRepository<Image>
 */
class EloquentImageRepository extends EloquentModelRepository implements ImageRepositoryInterface
{
    public function model(): Image
    {
        return new Image;
    }

    public function save(Image $image): void
    {
        $image->save();
    }

    public function firstById(string $id): ?Image
    {
        return $this->query()->find($id);
    }

    /**
     * @return CursorPaginator<int, Image>
     */
    public function paginateByCursor(int $perPage, ?string $cursor): CursorPaginator
    {
        return $this->query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->cursorPaginate(perPage: $perPage, cursor: $cursor);
    }
}
