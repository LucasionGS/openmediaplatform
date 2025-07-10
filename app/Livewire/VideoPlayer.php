<?php

namespace App\Livewire;

use App\Models\Video;
use Livewire\Component;

class VideoPlayer extends Component
{
    public $videoSrc;
    public $videoTitle;
    public $poster;
    public $autoplay = false;
    public $muted = false;
    public $controls = true;
    public $playbackSpeed = 1;
    public $volume = 1;
    public $quality = 'auto';
    public $isPlaying = false;
    public $currentTime = 0;
    public $duration = 0;
    public $buffered = 0;
    public $isFullscreen = false;
    public $showControls = true;
    public $playerId;
    
    // Legacy support for existing usage
    public ?Video $video = null;
    public ?string $videoId = null;

    protected $listeners = [
        'videoTimeUpdate' => 'updateTime',
        'videoDurationChange' => 'updateDuration',
        'videoPlay' => 'setPlaying',
        'videoPause' => 'setPaused',
        'videoEnded' => 'setEnded',
        'videoProgress' => 'updateBuffered',
        'videoVolumeChange' => 'updateVolume',
        'videoFullscreenChange' => 'updateFullscreen',
    ];

    public function mount($videoSrc = null, $videoTitle = '', $poster = '', $autoplay = false, $muted = false, $videoId = null, $video = null)
    {
        // Handle legacy usage with Video model
        if ($video instanceof Video) {
            $this->video = $video;
            $this->videoSrc = route('videos.raw', $video);
            $this->videoTitle = $video->title;
            $this->poster = $video->getThumbnailUrl();
        } elseif ($videoId) {
            $this->videoId = $videoId;
            $this->video = Video::find($videoId);
            if ($this->video) {
                $this->videoSrc = route('videos.raw', $this->video);
                $this->videoTitle = $this->video->title;
                $this->poster = $this->video->getThumbnailUrl();
            }
        } else {
            // New standalone usage
            $this->videoSrc = $videoSrc;
            $this->videoTitle = $videoTitle;
            $this->poster = $poster;
        }
        
        $this->autoplay = $autoplay;
        $this->muted = $muted;
        $this->playerId = 'video-player-' . uniqid();
    }

    public function togglePlay()
    {
        $this->isPlaying = !$this->isPlaying;
        $this->dispatch('player-toggle-play', playerId: $this->playerId);
    }

    public function seek($time)
    {
        $this->currentTime = $time;
        $this->dispatch('player-seek', time: $time, playerId: $this->playerId);
    }

    public function setVolume($volume)
    {
        $this->volume = max(0, min(1, $volume));
        $this->muted = ($volume == 0);
        $this->dispatch('player-set-volume', volume: $this->volume, playerId: $this->playerId);
    }

    public function setPlaybackSpeed($speed)
    {
        $this->playbackSpeed = $speed;
        $this->dispatch('player-set-speed', speed: $speed, playerId: $this->playerId);
    }

    public function toggleMute()
    {
        $this->muted = !$this->muted;
        $this->dispatch('player-toggle-mute', muted: $this->muted, playerId: $this->playerId);
    }

    public function toggleFullscreen()
    {
        $this->isFullscreen = !$this->isFullscreen;
        $this->dispatch('player-toggle-fullscreen', playerId: $this->playerId);
    }

    public function updateTime($time)
    {
        $this->currentTime = $time;
    }

    public function updateDuration($duration)
    {
        $this->duration = $duration;
    }

    public function setPlaying()
    {
        $this->isPlaying = true;
    }

    public function setPaused()
    {
        $this->isPlaying = false;
    }

    public function setEnded()
    {
        $this->isPlaying = false;
        $this->currentTime = $this->duration;
    }

    public function updateBuffered($buffered)
    {
        $this->buffered = $buffered;
    }

    public function updateVolume($volume, $muted)
    {
        $this->volume = $volume;
        $this->muted = $muted;
    }

    public function updateFullscreen($isFullscreen)
    {
        $this->isFullscreen = $isFullscreen;
    }

    public function setBuffering($isBuffering)
    {
        // This can be used for future loading state management
        $this->dispatch('player-buffering', isBuffering: $isBuffering, playerId: $this->playerId);
    }

    public function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = floor($seconds % 60);

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function render()
    {
        // Legacy support
        if (!$this->video && $this->videoId) {
            $this->video = Video::find($this->videoId);
            if ($this->video && !$this->videoSrc) {
                $this->videoSrc = route('videos.raw', $this->video);
                $this->videoTitle = $this->video->title;
                $this->poster = $this->video->getThumbnailUrl();
            }
        }
        
        return view('livewire.video-player', [
            'video' => $this->video,
            'videoUrl' => $this->videoSrc,
        ]);
    }
}
