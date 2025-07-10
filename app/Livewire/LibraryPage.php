<?php

namespace App\Livewire;

use App\Models\Video;
use App\Models\VideoEngagement;
use App\Models\WatchHistory;
use App\Models\Playlist;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Library - OpenMediaPlatform')]
class LibraryPage extends Component
{
    use WithPagination;

    public string $activeTab = 'watch-history';
    public int $perPage = 12;

    public function mount()
    {
        // Require authentication to access library
        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'You must be signed in to access your library.');
        }
    }

    public function setActiveTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
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

    public function render()
    {
        return view('livewire.library-page')
            ->layout('components.layouts.app');
    }
}
