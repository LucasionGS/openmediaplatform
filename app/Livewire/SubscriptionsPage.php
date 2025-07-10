<?php

namespace App\Livewire;

use App\Models\Subscription;
use App\Models\Video;
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
        ])->layout('components.layouts.app');
    }
}
