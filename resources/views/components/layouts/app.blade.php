<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    </head>
    <body>
        <div class="min-h-screen bg-gray-100">
            <header class="bg-white shadow">
                <div class="py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div class="left">
                        {{ $header ?? 'Header' }}
                    </div>
                    <div class="right">
                        @if (auth()->check())
                            <span class="text-gray-700">Welcome, {{ auth()->user()->name }}</span>
                            <a href="{{ route('logout') }}" class="ml-4 text-red-500">Logout</a>
                        @else
                            <livewire:login-component buttonText="Login" />
                        @endif
                    </div>
                </div>
            </header>

            <div class="flex">
                <!-- Sidebar -->
                <nav class="w-64 bg-gray-50 shadow">
                    <ul class="p-6">
                        <li><a wire:navigate href="/" class="block py-2 px-4 hover:bg-gray-100 hover:rounded-2xl">Home</a></li>
                        <li><a wire:navigate href="/subscriptions" class="block py-2 px-4 hover:bg-gray-100 hover:rounded-2xl">Subscriptions</a></li>
                        <li><a wire:navigate href="/videos/upload" class="block py-2 px-4 hover:bg-gray-100 hover:rounded-2xl">Upload</a></li>
                    </ul>

                    <!-- Upload video form -->
                    <livewire:upload-page />
                </nav>
                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
