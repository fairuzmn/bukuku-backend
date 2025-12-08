<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Utils\ResponseUtils;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return ResponseUtils::baseResponse(200, 'Categories retrieved', ['categories' => $categories]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return ResponseUtils::baseResponse(200, 'Category created', $category);
    }

    public function show(Category $category)
    {
        return ResponseUtils::baseResponse(200, 'Category details', $category->load('menuItems'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return ResponseUtils::baseResponse(200, 'Category updated', $category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return ResponseUtils::baseResponse(200, 'Category deleted');
    }
}
