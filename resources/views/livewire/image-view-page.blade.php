<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Image Display -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Image Gallery -->
                <div class="mb-6">
                    @if($image->imageFiles->count() > 0)
                        <!-- Main Image -->
                        <div class="relative mb-4">
                            <img src="{{ $image->imageFiles[$currentImageIndex]->getUrl() }}" 
                                 alt="{{ $image->title }}"
                                 class="w-full h-auto max-h-96 object-contain bg-gray-100 rounded-lg">
                            
                            @if($image->imageFiles->count() > 1)
                                <!-- Navigation Buttons -->
                                @if($currentImageIndex > 0)
                                    <button wire:click="previousImage" 
                                            class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                @endif
                                
                                @if($currentImageIndex < $image->imageFiles->count() - 1)
                                    <button wire:click="nextImage" 
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                @endif
                                
                                <!-- Image Counter -->
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white px-3 py-1 rounded text-sm">
                                    {{ $currentImageIndex + 1 }} / {{ $image->imageFiles->count() }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Thumbnail Strip -->
                        @if($image->imageFiles->count() > 1)
                            <div class="flex space-x-2 overflow-x-auto pb-2">
                                @foreach($image->imageFiles as $index => $imageFile)
                                    <button wire:click="setCurrentImage({{ $index }})" 
                                            class="flex-shrink-0 w-16 h-16 rounded border-2 overflow-hidden {{ $index === $currentImageIndex ? 'border-blue-500' : 'border-gray-300' }}">
                                        <img src="{{ $imageFile->getUrl() }}" 
                                             alt="Thumbnail {{ $index + 1 }}"
                                             class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Image Info -->
                <div class="border-t pt-4">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $image->title }}</h1>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span>{{ $image->views }} {{ Str::plural('view', $image->views) }}</span>
                            <span>{{ $image->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <!-- Like/Dislike Buttons -->
                        @auth
                            <div class="flex items-center space-x-2">
                                <button wire:click="toggleLike" 
                                        class="flex items-center space-x-1 px-3 py-2 rounded-full hover:bg-gray-100 {{ $userEngagement && $userEngagement->type === 'like' ? 'bg-blue-50 text-blue-600' : 'text-gray-600' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                    </svg>
                                    <span>{{ $image->likes }}</span>
                                </button>
                                
                                <button wire:click="toggleDislike" 
                                        class="flex items-center space-x-1 px-3 py-2 rounded-full hover:bg-gray-100 {{ $userEngagement && $userEngagement->type === 'dislike' ? 'bg-red-50 text-red-600' : 'text-gray-600' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v5a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13V4m-7 10h2M17 4H19a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                                    </svg>
                                    <span>{{ $image->dislikes }}</span>
                                </button>
                            </div>
                        @endauth
                    </div>

                    <!-- Description -->
                    @if($image->description)
                        <div class="mb-4">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $image->description }}</p>
                        </div>
                    @endif

                    <!-- Tags -->
                    @if($image->tags && count($image->tags) > 0)
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($image->tags as $tag)
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Channel Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <img src="{{ asset('https://ui-avatars.com/api/?name=' . urlencode($image->user->name) . '&background=random') }}" 
                         alt="{{ $image->user->name }}" 
                         class="w-12 h-12 rounded-full">
                    
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $image->user->getChannelName() }}</h3>
                        <p class="text-sm text-gray-600">{{ $image->user->getSubscriberCount() }} subscribers</p>
                    </div>
                </div>

                @auth
                    @if($image->user_id !== auth()->id())
                        <button wire:click="{{ $isSubscribed ? 'unsubscribe' : 'subscribe' }}" 
                                class="w-full px-4 py-2 rounded-lg font-medium transition-colors {{ $isSubscribed ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                            {{ $isSubscribed ? 'Subscribed' : 'Subscribe' }}
                        </button>
                    @else
                        <div class="space-y-2">
                            <a href="{{ route('images.edit', $image) }}" 
                               class="block w-full px-4 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700 transition-colors">
                                Edit Image
                            </a>
                            <button wire:click="generateShareLink" 
                                    class="w-full px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                Share
                            </button>
                        </div>
                    @endif
                    
                    <!-- Edit button for moderators/admins (if not owner) -->
                    @if($image->user_id !== auth()->id() && auth()->user()->canModerateContent())
                        <div class="mt-2">
                            <a href="{{ route('images.edit', $image) }}" 
                               class="block w-full px-4 py-2 bg-orange-600 text-white text-center rounded-lg hover:bg-orange-700 transition-colors">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Moderate</span>
                                </div>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Image Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Details</h3>
                
                <div class="space-y-3 text-sm">
                    @if($image->category)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="text-gray-900">{{ ucfirst($image->category) }}</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Images:</span>
                        <span class="text-gray-900">{{ $image->imageFiles->count() }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Uploaded:</span>
                        <span class="text-gray-900">{{ $image->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    @if($showShareModal && $image->share_token)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold mb-4">Share Image Post</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Share Link</label>
                        <div class="flex">
                            <input type="text" 
                                   value="{{ $image->getShareUrl() }}" 
                                   readonly
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50">
                            <button onclick="navigator.clipboard.writeText('{{ $image->getShareUrl() }}')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="revokeShareLink" 
                            class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">
                        Revoke Link
                    </button>
                    <button wire:click="$set('showShareModal', false)" 
                            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
