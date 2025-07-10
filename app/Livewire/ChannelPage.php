<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Video;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

class ChannelPage extends Component
{
    use WithPagination;

    public User $user;
    public $activeTab = 'videos';
    public $isSubscribed = false;
    public $subscriberCount = 0;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->checkSubscription();
        $this->updateSubscriberCount();
    }

    public function checkSubscription()
    {
        if (Auth::check()) {
            $this->isSubscribed = Subscription::where([
                'subscriber_id' => Auth::id(),
                'channel_id' => $this->user->id,
            ])->exists();
        }
    }

    public function updateSubscriberCount()
    {
        $this->subscriberCount = $this->user->subscribers()->count();
    }

    public function toggleSubscription()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::id() === $this->user->id) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => 'You cannot subscribe to yourself!']);
            return;
        }

        $subscription = Subscription::where([
            'subscriber_id' => Auth::id(),
            'channel_id' => $this->user->id,
        ])->first();

        if ($subscription) {
            $subscription->delete();
            $this->isSubscribed = false;
            $this->dispatch('show-message', ['type' => 'success', 'message' => 'Unsubscribed successfully!']);
        } else {
            Subscription::create([
                'subscriber_id' => Auth::id(),
                'channel_id' => $this->user->id,
            ]);
            $this->isSubscribed = true;
            $this->dispatch('show-message', ['type' => 'success', 'message' => 'Subscribed successfully!']);
        }

        $this->updateSubscriberCount();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getVideosProperty()
    {
        return $this->user->videos()
            ->where('visibility', Video::VISIBILITY_PUBLIC)
            ->latest('published_at')
            ->paginate(12);
    }

    public function getPlaylistsProperty()
    {
        return $this->user->playlists()
            ->where('is_public', true)
            ->latest()
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.channel-page', [
            'videos' => $this->videos,
            'playlists' => $this->playlists,
        ])->layout('components.layouts.app')
          ->title($this->user->getChannelName() . ' - OpenMediaPlatform');
    }
}
