<?php

namespace Modules\Account\Repositories;

use App\Filters\Ordering\Direction;
use App\Repositories\EloquentRepository;
use Illuminate\Support\Collection;
use Modules\Account\Models\Category;

/**
 * @method findById(int $id, array $relationList = null)
 * @method create(Category $category)
 * @method update(Category $category)
 * @method delete(Category $category)
 */
class CategoryRepository extends EloquentRepository
{
    protected string $modelClass = Category::class;

    public function findAllAndOrderBy(Direction $direction = Direction::DESC): Collection
    {
        return $this->makeBuilder()
            ->orderBy('name', $direction->value)->get();
    }
}