<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoEngagement extends Model
{
    protected $fillable = [
        'video_id',
        'user_id',
        'engagement_type', // 'like', 'dislike', etc.
    ];

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id', 'vid');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
