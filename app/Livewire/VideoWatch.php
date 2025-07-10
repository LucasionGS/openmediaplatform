<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use App\Models\Comment;
use App\Models\VideoEngagement;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VideoWatch extends Component
{
    public Video $video;
    public $newComment = '';
    public $replyTo = null;
    public $replyContent = '';
    public $userEngagement = null;
    public $showReplies = [];

    public function mount(Video $video)
    {
        $this->video = $video;
        
        // Record view
        $this->recordView();
        
        // Load user engagement if logged in
        if (Auth::check()) {
            $this->userEngagement = $this->video->userEngagement(Auth::id());
        }
    }

    public function recordView()
    {
        // Increment video views
        $this->video->incrementViews();
        
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

        $engagement = VideoEngagement::where([
            'video_id' => $this->video->vid,
            'user_id' => Auth::id(),
        ])->first();

        if ($engagement) {
            if ($engagement->engagement_type === 'like') {
                // User already liked, remove the like
                $engagement->delete();
                $this->userEngagement = null;
            } else {
                // User disliked before, change to like
                $engagement->engagement_type = 'like';
                $engagement->save();
                $this->userEngagement = $engagement;
            }
        } else {
            // Create new like engagement
            $engagement = VideoEngagement::create([
                'video_id' => $this->video->vid,
                'user_id' => Auth::id(),
                'engagement_type' => 'like',
            ]);
            $this->userEngagement = $engagement;
        }

        $this->video->updateLikesCount();
        $this->video->updateDislikesCount();
        $this->video->refresh();
    }

    public function toggleDislike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $engagement = VideoEngagement::where([
            'video_id' => $this->video->vid,
            'user_id' => Auth::id(),
        ])->first();

        if ($engagement) {
            if ($engagement->engagement_type === 'dislike') {
                // User already disliked, remove the dislike
                $engagement->delete();
                $this->userEngagement = null;
            } else {
                // User liked before, change to dislike
                $engagement->engagement_type = 'dislike';
                $engagement->save();
                $this->userEngagement = $engagement;
            }
        } else {
            // Create new dislike engagement
            $engagement = VideoEngagement::create([
                'video_id' => $this->video->vid,
                'user_id' => Auth::id(),
                'engagement_type' => 'dislike',
            ]);
            $this->userEngagement = $engagement;
        }

        $this->video->updateLikesCount();
        $this->video->updateDislikesCount();
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
        $this->video->updateCommentsCount();
        $this->video->refresh();
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
        $this->video->updateCommentsCount();
        $this->video->refresh();
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

    public function toggleReplies($commentId)
    {
        if (isset($this->showReplies[$commentId])) {
            unset($this->showReplies[$commentId]);
        } else {
            $this->showReplies[$commentId] = true;
        }
    }

    public function render()
    {
        $comments = $this->video->topLevelComments()
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        $relatedVideos = Video::public()
            ->with(['user'])
            ->where('vid', '!=', $this->video->vid)
            ->when($this->video->category, function ($query) {
                $query->byCategory($this->video->category);
            })
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return view('livewire.video-watch', [
            'comments' => $comments,
            'relatedVideos' => $relatedVideos,
        ]);
    }
}
