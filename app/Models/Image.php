<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Image extends Model
{
    use HasFactory;

    protected $primaryKey = 'iid';
    public $incrementing = false;
    protected $keyType = 'string';

    // Visibility constants
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';
    const VISIBILITY_UNLISTED = 'unlisted';
    const VISIBILITY_UNPUBLISHED = 'unpublished';

    protected $fillable = [
        'title',
        'description',
        'category',
        'tags',
        'visibility',
        'published_at',
        'user_id',
        'share_token',
        'is_shareable',
        'share_expires_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'share_expires_at' => 'datetime',
        'is_shareable' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (empty($image->iid)) {
                $image->iid = (string) Str::uuid();
            }
        });

        static::deleting(function ($image) {
            // Delete all associated image files when image post is deleted
            foreach ($image->imageFiles as $imageFile) {
                $path = storage_path('app/public/images/' . $imageFile->stored_filename);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function imageFiles()
    {
        return $this->hasMany(ImageFile::class, 'image_id', 'iid')->orderBy('order');
    }

    public function engagement()
    {
        return $this->hasMany(ImageEngagement::class, 'image_id', 'iid');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Helper methods
    public function incrementViews()
    {
        $this->increment('views');
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

    public function getFormattedDuration(): ?string
    {
        return null; // Images don't have duration
    }

    public function getDurationAttribute(): ?int
    {
        return null; // Images don't have duration
    }

    public function getTimeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function userEngagement($userId)
    {
        return $this->engagement()->where('user_id', $userId)->first();
    }

    public function toggleLike($userId)
    {
        $engagement = $this->userEngagement($userId);

        if ($engagement) {
            if ($engagement->type === 'like') {
                // Remove like
                $engagement->delete();
                $this->decrement('likes');
            } else {
                // Change dislike to like
                $engagement->update(['type' => 'like']);
                $this->increment('likes');
                $this->decrement('dislikes');
            }
        } else {
            // Add new like
            ImageEngagement::create([
                'image_id' => $this->iid,
                'user_id' => $userId,
                'type' => 'like'
            ]);
            $this->increment('likes');
        }
    }

    public function toggleDislike($userId)
    {
        $engagement = $this->userEngagement($userId);

        if ($engagement) {
            if ($engagement->type === 'dislike') {
                // Remove dislike
                $engagement->delete();
                $this->decrement('dislikes');
            } else {
                // Change like to dislike
                $engagement->update(['type' => 'dislike']);
                $this->increment('dislikes');
                $this->decrement('likes');
            }
        } else {
            // Add new dislike
            ImageEngagement::create([
                'image_id' => $this->iid,
                'user_id' => $userId,
                'type' => 'dislike'
            ]);
            $this->increment('dislikes');
        }
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('visibility', self::VISIBILITY_PUBLIC);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeVisible($query, $userId = null)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('visibility', self::VISIBILITY_PUBLIC);
            
            if ($userId) {
                $q->orWhere(function ($subQ) use ($userId) {
                    $subQ->where('user_id', $userId)
                         ->whereIn('visibility', [
                             self::VISIBILITY_PRIVATE,
                             self::VISIBILITY_UNLISTED,
                             self::VISIBILITY_UNPUBLISHED
                         ]);
                });
            }
        });
    }

    // Get primary image (first image file)
    public function getPrimaryImage()
    {
        return $this->imageFiles()->first();
    }

    // Get image URL
    public function getImageUrl($filename = null)
    {
        if (!$filename) {
            $primaryImage = $this->getPrimaryImage();
            if (!$primaryImage) {
                return null;
            }
            $filename = $primaryImage->stored_filename;
        }
        
        // Will create this route later
        return url('/images/' . $this->iid . '/' . $filename);
    }

    public function getThumbnailUrl()
    {
        $primaryImage = $this->getPrimaryImage();
        if (!$primaryImage) {
            return asset('images/placeholder-image.png');
        }
        
        return $this->getImageUrl($primaryImage->stored_filename);
    }

    // Check if user can view this image
    public function canView($user = null)
    {
        if ($this->visibility === self::VISIBILITY_PUBLIC) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($user->id === $this->user_id) {
            return true;
        }

        if ($user->role === 'admin' || $user->role === 'moderator') {
            return true;
        }

        return false;
    }

    // Share functionality (similar to videos)
    public function generateShareToken($expiresAt = null)
    {
        $this->share_token = Str::random(32);
        $this->is_shareable = true;
        $this->share_expires_at = $expiresAt;
        $this->save();
        
        return $this->share_token;
    }

    public function getShareUrl()
    {
        if (!$this->share_token) {
            return null;
        }
        
        // Will create this route later
        return url('/share/image/' . $this->share_token);
    }

    public function revokeShare()
    {
        $this->share_token = null;
        $this->is_shareable = false;
        $this->share_expires_at = null;
        $this->save();
    }

    public function isShareValid()
    {
        if (!$this->is_shareable || !$this->share_token) {
            return false;
        }
        
        if ($this->share_expires_at && $this->share_expires_at->isPast()) {
            return false;
        }
        
        return true;
    }

    public static function findByShareToken(string $token): ?self
    {
        return self::with(['user', 'imageFiles'])
                  ->where('share_token', $token)
                  ->where('is_shareable', true)
                  ->where(function ($query) {
                      $query->whereNull('share_expires_at')
                            ->orWhere('share_expires_at', '>', now());
                  })
                  ->first();
    }
}
