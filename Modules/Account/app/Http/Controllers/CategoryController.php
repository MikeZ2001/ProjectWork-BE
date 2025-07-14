<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Modules\Account\Http\Requests\CategoryRequest;
use Modules\Account\Http\Resources\CategoryResource;
use Modules\Account\Services\CategoryService;

/**
 * @group Modules
 * @subgroup Category
 */
class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ){
    }

    /**
     * List Categories
     *
     * Retrieve all categories.
     *
     * @responseFile 200 storage/responses/categories/index-success.json
     */
    public function index(): Collection
    {
        return $this->categoryService->findAll();
    }

    /**
     * Create a Category
     *
     * @bodyParam name string required The name of the category. Example: Savings
     *
     * @responseFile 201 storage/responses/categories/create-success.json
     * @responseFile 422 storage/responses/categories/validation-error.json
     * @responseFile 500 storage/responses/categories/create-error.json
     */
    public function store(CategoryRequest $categoryRequest): CategoryResource
    {
        return new CategoryResource($this->categoryService->create($categoryRequest->getDTO()));
    }

    /**
     * Update a Category
     *
     * @urlParam id integer required The ID of the category. Example: 1
     *
     * @bodyParam name string required The name of the category. Example: Investments
     *
     * @responseFile 200 storage/responses/categories/update-success.json
     * @responseFile 422 storage/responses/categories/validation-error.json
     * @responseFile 404 storage/responses/categories/not-found.json
     * @responseFile 500 storage/responses/categories/update-error.json
     */
    public function update(CategoryRequest $categoryRequest, int $categoryId): CategoryResource
    {
        return new CategoryResource($this->categoryService->update($categoryRequest->getDTO(), $categoryId));
    }

    /**
     * Delete a Category
     *
     * @urlParam id integer required The ID of the category. Example: 1
     *
     * @response 204
     * @responseFile 404 storage/responses/categories/not-found.json
     * @responseFile 500 storage/responses/categories/delete-error.json
     */
    public function destroy(int $categoryId): Response
    {
        $this->categoryService->delete($categoryId);
        return response()->noContent();
    }
}