<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\Infrastructure\Repositories;

use App\Core\Infrastructure\Repositories\Eloquent\EloquentModelRepository;
use App\Modules\MediaLibrary\Image\Domain\DTO\ImageMetadataDTO;
use App\Modules\MediaLibrary\Image\Domain\DTO\NewImageDTO;
use App\Modules\MediaLibrary\Image\Domain\DTO\StoredImageFilesDTO;
use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Modules\Weather\Domain\ValueObjects\Temperature;
use Illuminate\Contracts\Pagination\CursorPaginator;

/**
 * @extends EloquentModelRepository<Image>
 */
class EloquentImageRepository extends EloquentModelRepository
{
    public function model(): Image
    {
        return new Image;
    }

    public function save(Image $image): void
    {
        $image->save();
    }

    public function createFromUpload(
        NewImageDTO $newImage,
        StoredImageFilesDTO $storedFiles,
        ImageMetadataDTO $metadata,
        ?Temperature $temperature,
    ): Image {
        return $this->create([
            'original_name' => $newImage->original_name->value,
            'extension' => $newImage->format->value,
            'mime_type' => $newImage->format->mimeType(),
            'size_bytes' => $newImage->size->bytes,
            'width' => $newImage->dimensions->width,
            'height' => $newImage->dimensions->height,
            'storage_path' => $storedFiles->original_path,
            'thumbnail_path' => $storedFiles->thumbnail_path,
            'uploader_name' => $newImage->uploader->name,
            'uploader_email' => $newImage->uploader->email->value,
            'exif' => $metadata->exif,
            'iptc' => $metadata->iptc,
            'temperature_celsius' => $temperature?->celsius,
            'temperature_measured_at' => $temperature?->measuredAt,
        ]);
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
