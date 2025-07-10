<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Nette\Utils\Random;
use Pawlox\VideoThumbnail\Facade\VideoThumbnail;

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
    ];

    protected $primaryKey = 'vid';
    public $incrementing = false;

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
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
        $thumbnailPath = $this->getThumbnailPath();
        if (!file_exists($thumbnailPath)) {
            if (!file_exists($this->getThumbnailFolderPath())) {
                mkdir($this->getThumbnailFolderPath(), 0755, true);
            }

            // Generate a thumbnail if it doesn't exist
            VideoThumbnail::createThumbnail(
                $this->getPath(),
                $this->getThumbnailFolderPath(),
                $this->vid . '.jpg',
                $second ?? 1,
                1280,
                720
            );
        }
        return $thumbnailPath;
    }

    public function calculateDuration(bool $save = false)
    {
        if ($this->duration) {
            return $this->duration;
        }

        $ffprobe = \FFMpeg\FFProbe::create();
        $duration = $ffprobe
            ->format($this->getPath())
            ->get('duration');

        // Convert duration to seconds
        $duration = (int) $duration;

        // Save the duration to the database if requested
        if ($save) {
            $this->duration = $duration;
            $this->save();
        }

        return $duration;
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
}
