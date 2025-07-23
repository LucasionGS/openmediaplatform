<?php

namespace App\Livewire;

use App\Models\Subscription;
use App\Models\Video;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Subscriptions - OpenMediaPlatform')]
class SubscriptionsPage extends Component
{
    use WithPagination;

    public $activeTab = 'videos';

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getSubscriptionsProperty()
    {
        return Auth::user()->subscriptions()
            ->with('channel')
            ->latest()
            ->paginate(20);
    }

    public function getSubscriptionVideosProperty()
    {
        $subscribedChannelIds = Auth::user()->subscriptions()->pluck('channel_id');
        
        return Video::whereIn('user_id', $subscribedChannelIds)
            ->where('visibility', Video::VISIBILITY_PUBLIC)
            ->with('user')
            ->latest('published_at')
            ->paginate(12);
    }

    public function getSubscriptionImagesProperty()
    {
        $subscribedChannelIds = Auth::user()->subscriptions()->pluck('channel_id');
        
        return Image::whereIn('user_id', $subscribedChannelIds)
            ->where('visibility', Image::VISIBILITY_PUBLIC)
            ->with('user')
            ->latest('published_at')
            ->paginate(12);
    }

    public function getSubscriptionMediaProperty()
    {
        $subscribedChannelIds = Auth::user()->subscriptions()->pluck('channel_id');
        
        // Get both videos and images
        $videos = Video::whereIn('user_id', $subscribedChannelIds)
            ->where('visibility', Video::VISIBILITY_PUBLIC)
            ->with('user')
            ->get()
            ->map(function ($video) {
                $video->media_type = 'video';
                return $video;
            });

        $images = Image::whereIn('user_id', $subscribedChannelIds)
            ->where('visibility', Image::VISIBILITY_PUBLIC)
            ->with('user')
            ->get()
            ->map(function ($image) {
                $image->media_type = 'image';
                return $image;
            });

        // Combine and sort by published_at/created_at
        $allMedia = $videos->concat($images)->sortByDesc(function ($item) {
            return $item->published_at ?? $item->created_at;
        });

        // Manual pagination since we can't paginate a collection directly
        $page = request()->get('page', 1);
        $perPage = 12;
        $total = $allMedia->count();
        $items = $allMedia->slice(($page - 1) * $perPage, $perPage);
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    public function unsubscribe($channelId)
    {
        Subscription::where([
            'subscriber_id' => Auth::id(),
            'channel_id' => $channelId,
        ])->delete();

        $this->dispatch('show-message', ['type' => 'success', 'message' => 'Unsubscribed successfully!']);
    }

    public function render()
    {
        return view('livewire.subscriptions-page', [
            'subscriptions' => $this->subscriptions,
            'subscriptionVideos' => $this->subscriptionVideos,
            'subscriptionImages' => $this->subscriptionImages,
            'subscriptionMedia' => $this->subscriptionMedia,
        ])->layout('components.layouts.app');
    }
}
