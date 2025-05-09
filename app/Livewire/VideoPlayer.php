<?php

namespace App\Livewire;

use App\Models\Video;
use Livewire\Component;

class VideoPlayer extends Component
{
    public ?Video $video = null;
    public ?string $videoId = null;
    
    public function render()
    {
        if (!$this->video && $this->videoId) {
            $this->video = Video::find($this->videoId);
        }
        
        return view('livewire.video-player', [
            'video' => $this->video,
            'videoUrl' => $this->video ? route('videos.raw', $this->video) : null,
        ]);
    }
}
