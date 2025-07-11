<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $video->title ?? 'Shared Video' }} - {{ \App\Models\SiteSetting::get('site_title', 'Open Media Platform') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon">
    
    <!-- Basic Meta Tags -->
    <meta name="description" content="{{ Str::limit($video->description ?? 'Watch this video on ' . \App\Models\SiteSetting::get('site_title', 'Open Media Platform'), 160) }}">
    <meta name="author" content="{{ $video->user?->getChannelName() ?? 'Unknown' }}">
    
    <!-- Open Graph Meta Tags for Video -->
    <meta property="og:title" content="{{ $video->title }}">
    <meta property="og:description" content="{{ Str::limit($video->description ?? 'Watch this video on ' . \App\Models\SiteSetting::get('site_title', 'Open Media Platform'), 300) }}">
    <meta property="og:type" content="video.other">
    <meta property="og:url" content="{{ $video->getShareUrl() }}">
    <meta property="og:site_name" content="{{ \App\Models\SiteSetting::get('site_title', 'Open Media Platform') }}">
    
    <!-- Video-specific Open Graph tags -->
    <meta property="og:video" content="{{ route('videos.share.raw', ['token' => $video->share_token]) }}">
    <meta property="og:video:secure_url" content="{{ route('videos.share.raw', ['token' => $video->share_token]) }}">
    <meta property="og:video:type" content="video/mp4">
    <meta property="og:video:width" content="1280">
    <meta property="og:video:height" content="720">
    @if($video->duration)
        <meta property="video:duration" content="{{ $video->duration }}">
    @endif
    
    <!-- Thumbnail/Image -->
    <meta property="og:image" content="{{ route('videos.share.thumbnail', ['token' => $video->share_token]) }}">
    <meta property="og:image:secure_url" content="{{ route('videos.share.thumbnail', ['token' => $video->share_token]) }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1280">
    <meta property="og:image:height" content="720">
    <meta property="og:image:alt" content="{{ $video->title }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="player">
    <meta name="twitter:title" content="{{ $video->title }}">
    <meta name="twitter:description" content="{{ Str::limit($video->description ?? 'Watch this video on ' . \App\Models\SiteSetting::get('site_title', 'Open Media Platform'), 200) }}">
    <meta name="twitter:image" content="{{ route('videos.share.thumbnail', ['token' => $video->share_token]) }}">
    <meta name="twitter:player" content="{{ url('/share/' . $video->share_token . '/embed') }}">
    <meta name="twitter:player:width" content="1280">
    <meta name="twitter:player:height" content="720">
    <meta name="twitter:player:stream" content="{{ route('videos.share.raw', ['token' => $video->share_token]) }}">
    <meta name="twitter:player:stream:content_type" content="video/mp4">
    
    <!-- Additional Video Meta Tags -->
    <meta name="video" content="{{ route('videos.share.raw', ['token' => $video->share_token]) }}">
    <meta name="thumbnail" content="{{ route('videos.share.thumbnail', ['token' => $video->share_token]) }}">
    
    <!-- Schema.org structured data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "VideoObject",
      "name": "{{ $video->title }}",
      "description": "{{ $video->description ?? 'Watch this video on ' . \App\Models\SiteSetting::get('site_title', 'Open Media Platform') }}",
      "thumbnailUrl": "{{ route('videos.share.thumbnail', ['token' => $video->share_token]) }}",
      "uploadDate": "{{ $video->created_at->toISOString() }}",
      "contentUrl": "{{ route('videos.share.raw', ['token' => $video->share_token]) }}",
      "embedUrl": "{{ url('/share/' . $video->share_token . '/embed') }}",
      @if($video->duration)
      "duration": "PT{{ $video->duration }}S",
      @endif
      "author": {
        "@type": "Person",
        "name": "{{ $video->user?->getChannelName() ?? 'Unknown' }}"
      },
      "publisher": {
        "@type": "Organization",
        "name": "{{ \App\Models\SiteSetting::get('site_title', 'Open Media Platform') }}"
      }
    }
    </script>
    
    <!-- oEmbed discovery -->
    <link rel="alternate" type="application/json+oembed" href="{{ route('oembed') }}?url={{ urlencode($video->getShareUrl()) }}&format=json" title="{{ $video->title }}">
    <link rel="alternate" type="text/xml+oembed" href="{{ route('oembed') }}?url={{ urlencode($video->getShareUrl()) }}&format=xml" title="{{ $video->title }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Minimal Header for Shared Videos -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Site Title -->
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">
                        {{ \App\Models\SiteSetting::get('site_title', 'Open Media Platform') }}
                    </h1>
                    <span class="ml-2 text-sm text-gray-500">- Shared Video</span>
                </div>
                
                <!-- Login/Register Link -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" 
                       class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} {{ \App\Models\SiteSetting::get('site_title', 'Open Media Platform') }}. All rights reserved.</p>
                <p class="mt-2">
                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">
                        Visit our platform to explore more content
                    </a>
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
