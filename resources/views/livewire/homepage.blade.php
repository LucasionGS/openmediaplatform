<div>
    <!-- Filter Bar -->
    <div class="mb-6 border-b border-gray-200">
        <!-- Media Type Filter -->
        <div class="flex space-x-1 overflow-x-auto pb-2 mb-4">
            @foreach($types as $key => $label)
                <button wire:click="$set('selectedType', '{{ $key }}')" 
                        class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                               {{ $selectedType === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        
        <!-- Category Filter -->
        <div class="flex space-x-1 overflow-x-auto pb-2">
            @foreach($categories as $key => $label)
                <button wire:click="$set('selectedCategory', '{{ $key }}')" 
                        class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors
                               {{ $selectedCategory === $key ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Search Section -->
    @if($searchQuery)
        <div class="mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-blue-900">Search Results</h3>
                        <p class="text-blue-700">Showing results for: <strong>"{{ $searchQuery }}"</strong></p>
                    </div>
                    <button wire:click="$set('searchQuery', '')" 
                            class="text-blue-600 hover:text-blue-800 font-medium">
                        Clear Search
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Content Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($content as $item)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                @if($item->content_type === 'video')
                    <a href="{{ route('videos.show', $item) }}" class="block">
                        <!-- Video Thumbnail -->
                        <div class="relative aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                            <img src="{{ $item->getThumbnailUrl() }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-full object-cover">
                            
                            <!-- Duration Badge -->
                            @if($item->duration)
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                    {{ $item->getFormattedDuration() }}
                                </div>
                            @endif
                            
                            <!-- Video Icon -->
                            <div class="absolute top-2 left-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Video Info -->
                        <div class="p-3">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <a href="{{ $item->user ? route('channel.show', $item->user) : '#' }}">
                                        @if($item->user && $item->user->profile_picture)
                                            <img src="{{ asset('sf/' . $item->user->profile_picture) }}" 
                                                 alt="{{ $item->user->getChannelName() }}" 
                                                 class="w-8 h-8 rounded-full object-cover hover:opacity-80 transition-opacity">
                                        @else
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold hover:bg-blue-600 transition-colors">
                                                {{ substr($item->user ? $item->user->getChannelName() : 'Unknown', 0, 1) }}
                                            </div>
                                        @endif
                                    </a>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if($item->user)
                                            <a href="{{ route('channel.show', $item->user) }}" class="hover:text-gray-800">
                                                {{ $item->user->getChannelName() }}
                                            </a>
                                        @else
                                            Unknown Channel
                                        @endif
                                    </p>
                                    <div class="flex items-center text-xs text-gray-500 mt-1 space-x-1">
                                        <span>{{ $item->getFormattedViews() }}</span>
                                        <span>•</span>
                                        <span>{{ $item->getTimeAgo() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('images.show', $item) }}" class="block">
                        <!-- Image Thumbnail -->
                        <div class="relative aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                            @if($item->imageFiles->first())
                                <img src="{{ $item->imageFiles->first()->getUrl() }}" 
                                     alt="{{ $item->title }}" 
                                     class="w-full h-full object-cover">
                            @endif
                            
                            <!-- Multiple Images Badge -->
                            @if($item->imageFiles->count() > 1)
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                    {{ $item->imageFiles->count() }} images
                                </div>
                            @endif
                            
                            <!-- Image Icon -->
                            <div class="absolute top-2 left-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Image Info -->
                        <div class="p-3">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <a href="{{ $item->user ? route('channel.show', $item->user) : '#' }}">
                                        @if($item->user && $item->user->profile_picture)
                                            <img src="{{ asset('sf/' . $item->user->profile_picture) }}" 
                                                 alt="{{ $item->user->getChannelName() }}" 
                                                 class="w-8 h-8 rounded-full object-cover hover:opacity-80 transition-opacity">
                                        @else
                                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-semibold hover:bg-green-600 transition-colors">
                                                {{ substr($item->user ? $item->user->getChannelName() : 'Unknown', 0, 1) }}
                                            </div>
                                        @endif
                                    </a>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if($item->user)
                                            <a href="{{ route('channel.show', $item->user) }}" class="hover:text-gray-800">
                                                {{ $item->user->getChannelName() }}
                                            </a>
                                        @else
                                            Unknown Channel
                                        @endif
                                    </p>
                                    <div class="flex items-center text-xs text-gray-500 mt-1 space-x-1">
                                        <span>{{ $item->getFormattedViews() }}</span>
                                        <span>•</span>
                                        <span>{{ $item->getTimeAgo() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500">
                    @if($selectedType === 'videos')
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">No videos found</h3>
                    @elseif($selectedType === 'images')
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">No images found</h3>
                    @else
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">No media found</h3>
                    @endif
                    
                    @if($searchQuery)
                        <p class="text-gray-500">No results found for "{{ $searchQuery }}". Try a different search term.</p>
                        <button wire:click="$set('searchQuery', '')" 
                                class="mt-2 text-blue-600 hover:text-blue-800 font-medium">
                            Clear search and view all content
                        </button>
                    @else
                        <p class="text-gray-500">Be the first to upload content!</p>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($content->hasPages())
        <div class="mt-8">
            {{ $content->links() }}
        </div>
    @endif
</div>
