<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Video;
use App\Models\User;

class Homepage extends Component
{
    use WithPagination;
    public $selectedCategory = 'all';
    public $searchQuery = '';
    
    public function mount()
    {
        // Get search query from URL parameter
        $this->searchQuery = request('q', '');
        
        // Get category from URL parameter
        $this->selectedCategory = request('category', 'all');
    }

    public function updatedSearchQuery()
    {
        // Reset pagination when search changes
        $this->resetPage();
        $this->dispatch('searchUpdated', $this->searchQuery);
    }

    public function updatedSelectedCategory()
    {
        // Reset pagination when category changes
        $this->resetPage();
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
                // Split search query into individual keywords
                $keywords = array_filter(explode(' ', strtolower(trim($this->searchQuery))));
                
                // Each keyword must match somewhere in the video content
                foreach ($keywords as $keyword) {
                    $searchTerm = '%' . $keyword . '%';
                    $query->where(function ($q) use ($searchTerm, $keyword) {
                        $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                          ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                          ->orWhereJsonContains('tags', $keyword)
                          ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                              $userQuery->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                                       ->orWhereRaw('LOWER(channel_name) LIKE ?', [$searchTerm]);
                          });
                    });
                }
            })
            ->orderByDesc('created_at')
            ->paginate(24);

        $categories = [
            'all' => 'All',
            ...Video::getAvailableCategories()
        ];

        return view('livewire.homepage', [
            'videos' => $videos,
            'categories' => $categories,
        ])->layout('components.layouts.app');
    }
}
