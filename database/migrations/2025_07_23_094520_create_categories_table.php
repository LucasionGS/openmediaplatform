<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });

        // Insert default shared categories for both images and videos
        $categories = [
            ['name' => 'Art & Design', 'slug' => 'art', 'description' => 'Artistic creations, designs, and visual art', 'sort_order' => 1],
            ['name' => 'Photography', 'slug' => 'photography', 'description' => 'Professional and amateur photography', 'sort_order' => 2],
            ['name' => 'Gaming', 'slug' => 'gaming', 'description' => 'Video games, gameplay, and gaming content', 'sort_order' => 3],
            ['name' => 'Music', 'slug' => 'music', 'description' => 'Music videos, performances, and audio content', 'sort_order' => 4],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'description' => 'Comedy, shows, movies, and entertainment content', 'sort_order' => 5],
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Educational content and tutorials', 'sort_order' => 6],
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Tech gadgets, software, reviews, and innovations', 'sort_order' => 7],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports activities, highlights, and events', 'sort_order' => 8],
            ['name' => 'News & Politics', 'slug' => 'news', 'description' => 'News, current events, and political content', 'sort_order' => 9],
            ['name' => 'Science & Nature', 'slug' => 'science', 'description' => 'Science, nature, landscapes, and documentary content', 'sort_order' => 10],
            ['name' => 'Travel', 'slug' => 'travel', 'description' => 'Travel destinations, vlogs, and event coverage', 'sort_order' => 11],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Daily life, fashion, vlogs, and personal content', 'sort_order' => 12],
            ['name' => 'Food & Cooking', 'slug' => 'food', 'description' => 'Food photography, recipes, and culinary arts', 'sort_order' => 13],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Business and professional content', 'sort_order' => 14],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Miscellaneous content', 'sort_order' => 99],
        ];

        // Insert all categories with timestamps
        $now = now();
        foreach ($categories as &$category) {
            $category['created_at'] = $now;
            $category['updated_at'] = $now;
        }

        DB::table('categories')->insert($categories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
