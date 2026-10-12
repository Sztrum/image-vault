<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Resources;

use App\Shared\UI\Http\Resources\ApiCollectionResource;
use App\Shared\UI\Http\Resources\ApiResponseResource;

class CollectionImageResource extends ApiCollectionResource
{
    public $collects = ImageResource::class;

    public function getResource(mixed $model): ApiResponseResource
    {
        return new ImageResource($model, $this->relationships);
    }
}
