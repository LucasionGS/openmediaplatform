<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageEngagement extends Model
{
    use HasFactory;

    protected $table = 'image_engagement';

    protected $fillable = [
        'image_id',
        'user_id',
        'type',
    ];

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id', 'iid');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
