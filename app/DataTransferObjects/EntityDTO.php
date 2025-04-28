<?php

namespace App\DataTransferObjects;

use Illuminate\Database\Eloquent\Model;

abstract readonly class EntityDTO extends DTO
{
    /**
     * Hydrates a given model instance filling up its properties.
     *
     * @param Model $model
     *
     * @return Model
     */
    public function hydrateModel(Model $model): Model
    {
        $propertyList = $this->toArray();
        foreach ($propertyList as $property => $value) {
            $model->{$property} = $value;
        }
        return $model;
    }

    /**
     * Converts this DTO into a Laravel model.
     *
     * @return Model
     */
    abstract public function toModel(): Model;
}
