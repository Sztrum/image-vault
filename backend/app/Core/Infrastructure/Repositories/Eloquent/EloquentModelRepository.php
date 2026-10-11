<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repositories\Eloquent;

use App\Core\Domain\Models\Model;
use App\Core\Infrastructure\Repositories\ModelRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of Model
 *
 * @implements ModelRepositoryInterface<TModel>
 */
abstract class EloquentModelRepository implements ModelRepositoryInterface
{
    /**
     * @return Builder<TModel>
     */
    protected function query(): Builder
    {
        /** @var Builder<TModel> $query */
        $query = $this->model()->newQuery();

        return $query;
    }

    /**
     * @param  array<string, mixed>  $params
     * @return TModel
     */
    public function create(array $params): Model
    {
        return $this->query()->create($params);
    }

    /**
     * @param  TModel  $model
     */
    public function delete(Model $model): void
    {
        $model->delete();
    }

    /**
     * @return TModel|null
     */
    public function firstById(string $id): ?Model
    {
        return $this->query()->find($id);
    }
}
