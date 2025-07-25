<?php

namespace App\Livewire;

use App\Models\Video;
use App\Models\Image;
use App\Models\VideoEngagement;
use App\Models\ImageEngagement;
use App\Models\WatchHistory;
use App\Models\Playlist;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Library - OpenMediaPlatform')]
class LibraryPage extends Component
{
    use WithPagination;

    public string $activeTab = 'my-videos';
    public int $perPage = 12;
    
    // Playlist management properties
    public $showCreatePlaylistModal = false;
    public $showEditPlaylistModal = false;
    public $editingPlaylistId = null;
    public $playlistTitle = '';
    public $playlistDescription = '';
    public $playlistVisibility = 'private';
    
    protected $rules = [
        'playlistTitle' => 'required|string|max:255',
        'playlistDescription' => 'nullable|string|max:5000',
        'playlistVisibility' => 'required|in:public,private,unlisted',
    ];

    // Valid tab names mapping
    private array $validTabs = [
        'my-videos' => 'my-videos',
        'videos' => 'my-videos',
        'my-images' => 'my-images',
        'images' => 'my-images',
        'history' => 'watch-history',
        'watch-history' => 'watch-history',
        'likes' => 'liked-videos', 
        'liked-videos' => 'liked-videos',
        'liked-images' => 'liked-images',
        'playlists' => 'playlists',
    ];

    public function mount($tab = null)
    {
        // Require authentication to access library
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'You must be signed in to access your library.');
        }
        
        // Set active tab based on URL parameter
        if ($tab && isset($this->validTabs[$tab])) {
            $this->activeTab = $this->validTabs[$tab];
        }
    }

    public function getWatchHistoryProperty()
    {
        // Debug: Let's see what we're getting
        \Log::info('Getting watch history for user: ' . auth()->id());
        
        $history = WatchHistory::with(['video.user'])
            ->where('user_id', auth()->id())
            ->whereHas('video') // Only include records where video exists
            ->orderBy('updated_at', 'desc')
            ->paginate($this->perPage, pageName: 'history-page');
            
        \Log::info('Watch history count: ' . $history->total());
        
        return $history;
    }

    public function getLikedVideosProperty()
    {
        // Debug: Let's see what we're getting
        \Log::info('Getting liked videos for user: ' . auth()->id());
        
        $liked = VideoEngagement::with(['video.user'])
            ->where('user_id', auth()->id())
            ->where('engagement_type', 'like')
            ->whereHas('video') // Only include records where video exists
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, pageName: 'liked-page');
            
        \Log::info('Liked videos count: ' . $liked->total());
        \Log::info('Raw engagement count: ' . VideoEngagement::where('user_id', auth()->id())->where('engagement_type', 'like')->count());
        
        return $liked;
    }

    public function getPlaylistsProperty()
    {
        return Playlist::where('user_id', auth()->id())
            ->withCount('videos')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, pageName: 'playlists-page');
    }

    public function getMyVideosProperty()
    {
        return Video::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, pageName: 'my-videos-page');
    }

    public function getMyImagesProperty()
    {
        return Image::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, pageName: 'my-images-page');
    }

    public function getLikedImagesProperty()
    {
        \Log::info('Getting liked images for user: ' . auth()->id());
        
        $liked = ImageEngagement::with(['image.user'])
            ->where('user_id', auth()->id())
            ->where('type', 'like')
            ->whereHas('image') // Only include records where image exists
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, pageName: 'liked-images-page');
            
        \Log::info('Liked images count: ' . $liked->total());
        
        return $liked;
    }

    public function clearWatchHistory()
    {
        WatchHistory::where('user_id', auth()->id())->delete();
        
        session()->flash('success', 'Watch history cleared successfully.');
        $this->resetPage();
    }

    public function removeFromWatchHistory($historyId)
    {
        WatchHistory::where('id', $historyId)
            ->where('user_id', auth()->id())
            ->delete();
        
        session()->flash('success', 'Video removed from watch history.');
    }

    public function removeFromLiked($videoId)
    {
        $engagement = VideoEngagement::where('video_id', $videoId)
            ->where('user_id', auth()->id())
            ->where('engagement_type', 'like')
            ->first();

        if ($engagement) {
            $engagement->delete();
            
            // Update video likes count
            Video::where('vid', $videoId)->decrement('likes');
            
            session()->flash('success', 'Video removed from liked videos.');
        }
    }

    // Playlist management methods
    public function createPlaylist()
    {
        $this->validate([
            'playlistTitle' => 'required|string|max:255',
            'playlistDescription' => 'nullable|string|max:5000',
            'playlistVisibility' => 'required|in:public,private,unlisted',
        ]);

        Playlist::create([
            'title' => $this->playlistTitle,
            'description' => $this->playlistDescription,
            'visibility' => $this->playlistVisibility,
            'user_id' => auth()->id(),
        ]);

        $this->resetPlaylistForm();
        $this->showCreatePlaylistModal = false;
        session()->flash('success', 'Playlist created successfully!');
    }

    public function editPlaylist($playlistId)
    {
        $playlist = Playlist::where('id', $playlistId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $this->editingPlaylistId = $playlist->id;
        $this->playlistTitle = $playlist->title;
        $this->playlistDescription = $playlist->description;
        $this->playlistVisibility = $playlist->visibility;
        $this->showEditPlaylistModal = true;
    }

    public function updatePlaylist()
    {
        $this->validate([
            'playlistTitle' => 'required|string|max:255',
            'playlistDescription' => 'nullable|string|max:5000',
            'playlistVisibility' => 'required|in:public,private,unlisted',
        ]);

        $playlist = Playlist::where('id', $this->editingPlaylistId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $playlist->update([
            'title' => $this->playlistTitle,
            'description' => $this->playlistDescription,
            'visibility' => $this->playlistVisibility,
        ]);

        $this->resetPlaylistForm();
        $this->showEditPlaylistModal = false;
        session()->flash('success', 'Playlist updated successfully!');
    }

    public function deletePlaylist($playlistId)
    {
        $playlist = Playlist::where('id', $playlistId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $playlist->delete();
        session()->flash('success', 'Playlist deleted successfully!');
    }

    public function resetPlaylistForm()
    {
        $this->playlistTitle = '';
        $this->playlistDescription = '';
        $this->playlistVisibility = 'private';
        $this->editingPlaylistId = null;
        $this->resetValidation();
    }

    public function closePlaylistModals()
    {
        $this->showCreatePlaylistModal = false;
        $this->showEditPlaylistModal = false;
        $this->resetPlaylistForm();
    }

    public function render()
    {
        return view('livewire.library-page', [
            'myVideos' => $this->myVideos,
            'myImages' => $this->myImages,
            'watchHistory' => $this->watchHistory,
            'likedVideos' => $this->likedVideos,
            'likedImages' => $this->likedImages,
            'playlists' => $this->playlists,
        ])->layout('components.layouts.app');
    }
}
