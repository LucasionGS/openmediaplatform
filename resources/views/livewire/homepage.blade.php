<div>
    <!-- Category Filter Bar -->
    <div class="mb-6 border-b border-gray-200">
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

    <!-- Video Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($videos as $video)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                <a href="{{ route('videos.show', $video) }}" class="block">
                    <!-- Thumbnail -->
                    <div class="relative aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                        <img src="{{ $video->getThumbnailUrl() }}" 
                             alt="{{ $video->title }}" 
                             class="w-full h-full object-cover">
                        
                        <!-- Duration Badge -->
                        @if($video->duration)
                            <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                {{ $video->getFormattedDuration() }}
                            </div>
                        @endif
                    </div>

                    <!-- Video Info -->
                    <div class="p-3">
                        <!-- Channel Avatar & Title -->
                        <div class="flex space-x-3">
                            <div class="flex-shrink-0">
                                <a href="{{ $video->user ? route('channel.show', $video->user) : '#' }}">
                                    @if($video->user && $video->user->profile_picture)
                                        <img src="{{ asset('sf/' . $video->user->profile_picture) }}" 
                                             alt="{{ $video->user->getChannelName() }}" 
                                             class="w-8 h-8 rounded-full object-cover hover:opacity-80 transition-opacity">
                                    @else
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold hover:bg-blue-600 transition-colors">
                                            {{ substr($video->user ? $video->user->getChannelName() : 'Unknown', 0, 1) }}
                                        </div>
                                    @endif
                                </a>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5">
                                    {{ $video->title }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    @if($video->user)
                                        <a href="{{ route('channel.show', $video->user) }}" class="hover:text-gray-800">
                                            {{ $video->user->getChannelName() }}
                                        </a>
                                    @else
                                        Unknown Channel
                                    @endif
                                </p>
                                <div class="flex items-center text-xs text-gray-500 mt-1 space-x-1">
                                    <span>{{ $video->getFormattedViews() }}</span>
                                    <span>•</span>
                                    <span>{{ $video->getTimeAgo() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">No videos found</h3>
                    <p class="text-gray-500">Be the first to upload a video!</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($videos->hasPages())
        <div class="mt-8">
            {{ $videos->links() }}
        </div>
    @endif
</div>
