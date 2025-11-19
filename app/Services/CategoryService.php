<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    /**
     * Get category by slug with published articles
     */
    public function getCategoryWithArticles(string $slug, int $perPage = 10): array
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $articles = $category->articles()
            ->where('status', 'published')
            ->with('author:id,name', 'categories:id,name,slug')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'articles' => $articles,
        ];
    }
}

