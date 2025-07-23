<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('images.show', $image) }}" 
               class="text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Image</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Image Files Management -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Image Files</h2>
                <div class="flex items-center space-x-4">
                    @if($image->imageFiles->count() > 1)
                        <p class="text-sm text-gray-500">Drag to reorder or use arrow buttons</p>
                        <!-- Debug button to test reordering -->
                        <button type="button" 
                                x-on:click="reorderFiles()"
                                class="text-xs px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">
                            Test Reorder
                        </button>
                        <!-- Simple reverse order button for testing -->
                        <button type="button" 
                                wire:click="testReverse"
                                class="text-xs px-2 py-1 bg-blue-200 rounded hover:bg-blue-300">
                            Reverse Order
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('message') }}
                </div>
            @endif
            
            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="space-y-3" x-data="{ 
                draggedElement: null,
                reorderFiles() {
                    // Find the container that actually contains the file elements
                    let container = this.$el;
                    let items = Array.from(container.querySelectorAll('[data-file-id]'));
                    
                    // If we don't find any, try looking in the parent
                    if (items.length === 0) {
                        container = this.$el.parentElement;
                        items = Array.from(container.querySelectorAll('[data-file-id]'));
                    }
                    
                    // If still no luck, try the entire document
                    if (items.length === 0) {
                        items = Array.from(document.querySelectorAll('[data-file-id]'));
                        console.log('Had to search entire document for file elements');
                    }
                    
                    const fileIds = items.map(item => {
                        const id = item.getAttribute('data-file-id');
                        return parseInt(id);
                    }).filter(id => !isNaN(id));
                    
                    console.log('Search strategy used:');
                    console.log('Container:', container);
                    console.log('Found items:', items);
                    console.log('Collected file IDs:', fileIds);
                    console.log('Total items found:', items.length);
                    
                    if (fileIds.length > 0) {
                        console.log('Calling reorderFiles with:', fileIds);
                        $wire.call('reorderFiles', fileIds);
                    } else {
                        console.error('No valid file IDs found');
                        console.log('this.$el:', this.$el);
                        console.log('this.$el.children:', Array.from(this.$el.children));
                        console.log('All elements with data-file-id in document:', document.querySelectorAll('[data-file-id]'));
                    }
                }
            }" id="file-container">
                @forelse($image->imageFiles as $file)
                    <div class="bg-white rounded-lg shadow-sm border p-4 cursor-move hover:shadow-md transition-shadow" 
                         data-file-id="{{ $file->id }}"
                         draggable="true"
                         @dragstart="draggedElement = $el; $el.style.opacity = '0.5'"
                         @dragend="$el.style.opacity = '1'; draggedElement = null"
                         @dragover.prevent
                         @dragenter.prevent
                         @drop.prevent="
                            if (draggedElement && draggedElement !== $el) {
                                const container = $el.parentElement;
                                const afterElement = $el.nextElementSibling;
                                if (afterElement) {
                                    container.insertBefore(draggedElement, afterElement);
                                } else {
                                    container.appendChild(draggedElement);
                                }
                                $nextTick(() => reorderFiles());
                            }
                         ">
                        <div class="flex items-start space-x-4">
                            <!-- Drag Handle -->
                            <div class="flex-shrink-0 mt-2">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                                </svg>
                            </div>
                            
                            <!-- Image Preview -->
                            <div class="flex-shrink-0">
                                <img src="{{ $file->getUrl() }}" 
                                     alt="Image {{ $file->order }}" 
                                     class="w-24 h-24 object-cover rounded-lg">
                            </div>
                            
                            <!-- File Info -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900">{{ $file->filename }}</h4>
                                <div class="mt-1 text-sm text-gray-500 space-y-1">
                                    <div>{{ $file->getFormattedSize() }}</div>
                                    <div>{{ $file->width }}x{{ $file->height }}px</div>
                                    <div>{{ $file->mime_type }}</div>
                                    <div>Order: {{ $file->order }}</div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex-shrink-0 flex items-center space-x-2">
                                <!-- Move Up/Down Buttons -->
                                @if($image->imageFiles->count() > 1)
                                    <div class="flex flex-col space-y-1">
                                        @if(!$loop->first)
                                            <button wire:click="moveFileUp({{ $file->id }})"
                                                    class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                                                    title="Move up">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        @if(!$loop->last)
                                            <button wire:click="moveFileDown({{ $file->id }})"
                                                    class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                                                    title="Move down">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Delete Button -->
                                @if($image->imageFiles->count() > 1)
                                    <button wire:click="deleteImageFile({{ $file->id }})"
                                            wire:confirm="Are you sure you want to delete this image file?"
                                            class="text-red-600 hover:text-red-800 transition-colors p-1"
                                            title="Delete this image">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-gray-400 text-sm">Last file</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>No image files found.</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Main Image Preview -->
            <div class="mt-6">
                <h3 class="text-md font-medium text-gray-900 mb-2">Main Preview</h3>
                <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <div class="aspect-square bg-gray-100">
                        <img src="{{ $image->getThumbnailUrl() }}" 
                             alt="{{ $image->title }}" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $image->title }}</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div>{{ $image->getFormattedViews() }} • {{ $image->getTimeAgo() }}</div>
                            <div>{{ $image->user->getChannelName() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="space-y-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title *
                    </label>
                    <input type="text" 
                           wire:model="title" 
                           id="title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Enter image title"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea wire:model="description" 
                              id="description"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Enter image description"></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Visibility -->
                <div>
                    <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">
                        Visibility *
                    </label>
                    <select wire:model="visibility" 
                            id="visibility"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="public">Public - Anyone can search for and view</option>
                        <option value="unlisted">Unlisted - Anyone with the link can view</option>
                        <option value="private">Private - Only you can view</option>
                    </select>
                    @error('visibility')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <button type="button" 
                            wire:click="delete"
                            wire:confirm="Are you sure you want to delete this entire image post? This will delete all files and cannot be undone."
                            class="px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 transition-colors">
                        Delete Entire Post
                    </button>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('images.show', $image) }}" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
