<?php

namespace Modules\Account\Services;

use App\Exceptions\ResourceNotCreatedException;
use App\Exceptions\ResourceNotDeletedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ResourceNotUpdatedException;
use App\Filters\Ordering\Direction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Modules\Account\DataTransferObjects\CategoryDTO;
use Modules\Account\Models\Category;
use Modules\Account\Repositories\CategoryRepository;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository
    )
    {
    }

    public function findAll(): Collection
    {
        return $this->categoryRepository->findAllAndOrderBy(Direction::ASC);
    }

    public function findById(int $id): Category
    {
        try {
            return $this->categoryRepository->findById($id);
        } catch (ModelNotFoundException $ex) {
            throw new ResourceNotFoundException('Category not found', previous: $ex);
        }
    }

    /**
     * @throws \Throwable
     * @throws ResourceNotCreatedException
     */
    public function create(CategoryDTO $categoryDTO): Category
    {
        try {
            return $this->categoryRepository->create($categoryDTO->toModel());
        } catch (\Exception $ex) {
            throw new ResourceNotCreatedException('Failed to create category', previous: $ex);
        }
    }

    public function update(CategoryDTO $categoryDTO, int $categoryId)
    {
        $category = $this->categoryRepository->findById($categoryId);
        $categoryDTO->hydrateModel($category);
        try {
            return $this->categoryRepository->update($category);
        } catch (\Exception $ex) {
            throw new ResourceNotUpdatedException('Failed to update category', previous: $ex);
        }
    }

    public function delete(int $categoryId)
    {
        $category = $this->categoryRepository->findById($categoryId);
        try {
            return $this->categoryRepository->delete($category);
        } catch (\Exception $ex) {
            throw new ResourceNotDeletedException('Failed to delete category', previous: $ex);
        }
    }
}