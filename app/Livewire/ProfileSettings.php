<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;

#[Title('Profile Settings - OpenMediaPlatform')]
class ProfileSettings extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $channel_name;
    public $channel_description;
    public $channel_banner;
    public $profile_picture;
    public $newProfilePicture;
    public $newChannelBanner;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    
    public $activeTab = 'general';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'channel_name' => 'required|string|max:255',
        'channel_description' => 'nullable|string|max:1000',
        'newProfilePicture' => 'nullable|image|max:2048', // 2MB max
        'newChannelBanner' => 'nullable|image|max:5120', // 5MB max
        'current_password' => 'nullable|required_with:new_password|current_password',
        'new_password' => 'nullable|min:8|confirmed',
    ];

    protected $messages = [
        'newProfilePicture.image' => 'Profile picture must be an image file.',
        'newProfilePicture.max' => 'Profile picture must be smaller than 2MB.',
        'newChannelBanner.image' => 'Channel banner must be an image file.',
        'newChannelBanner.max' => 'Channel banner must be smaller than 5MB.',
        'current_password.current_password' => 'The current password is incorrect.',
        'new_password.confirmed' => 'The password confirmation does not match.',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->channel_name = $user->channel_name ?? $user->name;
        $this->channel_description = $user->channel_description;
        $this->profile_picture = $user->profile_picture;
        $this->channel_banner = $user->channel_banner;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetValidation();
    }

    public function updateGeneral()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'channel_name' => 'required|string|max:255',
            'channel_description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'channel_name' => $this->channel_name,
            'channel_description' => $this->channel_description,
        ]);

        $this->dispatch('show-message', ['type' => 'success', 'message' => 'Profile updated successfully!']);
    }

    public function updateProfilePicture()
    {
        $this->validate([
            'newProfilePicture' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $path = $this->newProfilePicture->store('profile-pictures', 'public');

        $user->update([
            'profile_picture' => $path,
        ]);

        $this->profile_picture = $path;
        $this->newProfilePicture = null;

        $this->dispatch('show-message', ['type' => 'success', 'message' => 'Profile picture updated successfully!']);
    }

    public function updateChannelBanner()
    {
        $this->validate([
            'newChannelBanner' => 'required|image|max:5120',
        ]);

        $user = Auth::user();

        // Delete old channel banner if exists
        if ($user->channel_banner) {
            Storage::disk('public')->delete($user->channel_banner);
        }

        // Store new channel banner
        $path = $this->newChannelBanner->store('channel-banners', 'public');

        $user->update([
            'channel_banner' => $path,
        ]);

        $this->channel_banner = $path;
        $this->newChannelBanner = null;

        $this->dispatch('show-message', ['type' => 'success', 'message' => 'Channel banner updated successfully!']);
    }

    public function removeProfilePicture()
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
            $this->profile_picture = null;

            $this->dispatch('show-message', ['type' => 'success', 'message' => 'Profile picture removed successfully!']);
        }
    }

    public function removeChannelBanner()
    {
        $user = Auth::user();

        if ($user->channel_banner) {
            Storage::disk('public')->delete($user->channel_banner);
            $user->update(['channel_banner' => null]);
            $this->channel_banner = null;

            $this->dispatch('show-message', ['type' => 'success', 'message' => 'Channel banner removed successfully!']);
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->dispatch('show-message', ['type' => 'success', 'message' => 'Password updated successfully!']);
    }

    public function render()
    {
        return view('livewire.profile-settings')
            ->layout('components.layouts.app');
    }
}
