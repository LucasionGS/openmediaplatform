<?php

namespace App\Livewire;

use App\Models\Video;
use Livewire\Component;

class EditVideoPage extends Component
{
    public Video $video;
    public string $error = '';

    public string $title = '';
    public ?string $description = '';
    public string $visibility = Video::VISIBILITY_PUBLIC;

    public function mount(Video $video)
    {
        $this->video = $video;
        $this->title = $video->title;
        $this->description = $video->description;
        $this->visibility = $video->visibility;
    }
    
    public function render()
    {
        return view('livewire.edit-video-page');
    }
}
