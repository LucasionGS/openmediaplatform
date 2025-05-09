<?php

namespace App\Livewire;

use Livewire\Component;

class UploadPage extends Component
{
    use \Livewire\WithFileUploads;
    public $video;
    public string $error = '';
    public function render()
    {
        return view('livewire.upload-page');
    }

    public function upload()
    {
        $this->error = '';
        $this->validate([
            'video' => 'required|mimes:mp4,mov,avi,wmv', // 10MB max
        ]);

        // Store the video
        $path = $this->video->store('videos');

        // Save the video information to the database
        // Video::create(['path' => $path]);

        session()->flash('message', 'Video uploaded successfully.');
    }
}
