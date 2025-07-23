<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_id',
        'filename',
        'stored_filename',
        'mime_type',
        'file_size',
        'width',
        'height',
        'order',
    ];

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id', 'iid');
    }

    public function getUrl()
    {
        return route('images.file', [
            'image' => $this->image_id,
            'filename' => $this->stored_filename
        ]);
    }

    public function getFormattedSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
