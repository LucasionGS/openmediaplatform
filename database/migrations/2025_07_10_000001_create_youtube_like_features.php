<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing user_id to videos table
        Schema::table('videos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('comments');
            $table->string('category')->nullable()->after('description');
            $table->json('tags')->nullable()->after('category');
            $table->timestamp('published_at')->nullable()->after('tags');
        });

        // Create comments table
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->char('video_id', 6);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->text('content');
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('dislikes')->default(0);
            $table->timestamps();
            
            $table->foreign('video_id')->references('vid')->on('videos')->onDelete('cascade');
            $table->index(['video_id', 'parent_id']);
        });

        // Create subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('channel_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['subscriber_id', 'channel_id']);
        });

        // Create playlists table
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->timestamps();
        });

        // Create playlist_videos table
        Schema::create('playlist_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained('playlists')->onDelete('cascade');
            $table->char('video_id', 6);
            $table->integer('position')->default(0);
            $table->timestamps();
            
            $table->foreign('video_id')->references('vid')->on('videos')->onDelete('cascade');
            $table->unique(['playlist_id', 'video_id']);
        });

        // Create watch_history table
        Schema::create('watch_history', function (Blueprint $table) {
            $table->id();
            $table->char('video_id', 6);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id')->nullable(); // For anonymous users
            $table->integer('watch_time')->default(0); // seconds watched
            $table->timestamps();
            
            $table->foreign('video_id')->references('vid')->on('videos')->onDelete('cascade');
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });

        // Create comment_engagements table for likes/dislikes on comments
        Schema::create('comment_engagements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('engagement_type', ['like', 'dislike']);
            $table->timestamps();
            
            $table->unique(['comment_id', 'user_id']);
        });

        // Add channel-related fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('channel_name')->nullable()->after('name');
            $table->text('channel_description')->nullable()->after('channel_name');
            $table->string('profile_picture')->nullable()->after('channel_description');
            $table->string('channel_banner')->nullable()->after('profile_picture');
            $table->json('channel_links')->nullable()->after('channel_banner');
            $table->unsignedInteger('subscribers_count')->default(0)->after('channel_links');
            $table->timestamp('channel_created_at')->nullable()->after('subscribers_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['channel_name', 'channel_description', 'profile_picture', 'channel_banner', 'channel_links', 'subscribers_count', 'channel_created_at']);
        });
        
        Schema::dropIfExists('comment_engagements');
        Schema::dropIfExists('watch_history');
        Schema::dropIfExists('playlist_videos');
        Schema::dropIfExists('playlists');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('comments');
        
        Schema::table('videos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'category', 'tags', 'published_at']);
        });
    }
};
