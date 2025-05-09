<div>
    <form class="w-full max-w-md bg-white shadow-md rounded-lg p-6" method="POST" action="{{ route('videos.update', $video->vid) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <h2 class="text-lg font-semibold mb-4">Edit Video</h2>
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" wire:model.defer="title" name="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
        </div>
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <textarea id="description" wire:model.defer="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </textarea>
        </div>
        <div class="mb-4">
            <label for="visibility" class="block text-sm font-medium text-gray-700">Visibility</label>
            <select id="visibility" wire:model.defer="visibility" name="visibility" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                <option value="unpublished">Unpublished</option>
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="unlisted">Unlisted</option>
            </select>
        </div>
        @if ($error)
            <p class="text-red-500 text-sm mt-2">{{ $error }}</p>
        @endif
        <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">
            Update
        </button>
        <button type="button" wire:click="deleteVideo" class="bg-red-500 text-white font-bold py-2 px-4 rounded mt-4">
            Delete
        </button>
    </form>
</div>
