<?php

namespace App\Livewire;

use App\Models\Video;
use App\Models\Comment;
use App\Models\VideoEngagement;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class VideoViewPage extends Component
{
    public Video $video;
    public $newComment = '';
    public $replyTo = null;
    public $replyContent = '';
    public $userEngagement = null;

    public function mount(Video $video)
    {
        $this->video = $video;
        
        // Record view
        $this->recordView();
        
        // Load user engagement if logged in
        if (Auth::check()) {
            $this->userEngagement = $this->video->engagements()
                ->where('user_id', Auth::id())
                ->first();
        }
    }

    public function recordView()
    {
        // Increment video views
        $this->video->increment('views');
        
        // Record in watch history
        WatchHistory::create([
            'video_id' => $this->video->vid,
            'user_id' => Auth::id(),
            'session_id' => Auth::check() ? null : Session::getId(),
            'watch_time' => 0,
        ]);
    }

    public function toggleLike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $engagement = VideoEngagement::firstOrCreate([
            'video_id' => $this->video->vid,
            'user_id' => Auth::id(),
        ]);

        if ($engagement->engagement_type === 'like') {
            $engagement->delete();
            $this->userEngagement = null;
        } else {
            $engagement->engagement_type = 'like';
            $engagement->save();
            $this->userEngagement = $engagement;
        }

        $this->updateEngagementCounts();
    }

    public function toggleDislike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $engagement = VideoEngagement::firstOrCreate([
            'video_id' => $this->video->vid,
            'user_id' => Auth::id(),
        ]);

        if ($engagement->engagement_type === 'dislike') {
            $engagement->delete();
            $this->userEngagement = null;
        } else {
            $engagement->engagement_type = 'dislike';
            $engagement->save();
            $this->userEngagement = $engagement;
        }

        $this->updateEngagementCounts();
    }

    private function updateEngagementCounts()
    {
        $this->video->likes = $this->video->engagements()->where('engagement_type', 'like')->count();
        $this->video->dislikes = $this->video->engagements()->where('engagement_type', 'dislike')->count();
        $this->video->save();
        $this->video->refresh();
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        Comment::create([
            'video_id' => $this->video->vid,
            'user_id' => Auth::id(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->updateCommentsCount();
    }

    public function addReply()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'replyContent' => 'required|string|max:1000',
        ]);

        Comment::create([
            'video_id' => $this->video->vid,
            'user_id' => Auth::id(),
            'parent_id' => $this->replyTo,
            'content' => $this->replyContent,
        ]);

        $this->replyContent = '';
        $this->replyTo = null;
        $this->updateCommentsCount();
    }

    public function setReplyTo($commentId)
    {
        $this->replyTo = $commentId;
    }

    public function cancelReply()
    {
        $this->replyTo = null;
        $this->replyContent = '';
    }

    private function updateCommentsCount()
    {
        $this->video->comments = $this->video->comments()->count();
        $this->video->save();
        $this->video->refresh();
    }

    public function render()
    {
        $comments = $this->video->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        $relatedVideos = Video::where('visibility', Video::VISIBILITY_PUBLIC)
            ->with(['user'])
            ->where('vid', '!=', $this->video->vid)
            ->when($this->video->category, function ($query) {
                $query->where('category', $this->video->category);
            })
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return view('livewire.video-view-page', [
            'video' => $this->video,
            'videoUrl' => route('videos.raw', $this->video),
            'comments' => $comments,
            'relatedVideos' => $relatedVideos,
        ]);
    }
}
