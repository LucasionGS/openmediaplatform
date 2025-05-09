@php
    use App\Models\Video;
    $videos = Video::inRandomOrder()->limit(5)->get();
@endphp

<div>
    <div class="flex flex-col items-center justify-center bg-gray-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 w-full">
            @foreach($videos as $video)
                <a wire:navigate href="{{ route('videos.edit', $video) }}" class="block p-4 shrink-0 w-full">
                    <div class="w-full aspect-video shrink-0 bg-white shadow-md rounded-lg">
                        <img src="{{ $video->getThumbnailUrl() }}" alt="{{ $video->title }}" class="w-full aspect-video shrink-0 rounded-lg">
                    </div>
                    <h2 class="text-xl font-semibold mb-2">{{ $video->title }}</h2>
                </a>
            @endforeach
        </div>
    </div>
</div>
