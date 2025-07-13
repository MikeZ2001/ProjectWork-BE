<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Modules\Account\Http\Requests\CategoryRequest;
use Modules\Account\Http\Resources\CategoryResource;
use Modules\Account\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ){
    }

    public function index(): Collection
    {
        return $this->categoryService->findAll();
    }

    public function store(CategoryRequest $categoryRequest): CategoryResource
    {
        return new CategoryResource($this->categoryService->create($categoryRequest->getDTO()));
    }

    public function update(CategoryRequest $categoryRequest, int $categoryId): CategoryResource
    {
        return new CategoryResource($this->categoryService->update($categoryRequest->getDTO(), $categoryId));
    }

    public function destroy(int $categoryId): Response
    {
        $this->categoryService->delete($categoryId);
        return response()->noContent();
    }
}