<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Resources;

use App\Modules\MediaLibrary\Image\Application\Providers\ImageRouteServiceProvider;
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
            'thumbnail_url' => $this->imageUrl($image, 'thumbnail'),
            'download_url' => $this->imageUrl($image, 'download'),
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

    /**
     * Absolute, because the frontend runs on a different host.
     */
    private function imageUrl(Image $image, string $file): string
    {
        return implode('/', [ImageRouteServiceProvider::getBaseUrl(), 'images', $image->id, $file]);
    }
}
