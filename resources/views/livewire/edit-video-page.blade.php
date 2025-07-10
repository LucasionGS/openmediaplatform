<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Video</h1>
                <p class="text-gray-600 mt-1">Update your video information and settings</p>
            </div>
            <a href="{{ route('videos.show', $video) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Video
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if($success)
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ $success }}
        </div>
    @endif

    @if($error)
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
            {{ $error }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Video Preview -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Video Preview</h3>
                
                <!-- Current Thumbnail -->
                <div class="mb-4">
                    <img src="{{ $video->getThumbnailUrl() }}" 
                         alt="{{ $video->title }}" 
                         class="w-full rounded-lg shadow-sm">
                </div>

                <!-- Video Info -->
                <div class="space-y-2 text-sm text-gray-600">
                    <div><strong>Video ID:</strong> {{ $video->vid }}</div>
                    <div><strong>Duration:</strong> {{ gmdate('H:i:s', $video->duration ?? 0) }}</div>
                    <div><strong>Views:</strong> {{ number_format($video->views) }}</div>
                    <div><strong>Uploaded:</strong> {{ $video->created_at->format('M j, Y') }}</div>
                </div>
            </div>

            <!-- Thumbnail Upload -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Custom Thumbnail</h3>
                
                <form wire:submit.prevent="uploadThumbnail">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload New Thumbnail
                        </label>
                        <input type="file" 
                               wire:model="thumbnail" 
                               accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">JPEG, PNG up to 2MB</p>
                        @error('thumbnail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if($thumbnail)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Preview:</p>
                            <img src="{{ $thumbnail->temporaryUrl() }}" 
                                 alt="Thumbnail preview" 
                                 class="w-full rounded-lg shadow-sm">
                        </div>
                    @endif

                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="uploadThumbnail">Upload Thumbnail</span>
                        <span wire:loading wire:target="uploadThumbnail">Uploading...</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Video Details Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Video Details</h3>
                
                <form wire:submit.prevent="updateVideo" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Title *
                        </label>
                        <input type="text" 
                               id="title" 
                               wire:model="title" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Enter video title">
                        @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" 
                                  wire:model="description" 
                                  rows="4"
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Tell viewers about your video"></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category and Tags -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category
                            </label>
                            <select id="category" 
                                    wire:model="category"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Category</option>
                                @foreach(\App\Models\Video::getAvailableCategories() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                                Tags
                            </label>
                            <input type="text" 
                                   id="tags" 
                                   wire:model="tags" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="tag1, tag2, tag3">
                            <p class="mt-1 text-xs text-gray-500">Separate tags with commas</p>
                            @error('tags') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Visibility -->
                    <div>
                        <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">
                            Visibility *
                        </label>
                        <select id="visibility" 
                                wire:model="visibility"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="{{ \App\Models\Video::VISIBILITY_PUBLIC }}">Public - Anyone can search for and view</option>
                            <option value="{{ \App\Models\Video::VISIBILITY_UNLISTED }}">Unlisted - Anyone with the link can view</option>
                            <option value="{{ \App\Models\Video::VISIBILITY_PRIVATE }}">Private - Only you can view</option>
                            <option value="{{ \App\Models\Video::VISIBILITY_UNPUBLISHED }}">Unpublished - Not visible to anyone</option>
                        </select>
                        @error('visibility') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <button type="button" 
                                wire:click="confirmDelete"
                                class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Video
                        </button>

                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
                                wire:loading.attr="disabled"
                                wire:target="updateVideo">
                            <span wire:loading.remove wire:target="updateVideo">Save Changes</span>
                            <span wire:loading wire:target="updateVideo">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete Video</h3>
                    </div>
                    
                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete "{{ $video->title }}"? This action cannot be undone.
                        The video file and all associated data will be permanently removed.
                    </p>
                    
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" 
                                wire:click="cancelDelete"
                                class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="button" 
                                wire:click="deleteVideo"
                                class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteVideo">Delete Video</span>
                            <span wire:loading wire:target="deleteVideo">Deleting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
