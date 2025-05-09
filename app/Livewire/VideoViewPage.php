<?php

namespace App\Livewire;

use App\Models\Video;
use Livewire\Component;

class VideoViewPage extends Component
{
    public Video $video;
    public function render()
    {
        return view('livewire.video-view-page', [
                'video' => $this->video,
                'videoUrl' => route('videos.raw', $this->video),
            ]
        );
    }
}
