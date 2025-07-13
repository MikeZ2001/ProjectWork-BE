<?php

namespace Modules\Account\Repositories;

use App\Repositories\EloquentRepository;
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
}