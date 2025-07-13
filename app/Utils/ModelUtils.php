<?php

namespace App\Utils;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class ModelUtils
{
    /**
     * Convert a given model instance into an array representation serializing its properties into primitives.
     *
     * @param Model $model
     *
     * @return array<string, mixed>
     */
    public static function extractToPlainArray(Model $model): array
    {
        $properties = $model->getAttributes();
        foreach ($properties as $name => $property) {
            if ($property instanceof DateTimeInterface) {
                $properties[$name] = DateUtils::toDateTimeString($property);
            }
        }
        return $properties;
    }
}