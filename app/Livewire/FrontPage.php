<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Video;
use Livewire\WithPagination;

#[Title('OpenMediaPlatform - Home')]
class FrontPage extends Component
{
    use WithPagination;

    public $selectedCategory = 'all';
    public $searchQuery = '';

    protected $queryString = [
        'selectedCategory' => ['except' => 'all'],
        'searchQuery' => ['except' => ''],
    ];

    public function mount()
    {
        $this->searchQuery = request('q', '');
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatedSearchQuery()
    {
        $this->resetPage();
    }

    public function render()
    {
        $videos = Video::query()
            ->where('visibility', Video::VISIBILITY_PUBLIC)
            ->with(['user'])
            ->when($this->selectedCategory !== 'all', function ($query) {
                $query->where('category', $this->selectedCategory);
            })
            ->when($this->searchQuery, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('description', 'like', '%' . $this->searchQuery . '%');
                });
            })
            ->latest('published_at')
            ->paginate(20);

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

        return view('livewire.front-page', [
            'videos' => $videos,
            'categories' => $categories,
        ]);
    }
}
