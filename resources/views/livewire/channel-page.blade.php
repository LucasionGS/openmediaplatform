<div>
    <!-- Channel Header -->
    <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
        <!-- Channel Banner -->
        @if($user->channel_banner)
            <div class="w-full h-32 md:h-48 lg:h-64 bg-gray-100 overflow-hidden">
                <img src="{{ asset('sf/' . $user->channel_banner) }}" 
                     alt="{{ $user->getChannelName() }} Banner" 
                     class="w-full h-full object-cover">
            </div>
        @endif
        
        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- Channel Info -->
            <div class="flex items-start space-x-6">
                <!-- Channel Avatar -->
                <div class="flex-shrink-0">
                    @if($user->profile_picture)
                        <img src="{{ asset('sf/' . $user->profile_picture) }}" 
                             alt="{{ $user->getChannelName() }}" 
                             class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg">
                    @else
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold border-4 border-white shadow-lg">
                            {{ substr($user->getChannelName(), 0, 1) }}
                        </div>
                    @endif
                </div>

                <!-- Channel Details -->
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $user->getChannelName() }}</h1>
                    <div class="flex items-center space-x-4 mt-2 text-gray-600">
                        <span>{{ number_format($subscriberCount) }} {{ $subscriberCount === 1 ? 'subscriber' : 'subscribers' }}</span>
                        <span>•</span>
                        <span>{{ $user->videos()->where('visibility', App\Models\Video::VISIBILITY_PUBLIC)->count() }} videos</span>
                    </div>
                    @if($user->channel_description)
                        <p class="mt-3 text-gray-700 leading-relaxed">{{ $user->channel_description }}</p>
                    @endif
                </div>

                <!-- Subscribe Button -->
                <div class="flex-shrink-0">
                    @auth
                        @if(auth()->id() !== $user->id)
                            <button 
                                wire:click="toggleSubscription"
                                class="px-6 py-2 rounded-full font-medium transition-colors
                                       {{ $isSubscribed 
                                          ? 'bg-gray-200 text-gray-800 hover:bg-gray-300' 
                                          : 'bg-red-600 text-white hover:bg-red-700' }}">
                                {{ $isSubscribed ? 'Subscribed' : 'Subscribe' }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-6 py-2 bg-red-600 text-white rounded-full font-medium hover:bg-red-700 transition-colors">
                            Subscribe
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Channel Navigation -->
            <div class="mt-6 border-b border-gray-200">
                <nav class="flex space-x-8">
                    <button 
                        wire:click="setActiveTab('videos')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'videos' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Videos
                    </button>
                    <button 
                        wire:click="setActiveTab('playlists')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'playlists' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Playlists
                    </button>
                    <button 
                        wire:click="setActiveTab('about')"
                        class="py-2 border-b-2 font-medium text-sm transition-colors
                               {{ $activeTab === 'about' 
                                  ? 'border-red-600 text-red-600' 
                                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        About
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="max-w-7xl mx-auto px-4">
        @if($activeTab === 'videos')
            <!-- Videos Tab -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($videos as $video)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer relative group">
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
                                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5 mb-1">
                                    {{ $video->title }}
                                </h3>
                                <div class="flex items-center text-xs text-gray-500 space-x-1">
                                    <span>{{ $video->getFormattedViews() }}</span>
                                    <span>•</span>
                                    <span>{{ $video->getTimeAgo() }}</span>
                                </div>
                            </div>
                        </a>

                        <!-- Edit Button (Only for Channel Owner) -->
                        @auth
                            @if(auth()->id() === $user->id)
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('videos.edit', $video) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 bg-black bg-opacity-75 text-white rounded-full hover:bg-opacity-100 transition-all"
                                       title="Edit video"
                                       onclick="event.stopPropagation();">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        @endauth
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900">No videos</h3>
                            <p class="text-gray-500">This channel hasn't uploaded any videos yet.</p>
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

        @elseif($activeTab === 'playlists')
            <!-- Playlists Tab -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($playlists as $playlist)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                        <a href="{{ route('playlists.show', $playlist) }}" class="block">
                            <!-- Playlist Thumbnail -->
                            <div class="relative aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                                @if($playlist->videos->first())
                                    <img src="{{ $playlist->videos->first()->getThumbnailUrl() }}" 
                                         alt="{{ $playlist->title }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Video Count Badge -->
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                    {{ $playlist->videos_count }} videos
                                </div>
                            </div>

                            <!-- Playlist Info -->
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5 mb-1">
                                    {{ $playlist->title }}
                                </h3>
                                @if($playlist->description)
                                    <p class="text-xs text-gray-500 line-clamp-2">{{ $playlist->description }}</p>
                                @endif
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900">No playlists</h3>
                            <p class="text-gray-500">This channel hasn't created any public playlists yet.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($playlists->hasPages())
                <div class="mt-8">
                    {{ $playlists->links() }}
                </div>
            @endif

        @elseif($activeTab === 'about')
            <!-- About Tab -->
            <div class="max-w-4xl">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">About {{ $user->getChannelName() }}</h2>
                    
                    <div class="space-y-4">
                        @if($user->channel_description)
                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Description</h3>
                                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $user->channel_description }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-medium text-gray-900 mb-2">Channel Stats</h3>
                                <dl class="space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Joined</dt>
                                        <dd class="text-gray-900">{{ $user->created_at->format('M j, Y') }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Total views</dt>
                                        <dd class="text-gray-900">{{ number_format($user->videos()->sum('views')) }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Subscribers</dt>
                                        <dd class="text-gray-900">{{ number_format($subscriberCount) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            @if($user->channel_links)
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-2">Links</h3>
                                    <div class="space-y-2">
                                        @foreach($user->channel_links as $link)
                                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" 
                                               class="flex items-center text-blue-600 hover:text-blue-800">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                {{ $link['title'] ?? $link['url'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
