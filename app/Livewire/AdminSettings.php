<?php

namespace App\Livewire;

use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Category;
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
    
    // Upload limit properties
    public int $maxImageSize = 10240; // KB (10MB default)
    public int $maxImageCount = 50;   // Maximum images per post
    
    // Category management properties
    public $categories;
    public $editingCategoryId = null;
    public $categoryName = '';
    public $categorySlug = '';
    public $categoryDescription = '';
    public $categoryIsActive = true;
    public $categorySortOrder = 0;
    public $showCategoryModal = false;
    
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
        'maxImageSize' => 'required|integer|min:1|max:102400', // 1KB to 100MB
        'maxImageCount' => 'required|integer|min:1|max:100',   // 1 to 100 images
        'categoryName' => 'required|string|max:255',
        'categorySlug' => 'required|string|max:255|alpha_dash',
        'categoryDescription' => 'nullable|string|max:500',
        'categoryIsActive' => 'boolean',
        'categorySortOrder' => 'required|integer|min:0|max:999',
    ];

    protected $messages = [
        'siteTitle.required' => 'Site title is required.',
        'newSiteIcon.file' => 'Site icon must be a valid file.',
        'newSiteIcon.max' => 'Site icon must be smaller than 2MB.',
        'newSiteIcon.mimes' => 'Site icon must be a PNG, JPG, JPEG, GIF, SVG, or ICO file.',
        'editingUserRole.required' => 'Role is required.',
        'editingUserRole.in' => 'Invalid role selected.',
        'maxImageSize.required' => 'Maximum image size is required.',
        'maxImageSize.integer' => 'Maximum image size must be a number.',
        'maxImageSize.min' => 'Maximum image size must be at least 1 KB.',
        'maxImageSize.max' => 'Maximum image size cannot exceed 100 MB.',
        'maxImageCount.required' => 'Maximum image count is required.',
        'maxImageCount.integer' => 'Maximum image count must be a number.',
        'maxImageCount.min' => 'Maximum image count must be at least 1.',
        'maxImageCount.max' => 'Maximum image count cannot exceed 100.',
        'categoryName.required' => 'Category name is required.',
        'categoryName.max' => 'Category name cannot exceed 255 characters.',
        'categorySlug.required' => 'Category slug is required.',
        'categorySlug.max' => 'Category slug cannot exceed 255 characters.',
        'categorySlug.alpha_dash' => 'Category slug can only contain letters, numbers, dashes, and underscores.',
        'categoryDescription.max' => 'Category description cannot exceed 500 characters.',
        'categorySortOrder.required' => 'Sort order is required.',
        'categorySortOrder.integer' => 'Sort order must be a number.',
        'categorySortOrder.min' => 'Sort order must be at least 0.',
        'categorySortOrder.max' => 'Sort order cannot exceed 999.',
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
        
        // Load upload limit settings with defaults
        $this->maxImageSize = (int) SiteSetting::get('max_image_size', 10240); // 10MB default
        $this->maxImageCount = (int) SiteSetting::get('max_image_count', 50);   // 50 images default
        
        // Load categories
        $this->loadCategories();
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

    public function updateUploadLimits()
    {
        $this->validate([
            'maxImageSize' => 'required|integer|min:1|max:102400',
            'maxImageCount' => 'required|integer|min:1|max:100',
        ]);

        SiteSetting::set('max_image_size', $this->maxImageSize, 'integer', 'Maximum image file size in KB');
        SiteSetting::set('max_image_count', $this->maxImageCount, 'integer', 'Maximum number of images per post');

        $this->dispatch('show-message', ['type' => 'success', 'message' => 'Upload limits updated successfully!']);
    }

    // Category Management Methods
    public function loadCategories()
    {
        $this->categories = Category::orderBy('sort_order')->orderBy('name')->get();
    }

    public function openCategoryModal($categoryId = null)
    {
        if ($categoryId) {
            $category = Category::find($categoryId);
            $this->editingCategoryId = $categoryId;
            $this->categoryName = $category->name;
            $this->categorySlug = $category->slug;
            $this->categoryDescription = $category->description;
            $this->categoryIsActive = $category->is_active;
            $this->categorySortOrder = $category->sort_order;
        } else {
            $this->resetCategoryForm();
        }
        
        $this->showCategoryModal = true;
    }

    public function resetCategoryForm()
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->categorySlug = '';
        $this->categoryDescription = '';
        $this->categoryIsActive = true;
        $this->categorySortOrder = 0;
        $this->resetValidation(['categoryName', 'categorySlug', 'categoryDescription', 'categorySortOrder']);
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
            'categorySlug' => 'required|string|max:255|alpha_dash',
            'categoryDescription' => 'nullable|string|max:500',
            'categorySortOrder' => 'required|integer|min:0|max:999',
        ]);

        // Check for unique slug
        $existingCategory = Category::where('slug', $this->categorySlug)
                                   ->when($this->editingCategoryId, function ($query) {
                                       return $query->where('id', '!=', $this->editingCategoryId);
                                   })
                                   ->first();

        if ($existingCategory) {
            $this->addError('categorySlug', 'This slug already exists.');
            return;
        }

        $categoryData = [
            'name' => $this->categoryName,
            'slug' => $this->categorySlug,
            'description' => $this->categoryDescription,
            'is_active' => $this->categoryIsActive,
            'sort_order' => $this->categorySortOrder,
        ];

        if ($this->editingCategoryId) {
            Category::find($this->editingCategoryId)->update($categoryData);
            $message = 'Category updated successfully!';
        } else {
            Category::create($categoryData);
            $message = 'Category created successfully!';
        }

        $this->loadCategories();
        $this->showCategoryModal = false;
        $this->resetCategoryForm();
        $this->dispatch('show-message', ['type' => 'success', 'message' => $message]);
    }

    public function deleteCategory($categoryId)
    {
        $category = Category::find($categoryId);
        
        if ($category) {
            $category->delete();
            $this->loadCategories();
            $this->dispatch('show-message', ['type' => 'success', 'message' => 'Category deleted successfully!']);
        }
    }

    public function toggleCategoryStatus($categoryId)
    {
        $category = Category::find($categoryId);
        
        if ($category) {
            $category->update(['is_active' => !$category->is_active]);
            $this->loadCategories();
            $status = $category->is_active ? 'activated' : 'deactivated';
            $this->dispatch('show-message', ['type' => 'success', 'message' => "Category {$status} successfully!"]);
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
