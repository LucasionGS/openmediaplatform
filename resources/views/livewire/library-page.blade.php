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
          <a href="{{ route('library.tab', 'videos') }}"
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
            </a>
          
            <a href="{{ route('library.tab', 'history') }}"
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
            </a>
            
            <a href="{{ route('library.tab', 'likes') }}"
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
            </a>
            
            <a href="{{ route('library.tab', 'playlists') }}"
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
            </a>
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
                      // if ($last && $last->video && $last->video->vid === $history->video->vid) {
                      //     continue; // Skip if the video is the same as the last one
                      // }
                      // $last = $history;
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
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Playlists</h2>
                    <p class="text-gray-600 text-sm mt-1">Organize your videos into collections</p>
                </div>
                <button wire:click="$set('showCreatePlaylistModal', true)" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-full flex items-center gap-2 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New playlist
                </button>
            </div>

            @if($this->playlists->count() > 0)
                <div class="space-y-4">
                    @foreach($this->playlists as $playlist)
                        <div class="flex gap-4 p-3 hover:bg-gray-50 rounded-xl transition-colors group">
                            <!-- Playlist Thumbnail -->
                            <div class="flex-shrink-0">
                                <a href="{{ route('playlists.show', $playlist) }}" class="block relative">
                                    <div class="w-40 h-24 bg-gray-200 rounded-lg overflow-hidden shadow-sm">
                                        @if($playlist->thumbnail_path)
                                            <img src="{{ Storage::url($playlist->thumbnail_path) }}" 
                                                 alt="{{ $playlist->title }}" 
                                                 class="w-full h-full object-cover">
                                        @elseif($playlist->videos_count > 0)
                                            <!-- Use first video's thumbnail if available -->
                                            <div class="w-full h-full bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Video count overlay -->
                                        @if($playlist->videos_count > 0)
                                            <div class="absolute bottom-1 right-1 bg-black bg-opacity-80 text-white text-xs px-1.5 py-0.5 rounded">
                                                {{ $playlist->videos_count }}
                                            </div>
                                        @endif
                                        
                                        <!-- Play icon overlay -->
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black bg-opacity-20">
                                            <div class="bg-white bg-opacity-90 rounded-full p-2">
                                                <svg class="w-4 h-4 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Playlist Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0 pr-4">
                                        <a href="{{ route('playlists.show', $playlist) }}" class="block">
                                            <h3 class="text-base font-semibold text-gray-900 line-clamp-2 leading-5 mb-1 hover:text-red-600 transition-colors">
                                                {{ $playlist->title }}
                                            </h3>
                                        </a>
                                        
                                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                            <span class="flex items-center gap-1">
                                                @if($playlist->visibility === 'private')
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11H16V16H8V11H9.2V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.4,8.7 10.4,10V11H13.6V10C13.6,8.7 12.8,8.2 12,8.2Z"/>
                                                    </svg>
                                                    Private
                                                @elseif($playlist->visibility === 'unlisted')
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
                                                    </svg>
                                                    Unlisted
                                                @else
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                                    </svg>
                                                    Public
                                                @endif
                                            </span>
                                            <span>•</span>
                                            <span>{{ $playlist->videos_count }} {{ Str::plural('video', $playlist->videos_count) }}</span>
                                            <span>•</span>
                                            <span>Updated {{ $playlist->updated_at->diffForHumans() }}</span>
                                        </div>

                                        @if($playlist->description)
                                            <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
                                                {{ $playlist->description }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Menu -->
                                    <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click.stop @click="open = !open" 
                                                    class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12,16A2,2 0 0,1 14,18A2,2 0 0,1 12,20A2,2 0 0,1 10,18A2,2 0 0,1 12,16M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10M12,4A2,2 0 0,1 14,6A2,2 0 0,1 12,8A2,2 0 0,1 10,6A2,2 0 0,1 12,4Z"/>
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition 
                                                 class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg z-10 border border-gray-200 py-1">
                                                <button wire:click="editPlaylist({{ $playlist->id }})" @click="open = false"
                                                        class="flex items-center gap-2 w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit details
                                                </button>
                                                <button wire:click="deletePlaylist({{ $playlist->id }})" @click="open = false"
                                                        wire:confirm="Are you sure you want to delete this playlist? This action cannot be undone."
                                                        class="flex items-center gap-2 w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete playlist
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($this->playlists->hasPages())
                    <div class="mt-8">
                        {{ $this->playlists->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">No playlists yet</h3>
                    <p class="text-gray-600 mb-6 max-w-sm mx-auto">Collect videos you like in playlists. Share them with others or keep them just for yourself.</p>
                    <button wire:click="$set('showCreatePlaylistModal', true)"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-full transition-all duration-200 shadow-sm hover:shadow-md">
                        Create your first playlist
                    </button>
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

    <!-- Create Playlist Modal -->
    @if($showCreatePlaylistModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Create playlist</h3>
                        <button wire:click="closePlaylistModals" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit="createPlaylist" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Name</label>
                            <input type="text" wire:model="playlistTitle" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   placeholder="Enter playlist name">
                            @error('playlistTitle') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Description</label>
                            <textarea wire:model="playlistDescription" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                                      placeholder="Tell viewers about your playlist (optional)"></textarea>
                            @error('playlistDescription') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Visibility</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" wire:model="playlistVisibility" value="private" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11H16V16H8V11H9.2V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.4,8.7 10.4,10V11H13.6V10C13.6,8.7 12.8,8.2 12,8.2Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Private</span>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model="playlistVisibility" value="unlisted" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.09L19.73,22L21,20.73L3.27,3M12,7A5,5 0 0,1 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.76,7.13 11.37,7 12,7Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Unlisted</span>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model="playlistVisibility" value="public" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Public</span>
                                    </div>
                                </label>
                            </div>
                            @error('playlistVisibility') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" wire:click="closePlaylistModals"
                                    class="flex-1 px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors font-medium">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 px-4 rounded-lg transition-colors font-medium">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Playlist Modal -->
    @if($showEditPlaylistModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Edit playlist</h3>
                        <button wire:click="closePlaylistModals" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit="updatePlaylist" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Name</label>
                            <input type="text" wire:model="playlistTitle" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   placeholder="Enter playlist name">
                            @error('playlistTitle') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Description</label>
                            <textarea wire:model="playlistDescription" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                                      placeholder="Tell viewers about your playlist (optional)"></textarea>
                            @error('playlistDescription') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Visibility</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" wire:model="playlistVisibility" value="private" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11H16V16H8V11H9.2V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.4,8.7 10.4,10V11H13.6V10C13.6,8.7 12.8,8.2 12,8.2Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Private</span>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model="playlistVisibility" value="unlisted" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.09L19.73,22L21,20.73L3.27,3M12,7A5,5 0 0,1 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.76,7.13 11.37,7 12,7Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Unlisted</span>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model="playlistVisibility" value="public" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Public</span>
                                    </div>
                                </label>
                            </div>
                            @error('playlistVisibility') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" wire:click="closePlaylistModals"
                                    class="flex-1 px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors font-medium">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 px-4 rounded-lg transition-colors font-medium">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
