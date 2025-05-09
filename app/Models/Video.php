<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected $primaryKey = 'vid';
    public $incrementing = false;

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
}
