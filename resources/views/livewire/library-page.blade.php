<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Library</h1>
        <p class="text-gray-600 mt-1">Your personal collection of videos, playlists, and watch history</p>
    </div>

    <!-- Success Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="flex space-x-8">
            <button 
                wire:click="setActiveTab('watch-history')"
                class="py-2 border-b-2 font-medium text-sm transition-colors
                       {{ $activeTab === 'watch-history' 
                          ? 'border-red-600 text-red-600' 
                          : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13,3A9,9 0 0,0 4,12H1L4.89,15.89L4.96,16.03L9,12H6A7,7 0 0,1 13,5A7,7 0 0,1 20,12A7,7 0 0,1 13,19C11.07,19 9.32,18.21 8.06,16.94L6.64,18.36C8.27,20 10.5,21 13,21A9,9 0 0,0 22,12A9,9 0 0,0 13,3Z"/>
                    </svg>
                    <span>Watch History</span>
                </div>
            </button>
            
            <button 
                wire:click="setActiveTab('liked-videos')"
                class="py-2 border-b-2 font-medium text-sm transition-colors
                       {{ $activeTab === 'liked-videos' 
                          ? 'border-red-600 text-red-600' 
                          : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227z"/>
                    </svg>
                    <span>Liked Videos</span>
                </div>
            </button>
            
            <button 
                wire:click="setActiveTab('playlists')"
                class="py-2 border-b-2 font-medium text-sm transition-colors
                       {{ $activeTab === 'playlists' 
                          ? 'border-red-600 text-red-600' 
                          : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15,6H3V8H15V6M15,10H3V12H15V10M3,16H11V14H3V16M17,6V14.18C16.69,14.07 16.35,14 16,14A3,3 0 0,0 13,17A3,3 0 0,0 16,20A3,3 0 0,0 19,17V8H22V6H17Z"/>
                    </svg>
                    <span>Playlists</span>
                </div>
            </button>

            <button 
                wire:click="setActiveTab('my-videos')"
                class="py-2 border-b-2 font-medium text-sm transition-colors
                       {{ $activeTab === 'my-videos' 
                          ? 'border-red-600 text-red-600' 
                          : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17,10.5V7A1,1 0 0,0 16,6H4A1,1 0 0,0 3,7V17A1,1 0 0,0 4,18H16A1,1 0 0,0 17,17V13.5L21,17.5V6.5L17,10.5Z"/>
                    </svg>
                    <span>My Videos</span>
                </div>
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="min-h-screen">
        @if($activeTab === 'watch-history')
            <!-- Watch History Tab -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Watch History</h2>
                @if($this->watchHistory->count() > 0)
                    <button wire:click="clearWatchHistory" 
                            onclick="return confirm('Are you sure you want to clear all watch history? This action cannot be undone.')"
                            class="px-4 py-2 text-sm text-red-600 hover:text-red-800 border border-red-300 rounded-lg hover:bg-red-50 transition-colors">
                        Clear All History
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                  $last = null;
                @endphp
                @forelse($this->watchHistory as $history)
                    @php
                      $history->video = $history->video()->first();
                      if ($last && $last->video && $last->video->vid === $history->video->vid) {
                          continue; // Skip if the video is the same as the last one
                      }
                      $last = $history;
                    @endphp
                    @if($history->video)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow relative group">
                            <a href="{{ route('videos.show', $history->video) }}" class="block">
                                <!-- Thumbnail -->
                                <div class="relative aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                                    <img src="{{ $history->video->getThumbnailUrl() }}" 
                                         alt="{{ $history->video->title }}" 
                                         class="w-full h-full object-cover">
                                    
                                    <!-- Duration Badge -->
                                    @if($history->video->duration)
                                        <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                            {{ $history->video->getFormattedDuration() }}
                                        </div>
                                    @endif

                                    <!-- Watch Progress -->
                                    @if($history->watch_time > 0 && $history->video->duration > 0)
                                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-black bg-opacity-50">
                                            <div class="h-full bg-red-600" 
                                                 style="width: {{ min(100, ($history->watch_time / $history->video->duration) * 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Video Info -->
                                <div class="p-3">
                                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5 mb-1">
                                        {{ $history->video->title }}
                                    </h3>
                                    <p class="text-xs text-gray-600 mb-1">{{ $history->video->user->getChannelName() }}</p>
                                    <div class="flex items-center text-xs text-gray-500 space-x-1">
                                        <span>{{ $history->video->getFormattedViews() }}</span>
                                        <span>•</span>
                                        <span>Watched {{ $history->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>

                            <!-- Remove Button -->
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="removeFromWatchHistory({{ $history->id }})" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-black bg-opacity-75 text-white rounded-full hover:bg-opacity-100 transition-all"
                                        title="Remove from history"
                                        onclick="event.stopPropagation();">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13,3A9,9 0 0,0 4,12H1L4.89,15.89L4.96,16.03L9,12H6A7,7 0 0,1 13,5A7,7 0 0,1 20,12A7,7 0 0,1 13,19C11.07,19 9.32,18.21 8.06,16.94L6.64,18.36C8.27,20 10.5,21 13,21A9,9 0 0,0 22,12A9,9 0 0,0 13,3Z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">No watch history</h3>
                        <p class="text-gray-500">Videos you watch will show up here</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($this->watchHistory->hasPages())
                <div class="mt-8">
                    {{ $this->watchHistory->links() }}
                </div>
            @endif

        @elseif($activeTab === 'liked-videos')
            <!-- Liked Videos Tab -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Liked Videos</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($this->likedVideos as $engagement)
                    @php
                      $engagement->video = $engagement->video()->first();
                    @endphp
                    @if($engagement->video)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow relative group">
                            <a href="{{ route('videos.show', $engagement->video) }}" class="block">
                                <!-- Thumbnail -->
                                <div class="relative aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                                    <img src="{{ $engagement->video->getThumbnailUrl() }}" 
                                         alt="{{ $engagement->video->title }}" 
                                         class="w-full h-full object-cover">
                                    
                                    <!-- Duration Badge -->
                                    @if($engagement->video->duration)
                                        <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                            {{ $engagement->video->getFormattedDuration() }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Video Info -->
                                <div class="p-3">
                                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5 mb-1">
                                        {{ $engagement->video->title }}
                                    </h3>
                                    <p class="text-xs text-gray-600 mb-1">{{ $engagement->video->user->getChannelName() }}</p>
                                    <div class="flex items-center text-xs text-gray-500 space-x-1">
                                        <span>{{ $engagement->video->getFormattedViews() }}</span>
                                        <span>•</span>
                                        <span>Liked {{ $engagement->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>

                            <!-- Remove Button -->
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="removeFromLiked('{{ $engagement->video->vid }}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-black bg-opacity-75 text-white rounded-full hover:bg-opacity-100 transition-all"
                                        title="Remove from liked videos"
                                        onclick="event.stopPropagation();">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">No liked videos</h3>
                        <p class="text-gray-500">Videos you like will show up here</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($this->likedVideos->hasPages())
                <div class="mt-8">
                    {{ $this->likedVideos->links() }}
                </div>
            @endif

        @elseif($activeTab === 'playlists')
            <!-- Playlists Tab -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Playlists</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($this->playlists as $playlist)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <a href="#" class="block">
                            <!-- Playlist Thumbnail (using first video or placeholder) -->
                            <div class="relative aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M15,6H3V8H15V6M15,10H3V12H15V10M3,16H11V14H3V16M17,6V14.18C16.69,14.07 16.35,14 16,14A3,3 0 0,0 13,17A3,3 0 0,0 16,20A3,3 0 0,0 19,17V8H22V6H17Z"/>
                                    </svg>
                                </div>
                                
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
                                    <p class="text-xs text-gray-600 line-clamp-2 mb-1">{{ $playlist->description }}</p>
                                @endif
                                <div class="flex items-center text-xs text-gray-500 space-x-1">
                                    <span>{{ ucfirst($playlist->visibility) }}</span>
                                    <span>•</span>
                                    <span>{{ $playlist->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M15,6H3V8H15V6M15,10H3V12H15V10M3,16H11V14H3V16M17,6V14.18C16.69,14.07 16.35,14 16,14A3,3 0 0,0 13,17A3,3 0 0,0 16,20A3,3 0 0,0 19,17V8H22V6H17Z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">No playlists</h3>
                        <p class="text-gray-500">Playlists you create will show up here</p>
                        <button class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Create Your First Playlist
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($this->playlists->hasPages())
                <div class="mt-8">
                    {{ $this->playlists->links() }}
                </div>
            @endif

        @elseif($activeTab === 'my-videos')
            <!-- My Videos Tab -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">My Videos</h2>
                <a href="{{ route('videos.upload') }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                    Upload New Video
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($this->myVideos as $video)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow relative group">
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

                                <!-- Privacy/Status Badge -->
                                <div class="absolute top-2 left-2">
                                    @if($video->visibility === 'private')
                                        <span class="bg-red-600 text-white text-xs px-2 py-1 rounded">Private</span>
                                    @elseif($video->visibility === 'unlisted')
                                        <span class="bg-yellow-600 text-white text-xs px-2 py-1 rounded">Unlisted</span>
                                    @elseif(!$video->published_at)
                                        <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded">Draft</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Video Info -->
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-5 mb-1">
                                    {{ $video->title }}
                                </h3>
                                <div class="flex items-center text-xs text-gray-500 space-x-1">
                                    <span>{{ $video->getFormattedViews() }}</span>
                                    <span>•</span>
                                    <span>{{ $video->likes }} likes</span>
                                    <span>•</span>
                                    <span>{{ $video->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>

                        <!-- Edit Button -->
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
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17,10.5V7A1,1 0 0,0 16,6H4A1,1 0 0,0 3,7V17A1,1 0 0,0 4,18H16A1,1 0 0,0 17,17V13.5L21,17.5V6.5L17,10.5Z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">No videos uploaded</h3>
                        <p class="text-gray-500 mb-4">Start creating content by uploading your first video</p>
                        <a href="{{ route('videos.upload') }}" 
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Upload Your First Video
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($this->myVideos->hasPages())
                <div class="mt-8">
                    {{ $this->myVideos->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
