<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repositories;

use App\Core\Domain\Models\Model;

/**
 * @template TModel of Model
 */
interface ModelRepositoryInterface
{
    /**
     * @return TModel
     */
    public function model(): Model;

    /**
     * @param  array<string, mixed>  $params
     * @return TModel
     */
    public function create(array $params): Model;

    /**
     * @param  TModel  $model
     */
    public function delete(Model $model): void;

    /**
     * @return TModel|null
     */
    public function firstById(string $id): ?Model;
}
