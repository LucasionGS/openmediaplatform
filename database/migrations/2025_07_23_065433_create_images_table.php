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
        Schema::create('images', function (Blueprint $table) {
            $table->string('iid', 36)->primary(); // Image ID
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->enum('visibility', ['public', 'private', 'unlisted', 'unpublished'])->default('public');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->string('share_token', 32)->nullable()->unique();
            $table->boolean('is_shareable')->default(false);
            $table->timestamp('share_expires_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'visibility']);
            $table->index(['visibility', 'published_at']);
            $table->index('category');
        });

        // Image files table (for multiple images per post)
        Schema::create('image_files', function (Blueprint $table) {
            $table->id();
            $table->string('image_id', 36); // References images.iid
            $table->string('filename'); // Original filename
            $table->string('stored_filename'); // Stored filename (UUID)
            $table->string('mime_type');
            $table->bigInteger('file_size'); // in bytes
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('order')->default(0); // For ordering multiple images
            $table->timestamps();
            
            $table->foreign('image_id')->references('iid')->on('images')->onDelete('cascade');
            $table->index(['image_id', 'order']);
        });

        // Image engagement table (likes/dislikes)
        Schema::create('image_engagement', function (Blueprint $table) {
            $table->id();
            $table->string('image_id', 36);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['like', 'dislike']);
            $table->timestamps();
            
            $table->foreign('image_id')->references('iid')->on('images')->onDelete('cascade');
            $table->unique(['image_id', 'user_id']);
        });

        // Update comments table to support polymorphic relationships
        // This allows comments on both videos and images
        Schema::table('comments', function (Blueprint $table) {
            // Add polymorphic columns
            $table->unsignedBigInteger('commentable_id')->after('user_id');
            $table->string('commentable_type')->after('commentable_id');
            
            // Index for polymorphic relationship
            $table->index(['commentable_id', 'commentable_type']);
        });

        // Migrate existing video comments to use polymorphic relationship
        DB::statement("UPDATE comments SET commentable_id = video_id, commentable_type = 'App\\\\Models\\\\Video'");

        // Drop the old video_id column and its foreign key and indexes
        Schema::table('comments', function (Blueprint $table) {
            // Drop indexes that reference video_id first
            $table->dropIndex('comments_video_id_parent_id_index');
            $table->dropForeign(['video_id']);
            $table->dropColumn('video_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore comments table structure
        Schema::table('comments', function (Blueprint $table) {
            $table->string('video_id')->after('user_id');
            $table->foreign('video_id')->references('vid')->on('videos')->onDelete('cascade');
        });

        // Migrate polymorphic comments back to video_id (only video comments)
        DB::statement("UPDATE comments SET video_id = commentable_id WHERE commentable_type = 'App\\\\Models\\\\Video'");

        // Remove polymorphic columns
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['commentable_id', 'commentable_type']);
            $table->dropColumn(['commentable_id', 'commentable_type']);
        });

        Schema::dropIfExists('image_engagement');
        Schema::dropIfExists('image_files');
        Schema::dropIfExists('images');
    }
};
