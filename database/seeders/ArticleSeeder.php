<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $articles = [
            [
                'title' => 'Introduction to Laravel Framework',
                'content' => 'Laravel is a powerful PHP framework that makes web development elegant and enjoyable. It provides a rich set of features including routing, authentication, and database management.',
                'status' => 'published',
                'author_id' => $users->first()->id,
                'category_ids' => [$categories->where('name', 'Technology')->first()->id],
            ],
            [
                'title' => 'Understanding Quantum Computing',
                'content' => 'Quantum computing represents a revolutionary approach to computation, leveraging quantum mechanical phenomena to process information in fundamentally new ways.',
                'status' => 'published',
                'author_id' => $users->skip(1)->first()->id,
                'category_ids' => [$categories->where('name', 'Science')->first()->id],
            ],
            [
                'title' => 'Startup Success Strategies',
                'content' => 'Building a successful startup requires careful planning, market research, and a strong team. This article explores key strategies for startup success in today\'s competitive market.',
                'status' => 'published',
                'author_id' => $users->first()->id,
                'category_ids' => [$categories->where('name', 'Business')->first()->id],
            ],
            [
                'title' => 'Healthy Living Tips for 2024',
                'content' => 'Maintaining a healthy lifestyle involves balanced nutrition, regular exercise, and mental well-being. Here are practical tips to improve your overall health and wellness.',
                'status' => 'published',
                'author_id' => $users->skip(1)->first()->id,
                'category_ids' => [$categories->where('name', 'Lifestyle')->first()->id],
            ],
            [
                'title' => 'Advanced PHP Techniques',
                'content' => 'Explore advanced PHP programming techniques including design patterns, dependency injection, and performance optimization strategies for modern web applications.',
                'status' => 'published',
                'author_id' => $users->first()->id,
                'category_ids' => [$categories->where('name', 'Technology')->first()->id],
            ],
            [
                'title' => 'Climate Change and Global Impact',
                'content' => 'Climate change is one of the most pressing issues of our time. This article examines the scientific evidence and global efforts to address environmental challenges.',
                'status' => 'published',
                'author_id' => $users->skip(1)->first()->id,
                'category_ids' => [$categories->where('name', 'Science')->first()->id],
            ],
            [
                'title' => 'Draft Article - Not Published',
                'content' => 'This is a draft article that should not appear in published listings.',
                'status' => 'draft',
                'author_id' => $users->first()->id,
                'category_ids' => [$categories->first()->id],
            ],
        ];

        foreach ($articles as $articleData) {
            $categoryIds = $articleData['category_ids'];
            unset($articleData['category_ids']);

            // Generate slug from title
            $articleData['slug'] = Str::slug($articleData['title']);

            $article = Article::create($articleData);
            
            // Filter out null category IDs (in case category doesn't exist)
            $validCategoryIds = array_filter($categoryIds);
            if (!empty($validCategoryIds)) {
                $article->categories()->attach($validCategoryIds);
            }
        }
    }
}
