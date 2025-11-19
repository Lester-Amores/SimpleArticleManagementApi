<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleService
{
   
    public function getPublishedArticles(Request $request): LengthAwarePaginator
    {
        $query = Article::query()
            ->where('status', 'published')
            ->with('author:id,name', 'categories:id,name,slug');

        if ($request->has('q') && !empty($request->q)) {
            $query->where(function ($q) use ($request) {
                $q->where('articles.title', 'like', "%{$request->q}%")
                  ->orWhere('articles.content', 'like', "%{$request->q}%");
            });
        }

        $sortBy = $request->input('sortBy', 'created_at');
        $sortOrder = $request->filled('sortOrder') ? $request->sortOrder : 'desc';
        $query->orderBy('articles.' . $sortBy, $sortOrder);

        $perPage = $request->input('perPage', 10);

        return $query->paginate($perPage);
    }


    public function getArticleBySlug(string $slug): Article
    {
        return Article::where('slug', $slug)
            ->with('author:id,name', 'categories:id,name,slug')
            ->firstOrFail();
    }


    public function createArticle(array $data, int $authorId): Article
    {
        $slug = $data['slug'] ?? Str::slug($data['title']);

        $article = Article::create([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'author_id' => $authorId,
            'status' => $data['status'] ?? 'draft',
        ]);

        if (isset($data['category_ids'])) {
            $article->categories()->attach($data['category_ids']);
        }

        return $article->load('author:id,name', 'categories:id,name,slug');
    }


    public function updateArticle(int $id, array $data, int $userId): Article
    {
        $article = Article::findOrFail($id);

        if ($article->author_id !== $userId) {
            throw new \Exception('Unauthorized. You can only update your own articles.', 403);
        }

        if (isset($data['title'])) {
            $article->title = $data['title'];
            if (!isset($data['slug'])) {
                $article->slug = Str::slug($data['title']);
            }
        }
        if (isset($data['slug'])) {
            $article->slug = $data['slug'];
        }
        if (isset($data['content'])) {
            $article->content = $data['content'];
        }
        if (isset($data['status'])) {
            $article->status = $data['status'];
        }

        $article->save();

        if (isset($data['category_ids'])) {
            $article->categories()->sync($data['category_ids']);
        }

        return $article->load('author:id,name', 'categories:id,name,slug');
    }


    public function deleteArticle(int $id, int $userId): void
    {
        $article = Article::findOrFail($id);

        if ($article->author_id !== $userId) {
            throw new \Exception('Unauthorized. You can only delete your own articles.', 403);
        }

        $article->delete();
    }
}

