<div>
    <!-- Category Filter Bar -->
    @if(isset($categories))
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
    @endif

    <!-- Video Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach($videos as $video)
            <div class="group cursor-pointer">
                <a href="{{ route('videos.show', $video) }}" wire:navigate class="block">
                    <!-- Video Thumbnail -->
                    <div class="relative aspect-video bg-gray-200 rounded-lg overflow-hidden mb-3">
                        <img src="{{ $video->getThumbnailUrl() }}" 
                             alt="{{ $video->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                        
                        <!-- Duration Badge -->
                        @if($video->duration)
                            <div class="absolute bottom-2 right-2 bg-black bg-opacity-80 text-white text-xs px-2 py-1 rounded">
                                {{ $video->getFormattedDuration() }}
                            </div>
                        @endif
                    </div>

                    <!-- Video Info -->
                    <div class="flex space-x-3">
                        <!-- Channel Avatar -->
                        <div class="flex-shrink-0">
                            @if($video->user && $video->user->profile_picture)
                                <img src="{{ asset('sf/' . $video->user->profile_picture) }}" 
                                     alt="{{ $video->user->name }}" 
                                     class="w-9 h-9 rounded-full object-cover">
                            @else
                                <div class="w-9 h-9 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr($video->user ? $video->user->name : 'U', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Video Details -->
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-sm line-clamp-2 text-gray-900 group-hover:text-blue-600">
                                {{ $video->title }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $video->user ? $video->user->name : 'Unknown' }}
                            </p>
                            <div class="flex items-center space-x-1 text-xs text-gray-500 mt-1">
                                <span>{{ number_format($video->views) }} views</span>
                                <span>•</span>
                                <span>{{ $video->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($videos->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No videos found</h3>
            <p class="text-gray-500">
                @if($searchQuery)
                    No videos found for "{{ $searchQuery }}". Try a different search term.
                @else
                    Upload your first video to get started!
                @endif
            </p>
        </div>
    @endif

    <!-- Pagination -->
    @if($videos->hasPages())
        <div class="mt-8">
            {{ $videos->links() }}
        </div>
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
