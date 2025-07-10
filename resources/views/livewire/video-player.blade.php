<div class="w-full h-full">
    <!-- Video player -->
    <video id="videoPlayer" 
           class="w-full h-full bg-black" 
           controls 
           autoplay
           preload="metadata"
           poster="{{ $video->getThumbnailUrl() }}">
        <source src="{{ $videoUrl }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('videoPlayer');
    let watchTime = 0;
    
    // Track watch time
    setInterval(() => {
        if (!video.paused) {
            watchTime += 1;
        }
    }, 1000);
    
    // Update watch time on page unload
    window.addEventListener('beforeunload', function() {
        if (watchTime > 0) {
            navigator.sendBeacon('/api/videos/{{ $video->vid }}/watch-time', 
                JSON.stringify({watch_time: watchTime}));
        }
    });
});
</script>
