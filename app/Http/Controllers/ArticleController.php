<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {}


    public function index(Request $request): JsonResponse
    {
        $articles = $this->articleService->getPublishedArticles($request);

        return response()->json($articles);
    }

    public function show(string $slug): JsonResponse
    {
        $article = $this->articleService->getArticleBySlug($slug);

        return response()->json($article);
    }


    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->createArticle(
            $request->validated(),
            Auth::id()
        );

        return response()->json($article, 201);
    }

    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        try {
            $article = $this->articleService->updateArticle(
                $id,
                $request->validated(),
                Auth::id()
            );

            return response()->json($article);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 403);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->articleService->deleteArticle($id, Auth::id());

            return response()->json([
                'message' => 'Article deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 403);
        }
    }
}
