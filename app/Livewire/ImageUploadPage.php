<?php

namespace App\Livewire;

use App\Models\SiteSetting;
use App\Models\Category;
use App\Traits\HandlesUploadLimits;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Upload Images - OpenMediaPlatform')]
class ImageUploadPage extends Component
{
    use HandlesUploadLimits;
    
    public $maxImageSize;        // KB
    public $maxImageSizeMB;      // MB for display
    public $maxImageCount;
    public $imageCategories;     // Available categories for images

    public function mount()
    {
        // Require authentication to access upload page
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'You must be signed in to upload images.');
        }

        // Get upload limits from admin settings
        $this->maxImageSize = (int) SiteSetting::get('max_image_size', 10240);     // KB, default 10MB
        $this->maxImageCount = (int) SiteSetting::get('max_image_count', 50);      // files, default 50
        $this->maxImageSizeMB = number_format($this->maxImageSize / 1024, 1);      // Convert to MB for display
        
        // Load image categories
        $this->imageCategories = Category::getImageCategories();
    }

    public function render()
    {
        return view('livewire.image-upload-page');
    }
}
