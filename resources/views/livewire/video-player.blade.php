<div class="relative w-full bg-black rounded-lg overflow-hidden group" 
     x-data="{ 
         showControls: true, 
         hideTimeout: null,
         volumeHover: false,
         settingsOpen: false,
         isBuffering: false,
         isDragging: false,
         mouseInPlayer: false,
         isPlaying: false
     }"
     :class="{ 'cursor-none': !showControls && mouseInPlayer }"
     @mouseenter="mouseInPlayer = true; showControls = true; clearTimeout(hideTimeout);"
     @mouseleave="mouseInPlayer = false; showControls = true; clearTimeout(hideTimeout);"
     @mousemove="showControls = true; clearTimeout(hideTimeout); hideTimeout = setTimeout(() => showControls = false, 3000);">
    
    <!-- Debug Info -->
    {{-- <div class="absolute top-2 left-2 text-white text-xs z-50" x-text="'Controls: ' + showControls + ', Playing: ' + isPlaying + ', Mouse: ' + mouseInPlayer"></div> --}}
    
    <!-- Video Element -->
    <video id="{{ $playerId }}"
           class="w-full h-full object-contain"
           preload="metadata"
           @if($poster) poster="{{ $poster }}" @endif
           @if($autoplay) autoplay @endif
           @if($muted) muted @endif
           playsinline
           crossorigin="anonymous">
        <source src="{{ $videoSrc }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Loading/Buffering Spinner -->
    <div x-show="isBuffering" 
         class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-10">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
    </div>

    <!-- Video Controls -->
    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black to-transparent p-4 transition-opacity duration-300 z-30"
         :class="{ 'opacity-0': !showControls, 'opacity-100': showControls }"
         @mouseenter="showControls = true; clearTimeout(hideTimeout)"
         @mouseleave="hideTimeout = setTimeout(() => showControls = false, 1000);">
        
        <!-- Progress Bar -->
        <div class="mb-4">
            <!-- Buffered Progress -->
            <div id="progressContainer-{{ $playerId }}" class="relative w-full h-2 hover:h-4 bg-gray-600 rounded-full overflow-hidden cursor-pointer transition-all duration-200 group/progress"
                 onclick="window.videoPlayers['{{ $playerId }}'].seekToPosition(event)"
                 onmousedown="window.videoPlayers['{{ $playerId }}'].startDrag(event)"
                 onmousemove="window.videoPlayers['{{ $playerId }}'].updateProgressHover(event)">
                <!-- Buffered Bar -->
                <div id="bufferedBar-{{ $playerId }}" class="absolute left-0 top-0 h-full bg-gray-400 rounded-full transition-all"
                     style="width: {{ $duration > 0 ? ($buffered / $duration * 100) : 0 }}%"></div>
                <!-- Progress Bar -->
                <div id="progressBar-{{ $playerId }}" class="absolute left-0 top-0 h-full bg-red-600 rounded-full transition-all"
                     style="width: {{ $duration > 0 ? ($currentTime / $duration * 100) : 0 }}%"></div>
                <!-- Progress Handle (visible on hover) -->
                <div id="progressHandle-{{ $playerId }}" class="absolute top-1/2 transform -translate-y-1/2 w-3 h-3 bg-red-600 rounded-full opacity-0 group-hover/progress:opacity-100 transition-all duration-200 pointer-events-none"
                     style="left: {{ $duration > 0 ? ($currentTime / $duration * 100) : 0 }}%"></div>
                <!-- Hover Preview -->
                <div class="absolute top-0 h-full w-1 bg-white opacity-0 group-hover/progress:opacity-100 transition-opacity"
                     style="left: var(--hover-position, 0%)"></div>
            </div>
        </div>

        <!-- Control Buttons -->
        <div class="flex items-center justify-between">
            <!-- Left Controls -->
            <div class="flex items-center space-x-2">
                <!-- Play/Pause Button -->
                <button id="playPauseBtn-{{ $playerId }}" 
                        onclick="window.videoPlayers['{{ $playerId }}'].togglePlay()" 
                        class="text-white hover:text-gray-300 transition-colors p-1">
                    <svg class="w-6 h-6 fill-current pause-icon" viewBox="0 0 24 24" style="display: {{ $isPlaying ? 'block' : 'none' }}">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                    </svg>
                    <svg class="w-6 h-6 fill-current play-icon" viewBox="0 0 24 24" style="display: {{ $isPlaying ? 'none' : 'block' }}">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </button>

                <!-- Volume Control -->
                <div class="relative flex items-center space-x-2"
                     @mouseenter="volumeHover = true"
                     @mouseleave="volumeHover = false">
                    <button id="muteBtn-{{ $playerId }}"
                            onclick="window.videoPlayers['{{ $playerId }}'].toggleMute()" 
                            class="text-white hover:text-gray-300 transition-colors p-1">
                        <svg class="w-6 h-6 fill-current muted-icon" viewBox="0 0 24 24" style="display: {{ $muted || $volume == 0 ? 'block' : 'none' }}">
                            <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                        </svg>
                        <svg class="w-6 h-6 fill-current low-volume-icon" viewBox="0 0 24 24" style="display: {{ !$muted && $volume > 0 && $volume < 0.5 ? 'block' : 'none' }}">
                            <path d="M18.5 12c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM5 9v6h4l5 5V4L9 9H5z"/>
                        </svg>
                        <svg class="w-6 h-6 fill-current high-volume-icon" viewBox="0 0 24 24" style="display: {{ !$muted && $volume >= 0.5 ? 'block' : 'none' }}">
                            <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                        </svg>
                    </button>
                    
                    <!-- Volume Slider -->
                    <div class="volume-slider opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                         x-show="volumeHover">
                        <input id="volumeSlider-{{ $playerId }}"
                               type="range" 
                               min="0" 
                               max="1" 
                               step="0.1"
                               value="{{ $volume }}"
                               oninput="window.videoPlayers['{{ $playerId }}'].setVolume(this.value)"
                               class="w-20 h-1 bg-gray-600 rounded-lg appearance-none cursor-pointer slider">
                    </div>
                </div>

                <!-- Time Display -->
                <div id="timeDisplay-{{ $playerId }}" class="text-white text-sm font-mono">
                    {{ $this->formatTime($currentTime) }} / {{ $this->formatTime($duration) }}
                </div>
            </div>

            <!-- Right Controls -->
            <div class="flex items-center space-x-2">
                <!-- Playback Speed -->
                <div class="relative"
                     @click.away="settingsOpen = false">
                    <button id="speedBtn-{{ $playerId }}"
                            @click="settingsOpen = !settingsOpen"
                            class="text-white hover:text-gray-300 transition-colors px-2 py-1 text-sm font-semibold">
                        {{ $playbackSpeed }}x
                    </button>
                    
                    <!-- Speed Menu -->
                    <div x-show="settingsOpen"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute bottom-full right-0 mb-2 bg-black bg-opacity-90 rounded-lg py-2 min-w-[80px]">
                        @foreach([0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2] as $speed)
                            <button onclick="window.videoPlayers['{{ $playerId }}'].setPlaybackSpeed({{ $speed }})"
                                    class="block w-full text-left px-3 py-1 text-white hover:bg-gray-700 text-sm {{ $playbackSpeed == $speed ? 'bg-gray-700' : '' }}">
                                {{ $speed }}x
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Fullscreen Button -->
                <button onclick="window.videoPlayers['{{ $playerId }}'].toggleFullscreen()" 
                        class="text-white hover:text-gray-300 transition-colors p-1">
                    @if($isFullscreen)
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Inline Styles -->
    <style>
    .slider::-webkit-slider-thumb {
        appearance: none;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #ff0000;
        cursor: pointer;
    }

    .slider::-moz-range-thumb {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #ff0000;
        cursor: pointer;
        border: none;
    }

    .volume-slider {
        display: none;
    }

    .relative:hover .volume-slider {
        display: block;
    }
    </style>

    <!-- Inline Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('{{ $playerId }}');
        
        if (!video) return;
        
        // Initialize videoPlayers object if it doesn't exist
        if (!window.videoPlayers) {
            window.videoPlayers = {};
        }
        
        // Get Livewire component safely
        let component = null;
        try {
            component = @this;
        } catch (e) {
            console.log('Livewire component not available for video player {{ $playerId }}');
        }
        
        // Load saved volume settings from localStorage
        const savedVolume = localStorage.getItem('videoPlayer_volume');
        const savedMuted = localStorage.getItem('videoPlayer_muted') === 'true';
        
        // Apply saved settings to video element
        if (savedVolume !== null) {
            video.volume = parseFloat(savedVolume);
        }
        video.muted = savedMuted;
        
        // Create video player controller
        window.videoPlayers['{{ $playerId }}'] = {
            video: video,
            component: component,
            playerId: '{{ $playerId }}',
            
            formatTime: function(seconds) {
                if (!seconds || isNaN(seconds)) return '0:00';
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = Math.floor(seconds % 60);

                if (hours > 0) {
                    return hours + ':' + (minutes < 10 ? '0' : '') + minutes + ':' + (secs < 10 ? '0' : '') + secs;
                }
                return minutes + ':' + (secs < 10 ? '0' : '') + secs;
            },
            
            updatePlayButton: function(isPlaying) {
                const playIcon = document.querySelector(`#playPauseBtn-${this.playerId} .play-icon`);
                const pauseIcon = document.querySelector(`#playPauseBtn-${this.playerId} .pause-icon`);
                if (playIcon && pauseIcon) {
                    playIcon.style.display = isPlaying ? 'none' : 'block';
                    pauseIcon.style.display = isPlaying ? 'block' : 'none';
                }
            },
            
            updateVolumeButton: function(volume, muted) {
                const mutedIcon = document.querySelector(`#muteBtn-${this.playerId} .muted-icon`);
                const lowVolumeIcon = document.querySelector(`#muteBtn-${this.playerId} .low-volume-icon`);
                const highVolumeIcon = document.querySelector(`#muteBtn-${this.playerId} .high-volume-icon`);
                const volumeSlider = document.querySelector(`#volumeSlider-${this.playerId}`);
                
                if (mutedIcon && lowVolumeIcon && highVolumeIcon) {
                    mutedIcon.style.display = (muted || volume == 0) ? 'block' : 'none';
                    lowVolumeIcon.style.display = (!muted && volume > 0 && volume < 0.5) ? 'block' : 'none';
                    highVolumeIcon.style.display = (!muted && volume >= 0.5) ? 'block' : 'none';
                }
                
                if (volumeSlider) {
                    volumeSlider.value = volume;
                }
            },
            
            updateProgress: function(currentTime, duration) {
                const progressBar = document.querySelector(`#progressBar-${this.playerId}`);
                const progressHandle = document.querySelector(`#progressHandle-${this.playerId}`);
                const timeDisplay = document.querySelector(`#timeDisplay-${this.playerId}`);
                
                if (progressBar && duration > 0) {
                    const percentage = (currentTime / duration * 100);
                    progressBar.style.width = percentage + '%';
                    
                    // Update progress handle position
                    if (progressHandle) {
                        progressHandle.style.left = percentage + '%';
                    }
                }
                
                if (timeDisplay) {
                    timeDisplay.textContent = this.formatTime(currentTime) + ' / ' + this.formatTime(duration);
                }
            },
            
            updateBuffered: function(buffered, duration) {
                const bufferedBar = document.querySelector(`#bufferedBar-${this.playerId}`);
                if (bufferedBar && duration > 0) {
                    bufferedBar.style.width = (buffered / duration * 100) + '%';
                }
            },
            
            updateSpeed: function(speed) {
                const speedBtn = document.querySelector(`#speedBtn-${this.playerId}`);
                if (speedBtn) {
                    speedBtn.textContent = speed + 'x';
                }
            },
            
            togglePlay: function() {
                if (this.video.paused) {
                    this.video.play();
                } else {
                    this.video.pause();
                }
            },
            
            toggleMute: function() {
                this.video.muted = !this.video.muted;
                this.updateVolumeButton(this.video.volume, this.video.muted);
                
                // Save mute state to localStorage
                localStorage.setItem('videoPlayer_muted', this.video.muted);
                
                if (this.component && typeof this.component.call === 'function') {
                    this.component.call('updateVolume', this.video.volume, this.video.muted);
                }
            },
            
            setVolume: function(volume) {
                this.video.volume = parseFloat(volume);
                this.video.muted = (volume == 0);
                this.updateVolumeButton(this.video.volume, this.video.muted);
                
                // Save volume and mute state to localStorage
                localStorage.setItem('videoPlayer_volume', this.video.volume);
                localStorage.setItem('videoPlayer_muted', this.video.muted);
                
                if (this.component && typeof this.component.call === 'function') {
                    this.component.call('updateVolume', this.video.volume, this.video.muted);
                }
            },
            
            setPlaybackSpeed: function(speed) {
                this.video.playbackRate = speed;
                this.updateSpeed(speed);
                if (this.component && typeof this.component.call === 'function') {
                    this.component.call('setPlaybackSpeed', speed);
                }
                // Close the settings menu
                const menuContainer = document.querySelector('[x-data]');
                if (menuContainer && menuContainer.__x) {
                    menuContainer.__x.$data.settingsOpen = false;
                }
            },
            
            toggleFullscreen: function() {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    this.video.closest('.relative').requestFullscreen();
                }
            },
            
            seekToPosition: function(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const clickX = event.clientX - rect.left;
                const width = rect.width;
                const percentage = clickX / width;
                const seekTime = percentage * this.video.duration;
                this.video.currentTime = seekTime;
            },
            
            startDrag: function(event) {
                event.preventDefault();
                const progressContainer = document.getElementById(`progressContainer-${this.playerId}`);
                const player = this;
                let isDragging = true;
                
                // Set dragging state in Alpine.js
                const videoContainer = document.getElementById(this.playerId).closest('[x-data]');
                if (videoContainer && videoContainer.__x) {
                    videoContainer.__x.$data.isDragging = true;
                }
                
                // Seek to initial position
                this.seekToPosition(event);
                
                function handleMouseMove(e) {
                    if (!isDragging) return;
                    e.preventDefault();
                    
                    const rect = progressContainer.getBoundingClientRect();
                    const moveX = Math.max(0, Math.min(rect.width, e.clientX - rect.left));
                    const percentage = moveX / rect.width;
                    const seekTime = percentage * player.video.duration;
                    
                    if (!isNaN(seekTime) && seekTime >= 0 && seekTime <= player.video.duration) {
                        player.video.currentTime = seekTime;
                    }
                    
                    // Update hover position for visual feedback
                    const hoverPercentage = (moveX / rect.width) * 100;
                    progressContainer.style.setProperty('--hover-position', hoverPercentage + '%');
                }
                
                function handleMouseUp() {
                    isDragging = false;
                    
                    // Clear dragging state in Alpine.js
                    if (videoContainer && videoContainer.__x) {
                        videoContainer.__x.$data.isDragging = false;
                    }
                    
                    document.removeEventListener('mousemove', handleMouseMove);
                    document.removeEventListener('mouseup', handleMouseUp);
                    document.removeEventListener('mouseleave', handleMouseUp);
                }
                
                document.addEventListener('mousemove', handleMouseMove);
                document.addEventListener('mouseup', handleMouseUp);
                document.addEventListener('mouseleave', handleMouseUp);
            },
            
            updateProgressHover: function(event) {
                const rect = event.currentTarget.getBoundingClientRect();
                const hoverX = event.clientX - rect.left;
                const percentage = (hoverX / rect.width) * 100;
                event.currentTarget.style.setProperty('--hover-position', percentage + '%');
            }
        };
        
        // Handle video click events (single click = play/pause, double click = fullscreen)
        let clickTimer = null;
        let clickPrevent = false;
        
        video.addEventListener('click', (e) => {
            if (clickPrevent) {
                clickPrevent = false;
                return;
            }
            
            clickTimer = setTimeout(() => {
                if (!clickPrevent) {
                    window.videoPlayers['{{ $playerId }}'].togglePlay();
                }
            }, 200); // Short delay to detect double-clicks
        });
        
        video.addEventListener('dblclick', (e) => {
            clearTimeout(clickTimer);
            clickPrevent = true;
            window.videoPlayers['{{ $playerId }}'].toggleFullscreen();
            
            // Reset the prevent flag after a short delay
            setTimeout(() => {
                clickPrevent = false;
            }, 300);
        });
        
        // Video event listeners
        video.addEventListener('loadedmetadata', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            if (component && typeof component.call === 'function') {
                component.call('updateDuration', video.duration);
                component.call('updateVolume', video.volume, video.muted);
                component.call('setPlaybackSpeed', video.playbackRate);
                if (!video.paused) {
                    component.call('setPlaying');
                }
            }
            // Always update UI regardless of Livewire state
            player.updateVolumeButton(video.volume, video.muted);
            player.updateSpeed(video.playbackRate);
            player.updatePlayButton(!video.paused);
            player.updateProgress(video.currentTime, video.duration);
            
            // Attempt to autoplay the video
            const playPromise = video.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    // Autoplay succeeded
                    console.log('Video autoplay started successfully');
                }).catch((error) => {
                    // Autoplay was prevented (common in modern browsers)
                    console.log('Video autoplay was prevented:', error.message);
                    // The video will remain paused and show the play button
                });
            }
        });
        
        video.addEventListener('timeupdate', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            player.updateProgress(video.currentTime, video.duration);
            if (component && typeof component.call === 'function') {
                component.call('updateTime', video.currentTime);
            }
        });
        
        video.addEventListener('play', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            player.updatePlayButton(true);
            
            // Update Alpine.js state
            const videoContainer = document.getElementById('{{ $playerId }}').closest('[x-data]');
            if (videoContainer && videoContainer.__x) {
                videoContainer.__x.$data.isPlaying = true;
            }
            
            if (component && typeof component.call === 'function') {
                component.call('setPlaying');
            }
        });
        
        video.addEventListener('pause', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            player.updatePlayButton(false);
            
            // Update Alpine.js state
            const videoContainer = document.getElementById('{{ $playerId }}').closest('[x-data]');
            if (videoContainer && videoContainer.__x) {
                videoContainer.__x.$data.isPlaying = false;
            }
            
            if (component && typeof component.call === 'function') {
                component.call('setPaused');
            }
        });
        
        video.addEventListener('ended', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            player.updatePlayButton(false);
            
            // Update Alpine.js state
            const videoContainer = document.getElementById('{{ $playerId }}').closest('[x-data]');
            if (videoContainer && videoContainer.__x) {
                videoContainer.__x.$data.isPlaying = false;
            }
            
            if (component && typeof component.call === 'function') {
                component.call('setEnded');
            }
        });
        
        video.addEventListener('progress', () => {
            if (video.buffered.length > 0) {
                const buffered = video.buffered.end(video.buffered.length - 1);
                const player = window.videoPlayers['{{ $playerId }}'];
                player.updateBuffered(buffered, video.duration);
                if (component && typeof component.call === 'function') {
                    component.call('updateBuffered', buffered);
                }
            }
        });
        
        video.addEventListener('volumechange', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            player.updateVolumeButton(video.volume, video.muted);
            
            // Save volume settings to localStorage
            localStorage.setItem('videoPlayer_volume', video.volume);
            localStorage.setItem('videoPlayer_muted', video.muted);
            
            if (component && typeof component.call === 'function') {
                component.call('updateVolume', video.volume, video.muted);
            }
        });
        
        video.addEventListener('ratechange', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            player.updateSpeed(video.playbackRate);
            if (component && typeof component.call === 'function') {
                component.call('setPlaybackSpeed', video.playbackRate);
            }
        });
        
        video.addEventListener('waiting', () => {
            if (component && typeof component.call === 'function') {
                component.call('setBuffering', true);
            }
        });
        
        video.addEventListener('canplay', () => {
            if (component && typeof component.call === 'function') {
                component.call('setBuffering', false);
            }
        });
        
        video.addEventListener('seeked', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            player.updateProgress(video.currentTime, video.duration);
            if (component && typeof component.call === 'function') {
                component.call('updateTime', video.currentTime);
            }
        });
        
        video.addEventListener('seeking', () => {
            const player = window.videoPlayers['{{ $playerId }}'];
            player.updateProgress(video.currentTime, video.duration);
            if (component && typeof component.call === 'function') {
                component.call('updateTime', video.currentTime);
            }
        });
        
        // Keyboard shortcuts
        const container = video.closest('.relative');
        let isHovered = false;
        
        container.addEventListener('mouseenter', () => { isHovered = true; });
        container.addEventListener('mouseleave', () => { isHovered = false; });
        
        document.addEventListener('keydown', (e) => {
            // Only handle shortcuts when hovering over this video player
            if (!isHovered) return;
            
            switch(e.code) {
                case 'Space':
                    e.preventDefault();
                    window.videoPlayers['{{ $playerId }}'].togglePlay();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    video.currentTime = Math.max(0, video.currentTime - 10);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    video.currentTime = Math.min(video.duration, video.currentTime + 10);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    window.videoPlayers['{{ $playerId }}'].setVolume(Math.min(1, video.volume + 0.1));
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    window.videoPlayers['{{ $playerId }}'].setVolume(Math.max(0, video.volume - 0.1));
                    break;
                case 'KeyM':
                    e.preventDefault();
                    window.videoPlayers['{{ $playerId }}'].toggleMute();
                    break;
                case 'KeyF':
                    e.preventDefault();
                    window.videoPlayers['{{ $playerId }}'].toggleFullscreen();
                    break;
            }
        });
        
        // Fullscreen change
        document.addEventListener('fullscreenchange', () => {
            if (component && typeof component.call === 'function') {
                component.call('updateFullscreen', !!document.fullscreenElement);
            }
        });
        
        // Watch time tracking
        let watchTime = 0;
        setInterval(() => {
            if (!video.paused && !video.ended) {
                watchTime += 1;
            }
        }, 1000);
        
        // End of DOMContentLoaded
    });
    </script>
</div>