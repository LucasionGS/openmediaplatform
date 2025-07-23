<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Video;
use App\Models\Image;
use App\Models\User;
use App\Models\Category;

class Homepage extends Component
{
    use WithPagination;
    public $selectedCategory = 'all';
    public $selectedType = 'all'; // all, videos, images
    public $searchQuery = '';
    
    public function mount()
    {
        // Get search query from URL parameter
        $this->searchQuery = request('q', '');
        
        // Get category from URL parameter
        $this->selectedCategory = request('category', 'all');
        
        // Get type from URL parameter
        $this->selectedType = request('type', 'all');
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

    public function updatedSelectedType()
    {
        // Reset pagination when type changes
        $this->resetPage();
        $this->dispatch('typeChanged', $this->selectedType);
    }

    public function search()
    {
        $this->dispatch('searchPerformed', $this->searchQuery);
    }

    public function render()
    {
        $content = collect();
        
        // Get videos if needed
        if ($this->selectedType === 'all' || $this->selectedType === 'videos') {
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
                ->limit(50)
                ->get()
                ->map(function ($video) {
                    $video->content_type = 'video';
                    return $video;
                });
            
            $content = $content->merge($videos);
        }
        
        // Get images if needed
        if ($this->selectedType === 'all' || $this->selectedType === 'images') {
            $images = Image::query()
                ->public()
                ->with(['user', 'imageFiles'])
                ->when($this->selectedCategory !== 'all', function ($query) {
                    $query->byCategory($this->selectedCategory);
                })
                ->when($this->searchQuery, function ($query) {
                    // Split search query into individual keywords
                    $keywords = array_filter(explode(' ', strtolower(trim($this->searchQuery))));
                    
                    // Each keyword must match somewhere in the image content
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
                ->limit(50)
                ->get()
                ->map(function ($image) {
                    $image->content_type = 'image';
                    return $image;
                });
            
            $content = $content->merge($images);
        }
        
        // Sort all content by creation date
        $content = $content->sortByDesc('created_at')->values();
        
        // Paginate manually
        $perPage = 24;
        $currentPage = $this->getPage();
        $items = $content->forPage($currentPage, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $content->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        $categories = [
            'all' => 'All'
        ];

        // Add shared categories (used by both images and videos)
        foreach (Category::getActive() as $category) {
            $categories[$category->slug] = $category->name;
        }

        $types = [
            'all' => 'All Media',
            'videos' => 'Videos',
            'images' => 'Images'
        ];

        return view('livewire.homepage', [
            'content' => $paginator,
            'categories' => $categories,
            'types' => $types,
        ]);
    }
}
