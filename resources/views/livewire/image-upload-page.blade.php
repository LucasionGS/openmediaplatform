<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Upload Images</h1>
        
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('images.store') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <!-- Image Upload Section -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Image Files
                </label>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                    <input type="file" 
                           id="images" 
                           name="images[]" 
                           accept="image/*"
                           multiple
                           class="hidden"
                           onchange="handleFileSelect(this)">
                    
                    <div id="dropZone" class="cursor-pointer" onclick="document.getElementById('images').click()">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-lg text-gray-600 mb-2">Click to select or drag and drop your images</p>
                        <p class="text-sm text-gray-500">JPEG, PNG, JPG, GIF, WebP (Max: {{ $maxImageCount }} images, {{ $maxImageSizeMB }}MB each)</p>
                    </div>

                    <div id="fileInfo" class="hidden mt-4">
                        <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4"></div>
                        <div id="progressContainer" class="hidden mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="progressText" class="text-sm text-gray-600 mt-1">Uploading...</p>
                        </div>
                    </div>
                </div>
                
                @error('images')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Details Section -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Image Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Image Post Title *
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               required
                               maxlength="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Enter title for your image post">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Category
                        </label>
                        <select id="category" 
                                name="category"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select a category</option>
                            <option value="art">Art & Design</option>
                            <option value="photography">Photography</option>
                            <option value="nature">Nature</option>
                            <option value="technology">Technology</option>
                            <option value="food">Food & Cooking</option>
                            <option value="travel">Travel</option>
                            <option value="lifestyle">Lifestyle</option>
                            <option value="sports">Sports</option>
                            <option value="entertainment">Entertainment</option>
                            <option value="education">Education</option>
                            <option value="business">Business</option>
                            <option value="other">Other</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description"
                              rows="4"
                              maxlength="1000"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                              placeholder="Describe your images..."></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                        Tags
                    </label>
                    <input type="text" 
                           id="tags" 
                           name="tags"
                           maxlength="500"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Enter tags separated by commas (e.g., nature, landscape, sunset)">
                    <p class="text-sm text-gray-500 mt-1">Separate tags with commas</p>
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Visibility & Publishing Options -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Visibility & Publishing</h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Visibility *</label>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="visibility-public" 
                                   name="visibility" 
                                   type="radio" 
                                   value="public"
                                   checked
                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <label for="visibility-public" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">Public</span> - Anyone can see this image post
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="visibility-unlisted" 
                                   name="visibility" 
                                   type="radio" 
                                   value="unlisted"
                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <label for="visibility-unlisted" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">Unlisted</span> - Only people with the link can see this
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="visibility-private" 
                                   name="visibility" 
                                   type="radio" 
                                   value="private"
                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <label for="visibility-private" class="ml-3 block text-sm text-gray-700">
                                <span class="font-medium">Private</span> - Only you can see this image post
                            </label>
                        </div>
                    </div>
                    @error('visibility')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <div class="flex items-center">
                        <input id="save-as-draft" 
                               name="save_as_draft" 
                               type="checkbox"
                               value="1"
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="save-as-draft" class="ml-3 block text-sm text-gray-700">
                            Save as draft (you can publish it later)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('home') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                
                <button type="submit" 
                        id="submitBtn"
                        class="px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="submitText">Upload Images</span>
                    <span id="submitLoader" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedFiles = [];

// Get dynamic limits from Livewire component
const MAX_IMAGE_SIZE_KB = {{ $maxImageSize }};
const MAX_IMAGE_SIZE_BYTES = MAX_IMAGE_SIZE_KB * 1024;
const MAX_IMAGE_COUNT = {{ $maxImageCount }};
const MAX_IMAGE_SIZE_MB = {{ $maxImageSizeMB }};

// Drag and drop functionality
const dropZone = document.getElementById('dropZone');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('border-blue-500', 'bg-blue-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-blue-500', 'bg-blue-50');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
}

function handleFileSelect(input) {
    const files = input.files;
    handleFiles(files);
}

function handleFiles(files) {
    selectedFiles = Array.from(files).filter(file => {
        return file.type.startsWith('image/') && file.size <= MAX_IMAGE_SIZE_BYTES;
    });

    if (selectedFiles.length === 0) {
        alert(`Please select valid image files (max ${MAX_IMAGE_SIZE_MB}MB each)`);
        return;
    }

    if (selectedFiles.length > MAX_IMAGE_COUNT) {
        alert(`Maximum ${MAX_IMAGE_COUNT} images allowed`);
        selectedFiles = selectedFiles.slice(0, MAX_IMAGE_COUNT);
    }

    displayImagePreviews();
    document.getElementById('fileInfo').classList.remove('hidden');
}

function displayImagePreviews() {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${e.target.result}" 
                     alt="Preview ${index + 1}"
                     class="w-full h-24 object-cover rounded-lg">
                <button type="button" 
                        onclick="removeImage(${index})"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                    ×
                </button>
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg truncate">
                    ${file.name}
                </div>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    
    if (selectedFiles.length === 0) {
        document.getElementById('fileInfo').classList.add('hidden');
        document.getElementById('images').value = '';
    } else {
        displayImagePreviews();
    }
    
    // Update the file input
    const input = document.getElementById('images');
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

// Form submission with progress
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    if (selectedFiles.length === 0) {
        e.preventDefault();
        alert('Please select at least one image to upload');
        return;
    }

    // Show loading state
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitText').classList.add('hidden');
    document.getElementById('submitLoader').classList.remove('hidden');
    document.getElementById('progressContainer').classList.remove('hidden');

    // Simulate upload progress (in real implementation, this would be handled by the server)
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 95) progress = 95;
        
        document.getElementById('progressBar').style.width = progress + '%';
        document.getElementById('progressText').textContent = `Uploading... ${Math.round(progress)}%`;
        
        if (progress >= 95) {
            clearInterval(interval);
            document.getElementById('progressText').textContent = 'Processing...';
        }
    }, 200);
});

// Update draft text when checkbox changes
document.getElementById('save-as-draft').addEventListener('change', function() {
    const submitText = document.getElementById('submitText');
    if (this.checked) {
        submitText.textContent = 'Save as Draft';
    } else {
        submitText.textContent = 'Upload Images';
    }
});
</script>
