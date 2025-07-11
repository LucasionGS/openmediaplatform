<div>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
        <div class="max-w-6xl mx-auto px-4 py-6">
            <div class="flex items-center space-x-3">
                <div class="bg-red-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Admin Settings</h1>
                    <p class="mt-1 text-gray-600">Manage your site configuration and settings</p>
                </div>
            </div>
            
            <!-- Tab Navigation -->
            <div class="mt-6 border-b border-gray-200">
                <nav class="flex space-x-8">
                    <button 
                        wire:click="setActiveTab('general')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'general' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        General
                    </button>
                    <button 
                        wire:click="setActiveTab('appearance')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'appearance' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Appearance
                    </button>
                    <button 
                        wire:click="setActiveTab('users')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'users' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        User Management
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="max-w-6xl mx-auto px-4">
        @if($activeTab === 'general')
            <!-- General Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">General Site Settings</h2>
                
                <form wire:submit.prevent="updateGeneralSettings" class="space-y-6">
                    <!-- Site Title -->
                    <div>
                        <label for="siteTitle" class="block text-sm font-medium text-gray-700">Site Title</label>
                        <input 
                            type="text" 
                            id="siteTitle" 
                            wire:model="siteTitle" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Enter your site title"
                        >
                        @error('siteTitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-sm text-gray-500">This will appear in the browser title and throughout the site</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition-colors"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="updateGeneralSettings">Save General Settings</span>
                            <span wire:loading wire:target="updateGeneralSettings">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

        @elseif($activeTab === 'appearance')
            <!-- Appearance Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Site Appearance</h2>
                
                <!-- Site Icon Section -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Site Icon</h3>
                        
                        <!-- Current Icon Display -->
                        <div class="flex items-center space-x-6 mb-4">
                            <div class="flex-shrink-0">
                                @if($siteIcon)
                                    <img src="{{ asset('sf/' . $siteIcon) }}"
                                         alt="Site Icon" 
                                         class="w-16 h-16 object-cover border-2 border-gray-200 rounded">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 border-2 border-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Upload Controls -->
                            <div class="flex-1">
                                <form wire:submit.prevent="updateSiteIcon">
                                    <div class="space-y-4">
                                        <div>
                                            <input 
                                                type="file" 
                                                id="newSiteIcon" 
                                                wire:model="newSiteIcon" 
                                                accept="image/*"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                                            >
                                            @error('newSiteIcon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                            <p class="mt-1 text-sm text-gray-500">Recommended: 32x32px or 64x64px, PNG or ICO format</p>
                                        </div>

                                        <div class="flex space-x-2">
                                            <button 
                                                type="submit" 
                                                class="px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition-colors"
                                                wire:loading.attr="disabled"
                                                @if(!$newSiteIcon) disabled @endif
                                            >
                                                <span wire:loading.remove wire:target="updateSiteIcon">Upload Icon</span>
                                                <span wire:loading wire:target="updateSiteIcon">Uploading...</span>
                                            </button>

                                            @if($siteIcon)
                                                <button 
                                                    type="button"
                                                    wire:click="removeSiteIcon"
                                                    wire:confirm="Are you sure you want to remove the site icon?"
                                                    class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors"
                                                >
                                                    Remove Icon
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'users')
            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">User Management</h2>
                
                <!-- Search and Filter Controls -->
                <div class="mb-6 flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            wire:model.live="search"
                            placeholder="Search users by name or email..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                        >
                    </div>
                    <div class="sm:w-48">
                        <select 
                            wire:model.live="roleFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                        >
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="moderator">Moderator</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Videos</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($user->profile_picture)
                                                <img src="{{ asset('sf/' . $user->profile_picture) }}" 
                                                     alt="{{ $user->name }}" 
                                                     class="w-10 h-10 rounded-full object-cover mr-3">
                                            @else
                                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold mr-3">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($editingUserId === $user->id)
                                            <select 
                                                wire:model="editingUserRole"
                                                class="text-xs px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-red-500 focus:border-red-500"
                                            >
                                                <option value="admin">Admin</option>
                                                <option value="moderator">Moderator</option>
                                                <option value="user">User</option>
                                            </select>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                                   ($user->role === 'moderator' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->videos()->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($editingUserId === $user->id)
                                            <div class="flex justify-end space-x-2">
                                                <button 
                                                    wire:click="updateUserRole"
                                                    class="text-green-600 hover:text-green-900"
                                                >
                                                    Save
                                                </button>
                                                <button 
                                                    wire:click="cancelEdit"
                                                    class="text-gray-600 hover:text-gray-900"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        @else
                                            <div class="flex justify-end space-x-2">
                                                @if($user->id !== auth()->id())
                                                    <button 
                                                        wire:click="editUser({{ $user->id }})"
                                                        class="text-blue-600 hover:text-blue-900"
                                                    >
                                                        Edit Role
                                                    </button>
                                                    <button 
                                                        wire:click="confirmDeleteUser({{ $user->id }})"
                                                        class="text-red-600 hover:text-red-900"
                                                    >
                                                        Delete
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 text-xs">(You)</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Delete User Account</h3>
                    <p class="text-gray-600 text-center mb-6">
                        Are you sure you want to delete this user account? This action cannot be undone and will also delete all their videos and content.
                    </p>
                    <div class="flex space-x-3">
                        <button 
                            wire:click="cancelDelete"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="deleteUser"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Success/Error Messages -->
    <div 
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:show-message.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-4 right-4 z-50"
        style="display: none;"
    >
        <div class="rounded-md p-4 shadow-lg" :class="type === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg x-show="type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <svg x-show="type === 'error'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" :class="type === 'success' ? 'text-green-800' : 'text-red-800'" x-text="message"></p>
                </div>
            </div>
        </div>
    </div>
</div>
