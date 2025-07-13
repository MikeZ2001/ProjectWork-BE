<?php

namespace Modules\Account\DataTransferObjects;

use App\DataTransferObjects\EntityDTO;
use Modules\Account\Models\Category;

/**
 * @method Category hydrateModel(Category $category)
 */
readonly class CategoryDTO extends EntityDTO
{
    public function __construct(
        protected string $name,
        protected ?string $description = null,
    ) {
    }

    /**
     * @return Category
     */
    public function toModel(): Category
    {
        return $this->hydrateModel(new Category());
    }
}