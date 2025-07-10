<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Upload Video - OpenMediaPlatform')]
class UploadPage extends Component
{
    public function mount()
    {
        // Require authentication to access upload page
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'You must be signed in to upload videos.');
        }
    }

    public function render()
    {
        return view('livewire.upload-page')
            ->layout('components.layouts.app');
    }
}
