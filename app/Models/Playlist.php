<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'visibility',
        'thumbnail_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'playlist_videos', 'playlist_id', 'video_id', 'id', 'vid')
                    ->withPivot('position')
                    ->withTimestamps()
                    ->orderBy('playlist_videos.position');
    }

    public function getVideoCount()
    {
        return $this->videos()->count();
    }
}
