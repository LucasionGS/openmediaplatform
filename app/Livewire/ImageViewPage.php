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
    public $isSharedView = false;

    public function mount($image = null, $token = null)
    {
        // Check if this is a shared view (accessed via /share/image/{token} route)
        $this->isSharedView = request()->routeIs('share.image');
        
        if ($this->isSharedView) {
            // Handle shared view with token
            $token = $token ?: request()->route('token');
            
            if (!$token) {
                abort(404, 'Share token required');
            }
            
            $foundImage = Image::findByShareToken($token);
            
            if (!$foundImage) {
                return abort(404, 'Shared image not found or share link has expired');
            }
            
            $this->image = $foundImage;
            
            // If user is logged in and accessing shared link, redirect to regular view page
            if (auth()->check() && $this->image->canView(auth()->user())) {
                return redirect()->route('images.show', $this->image);
            }
        } else {
            // Regular view - ensure image is passed and user can view it
            if (!$image) {
                abort(404, 'Image not found');
            }
            
            if (!$image->canView(auth()->user())) {
                abort(404, 'Image not found');
            }
            
            $this->image = $image;
        }

        $this->image->load(['user', 'imageFiles', 'comments.user']);
        
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

        // Only image owner can generate share links
        if (Auth::id() !== $this->image->user_id) {
            session()->flash('error', 'Only the image owner can create share links.');
            return;
        }

        if (!$this->image->share_token) {
            $this->image->generateShareToken();
        }

        session()->flash('success', 'Share link generated successfully!');
    }

    public function revokeShareLink()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Only image owner can revoke share links
        if (Auth::id() !== $this->image->user_id) {
            session()->flash('error', 'Only the image owner can revoke share links.');
            return;
        }

        $this->image->revokeShare();
        $this->showShareModal = false;

        session()->flash('success', 'Share link revoked successfully!');
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
