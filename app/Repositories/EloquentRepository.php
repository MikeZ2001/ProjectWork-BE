<?php

namespace App\Repositories;

use App\Exceptions\EntityNotBoundException;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class EloquentRepository
{
    /**
     * Return all the available records for the defined model.
     *
     * @param array<string>|null $relationList
     *
     * @return Collection<int, Model>
     */
    public function findAll(array $relationList = null): Collection
    {
        return $this->makeBuilder($relationList)->get();
    }

    /**
     * Return all the available records for the defined model and initialize a paginator for them.
     *
     * @param array<string>|null $relationList
     *
     * @return LengthAwarePaginator<Model>
     */
    public function findAllAndPaginate(array $relationList = null): LengthAwarePaginator
    {
        return $this->makeBuilder($relationList)->paginate();
    }

    /**
     * Find a record by its primary key, namely id.
     *
     * @param int $id
     * @param array<string>|null $relationList
     *
     * @return Model
     */
    public function findById(int $id, array $relationList = null): Model
    {
        return $this->makeBuilder($relationList)
            ->where($this->getModelPrimaryKeyField(), '=', $id)
            ->firstOrFail();
    }

    /**
     * Check if an entry exists given its id.
     *
     * @param int $id
     *
     * @return bool
     */
    public function existsById(int $id): bool
    {
        return $this->makeBuilder()->where($this->getModelPrimaryKeyField(), '=', $id)->exists();
    }

    /**
     * Persist a given model instance on the database.
     *
     * @param Model $model
     *
     * @return Model
     */
    public function create(Model $model): Model
    {
        $model->save();
        return $model;
    }

    /**
     * Persist a given model instance on the database updating it (record must exist).
     *
     * @param Model $model
     *
     * @return Model
     *
     * @throws EntityNotBoundException If given module is not bound to an existing record.
     */
    public function update(Model $model): Model
    {
        if (!$model->exists) {
            throw new EntityNotBoundException('Cannot update a non-existing entity.');
        }
        $model->save();
        return $model;
    }

    /**
     * Delete a given model instance.
     *
     * @param Model $entity
     *
     * @return bool|null
     */
    public function delete(Model $entity): ?bool
    {
        return $entity->delete();
    }

    /**
     * z
     * /**
     * @var string The name of the model class this repository instance performs queries on.
     */
    protected string $modelClass;

    /**
     * @var array<string> A list of related entities that should be included by default.
     */
    protected array $baseRelationList = [];

    /**
     * Generate a query builder instance based on the defined model class.
     *
     * @param array<string>|null $relationList
     *
     * @return Builder<Model>
     */
    protected function makeBuilder(array $relationList = null): Builder
    {
        /** @var Builder<Model> $builder */
        // @phpstan-ignore-next-line
        $builder = call_user_func([$this->modelClass, 'query']);
        $completeRelationList = $this->baseRelationList;
        if (!empty($relationList)) {
            $completeRelationList = array_merge($this->baseRelationList, $relationList);
        }
        if (!empty($completeRelationList)) {
            $builder->with($completeRelationList);
        }
        return $builder;
    }

    /**
     * Return defined model's primary key field name.
     *
     * @return string
     */
    protected function getModelPrimaryKeyField(): string
    {
        $model = new $this->modelClass();
        if (!($model instanceof Model)) {
            throw new BadMethodCallException('Invalid model class defined.');
        }
        return $model->getKeyName();
    }
}
