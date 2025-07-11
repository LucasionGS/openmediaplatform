<?php

namespace App\Livewire;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Admin Settings - OpenMediaPlatform')]
class AdminSettings extends Component
{
    use WithFileUploads, WithPagination;

    public string $siteTitle = '';
    public $siteIcon;
    public $newSiteIcon;
    public string $activeTab = 'general';
    
    // User management properties
    public $search = '';
    public $roleFilter = '';
    public $editingUserId = null;
    public $editingUserRole = '';
    public $showDeleteConfirm = false;
    public $deletingUserId = null;

    protected $rules = [
        'siteTitle' => 'required|string|max:255',
        'newSiteIcon' => 'nullable|file|max:2048|mimes:png,jpg,jpeg,gif,svg,ico',
        'editingUserRole' => 'required|in:admin,moderator,user',
    ];

    protected $messages = [
        'siteTitle.required' => 'Site title is required.',
        'newSiteIcon.file' => 'Site icon must be a valid file.',
        'newSiteIcon.max' => 'Site icon must be smaller than 2MB.',
        'newSiteIcon.mimes' => 'Site icon must be a PNG, JPG, JPEG, GIF, SVG, or ICO file.',
        'editingUserRole.required' => 'Role is required.',
        'editingUserRole.in' => 'Invalid role selected.',
    ];

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function mount()
    {
        // Check if user is admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Admin access required');
        }

        // Load current settings
        $this->siteTitle = SiteSetting::get('site_title', 'OpenMediaPlatform');
        $this->siteIcon = SiteSetting::get('site_icon');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetValidation();
    }

    public function updateGeneralSettings()
    {
        $this->validate([
            'siteTitle' => 'required|string|max:255',
        ]);

        SiteSetting::set('site_title', $this->siteTitle, 'string', 'The title of the website');

        $this->dispatch('show-message', ['type' => 'success', 'message' => 'Settings updated successfully!']);
    }

    public function updateSiteIcon()
    {
        $this->validate([
            'newSiteIcon' => 'required|file|max:2048|mimes:png,jpg,jpeg,gif,svg,ico',
        ]);

        // Delete old icon if exists
        if ($this->siteIcon) {
            Storage::disk('public')->delete($this->siteIcon);
        }

        // Store new icon
        $path = $this->newSiteIcon->store('site-assets', 'public');

        SiteSetting::set('site_icon', $path, 'image', 'The icon/favicon of the website');

        $this->siteIcon = $path;
        $this->newSiteIcon = null;

        $this->dispatch('show-message', ['type' => 'success', 'message' => 'Site icon updated successfully!']);
    }

    public function removeSiteIcon()
    {
        if ($this->siteIcon) {
            Storage::disk('public')->delete($this->siteIcon);
            SiteSetting::set('site_icon', null, 'image', 'The icon/favicon of the website');
            $this->siteIcon = null;

            $this->dispatch('show-message', ['type' => 'success', 'message' => 'Site icon removed successfully!']);
        }
    }

    // User Management Methods
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function getUsers()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function editUser($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => 'User not found.']);
            return;
        }

        $this->editingUserId = $userId;
        $this->editingUserRole = $user->role;
    }

    public function updateUserRole()
    {
        $this->validate([
            'editingUserRole' => 'required|in:admin,moderator,user',
        ]);

        $user = User::find($this->editingUserId);
        if (!$user) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => 'User not found.']);
            return;
        }

        // Prevent admin from changing their own role
        if ($user->id === auth()->id()) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => 'You cannot change your own role.']);
            return;
        }

        $oldRole = $user->role;
        $user->role = $this->editingUserRole;
        $user->save();

        $this->editingUserId = null;
        $this->editingUserRole = '';

        $this->dispatch('show-message', ['type' => 'success', 'message' => "User role changed from '{$oldRole}' to '{$user->role}' successfully."]);
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
        $this->editingUserRole = '';
        $this->resetValidation();
    }

    public function confirmDeleteUser($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => 'User not found.']);
            return;
        }

        // Prevent admin from deleting their own account
        if ($user->id === auth()->id()) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => 'You cannot delete your own account.']);
            return;
        }

        $this->deletingUserId = $userId;
        $this->showDeleteConfirm = true;
    }

    public function deleteUser()
    {
        $user = User::find($this->deletingUserId);
        if (!$user) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => 'User not found.']);
            return;
        }

        // Prevent admin from deleting their own account
        if ($user->id === auth()->id()) {
            $this->dispatch('show-message', ['type' => 'error', 'message' => 'You cannot delete your own account.']);
            return;
        }

        $userName = $user->name;
        
        // Delete user's videos and related data (you might want to do this in a job for large datasets)
        foreach ($user->videos as $video) {
            $video->deleteFiles(); // Assuming you have this method
            $video->delete();
        }
        
        // Delete user
        $user->delete();

        $this->showDeleteConfirm = false;
        $this->deletingUserId = null;

        $this->dispatch('show-message', ['type' => 'success', 'message' => "User '{$userName}' and all their content has been deleted."]);
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
        $this->deletingUserId = null;
    }

    public function render()
    {
        return view('livewire.admin-settings', [
            'users' => $this->getUsers(),
        ])->layout('components.layouts.app');
    }
}
