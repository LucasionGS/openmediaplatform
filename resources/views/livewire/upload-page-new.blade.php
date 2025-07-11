<x-layouts.app>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Upload Video</h1>
        
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data">
                @csrf
                
                <!-- Video Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select files to upload
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition-colors">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-lg font-semibold text-gray-700 mb-2">Drag and drop video files to upload</p>
                        <p class="text-sm text-gray-500 mb-4">Your videos will be private until you publish them.</p>
                        <input type="file" 
                                name="video"
                                accept="video/*"
                                required
                                class="hidden" 
                                id="video-upload">
                        <label for="video-upload" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 cursor-pointer">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            SELECT FILES
                        </label>
                    </div>
                    @error('video') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Video Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                Title (required)
                            </label>
                            <input type="text" 
                                    id="title"
                                    name="title"
                                    value="{{ old('title') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Add a title that describes your video"
                                    maxlength="100">
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="8"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Tell viewers about your video">{{ old('description') }}</textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                                Category
                            </label>
                            <select id="category"
                                    name="category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select a category</option>
                                <option value="entertainment" {{ old('category') === 'entertainment' ? 'selected' : '' }}>Entertainment</option>
                                <option value="education" {{ old('category') === 'education' ? 'selected' : '' }}>Education</option>
                                <option value="music" {{ old('category') === 'music' ? 'selected' : '' }}>Music</option>
                                <option value="gaming" {{ old('category') === 'gaming' ? 'selected' : '' }}>Gaming</option>
                                <option value="sports" {{ old('category') === 'sports' ? 'selected' : '' }}>Sports</option>
                                <option value="technology" {{ old('category') === 'technology' ? 'selected' : '' }}>Technology</option>
                                <option value="travel" {{ old('category') === 'travel' ? 'selected' : '' }}>Travel & Events</option>
                                <option value="food" {{ old('category') === 'food' ? 'selected' : '' }}>Food & Cooking</option>
                                <option value="lifestyle" {{ old('category') === 'lifestyle' ? 'selected' : '' }}>Lifestyle</option>
                                <option value="news" {{ old('category') === 'news' ? 'selected' : '' }}>News & Politics</option>
                                <option value="science" {{ old('category') === 'science' ? 'selected' : '' }}>Science & Technology</option>
                                <option value="pets" {{ old('category') === 'pets' ? 'selected' : '' }}>Pets & Animals</option>
                            </select>
                            @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Thumbnail Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Thumbnail (optional)
                            </label>
                            <div class="w-full aspect-video bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Upload custom thumbnail</p>
                                </div>
                            </div>
                            <input type="file" 
                                    name="thumbnail"
                                    accept="image/*"
                                    class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('thumbnail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Visibility -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Visibility
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="visibility" value="private" {{ old('visibility', 'private') === 'private' ? 'checked' : '' }} class="text-blue-600">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-700">Private</span>
                                        <span class="block text-xs text-gray-500">Only you can see this video</span>
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="visibility" value="unlisted" {{ old('visibility') === 'unlisted' ? 'checked' : '' }} class="text-blue-600">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-700">Unlisted</span>
                                        <span class="block text-xs text-gray-500">Anyone with the link can see this video</span>
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="visibility" value="public" {{ old('visibility') === 'public' ? 'checked' : '' }} class="text-blue-600">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-700">Public</span>
                                        <span class="block text-xs text-gray-500">Everyone can see this video</span>
                                    </span>
                                </label>
                            </div>
                            @error('visibility') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 mt-8">
                    <button type="button" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Save as draft
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Upload Video
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>