<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Nette\Utils\Random;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->char('vid', 6)->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('visibility', ['public', 'private', 'unlisted', 'unpublished', 'uploading'])->default('unpublished');
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('dislikes')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->timestamps();
        });

        Schema::create('video_engagements', function (Blueprint $table) {
            $table->id();
            $table->char('video_id', 6)->constrained('videos')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('engagement_type', ['like', 'dislike']);
            $table->integer('engagement_value')->nullable(); // NULL if no value needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_engagements');
        Schema::dropIfExists('videos');
    }
};
