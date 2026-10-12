<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Resources;

use App\Modules\MediaLibrary\Image\Domain\Models\Image;
use App\Shared\UI\Http\Resources\ApiResponseResource;

class ImageResource extends ApiResponseResource
{
    public function getResourceData(): array
    {
        $image = $this->getResource();

        return [
            'original_name' => $image->original_name,
            'extension' => $image->extension,
            'size_bytes' => $image->size_bytes,
            'width' => $image->width,
            'height' => $image->height,
            'uploader_name' => $image->uploader_name,
            'thumbnail_url' => route('api.images.thumbnail', ['id' => $image->id]),
            'download_url' => route('api.images.download', ['id' => $image->id]),
            'created_at' => $image->created_at->toIso8601String(),
        ];
    }

    public function getResource(): Image
    {
        /** @var Image $resource */
        $resource = $this->resource;

        return $resource;
    }

    public function getRelationships(): array
    {
        return [];
    }
}
