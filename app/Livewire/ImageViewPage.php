<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Image;
use App\Models\Comment;
use App\Models\ImageEngagement;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class ImageViewPage extends Component
{
    public Image $image;
    public $newComment = '';
    public $replyTo = null;
    public $replyContent = '';
    public $userEngagement = null;
    public $showReplies = [];
    public $isSubscribed = false;
    public $showShareModal = false;
    public $currentImageIndex = 0;

    public function mount(Image $image)
    {
        // Check if user can view this image
        if (!$image->canView(auth()->user())) {
            abort(404, 'Image not found');
        }

        $this->image = $image->load(['user', 'imageFiles', 'comments.user']);
        
        // Record view
        $this->recordView();
        
        // Load user engagement if logged in
        if (Auth::check()) {
            $this->userEngagement = $this->image->userEngagement(Auth::id());
            $this->checkSubscription();
        }
    }

    public function checkSubscription()
    {
        if (Auth::check()) {
            $this->isSubscribed = Subscription::where([
                'subscriber_id' => Auth::id(),
                'channel_id' => $this->image->user_id,
            ])->exists();
        }
    }

    public function recordView()
    {
        // Increment image views
        $this->image->incrementViews();
    }

    public function toggleLike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->image->toggleLike(Auth::id());
        $this->userEngagement = $this->image->userEngagement(Auth::id());
        $this->image->refresh();
    }

    public function toggleDislike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->image->toggleDislike(Auth::id());
        $this->userEngagement = $this->image->userEngagement(Auth::id());
        $this->image->refresh();
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
            'commentable_type' => Image::class,
            'commentable_id' => $this->image->iid,
            'user_id' => Auth::id(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->image->refresh();
        $this->image->load(['comments.user']);
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
            'commentable_type' => Image::class,
            'commentable_id' => $this->image->iid,
            'user_id' => Auth::id(),
            'content' => $this->replyContent,
            'parent_id' => $this->replyTo,
        ]);

        $this->replyContent = '';
        $this->replyTo = null;
        $this->image->refresh();
        $this->image->load(['comments.user']);
    }

    public function setReplyTo($commentId)
    {
        $this->replyTo = $commentId;
        $this->replyContent = '';
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

    public function subscribe()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->isSubscribed) {
            Subscription::create([
                'subscriber_id' => Auth::id(),
                'channel_id' => $this->image->user_id,
            ]);
            $this->isSubscribed = true;
        }
    }

    public function unsubscribe()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->isSubscribed) {
            Subscription::where([
                'subscriber_id' => Auth::id(),
                'channel_id' => $this->image->user_id,
            ])->delete();
            $this->isSubscribed = false;
        }
    }

    public function generateShareLink()
    {
        if (!Auth::check() || Auth::id() !== $this->image->user_id) {
            abort(403, 'Unauthorized');
        }

        $this->image->generateShareToken();
        $this->showShareModal = true;
    }

    public function revokeShareLink()
    {
        if (!Auth::check() || Auth::id() !== $this->image->user_id) {
            abort(403, 'Unauthorized');
        }

        $this->image->revokeShare();
        $this->showShareModal = false;
    }

    public function nextImage()
    {
        if ($this->currentImageIndex < count($this->image->imageFiles) - 1) {
            $this->currentImageIndex++;
        }
    }

    public function previousImage()
    {
        if ($this->currentImageIndex > 0) {
            $this->currentImageIndex--;
        }
    }

    public function setCurrentImage($index)
    {
        $this->currentImageIndex = $index;
    }

    public function render()
    {
        return view('livewire.image-view-page')->layout('components.layouts.app');
    }
}
