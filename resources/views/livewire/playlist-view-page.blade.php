<div class="max-w-7xl mx-auto">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Playlist Header -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
        <!-- Playlist Banner -->
        <div class="relative h-48 md:h-64 bg-gradient-to-br from-red-500 to-pink-600">
            @if($playlist->thumbnail_path)
                {{-- <img src="{{ Storage::url($playlist->thumbnail_path) }}" alt="{{ $playlist->title }}" class="w-full h-full object-cover"> --}}
                <img src="{{ asset('sf/' . $playlist->thumbnail_path) }}" alt="{{ $playlist->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            @else
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center text-white">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-80" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p class="text-xl font-medium opacity-90">{{ $playlist->getVideoCount() }} {{ Str::plural('video', $playlist->getVideoCount()) }}</p>
                    </div>
                </div>
            @endif
            
            <!-- Play All Button -->
            @if($playlist->videos && $playlist->videos->count() > 0)
                <div class="absolute top-4 right-4">
                    <button wire:click="playVideo(0)" 
                            class="bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-900 px-6 py-3 rounded-full font-medium flex items-center gap-2 transition-all shadow-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        Play all
                    </button>
                </div>
            @endif
        </div>

        <!-- Playlist Info -->
        <div class="p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
                <div class="flex-1">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">{{ $playlist->title }}</h1>
                    
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 mb-4">
                        <div class="flex items-center gap-1">
                            <img src="{{ $playlist->user->getAvatarUrl() }}" 
                                 alt="{{ $playlist->user->name }}" 
                                 class="w-6 h-6 rounded-full">
                            <span class="font-medium">{{ $playlist->user->getChannelName() }}</span>
                        </div>
                        <span>•</span>
                        <span>{{ $playlist->getVideoCount() }} {{ Str::plural('video', $playlist->getVideoCount()) }}</span>
                        <span>•</span>
                        <div class="flex items-center gap-1">
                            @if($playlist->visibility === 'private')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11H16V16H8V11H9.2V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.4,8.7 10.4,10V11H13.6V10C13.6,8.7 12.8,8.2 12,8.2Z"/>
                                </svg>
                                Private
                            @elseif($playlist->visibility === 'unlisted')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.09L19.73,22L21,20.73L3.27,3M12,7A5,5 0 0,1 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.76,7.13 11.37,7 12,7Z"/>
                                </svg>
                                Unlisted
                            @else
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                </svg>
                                Public
                            @endif
                        </div>
                        <span>•</span>
                        <span>Updated {{ $playlist->updated_at->diffForHumans() }}</span>
                    </div>
                    
                    @if($playlist->description)
                        <p class="text-gray-700 leading-relaxed">{{ $playlist->description }}</p>
                    @endif
                </div>

                @if(auth()->id() === $playlist->user_id)
                    <div class="flex flex-wrap gap-3">
                        <button wire:click="showEditModal" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-full transition-all">
                            Edit
                        </button>
                        <button wire:click="deletePlaylist" 
                                wire:confirm="Are you sure you want to delete this playlist?"
                                class="bg-red-100 hover:bg-red-200 text-red-700 px-6 py-2.5 rounded-full transition-all">
                            Delete
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Video Player Section -->
    @if($playlist->videos && $playlist->videos->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Video Player -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="aspect-video bg-black">
                        @php
                            $currentVideo = $playlist->videos->values()[$currentVideoIndex] ?? null;
                        @endphp
                        @if($currentVideo)
                            <livewire:video-player :video="$currentVideo" />
                        @endif
                    </div>
                    
                    @if($currentVideo)
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $currentVideo->title }}</h2>
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $currentVideo->user->getAvatarUrl() }}" 
                                         alt="{{ $currentVideo->user->name }}" 
                                         class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $currentVideo->user->getChannelName() }}</p>
                                        <p class="text-sm text-gray-600">
                                            Video {{ $currentVideoIndex + 1 }} of {{ $playlist->videos->count() }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button wire:click="playPrevious" 
                                            @if($currentVideoIndex <= 0) disabled @endif
                                            class="bg-gray-100 hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed text-gray-700 px-4 py-2 rounded-full text-sm font-medium transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/>
                                        </svg>
                                        Previous
                                    </button>
                                    <button wire:click="playNext" 
                                            @if($currentVideoIndex >= $playlist->videos->count() - 1) disabled @endif
                                            class="bg-gray-100 hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed text-gray-700 px-4 py-2 rounded-full text-sm font-medium transition-colors flex items-center gap-2">
                                        Next
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            @if($currentVideo->description)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 text-sm leading-relaxed">{{ $currentVideo->description }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Playlist Videos -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-8">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $playlist->title }}</h3>
                            <span class="text-sm text-gray-500">{{ $playlist->videos->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="max-h-[600px] overflow-y-auto">
                        @foreach($playlist->videos as $index => $video)
                            <div class="flex items-start gap-3 p-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-b-0 cursor-pointer group @if($index === $currentVideoIndex) bg-red-50 border-l-4 border-l-red-500 @endif"
                                 wire:click="playVideo({{ $index }})">
                                <!-- Video Thumbnail -->
                                <div class="flex-shrink-0 relative">
                                    <div class="w-24 h-14 bg-gray-200 rounded-lg overflow-hidden">
                                        <img src="{{ route('videos.thumbnail', $video->vid) }}" 
                                             alt="{{ $video->title }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    <div class="absolute top-1 left-1 bg-black bg-opacity-75 text-white text-xs px-1.5 py-0.5 rounded">
                                        {{ $index + 1 }}
                                    </div>
                                    @if($index === $currentVideoIndex)
                                        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 rounded-lg">
                                            <div class="bg-red-600 rounded-full p-1">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                    @if($video->duration)
                                        <div class="absolute bottom-1 right-1 bg-black bg-opacity-75 text-white text-xs px-1 py-0.5 rounded">
                                            {{ $video->getFormattedDuration() }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Video Info -->
                                <div class="flex-1 min-w-0 pr-2">
                                    <h4 class="text-sm font-medium line-clamp-2 leading-5 mb-1 {{ $index === $currentVideoIndex ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $video->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mb-1">
                                        {{ $video->user->getChannelName() }}
                                    </p>
                                    <div class="flex items-center text-xs text-gray-400 gap-1">
                                        @if($video->views > 0)
                                            <span>{{ $video->getFormattedViews() }}</span>
                                            <span>•</span>
                                        @endif
                                        <span>{{ $video->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <!-- Remove Button -->
                                @if(auth()->id() === $playlist->user_id)
                                    <button wire:click.stop="removeVideo('{{ $video->vid }}')" 
                                            wire:confirm="Remove this video from the playlist?"
                                            class="flex-shrink-0 text-gray-400 hover:text-red-600 transition-colors opacity-0 group-hover:opacity-100 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Playlist -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">This playlist is empty</h3>
            <p class="text-gray-600 mb-6 max-w-sm mx-auto">Videos can be added to this playlist from individual video pages</p>
        </div>
    @endif



    <!-- Edit Playlist Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Edit Playlist</h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit="updatePlaylist" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Name</label>
                            <input type="text" wire:model="title" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   placeholder="Enter playlist name">
                            @error('title') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Description</label>
                            <textarea wire:model="description" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                                      placeholder="Tell viewers about your playlist (optional)"></textarea>
                            @error('description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-3">Visibility</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" wire:model="visibility" value="private" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11H16V16H8V11H9.2V10C9.2,8.6 10.6,7 12,7M12,8.2C11.2,8.2 10.4,8.7 10.4,10V11H13.6V10C13.6,8.7 12.8,8.2 12,8.2Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Private</span>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model="visibility" value="unlisted" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.09L19.73,22L21,20.73L3.27,3M12,7A5,5 0 0,1 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.76,7.13 11.37,7 12,7Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Unlisted</span>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" wire:model="visibility" value="public" class="mr-3 text-red-600 focus:ring-red-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">Public</span>
                                    </div>
                                </label>
                            </div>
                            @error('visibility') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" wire:click="closeModal"
                                    class="flex-1 px-4 py-2.5 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors font-medium">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 px-4 rounded-lg transition-colors font-medium">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
