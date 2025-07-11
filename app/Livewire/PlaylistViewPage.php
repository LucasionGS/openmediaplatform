<?php

namespace App\Livewire;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlaylistViewPage extends Component
{
    public Playlist $playlist;
    public $currentVideoIndex = 0;
    public $showEditModal = false;
    
    // Edit form properties
    public $title = '';
    public $description = '';
    public $visibility = 'private';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:5000',
        'visibility' => 'required|in:public,private,unlisted',
    ];

    public function mount(Playlist $playlist)
    {
        // Check if user can view this playlist
        if ($playlist->visibility === 'private' && $playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $this->playlist = $playlist;
        $this->loadPlaylistWithVideos();
        
        // Set edit form defaults
        $this->title = $playlist->title;
        $this->description = $playlist->description;
        $this->visibility = $playlist->visibility;
    }

    public function loadPlaylistWithVideos()
    {
        $this->playlist->load(['videos' => function($query) {
            $query->where('visibility', '!=', 'private')
                  ->orWhere('user_id', Auth::id());
        }, 'user']);
    }

    public function getVideosProperty()
    {
        return $this->playlist->videos;
    }

    public function playVideo($index)
    {
        $this->currentVideoIndex = $index;
        $videos = $this->videos->values();
        if ($videos->has($index)) {
            $this->dispatch('play-video', videoId: $videos[$index]->vid);
        }
    }

    public function playNext()
    {
        $videos = $this->videos->values();
        if ($this->currentVideoIndex < $videos->count() - 1) {
            $this->currentVideoIndex++;
            $this->dispatch('play-video', videoId: $videos[$this->currentVideoIndex]->vid);
        }
    }

    public function playPrevious()
    {
        $videos = $this->videos->values();
        if ($this->currentVideoIndex > 0) {
            $this->currentVideoIndex--;
            $this->dispatch('play-video', videoId: $videos[$this->currentVideoIndex]->vid);
        }
    }

    public function removeVideo($videoId)
    {
        if ($this->playlist->user_id !== Auth::id()) {
            session()->flash('error', 'You can only remove videos from your own playlists.');
            return;
        }

        $this->playlist->videos()->detach($videoId);
        $this->reorderPlaylistVideos();
        $this->loadPlaylistWithVideos();
        
        // Adjust current video index if necessary
        $videosCount = $this->playlist->videos->count();
        if ($this->currentVideoIndex >= $videosCount) {
            $this->currentVideoIndex = max(0, $videosCount - 1);
        }

        session()->flash('message', 'Video removed from playlist successfully!');
    }

    public function showEditModal()
    {
        if ($this->playlist->user_id !== Auth::id()) {
            session()->flash('error', 'You can only edit your own playlists.');
            return;
        }

        $this->showEditModal = true;
    }

    public function updatePlaylist()
    {
        if ($this->playlist->user_id !== Auth::id()) {
            session()->flash('error', 'You can only edit your own playlists.');
            return;
        }

        $this->validate();

        $this->playlist->update([
            'title' => $this->title,
            'description' => $this->description,
            'visibility' => $this->visibility,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Playlist updated successfully!');
    }

    public function deletePlaylist()
    {
        if ($this->playlist->user_id !== Auth::id()) {
            session()->flash('error', 'You can only delete your own playlists.');
            return;
        }

        $this->playlist->delete();
        return $this->redirect('/playlists', navigate: true);
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    private function reorderPlaylistVideos()
    {
        $videos = $this->playlist->videos()->orderBy('playlist_videos.position')->get();
        
        foreach ($videos as $index => $video) {
            $this->playlist->videos()->updateExistingPivot($video->vid, [
                'position' => $index + 1,
                'updated_at' => now(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.playlist-view-page')
            ->layout('components.layouts.app');
    }
}
