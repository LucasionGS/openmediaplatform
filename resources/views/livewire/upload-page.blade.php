<div>
    <!-- Upload page for a new video -->
    <div class="flex flex-col items-center justify-center bg-gray-100">
        <form class="w-full max-w-md bg-white shadow-md rounded-lg p-6" method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data">
            @csrf
            <h2 class="text-lg font-semibold mb-4">Upload Video</h2>
            <div class="mb-4">
                <label for="video" class="block text-sm font-medium text-gray-700">Video File</label>
                <input type="file" id="video" wire:model.defer="video" name="video" accept=".mp4, .mov, .avi, .mkv, .flv, .wmv" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            @if ($error)
                <p class="text-red-500 text-sm mt-2">{{ $error }}</p>
            @endif
            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">
                Upload
            </button>
        </form>
    </div>
</div>
