<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Nette\Utils\Random;
use Pawlox\VideoThumbnail\Facade\VideoThumbnail;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_UNLISTED = 'unlisted';
    public const VISIBILITY_UNPUBLISHED = 'unpublished';
    public const VISIBILITY_UPLOADING = 'uploading';
    
    protected $fillable = [
        'title',
        'description',
        'visibility',
        'duration',
        'likes',
        'dislikes',
        'views',
        'comments',
        'user_id',
        'category',
        'tags',
        'published_at',
        'share_token',
        'is_shareable',
        'share_expires_at',
    ];

    protected $primaryKey = 'vid';
    public $incrementing = false;

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'share_expires_at' => 'datetime',
        'is_shareable' => 'boolean',
        'likes' => 'integer',
        'dislikes' => 'integer',
        'views' => 'integer',
        'comments' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Create a unique ID for the video before saving
        static::creating(function (Video $video) {
            do {
                $id = Random::generate(6, 'a-zA-Z0-9');
            } while (self::find($id));

            $video->vid = $id;
        });

        // Delete associated files when video is deleted
        static::deleting(function (Video $video) {
            $video->deleteFiles();
        });
    }

    public function getRouteKeyName()
    {
        return 'vid';
    }

    public function getPath()
    {
        return storage_path('app/public/videos/' . $this->vid);
    }

    public function getThumbnailFolderPath()
    {
        return storage_path('app/public/thumbnails');
    }
    
    public function getThumbnailPath()
    {
        return storage_path('app/public/thumbnails/' . $this->vid . '.jpg');
    }

    public function getThumbnailUrl()
    {
        return route('videos.thumbnail', ['video' => $this->vid]);
    }

    public function generateThumbnail(int $second = 1)
    {
        try {
            $thumbnailPath = $this->getThumbnailPath();
            if (file_exists($thumbnailPath)) {
                return $thumbnailPath; // Thumbnail already exists
            }

            // Ensure thumbnail directory exists
            $thumbnailDir = $this->getThumbnailFolderPath();
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Try to generate thumbnail using FFMpeg
            try {
                \Log::info("Generating thumbnail for video: {$this->vid}");
                \Log::info("Video path: " . $this->getPath());
                \Log::info("Thumbnail path: " . $thumbnailPath);

                // Check if video file exists
                if (!file_exists($this->getPath())) {
                    \Log::error("Video file not found: " . $this->getPath());
                    return $this->createDefaultThumbnail();
                }

                // Use FFMpeg to generate thumbnail
                $ffmpeg = \FFMpeg\FFMpeg::create([
                    'ffmpeg.binaries' => 'ffmpeg', // Make sure ffmpeg is in PATH
                    'ffprobe.binaries' => 'ffprobe',
                    'timeout' => 3600,
                    'ffmpeg.threads' => 12,
                ]);

                $video = $ffmpeg->open($this->getPath());
                $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($second));
                $frame->save($thumbnailPath);

                \Log::info("Thumbnail generated successfully: " . $thumbnailPath);
                return $thumbnailPath;

            } catch (\Exception $e) {
                \Log::warning("FFMpeg thumbnail generation failed: " . $e->getMessage());
                
                // Fallback: Try using VideoThumbnail package
                try {
                    VideoThumbnail::createThumbnail(
                        $this->getPath(),
                        $this->getThumbnailFolderPath(),
                        $this->vid . '.jpg',
                        $second,
                        1280,
                        720
                    );
                    \Log::info("VideoThumbnail package generated thumbnail successfully");
                    return $thumbnailPath;
                } catch (\Exception $e2) {
                    \Log::warning("VideoThumbnail package failed: " . $e2->getMessage());
                    return $this->createDefaultThumbnail();
                }
            }

        } catch (\Exception $e) {
            \Log::error("Thumbnail generation completely failed: " . $e->getMessage());
            return $this->createDefaultThumbnail();
        }
    }

    private function createDefaultThumbnail()
    {
        try {
            $thumbnailPath = $this->getThumbnailPath();
            $thumbnailDir = $this->getThumbnailFolderPath();
            
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Create a simple default thumbnail using GD
            $width = 1280;
            $height = 720;
            $image = imagecreate($width, $height);
            
            // Set colors
            $background = imagecolorallocate($image, 45, 45, 45); // Dark gray
            $textColor = imagecolorallocate($image, 255, 255, 255); // White
            $accentColor = imagecolorallocate($image, 255, 0, 0); // Red
            
            // Fill background
            imagefill($image, 0, 0, $background);
            
            // Draw play button (triangle)
            $playButton = [
                $width/2 - 50, $height/2 - 30,
                $width/2 - 50, $height/2 + 30,
                $width/2 + 40, $height/2
            ];
            imagefilledpolygon($image, $playButton, 3, $accentColor);
            
            // Add text
            $text = "Video Thumbnail";
            $textX = ($width - strlen($text) * 10) / 2;
            imagestring($image, 3, $textX, $height/2 + 50, $text, $textColor);
            
            // Save as JPEG
            imagejpeg($image, $thumbnailPath, 85);
            imagedestroy($image);
            
            \Log::info("Default thumbnail created: " . $thumbnailPath);
            return $thumbnailPath;
            
        } catch (\Exception $e) {
            \Log::error("Failed to create default thumbnail: " . $e->getMessage());
            return null;
        }
    }

    public function calculateDuration(bool $save = false)
    {
        if ($this->duration) {
            return $this->duration;
        }

        try {
            \Log::info("Calculating duration for video: {$this->vid}");
            \Log::info("Video file path: " . $this->getPath());
            
            // Check if video file exists
            if (!file_exists($this->getPath())) {
                \Log::error("Video file not found for duration calculation: " . $this->getPath());
                return 0;
            }

            $ffprobe = \FFMpeg\FFProbe::create([
                'ffprobe.binaries' => 'ffprobe', // Make sure ffprobe is in PATH
                'timeout' => 3600,
            ]);
            
            $duration = $ffprobe
                ->format($this->getPath())
                ->get('duration');

            // Convert duration to seconds
            $duration = (int) $duration;
            \Log::info("Duration calculated: {$duration} seconds");

            // Save the duration to the database if requested
            if ($save) {
                $this->duration = $duration;
                $this->save();
            }

            return $duration;
            
        } catch (\Exception $e) {
            \Log::error("Failed to calculate video duration: " . $e->getMessage());
            
            // Fallback: try to get file duration using getID3 or return default
            try {
                // If FFMpeg fails, return a default duration
                $duration = 60; // Default 1 minute
                
                if ($save) {
                    $this->duration = $duration;
                    $this->save();
                }
                
                return $duration;
            } catch (\Exception $e2) {
                \Log::error("Fallback duration calculation also failed: " . $e2->getMessage());
                return 0;
            }
        }
    }

    public function getSize()
    {
        return filesize($this->getPath());
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'video_id', 'vid');
    }

    public function topLevelComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'video_id', 'vid')
                    ->whereNull('parent_id')
                    ->with(['user', 'replies']);
    }

    public function engagements(): HasMany
    {
        return $this->hasMany(VideoEngagement::class, 'video_id', 'vid');
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_videos', 'video_id', 'playlist_id', 'vid', 'id')
                    ->withPivot('position')
                    ->withTimestamps();
    }

    public function watchHistory(): HasMany
    {
        return $this->hasMany(WatchHistory::class, 'video_id', 'vid');
    }

    // Helper methods
    public function userEngagement($userId = null)
    {
        if (!$userId) {
            return null;
        }
        
        return $this->engagements()->where('user_id', $userId)->first();
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function updateCommentsCount(): void
    {
        $this->comments = $this->comments()->count();
        $this->save();
    }

    public function updateLikesCount(): void
    {
        $this->likes = $this->engagements()->where('engagement_type', 'like')->count();
        $this->save();
    }

    public function updateDislikesCount(): void
    {
        $this->dislikes = $this->engagements()->where('engagement_type', 'dislike')->count();
        $this->save();
    }

    public function getFormattedDuration(): string
    {
        if (!$this->duration) {
            return '0:00';
        }

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getFormattedViews(): string
    {
        if ($this->views >= 1000000) {
            return round($this->views / 1000000, 1) . 'M views';
        } elseif ($this->views >= 1000) {
            return round($this->views / 1000, 1) . 'K views';
        }

        return $this->views . ' views';
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function isPublished(): bool
    {
        return $this->visibility === self::VISIBILITY_PUBLIC && $this->published_at !== null;
    }

    public function publish(): void
    {
        $this->visibility = self::VISIBILITY_PUBLIC;
        $this->published_at = now();
        $this->save();
    }

    // Scope methods
    public function scopePublic($query)
    {
        return $query->where('visibility', self::VISIBILITY_PUBLIC);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByTags($query, array $tags)
    {
        return $query->where(function ($q) use ($tags) {
            foreach ($tags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }

    // Category helpers
    public static function getAvailableCategories(): array
    {
        return [
            'music' => 'Music',
            'gaming' => 'Gaming', 
            'news' => 'News',
            'sports' => 'Sports',
            'education' => 'Education',
            'entertainment' => 'Entertainment',
            'technology' => 'Technology',
            'lifestyle' => 'Lifestyle',
            'other' => 'Other'
        ];
    }

    public function getFormattedCategory(): string
    {
        $categories = self::getAvailableCategories();
        return $categories[$this->category] ?? ucfirst($this->category ?? 'Uncategorized');
    }

    // Share functionality methods
    public function generateShareToken(): string
    {
        $this->share_token = \Illuminate\Support\Str::random(32);
        $this->is_shareable = true;
        $this->save();
        
        return $this->share_token;
    }

    public function getShareUrl(): ?string
    {
        if (!$this->is_shareable || !$this->share_token) {
            return null;
        }

        if ($this->share_expires_at && $this->share_expires_at < now()) {
            return null;
        }

        return route('videos.share', ['token' => $this->share_token]);
    }

    public function revokeShare(): void
    {
        $this->share_token = null;
        $this->is_shareable = false;
        $this->share_expires_at = null;
        $this->save();
    }

    public function isShareValid(): bool
    {
        if (!$this->is_shareable || !$this->share_token) {
            return false;
        }

        if ($this->share_expires_at && $this->share_expires_at < now()) {
            return false;
        }

        return true;
    }

    public static function findByShareToken(string $token): ?self
    {
        return self::with('user')
                  ->where('share_token', $token)
                  ->where('is_shareable', true)
                  ->where(function ($query) {
                      $query->whereNull('share_expires_at')
                            ->orWhere('share_expires_at', '>', now());
                  })
                  ->first();
    }

    // File management methods
    public function deleteFiles(): void
    {
        // Delete video file
        $videoPath = $this->getPath();
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }

        // Delete thumbnail
        $thumbnailPath = $this->getThumbnailPath();
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }

        // Delete from Laravel storage if exists
        try {
            Storage::disk('public')->delete('videos/' . $this->vid);
            Storage::disk('public')->delete('thumbnails/' . $this->vid . '.jpg');
        } catch (\Exception $e) {
            \Log::warning('Error deleting files from storage: ' . $e->getMessage());
        }
    }
}
