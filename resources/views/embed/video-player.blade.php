<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $video->title }}</title>
    
    <!-- Minimal CSS for video player -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #000;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow: hidden;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            height: 100vh;
            background: #000;
        }
        
        .video-player {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .video-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: white;
            padding: 20px;
            pointer-events: none;
        }
        
        .video-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        
        .video-info {
            font-size: 14px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .channel-name {
            font-weight: 500;
        }
        
        .video-stats {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .watch-link {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
            pointer-events: auto;
        }
        
        .watch-link:hover {
            background: rgba(0,0,0,0.9);
            color: white;
            text-decoration: none;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .video-overlay {
                padding: 15px;
            }
            
            .video-title {
                font-size: 16px;
            }
            
            .video-info {
                font-size: 13px;
            }
            
            .watch-link {
                top: 15px;
                right: 15px;
                padding: 6px 12px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="video-container">
        <!-- Video Player -->
        <video class="video-player" 
               controls 
               preload="metadata"
               poster="{{ route('share.video.thumbnail', ['token' => $token]) }}"
               aria-label="{{ $video->title }}">
            <source src="{{ route('share.video.raw', ['token' => $token]) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
        <!-- Watch on Site Link -->
        <a href="{{ $video->getShareUrl() }}" 
           class="watch-link" 
           target="_parent"
           rel="noopener">
            Watch on {{ \App\Models\SiteSetting::get('site_title', 'Site') }}
        </a>
        
        <!-- Video Information Overlay -->
        <div class="video-overlay">
            <div class="video-title">{{ $video->title }}</div>
            <div class="video-info">
                <span class="channel-name">{{ $video->user->getChannelName() }}</span>
                <span>•</span>
                <div class="video-stats">
                    <span>{{ $video->getFormattedViews() }}</span>
                    <span>•</span>
                    <span>{{ $video->getTimeAgo() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics and tracking (optional) -->
    <script>
        // Basic video tracking
        const video = document.querySelector('.video-player');
        let hasStarted = false;
        
        video.addEventListener('play', function() {
            if (!hasStarted) {
                hasStarted = true;
                // Track video start (you can send this to your analytics)
                console.log('Video started playing');
            }
        });
        
        video.addEventListener('ended', function() {
            // Track video completion
            console.log('Video ended');
        });
        
        // Prevent right-click context menu on video
        video.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        
        // Auto-play if allowed by browser
        video.play().catch(function(e) {
            // Auto-play was prevented, which is fine
            console.log('Auto-play prevented:', e);
        });
    </script>
</body>
</html>
