@props(['videoId'])

<div x-data="playlistManager('{{ $videoId }}')" class="inline-block">
    <!-- Trigger Button -->
    <button @click="openModal()" 
            class="flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <span class="hidden sm:inline">Save</span>
    </button>

    <!-- Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#000000af] flex items-center justify-center z-50 p-4"
         @click.away="showModal = false">
        
        <div @click.stop class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[80vh] overflow-hidden">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Save to playlist</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-4 max-h-96 overflow-y-auto">
                <!-- Loading State -->
                <div x-show="loading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                    <p class="mt-2 text-gray-600">Loading playlists...</p>
                </div>

                <!-- Playlists List -->
                <div x-show="!loading && playlists.length > 0" class="space-y-2">
                    <template x-for="playlist in playlists" :key="playlist.id">
                        <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" 
                                   :checked="selectedPlaylists.includes(playlist.id)"
                                   @change="togglePlaylist(playlist.id)"
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500 focus:border-red-500">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="playlist.title"></p>
                                <p class="text-xs text-gray-500" x-text="`${playlist.videos_count || 0} videos • ${playlist.visibility}`"></p>
                            </div>
                        </label>
                    </template>
                </div>

                <!-- Empty State -->
                <div x-show="!loading && playlists.length === 0" class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <p class="text-gray-500">No playlists found</p>
                </div>

                <!-- Create New Playlist -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button @click="showCreateForm = !showCreateForm" 
                            class="flex items-center gap-2 text-red-600 hover:text-red-700 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create new playlist
                    </button>

                    <div x-show="showCreateForm" x-transition class="mt-3 space-y-3">
                        <input type="text" 
                               x-model="newPlaylistTitle"
                               placeholder="Playlist title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               @keydown.enter="createPlaylist()">
                        <div class="flex gap-2">
                            <button @click="createPlaylist()" 
                                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition-colors">
                                Create
                            </button>
                            <button @click="showCreateForm = false; newPlaylistTitle = ''" 
                                    class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm rounded transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function playlistManager(videoId) {
    return {
        showModal: false,
        playlists: [],
        selectedPlaylists: [],
        newPlaylistTitle: '',
        showCreateForm: false,
        loading: false,

        async loadPlaylists() {
            try {
                this.loading = true;
                const response = await fetch('/playlists/user');
                const data = await response.json();
                if (data.success) {
                    this.playlists = data.playlists;
                    // Check which playlists already contain this video
                    this.selectedPlaylists = this.playlists
                        .filter(playlist => {
                            // Check if playlist has videos array and if the video is in it
                            return playlist.videos && playlist.videos.some(video => video.vid === videoId);
                        })
                        .map(playlist => playlist.id);
                    
                    console.log('Selected playlists:', this.selectedPlaylists); // Debug log
                }
            } catch (error) {
                console.error('Error loading playlists:', error);
            } finally {
                this.loading = false;
            }
        },

        async togglePlaylist(playlistId) {
            const isSelected = this.selectedPlaylists.includes(playlistId);

            try {
                if (isSelected) {
                    // Remove from playlist
                    const response = await fetch(`/playlists/${playlistId}/videos`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ video_id: videoId })
                    });

                    if (response.ok) {
                        this.selectedPlaylists = this.selectedPlaylists.filter(id => id !== playlistId);
                    }
                } else {
                    // Add to playlist
                    const response = await fetch(`/playlists/${playlistId}/videos`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ video_id: videoId })
                    });

                    if (response.ok) {
                        this.selectedPlaylists.push(playlistId);
                    } else {
                        const error = await response.json();
                        alert(error.message || 'Failed to add video to playlist');
                    }
                }
            } catch (error) {
                console.error('Error toggling playlist:', error);
                alert('An error occurred. Please try again.');
            }
        },

        async createPlaylist() {
            if (!this.newPlaylistTitle.trim()) return;

            try {
                const response = await fetch('/playlists', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        title: this.newPlaylistTitle,
                        description: '',
                        visibility: 'private'
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.playlists.push(data.playlist);
                    this.newPlaylistTitle = '';
                    this.showCreateForm = false;
                    // Automatically add the video to the new playlist
                    await this.togglePlaylist(data.playlist.id);
                }
            } catch (error) {
                console.error('Error creating playlist:', error);
                alert('Failed to create playlist. Please try again.');
            }
        },

        openModal() {
            this.showModal = true;
            this.loadPlaylists();
        }
    };
}
</script>
