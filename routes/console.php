<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Video;
use App\Models\VideoEngagement;
use App\Models\Comment;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('video:delete {video_id}', function (string $video_id) {
    $video = Video::find($video_id);
    
    if (!$video) {
        $this->error("Video with ID '{$video_id}' not found.");
        return 1;
    }
    
    $this->info("Found video: '{$video->title}' by " . ($video->user ? $video->user->name : 'Unknown User'));
    
    if (!$this->confirm('Are you sure you want to delete this video and all associated data?')) {
        $this->info('Video deletion cancelled.');
        return 0;
    }
    
    $this->info('Deleting associated data...');
    
    // Delete video engagements (likes/dislikes)
    $engagements = VideoEngagement::where('video_id', $video_id)->count();
    VideoEngagement::where('video_id', $video_id)->delete();
    $this->line("- Deleted {$engagements} video engagements");
    
    // Delete comments and their engagements
    $comments = Comment::where('video_id', $video_id)->get();
    $commentCount = $comments->count();
    foreach ($comments as $comment) {
        // Delete comment engagements first
        $comment->engagements()->delete();
        // Delete the comment
        $comment->delete();
    }
    $this->line("- Deleted {$commentCount} comments and their engagements");
    
    // Delete watch history
    $watchHistory = WatchHistory::where('video_id', $video_id)->count();
    WatchHistory::where('video_id', $video_id)->delete();
    $this->line("- Deleted {$watchHistory} watch history entries");
    
    // Delete video files
    $filesDeleted = 0;
    
    // Delete video file
    if (Storage::disk('public')->exists("videos/{$video_id}")) {
        Storage::disk('public')->delete("videos/{$video_id}");
        $filesDeleted++;
        $this->line("- Deleted video file");
    }
    
    // Delete thumbnail file
    if (Storage::disk('public')->exists("thumbnails/{$video_id}")) {
        Storage::disk('public')->delete("thumbnails/{$video_id}");
        $filesDeleted++;
        $this->line("- Deleted thumbnail file");
    }
    
    if ($filesDeleted === 0) {
        $this->line("- No video or thumbnail files found to delete");
    }
    
    // Finally, delete the video record
    $video->delete();
    
    $this->info("✅ Video '{$video->title}' and all associated data have been successfully deleted!");
    
    return 0;
})->purpose('Delete a video and all its associated data by video ID');
