<?php

declare(strict_types=1);

namespace App\Shared\UI\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiResponseResource extends JsonResource
{
    /**
     * @param  mixed  $resource
     * @param  array<string, mixed>  $additionalData
     */
    public function __construct(
        $resource,
        protected ?string $relationships = null,
        protected bool $asResponse = false,
        protected array $additionalData = [],
    ) {
        parent::__construct($resource);
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function getResourceData(): array;

    /**
     * @return array<string, mixed>
     */
    abstract public function getRelationships(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        if ($this->asResponse) {
            return [
                'status' => Response::HTTP_OK,
                'data' => $this->getAllResourceData() + $this->additionalData,
            ];
        }

        return $this->getAllResourceData();
    }

    /**
     * @return array<string, mixed>
     */
    private function getAllResourceData(): array
    {
        $id = data_get($this->resource, 'id');
        $idData = $this->shouldIncludeId() && (is_int($id) || is_string($id)) ? ['id' => $id] : [];

        return $idData + $this->getResourceData() + $this->getRelationships();
    }

    protected function shouldIncludeId(): bool
    {
        return true;
    }

    /**
     * @return Collection<int, string>
     */
    protected function getRelationshipsNames(): Collection
    {
        if ($this->relationships === '' || $this->relationships === null) {
            /** @var Collection<int, string> $empty */
            $empty = collect();

            return $empty;
        }

        /** @var Collection<int, string> $relationships */
        $relationships = collect(explode(',', trim($this->relationships)))
            ->map(static fn (string $relationship): string => trim($relationship))
            ->filter(static fn (string $relationship): bool => $relationship !== '')
            ->values();

        return $relationships;
    }
}
