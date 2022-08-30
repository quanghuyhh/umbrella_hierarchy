<?php

namespace App\Services;

use App\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, ModelNotFoundException};
use Illuminate\Support\{Enumerable, Str};
use Throwable;

/**
 * Class BaseService
 * @package App\Services
 *
 * @property Model|Builder $model
 */
abstract class BaseService
{

    protected Model $model;
    protected static array $relations = [];
    protected array $counts = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param $query
     * @param $orderBys
     *
     * @return Builder
     */
    protected function applyOrderBy(Builder $query, $orderBys): Builder
    {
        $orderByScopes = [];
        $isOrderedBy = false;
        foreach ($orderBys as $column => $direction) {
            if (!in_array($direction, ['asc', 'desc'], true)) {
                continue;
            }
            $scopeName = 'OrderBy' . Str::camel($column);
            if ($this->model->hasNamedScope($scopeName)) {
                $orderByScopes[$scopeName] = $direction;
            } else {
                $query->orderBy($column, $direction);
            }
            $isOrderedBy = true;
        }
        if ($isOrderedBy) {
            $query->withoutGlobalScope('defaultOrder');
        }

        return $query->scopes($orderByScopes);
    }

    /**
     * @param string $id
     *
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    public function get(string $id)
    {
        return $this->newQuery()->with(static::$relations)->findOrFail($id);
    }

    public function getEnumerable(array $ids, $relations = [], $counts = []): Enumerable
    {
        $query = $this->model->whereIn('id', $ids)->withoutGlobalScopes([BaseModel::DEFAULT_ORDER_SCOPE]);
        if ($relations) {
            $query->with($relations);
        }
        if ($counts) {
            $query->withCount($counts);
        }

        return $query->cursor();
    }

    /**
     * @param array $data
     *
     * @return Model
     *
     * @throws Exception
     */
    public function create(array $data): Model
    {
        return tap(
            $this->newQuery()->create($data),
            function ($instance) {
                if (!$instance) {
                    throw new Exception(__('exceptions.actions.create_failed'));
                }
            }
        )->fresh(static::$relations);
    }

    /**
     * @param $id
     * @param array $data
     *
     * @return Model
     *
     * @throws ModelNotFoundException|Exception
     */
    public function update($id, array $data): Model
    {
        $query = $this->newQuery();
        $model = tap(
            $query->findOrFail($id),
            function ($instance) use ($data) {
                if (!$instance->update($data)) {
                    throw new Exception(__('exceptions.actions.update_failed'));
                }
            }
        );

        return $model->load(static::$relations);
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function delete($id): void
    {
        $query = $this->newQuery();
        $model = $query->findOrFail($id);
        try {
            $model->delete();
        } catch (Throwable $e) {
            throw new Exception('Delete failed');
        }
    }

    /**
     * @return Builder|Model|mixed
     */
    public function newQuery()
    {
        return $this->model->newQuery();
    }

    /**
     * @param $scopes
     * @return array
     */
    protected function loadScopes($scopes): array
    {
        $namedScoped = [];
        foreach ($scopes as $name => $args) {
            $scopeName = Str::camel($name);
            if (!$this->model->hasNamedScope($scopeName) || is_null($args)) {
                continue;
            }
            $namedScoped[$scopeName] = $args;
        }

        return $namedScoped;
    }
}
