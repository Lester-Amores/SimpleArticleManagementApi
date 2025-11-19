<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    /**
     * Get all published articles in a category by slug
     */
    public function show(string $slug): JsonResponse
    {
        $data = $this->categoryService->getCategoryWithArticles($slug);

        return response()->json($data);
    }
}
