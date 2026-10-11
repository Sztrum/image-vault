<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiCollectionResource extends ResourceCollection
{
    /**
     * @param  mixed  $resource
     */
    public function __construct(
        $resource,
        protected ?string $relationships = null,
        protected bool $asResponse = false,
    ) {
        parent::__construct($resource);
    }

    abstract public function getResource(mixed $model): ApiResponseResource;

    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        if ($this->resource instanceof LengthAwarePaginator) {
            $collection = $this->resource->getCollection();

            /** @var Collection<int|string, mixed> $mapped */
            $mapped = $collection->map(function ($model) use ($request) {
                return $this->getResource($model)->toArray($request);
            });

            $this->resource->setCollection($mapped);

            /** @var array<string, mixed> $items */
            $items = $this->resource->toArray();

            return $items;
        }

        if ($this->resource instanceof Collection) {
            /** @var array<string, mixed> $items */
            $items = $this->resource->map(function ($model) use ($request) {
                return $this->getResource($model)->toArray($request);
            })->toArray();

            return $items;
        }

        /** @var array<string, mixed> $items */
        $items = parent::toArray($request);

        return $items;
    }

    /**
     * @return array<string, int>
     */
    public function with(Request $request): array
    {
        if ($this->asResponse) {
            return [
                'status' => Response::HTTP_OK,
            ];
        }

        return [];
    }
}
