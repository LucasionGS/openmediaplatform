<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="/favicon">
        <title>{{ $title ?? \App\Models\SiteSetting::get('site_title', 'OpenMediaPlatform') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        @livewireStyles
    </head>
    <body class="bg-gray-50">
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="flex items-center justify-between px-4 py-2">
                <!-- Logo and Menu -->
                <div class="flex items-center space-x-2 md:space-x-4">
                    <button id="sidebarToggle" class="p-2 hover:bg-gray-100 rounded-full xl:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('home') }}" class="flex items-center space-x-1 md:space-x-2">
                        @php
                            $siteIcon = \App\Models\SiteSetting::get('site_icon');
                            $siteTitle = \App\Models\SiteSetting::get('site_title', 'OpenMediaPlatform');
                        @endphp
                        
                        @if($siteIcon)
                            <img src="/favicon" alt="{{ $siteTitle }}" class="w-6 h-6 md:w-8 md:h-8 object-cover">
                        @else
                            <div class="w-6 h-6 md:w-8 md:h-8 bg-red-600 rounded-sm flex items-center justify-center">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </div>
                        @endif
                        <span class="text-lg md:text-xl font-bold hidden sm:block">{{ Str::limit($siteTitle, 15) }}</span>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-md md:max-w-2xl mx-2 md:mx-4">
                    <form action="{{ route('search') }}" method="GET" class="flex">
                        <div class="flex w-full">
                            <input type="text" 
                                   name="q"
                                   placeholder="Search" 
                                   value="{{ request('q') }}"
                                   class="w-full px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 rounded-l-full focus:outline-none focus:border-blue-500">
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <button type="submit" 
                                    class="px-4 md:px-6 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-full hover:bg-gray-200">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="flex items-center space-x-1 md:space-x-2">
                    @auth
                        <a href="{{ route('videos.upload') }}" class="p-2 hover:bg-gray-100 rounded-full hidden sm:block" title="Create">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </a>
                        <button class="p-2 hover:bg-gray-100 rounded-full hidden sm:block" title="Notifications">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            </svg>
                        </button>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center justify-center hover:opacity-80 transition-opacity">
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ asset('sf/' . auth()->user()->profile_picture) }}" 
                                         alt="{{ auth()->user()->name }}" 
                                         class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold hover:bg-blue-600">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <a href="{{ route('channel.show', auth()->user()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Channel</a>
                                    <a href="{{ route('videos.upload') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 sm:hidden">Upload</a>
                                    <a href="{{ route('profile.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                    @if(auth()->user()->isAdmin())
                                        <div class="border-t border-gray-100"></div>
                                        <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50 font-medium">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span>Admin Settings</span>
                                            </div>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-3 md:px-4 py-2 border border-blue-600 text-xs md:text-sm font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="items-center px-3 md:px-4 py-2 border border-transparent text-xs md:text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 hidden sm:block">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Mobile Sidebar Overlay -->
            <div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-25 z-40 xl:hidden hidden"></div>
            
            <!-- Sidebar -->
            <aside id="sidebar" class="fixed xl:static top-16 left-0 w-64 bg-white shadow-sm h-screen xl:h-auto overflow-y-auto transform -translate-x-full xl:translate-x-0 transition-transform duration-300 z-50">
                <nav class="p-4">
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('home') ? 'bg-gray-100' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                            <span>Home</span>
                        </a>
                        
                        <a href="{{ route('subscriptions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('subscriptions') ? 'bg-gray-100' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                            <span>Subscriptions</span>
                        </a>

                        @auth
                        <a href="{{ route('library.tab', 'my-videos') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request()->is('library/my-videos') ? 'bg-gray-100' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4,6H2V20A2,2 0 0,0 4,22H18V20H4V6M20,2H8A2,2 0 0,0 6,4V16A2,2 0 0,0 8,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2M20,16H8V4H20V16Z"/>
                            </svg>
                            <span>Library</span>
                        </a>
                        
                        <a href="{{ route('library.tab', 'playlists') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request()->is('library/playlists') ? 'bg-gray-100' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15,6H3V8H15V6M15,10H3V12H15V10M3,16H11V14H3V16M17,6V14.18C16.69,14.07 16.35,14 16,14A3,3 0 0,0 13,17A3,3 0 0,0 16,20A3,3 0 0,0 19,17V8H22V6H17Z"/>
                            </svg>
                            <span>My Playlists</span>
                        </a>

                        <a href="{{ route('library.tab', 'likes') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request()->is('library/likes') ? 'bg-gray-100' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.493 18.75c-.425 0-.82-.236-.975-.632A7.48 7.48 0 016 15.375c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75 2.25 2.25 0 012.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558-.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23h-.777zM2.331 10.977a11.969 11.969 0 00-.831 4.398 12 12 0 00.52 3.507c.26.85 1.084 1.368 1.973 1.368H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 01-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227z"/>
                            </svg>
                            <span>Liked videos</span>
                        </a>
                        
                        <a href="{{ route('library.tab', 'history') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request()->is('library/history') ? 'bg-gray-100' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>History</span>
                        </a>
                        @endauth
                        
                        <hr class="my-4">
                        
                        <a href="{{ route('videos.upload') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('videos.upload') ? 'bg-gray-100' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Upload</span>
                        </a>
                    </div>

                    <hr class="my-4">
                    
                    <div class="space-y-1">
                        <h3 class="px-3 text-sm font-semibold text-gray-600 uppercase tracking-wider">Browse</h3>
                        
                        @php
                            $browseCategories = [
                                'music' => ['Music', 'M12,3V13.55C11.41,13.21 10.73,13 10,13A4,4 0 0,0 6,17A4,4 0 0,0 10,21A4,4 0 0,0 14,17V7H18V3H12Z'],
                                'gaming' => ['Gaming', 'M17,10.5V7A1,1 0 0,0 16,6H4A1,1 0 0,0 3,7V10.5A1,1 0 0,0 4,11.5H16A1,1 0 0,0 17,10.5M12,2A1,1 0 0,0 11,3V6H13V3A1,1 0 0,0 12,2M21,9H18V7H16V9H15A1,1 0 0,0 14,10V15A1,1 0 0,0 15,16H16V18H18V16H21A1,1 0 0,0 22,15V10A1,1 0 0,0 21,9Z'],
                                'news' => ['News', 'M21,6V8H3V6H21M3,18H12V16H3V18M3,13H21V11H3V13Z'],
                                'sports' => ['Sports', 'M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z'],
                                'education' => ['Education', 'M12,3L1,9L12,15L21,10.09V17H23V9M5,13.18V17.18L12,21L19,17.18V13.18L12,17L5,13.18Z'],
                                'entertainment' => ['Entertainment', 'M18,4L20,8H17L15,4H13L15,8H12L10,4H8L10,8H7L5,4H4A2,2 0 0,0 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6A2,2 0 0,0 20,4H18Z'],
                                'technology' => ['Technology', 'M14.8,16H19V17.5H16.2L14.8,16M15.2,8H16.8L18.2,9.5H15.8L15.2,8M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z'],
                                'lifestyle' => ['Lifestyle', 'M12,2A2,2 0 0,1 14,4A2,2 0 0,1 12,6A2,2 0 0,1 10,4A2,2 0 0,1 12,2M21,9V7L15,1L13.5,2.5L16.75,5.75L14.5,8H9.5L7.25,5.75L10.5,2.5L9,1L3,7V9H21M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10Z'],
                                'other' => ['Other', 'M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z']
                            ];
                        @endphp
                        
                        @foreach($browseCategories as $key => $categoryData)
                            <a href="{{ route('home') }}?{{ http_build_query(['category' => $key]) }}" 
                               class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request('category') === $key ? 'bg-gray-100' : '' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="{{ $categoryData[1] }}"/>
                                </svg>
                                <span>{{ $categoryData[0] }}</span>
                            </a>
                        @endforeach
                    </div>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-3 md:p-6">
                {{ $slot }}
            </main>
        </div>

        <script>
            // Mobile sidebar toggle functionality
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarToggle = document.getElementById('sidebarToggle');
                const sidebar = document.getElementById('sidebar');
                const sidebarOverlay = document.getElementById('sidebarOverlay');
                
                function toggleSidebar() {
                    sidebar.classList.toggle('-translate-x-full');
                    sidebarOverlay.classList.toggle('hidden');
                }
                
                function closeSidebar() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                }
                
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', toggleSidebar);
                }
                
                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', closeSidebar);
                }
                
                // Close sidebar on window resize to desktop size
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 1280) { // xl breakpoint
                        closeSidebar();
                    }
                });
            });
        </script>

        @livewireScripts
    </body>
</html>
