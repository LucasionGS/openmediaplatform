<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Upload Video</h1>
        
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

        <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <!-- Video Upload Section -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Video File
                </label>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                    <input type="file" 
                           id="video" 
                           name="video" 
                           accept=".mp4,.mov,.avi,.mkv,.flv,.wmv,.webm"
                           class="hidden"
                           onchange="handleFileSelect(this)">
                    
                    <div id="dropZone" class="cursor-pointer" onclick="document.getElementById('video').click()">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-lg text-gray-600 mb-2">Click to select or drag and drop your video</p>
                        <p class="text-sm text-gray-500">MP4, MOV, AVI, MKV, FLV, WMV, WEBM (Max: 500MB)</p>
                    </div>

                    <div id="fileInfo" class="hidden mt-4 p-4 bg-gray-50 rounded">
                        <div class="flex items-center justify-between">
                            <div>
                                <p id="fileName" class="font-medium text-gray-900"></p>
                                <p id="fileSize" class="text-sm text-gray-600"></p>
                            </div>
                            <button type="button" onclick="clearFile()" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div id="progressContainer" class="hidden mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="progressText" class="text-sm text-gray-600 mt-1">Uploading...</p>
                        </div>
                    </div>
                </div>
                
                @error('video')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Video Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Video Title *
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required
                           maxlength="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter video title">
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
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a category</option>
                        <option value="music">Music</option>
                        <option value="gaming">Gaming</option>
                        <option value="sports">Sports</option>
                        <option value="news">News</option>
                        <option value="entertainment">Entertainment</option>
                        <option value="education">Education</option>
                        <option value="technology">Technology</option>
                        <option value="travel">Travel</option>
                        <option value="food">Food</option>
                        <option value="lifestyle">Lifestyle</option>
                    </select>
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
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Tell viewers about your video"></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    Tags (comma separated)
                </label>
                <input type="text" 
                       id="tags" 
                       name="tags"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g. tutorial, coding, web development">
                <p class="mt-1 text-sm text-gray-500">Separate tags with commas to help people find your video</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Visibility
                </label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="visibility" value="public" checked class="form-radio">
                        <span class="ml-2">Public - Anyone can search for and view</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="visibility" value="unlisted" class="form-radio">
                        <span class="ml-2">Unlisted - Anyone with the link can view</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="visibility" value="private" class="form-radio">
                        <span class="ml-2">Private - Only you can view</span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                    Save as Draft
                </button>
                <button type="submit" 
                        id="uploadBtn"
                        disabled
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                    Upload Video
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const MAX_FILE_SIZE = 500 * 1024 * 1024; // 500MB in bytes

function handleFileSelect(input) {
    const file = input.files[0];
    const uploadBtn = document.getElementById('uploadBtn');
    
    if (!file) {
        clearFile();
        return;
    }

    // Check file size
    if (file.size > MAX_FILE_SIZE) {
        alert(`File size (${formatFileSize(file.size)}) exceeds the maximum limit of 500MB. Please choose a smaller file.`);
        input.value = '';
        clearFile();
        return;
    }

    // Check file type
    const allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/x-flv', 'video/x-ms-wmv', 'video/webm'];
    if (!allowedTypes.includes(file.type)) {
        alert('Please select a valid video file (MP4, MOV, AVI, MKV, FLV, WMV, WEBM)');
        input.value = '';
        clearFile();
        return;
    }

    // Display file info
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    document.getElementById('fileInfo').classList.remove('hidden');
    document.getElementById('dropZone').classList.add('hidden');
    
    // Enable upload button
    uploadBtn.disabled = false;
}

function clearFile() {
    document.getElementById('video').value = '';
    document.getElementById('fileInfo').classList.add('hidden');
    document.getElementById('dropZone').classList.remove('hidden');
    document.getElementById('uploadBtn').disabled = true;
    document.getElementById('progressContainer').classList.add('hidden');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Handle drag and drop
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
    
    if (files.length > 0) {
        document.getElementById('video').files = files;
        handleFileSelect(document.getElementById('video'));
    }
}

// Form submission with progress
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('video');
    if (!fileInput.files[0]) {
        e.preventDefault();
        alert('Please select a video file to upload.');
        return;
    }

    // Show progress
    document.getElementById('progressContainer').classList.remove('hidden');
    document.getElementById('uploadBtn').disabled = true;
    document.getElementById('uploadBtn').textContent = 'Uploading...';
    
    // Simulate progress (in a real implementation, you'd use XHR or fetch with progress tracking)
    let progress = 0;
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        
        progressBar.style.width = progress + '%';
        progressText.textContent = `Uploading... ${Math.round(progress)}%`;
    }, 500);
    
    // Clear interval after 30 seconds (form should submit by then)
    setTimeout(() => clearInterval(interval), 30000);
});
</script>
