<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use App\Models\Comment;
use App\Models\VideoEngagement;
use App\Models\WatchHistory;
use App\Models\Subscription;
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
    public $isSubscribed = false;
    public $isSharedView = false;
    public $showShareModal = false;

    public function mount($video = null, $token = null)
    {
        // Check if this is a shared view (accessed via /share/{token} route)
        $this->isSharedView = request()->routeIs('videos.share');
        
        if ($this->isSharedView) {
            // Handle shared view with token
            $token = $token ?? request()->route('token');
            
            if (!$token) {
                abort(404, 'Share token required');
            }
            
            $foundVideo = Video::findByShareToken($token);

            if (!$foundVideo) {
                return abort(404, 'Shared video not found or share link has expired');
            }
            
            $this->video = $foundVideo;
            
            if (!$this->video) {
                abort(404, 'Shared video not found or share link has expired');
            }
            
            // If user is logged in and accessing shared link, redirect to regular watch page
            if (Auth::check()) {
                return redirect()->route('videos.show', $this->video);
            }
        } else {
            // Handle regular authenticated view with video object
            if (!$video) {
                abort(404, 'Video not found');
            }
            $this->video = $video;
        }
        
        // Record view
        $this->recordView();
        
        // Load user engagement if logged in
        if (Auth::check()) {
            $this->userEngagement = $this->video->userEngagement(Auth::id());
            $this->checkSubscription();
        }
    }

    public function checkSubscription()
    {
        if (Auth::check()) {
            $this->isSubscribed = Subscription::where([
                'subscriber_id' => Auth::id(),
                'channel_id' => $this->video->user_id,
            ])->exists();
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

    public function toggleSubscription()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::id() === $this->video->user_id) {
            session()->flash('error', 'You cannot subscribe to yourself!');
            return;
        }

        $subscription = Subscription::where([
            'subscriber_id' => Auth::id(),
            'channel_id' => $this->video->user_id,
        ])->first();

        if ($subscription) {
            $subscription->delete();
            $this->isSubscribed = false;
            session()->flash('success', 'Unsubscribed successfully!');
        } else {
            Subscription::create([
                'subscriber_id' => Auth::id(),
                'channel_id' => $this->video->user_id,
            ]);
            $this->isSubscribed = true;
            session()->flash('success', 'Subscribed successfully!');
        }

        // Update the user's subscriber count
        $this->video->user->refresh();
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        try {
            Comment::create([
                'video_id' => $this->video->vid,
                'user_id' => Auth::id(),
                'content' => trim($this->newComment),
            ]);

            $this->newComment = '';
            $this->video->updateCommentsCount();
            $this->video->refresh();
            
            // Add a success message
            session()->flash('comment_success', 'Comment added successfully!');
            
            // Dispatch browser event to reset button state
            $this->dispatch('comment-posted');
            
        } catch (\Exception $e) {
            $this->addError('newComment', 'Failed to add comment. Please try again.');
            \Log::error('Comment creation failed: ' . $e->getMessage());
        }
    }

    public function addReply()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'replyContent' => 'required|string|max:1000',
        ]);

        try {
            Comment::create([
                'video_id' => $this->video->vid,
                'user_id' => Auth::id(),
                'parent_id' => $this->replyTo,
                'content' => trim($this->replyContent),
            ]);

            $this->replyContent = '';
            $this->replyTo = null;
            $this->video->updateCommentsCount();
            $this->video->refresh();
            
            // Add a success message
            session()->flash('reply_success', 'Reply added successfully!');
            
        } catch (\Exception $e) {
            $this->addError('replyContent', 'Failed to add reply. Please try again.');
            \Log::error('Reply creation failed: ' . $e->getMessage());
        }
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

    public function openShareModal()
    {
        $this->showShareModal = true;
    }

    public function closeShareModal()
    {
        $this->showShareModal = false;
    }

    public function generateShareLink()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Only video owner can generate share links
        if (Auth::id() !== $this->video->user_id) {
            session()->flash('error', 'Only the video owner can create share links.');
            return;
        }

        if (!$this->video->share_token) {
            $this->video->generateShareToken();
        }

        session()->flash('success', 'Share link generated successfully!');
    }

    public function revokeShareLink()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Only video owner can revoke share links
        if (Auth::id() !== $this->video->user_id) {
            session()->flash('error', 'Only the video owner can revoke share links.');
            return;
        }

        $this->video->revokeShare();
        session()->flash('success', 'Share link revoked successfully!');
    }

    public function render()
    {
        $comments = $this->video->topLevelComments()
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        // Only load related videos for authenticated users (not for shared views)
        $relatedVideos = collect();
        if (!$this->isSharedView) {
            $relatedVideos = Video::public()
                ->with(['user'])
                ->where('vid', '!=', $this->video->vid)
                ->when($this->video->category, function ($query) {
                    $query->byCategory($this->video->category);
                })
                ->orderBy('views', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        $view = view('livewire.video-watch', [
            'comments' => $comments,
            'relatedVideos' => $relatedVideos,
            'isSharedView' => $this->isSharedView,
        ]);

        // Use different layout for shared views
        if ($this->isSharedView) {
            return $view->layout('components.layouts.shared', [
                'title' => $this->video->title,
                'video' => $this->video
            ]);
        }

        return $view->layout('components.layouts.app');
    }
}
