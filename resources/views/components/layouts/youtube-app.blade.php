<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'OpenMediaPlatform' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        @livewireStyles
    </head>
    <body class="bg-gray-50">
        <!-- YouTube-like Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="flex items-center justify-between px-4 py-2">
                <!-- Logo and Menu -->
                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:bg-gray-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-red-600 rounded-sm flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">OpenMedia</span>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-4">
                    <form class="flex" method="GET" action="{{ route('search') }}">
                        <div class="flex w-full">
                            <input type="text" 
                                   name="q"
                                   value="{{ request('q') }}"
                                   placeholder="Search" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-l-full focus:outline-none focus:border-blue-500">
                            <button type="submit" 
                                    class="px-6 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-full hover:bg-gray-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="flex items-center space-x-2">
                    @auth
                        <a href="{{ route('upload') }}" class="p-2 hover:bg-gray-100 rounded-full" title="Create">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </a>
                        <button class="p-2 hover:bg-gray-100 rounded-full" title="Notifications">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            </svg>
                        </button>
                        <div class="relative">
                            <button class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </button>
                        </div>
                    @else
                        <livewire:login-component buttonText="Sign in" />
                    @endauth
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-sm h-screen sticky top-16 overflow-y-auto">
                <nav class="p-4">
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('home') ? 'bg-gray-100' : '' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                            <span>Home</span>
                        </a>
                        
                        @auth
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                            <span>Subscriptions</span>
                        </a>

                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                            </svg>
                            <span>Library</span>
                        </a>
                        
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13,3A9,9 0 0,0 4,12H1L4.89,15.89L4.96,16.03L9,12H6A7,7 0 0,1 13,5A7,7 0 0,1 20,12A7,7 0 0,1 13,19C11.07,19 9.32,18.21 8.06,16.94L6.64,18.36C8.27,20 10.5,21 13,21A9,9 0 0,0 22,12A9,9 0 0,0 13,3Z"/>
                            </svg>
                            <span>History</span>
                        </a>
                        @endauth
                    </div>

                    <hr class="my-4">
                    
                    <div class="space-y-1">
                        <h3 class="px-3 text-sm font-semibold text-gray-600 uppercase tracking-wider">Browse</h3>
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12,3V13.55C11.41,13.21 10.73,13 10,13A4,4 0 0,0 6,17A4,4 0 0,0 10,21A4,4 0 0,0 14,17V7H18V3H12Z"/>
                            </svg>
                            <span>Music</span>
                        </a>
                        
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17,10.5V7A1,1 0 0,0 16,6H4A1,1 0 0,0 3,7V10.5A1,1 0 0,0 4,11.5H16A1,1 0 0,0 17,10.5M12,2A1,1 0 0,0 11,3V6H13V3A1,1 0 0,0 12,2M21,9H18V7H16V9H15A1,1 0 0,0 14,10V15A1,1 0 0,0 15,16H16V18H18V16H21A1,1 0 0,0 22,15V10A1,1 0 0,0 21,9Z"/>
                            </svg>
                            <span>Gaming</span>
                        </a>
                        
                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6,2L4,6V18A2,2 0 0,0 6,20H18A2,2 0 0,0 20,18V6L18,2H6M7,4H17L18.25,6H5.75L7,4M6,8H18V18H6V8Z"/>
                            </svg>
                            <span>Sports</span>
                        </a>

                        <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21,6V8H3V6H21M3,18H12V16H3V18M3,13H21V11H3V13Z"/>
                            </svg>
                            <span>News</span>
                        </a>
                    </div>

                    @auth
                    <hr class="my-4">
                    <div class="space-y-1">
                        <livewire:upload-page />
                    </div>
                    @endauth
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 min-h-screen">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
