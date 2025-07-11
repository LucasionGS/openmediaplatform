<div>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
        <div class="max-w-4xl mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="mt-2 text-gray-600">Manage your account settings and channel preferences</p>
            
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
                        wire:click="setActiveTab('security')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'security' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Security
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="max-w-4xl mx-auto px-4">
        @if($activeTab === 'general')
            <!-- General Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">General Information</h2>
                
                <form wire:submit.prevent="updateGeneral" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input 
                            type="text" 
                            id="name" 
                            wire:model="name" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Enter your full name"
                        >
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            wire:model="email" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Enter your email"
                        >
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Channel Name -->
                    <div>
                        <label for="channel_name" class="block text-sm font-medium text-gray-700">Channel Name</label>
                        <input 
                            type="text" 
                            id="channel_name" 
                            wire:model="channel_name" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Enter your channel name"
                        >
                        @error('channel_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-sm text-gray-500">This is how your channel will appear to viewers</p>
                    </div>

                    <!-- Channel Description -->
                    <div>
                        <label for="channel_description" class="block text-sm font-medium text-gray-700">Channel Description</label>
                        <textarea 
                            id="channel_description" 
                            wire:model="channel_description" 
                            rows="4"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Tell viewers about your channel..."
                        ></textarea>
                        @error('channel_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Save Changes</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

        @elseif($activeTab === 'appearance')
            <!-- Appearance Settings -->
            <div class="space-y-6">
                <!-- Profile Picture -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Profile Picture</h2>
                    
                    <div class="flex items-start space-x-6">
                        <!-- Current Profile Picture -->
                        <div class="flex-shrink-0">
                            @if($profile_picture)
                                <img src="{{ asset('sf/' . $profile_picture) }}" 
                                     alt="Profile Picture" 
                                     class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                            @else
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Upload Controls -->
                        <div class="flex-1">
                            <form wire:submit.prevent="updateProfilePicture">
                                <div class="space-y-4">
                                    <div>
                                        <input 
                                            type="file" 
                                            id="newProfilePicture" 
                                            wire:model="newProfilePicture" 
                                            accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                                        >
                                        @error('newProfilePicture') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                        <p class="mt-1 text-sm text-gray-500">JPG, PNG or GIF. Maximum 2MB.</p>
                                    </div>

                                    <div class="flex space-x-3">
                                        <button 
                                            type="submit" 
                                            class="px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition-colors"
                                            wire:loading.attr="disabled"
                                            @if(!$newProfilePicture) disabled @endif
                                        >
                                            <span wire:loading.remove wire:target="updateProfilePicture">Upload Picture</span>
                                            <span wire:loading wire:target="updateProfilePicture">Uploading...</span>
                                        </button>

                                        @if($profile_picture)
                                            <button 
                                                type="button"
                                                wire:click="removeProfilePicture"
                                                wire:confirm="Are you sure you want to remove your profile picture?"
                                                class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors"
                                            >
                                                Remove Picture
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Channel Banner -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Channel Banner</h2>
                    
                    <div class="space-y-4">
                        <!-- Current Banner -->
                        @if($channel_banner)
                            <div class="aspect-[6/1] w-full bg-gray-100 rounded-lg overflow-hidden">
                                <img src="{{ asset('sf/' . $channel_banner) }}" 
                                     alt="Channel Banner" 
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-[6/1] w-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <p class="text-white text-lg font-medium">No banner uploaded</p>
                            </div>
                        @endif

                        <!-- Upload Controls -->
                        <form wire:submit.prevent="updateChannelBanner">
                            <div class="space-y-4">
                                <div>
                                    <input 
                                        type="file" 
                                        id="newChannelBanner" 
                                        wire:model="newChannelBanner" 
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                                    >
                                    @error('newChannelBanner') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-sm text-gray-500">JPG, PNG or GIF. Maximum 5MB. Recommended size: 2560x427 pixels.</p>
                                </div>

                                <div class="flex space-x-3">
                                    <button 
                                        type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition-colors"
                                        wire:loading.attr="disabled"
                                        @if(!$newChannelBanner) disabled @endif
                                    >
                                        <span wire:loading.remove wire:target="updateChannelBanner">Upload Banner</span>
                                        <span wire:loading wire:target="updateChannelBanner">Uploading...</span>
                                    </button>

                                    @if($channel_banner)
                                        <button 
                                            type="button"
                                            wire:click="removeChannelBanner"
                                            wire:confirm="Are you sure you want to remove your channel banner?"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors"
                                        >
                                            Remove Banner
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'security')
            <!-- Security Settings -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Change Password</h2>
                
                <form wire:submit.prevent="updatePassword" class="space-y-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input 
                            type="password" 
                            id="current_password" 
                            wire:model="current_password" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Enter your current password"
                        >
                        @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input 
                            type="password" 
                            id="new_password" 
                            wire:model="new_password" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Enter your new password"
                        >
                        @error('new_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input 
                            type="password" 
                            id="new_password_confirmation" 
                            wire:model="new_password_confirmation" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                            placeholder="Confirm your new password"
                        >
                        @error('new_password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Update Password</span>
                            <span wire:loading>Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
