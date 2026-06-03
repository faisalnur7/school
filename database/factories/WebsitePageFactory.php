<?php

namespace Database\Factories;

use App\Models\WebsitePage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WebsitePageFactory extends Factory
{
    protected $model = WebsitePage::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(100, 999),
            'status' => 'draft',
            'excerpt' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
    }
}
