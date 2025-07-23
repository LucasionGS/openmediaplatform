<?php

namespace App\Livewire;

use App\Models\Category;
use App\Traits\HandlesUploadLimits;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Upload Video - OpenMediaPlatform')]
class UploadPage extends Component
{
    use HandlesUploadLimits;
    
    public $maxUploadSize;
    public $maxUploadSizeFormatted;
    public $videoCategories;     // Available categories for videos

    public function mount()
    {
        // Require authentication to access upload page
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'You must be signed in to upload videos.');
        }

        // Get upload limits from PHP configuration
        $this->maxUploadSize = $this->getMaxUploadSize();
        $this->maxUploadSizeFormatted = $this->formatBytes($this->maxUploadSize);
        
        // Load video categories
        $this->videoCategories = Category::getVideoCategories();
    }

    public function render()
    {
        return view('livewire.upload-page')
            ->layout('components.layouts.app');
    }
}
