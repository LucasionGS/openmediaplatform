<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchHistory extends Model
{
    protected $table = 'watch_history';
    
    protected $fillable = [
        'video_id',
        'user_id',
        'session_id',
        'watch_time',
    ];

    protected $casts = [
        'watch_time' => 'integer',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id', 'vid');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
