<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use App\Models\User;

class Homepage extends Component
{
    public $selectedCategory = 'all';
    public $searchQuery = '';
    
    public function mount()
    {
        // Initialize component
    }

    public function updatedSelectedCategory()
    {
        $this->dispatch('categoryChanged', $this->selectedCategory);
    }

    public function search()
    {
        $this->dispatch('searchPerformed', $this->searchQuery);
    }

    public function render()
    {
        $videos = Video::query()
            ->public()
            ->with(['user'])
            ->when($this->selectedCategory !== 'all', function ($query) {
                $query->byCategory($this->selectedCategory);
            })
            ->when($this->searchQuery, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('description', 'like', '%' . $this->searchQuery . '%');
                });
            })
            ->latest('published_at')
            ->paginate(24);

        $categories = [
            'all' => 'All',
            'music' => 'Music',
            'gaming' => 'Gaming',
            'sports' => 'Sports',
            'news' => 'News',
            'entertainment' => 'Entertainment',
            'education' => 'Education',
            'technology' => 'Technology',
            'travel' => 'Travel',
            'food' => 'Food',
            'lifestyle' => 'Lifestyle',
        ];

        return view('livewire.homepage', [
            'videos' => $videos,
            'categories' => $categories,
        ])->layout('components.layouts.app');
    }
}
