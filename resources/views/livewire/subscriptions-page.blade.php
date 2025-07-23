<div>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold text-gray-900">Subscriptions</h1>
            
            <!-- Tab Navigation -->
            <div class="mt-6 border-b border-gray-200">
                <nav class="flex space-x-8">
                    <button 
                        wire:click="setActiveTab('videos')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'videos' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Latest Videos
                    </button>
                    <button 
                        wire:click="setActiveTab('images')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'images' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Latest Images
                    </button>
                    <button 
                        wire:click="setActiveTab('all')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'all' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        All Media
                    </button>
                    <button 
                        wire:click="setActiveTab('channels')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'channels' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Manage Subscriptions
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="max-w-7xl mx-auto px-4">
        @if($activeTab === 'videos')
            <!-- Latest Videos from Subscriptions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($subscriptionVideos as $video)
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
                                        @if($video->user && $video->user->profile_picture)
                                            <img src="{{ asset('sf/' . $video->user->profile_picture) }}" 
                                                 alt="{{ $video->user->getChannelName() }}" 
                                                 class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                {{ substr($video->user ? $video->user->getChannelName() : 'Unknown', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5">
                                            {{ $video->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $video->user ? $video->user->getChannelName() : 'Unknown Channel' }}
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
                            <h3 class="text-lg font-medium text-gray-900">No videos from subscriptions</h3>
                            <p class="text-gray-500">Subscribe to channels to see their latest videos here!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($subscriptionVideos->hasPages())
                <div class="mt-8">
                    {{ $subscriptionVideos->links() }}
                </div>
            @endif

        @elseif($activeTab === 'channels')
            <!-- Manage Subscriptions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($subscriptions as $subscription)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center space-x-4">
                            <!-- Channel Avatar -->
                            <div class="flex-shrink-0">
                                <a href="{{ route('channel.show', $subscription->channel) }}">
                                    @if($subscription->channel->profile_picture)
                                        <img src="{{ asset('sf/' . $subscription->channel->profile_picture) }}" 
                                             alt="{{ $subscription->channel->getChannelName() }}" 
                                             class="w-16 h-16 rounded-full object-cover hover:opacity-80 transition-opacity">
                                    @else
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold hover:opacity-80 transition-opacity">
                                            {{ substr($subscription->channel->getChannelName(), 0, 1) }}
                                        </div>
                                    @endif
                                </a>
                            </div>

                            <!-- Channel Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    <a href="{{ route('channel.show', $subscription->channel) }}" class="hover:text-red-600">
                                        {{ $subscription->channel->getChannelName() }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    {{ number_format($subscription->channel->subscribers()->count()) }} subscribers
                                </p>
                                <p class="text-xs text-gray-500">
                                    Subscribed {{ $subscription->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <!-- Channel Description -->
                        @if($subscription->channel->channel_description)
                            <p class="mt-4 text-sm text-gray-700 line-clamp-3">
                                {{ $subscription->channel->channel_description }}
                            </p>
                        @endif

                        <!-- Channel Stats -->
                        <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                            <span>{{ $subscription->channel->videos()->where('visibility', App\Models\Video::VISIBILITY_PUBLIC)->count() }} videos</span>
                            <span>{{ number_format($subscription->channel->videos()->sum('views')) }} views</span>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex space-x-3">
                            <a href="{{ route('channel.show', $subscription->channel) }}" 
                               class="flex-1 bg-gray-100 text-gray-700 text-center py-2 px-4 rounded-md hover:bg-gray-200 transition-colors">
                                View Channel
                            </a>
                            <button 
                                wire:click="unsubscribe({{ $subscription->channel->id }})"
                                wire:confirm="Are you sure you want to unsubscribe from {{ $subscription->channel->getChannelName() }}?"
                                class="px-4 py-2 text-red-600 border border-red-600 rounded-md hover:bg-red-50 transition-colors">
                                Unsubscribe
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900">No subscriptions</h3>
                            <p class="text-gray-500 mb-4">You haven't subscribed to any channels yet.</p>
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                Discover Channels
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($subscriptions->hasPages())
                <div class="mt-8">
                    {{ $subscriptions->links() }}
                </div>
            @endif

        @elseif($activeTab === 'images')
            <!-- Latest Images from Subscriptions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($subscriptionImages as $image)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                        <a href="{{ route('images.show', $image) }}" class="block">
                            <!-- Thumbnail -->
                            <div class="relative aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                                <img src="{{ $image->getThumbnailUrl() }}" 
                                     alt="{{ $image->title }}" 
                                     class="w-full h-full object-cover">
                                
                                <!-- Image Icon -->
                                <div class="absolute top-2 left-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Image Info -->
                            <div class="p-3">
                                <!-- Channel Avatar & Title -->
                                <div class="flex space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($image->user && $image->user->profile_picture)
                                            <img src="{{ asset('sf/' . $image->user->profile_picture) }}" 
                                                 alt="{{ $image->user->getChannelName() }}" 
                                                 class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                {{ substr($image->user ? $image->user->getChannelName() : 'Unknown', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5">
                                            {{ $image->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $image->user ? $image->user->getChannelName() : 'Unknown Channel' }}
                                        </p>
                                        <div class="flex items-center text-xs text-gray-500 mt-1 space-x-1">
                                            <span>{{ $image->getFormattedViews() }}</span>
                                            <span>•</span>
                                            <span>{{ $image->getTimeAgo() }}</span>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900">No images from subscriptions</h3>
                            <p class="text-gray-500">Subscribe to channels to see their latest images here!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($subscriptionImages->hasPages())
                <div class="mt-8">
                    {{ $subscriptionImages->links() }}
                </div>
            @endif

        @elseif($activeTab === 'all')
            <!-- All Media from Subscriptions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($subscriptionMedia as $item)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                        <a href="{{ $item->media_type === 'video' ? route('videos.show', $item) : route('images.show', $item) }}" class="block">
                            <!-- Thumbnail -->
                            <div class="relative {{ $item->media_type === 'video' ? 'aspect-video' : 'aspect-square' }} bg-gray-100 rounded-t-lg overflow-hidden">
                                <img src="{{ $item->getThumbnailUrl() }}" 
                                     alt="{{ $item->title }}" 
                                     class="w-full h-full object-cover">
                                
                                @if($item->media_type === 'video')
                                    <!-- Duration Badge for Videos -->
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
                                @else
                                    <!-- Image Icon -->
                                    <div class="absolute top-2 left-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Media Info -->
                            <div class="p-3">
                                <!-- Channel Avatar & Title -->
                                <div class="flex space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($item->user && $item->user->profile_picture)
                                            <img src="{{ asset('sf/' . $item->user->profile_picture) }}" 
                                                 alt="{{ $item->user->getChannelName() }}" 
                                                 class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                                {{ substr($item->user ? $item->user->getChannelName() : 'Unknown', 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5">
                                            {{ $item->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $item->user ? $item->user->getChannelName() : 'Unknown Channel' }}
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
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900">No media from subscriptions</h3>
                            <p class="text-gray-500">Subscribe to channels to see their latest content here!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($subscriptionMedia->hasPages())
                <div class="mt-8">
                    {{ $subscriptionMedia->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
