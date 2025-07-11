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
                        <p class="text-sm text-gray-500">MP4, MOV, AVI, MKV, FLV, WMV, WEBM (Max: {{ $maxUploadSizeFormatted }})</p>
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
            <!-- Video Details Section -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Video Details</h2>
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
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
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
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                              placeholder="Tell viewers about your video"></textarea>
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
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                           placeholder="e.g. tutorial, coding, web development">
                    <p class="mt-1 text-sm text-gray-500">Separate tags with commas to help people find your video</p>
                </div>
            </div>

            <!-- Privacy & Publishing -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Privacy & Publishing</h2>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Who can see this video?
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors group">
                            <input type="radio" name="visibility" value="public" checked 
                                   class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500 focus:ring-2 mt-1">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900 group-hover:text-red-600">Public</div>
                                <div class="text-sm text-gray-500">Anyone can search for and view this video</div>
                            </div>
                        </label>
                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors group">
                            <input type="radio" name="visibility" value="unlisted" 
                                   class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500 focus:ring-2 mt-1">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900 group-hover:text-red-600">Unlisted</div>
                                <div class="text-sm text-gray-500">Anyone with the link can view this video</div>
                            </div>
                        </label>
                        <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors group">
                            <input type="radio" name="visibility" value="private" 
                                   class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500 focus:ring-2 mt-1">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900 group-hover:text-red-600">Private</div>
                                <div class="text-sm text-gray-500">Only you can view this video</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t border-gray-200">
                <button type="button" 
                        id="draftBtn"
                        onclick="saveAsDraft()"
                        disabled
                        class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    <span id="draftText">Save as Draft</span>
                    <svg id="draftSpinner" class="hidden animate-spin -mr-1 ml-3 h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
                <button type="submit" 
                        id="uploadBtn"
                        disabled
                        class="px-8 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors font-medium">
                    <span id="uploadText">Upload Video</span>
                    <svg id="uploadSpinner" class="hidden animate-spin -mr-1 ml-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const MAX_FILE_SIZE = {{ $maxUploadSize }}; // Dynamic limit from PHP configuration

function handleFileSelect(input) {
    const file = input.files[0];
    const uploadBtn = document.getElementById('uploadBtn');
    
    if (!file) {
        clearFile();
        return;
    }

    // Check file size
    if (file.size > MAX_FILE_SIZE) {
        alert(`File size (${formatFileSize(file.size)}) exceeds the maximum limit of {{ $maxUploadSizeFormatted }}. Please choose a smaller file.`);
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
    
    // Enable both upload and draft buttons
    uploadBtn.disabled = false;
    document.getElementById('draftBtn').disabled = false;
}

function clearFile() {
    document.getElementById('video').value = '';
    document.getElementById('fileInfo').classList.add('hidden');
    document.getElementById('dropZone').classList.remove('hidden');
    document.getElementById('uploadBtn').disabled = true;
    document.getElementById('draftBtn').disabled = true;
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
    document.getElementById('uploadText').textContent = 'Uploading...';
    document.getElementById('uploadSpinner').classList.remove('hidden');
    
    // Real upload with XMLHttpRequest for progress tracking
    e.preventDefault();
    
    const formData = new FormData(this);
    const xhr = new XMLHttpRequest();
    
    // Track upload progress
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            document.getElementById('progressBar').style.width = percentComplete + '%';
            document.getElementById('progressText').textContent = `Uploading... ${Math.round(percentComplete)}%`;
        }
    });
    
    // Handle completion
    xhr.addEventListener('load', function() {
        console.log('Upload completed with status:', xhr.status);
        console.log('Response text:', xhr.responseText);
        
        if (xhr.status === 200) {
            document.getElementById('progressText').textContent = 'Upload complete! Processing...';
            // Parse response and redirect
            try {
                const response = JSON.parse(xhr.responseText);
                console.log('Parsed response:', response);
                
                if (response.success && response.redirect) {
                    console.log('Redirecting to:', response.redirect);
                    window.location.href = response.redirect;
                } else if (response.redirect) {
                    console.log('Redirecting to:', response.redirect);
                    window.location.href = response.redirect;
                } else {
                    console.log('No redirect found, going to homepage');
                    // Fallback - go to videos list or reload
                    window.location.href = '/';
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                console.log('Response text:', xhr.responseText);
                // Try to redirect anyway or reload
                window.location.href = '/';
            }
        } else {
            // Handle HTTP errors
            try {
                const response = JSON.parse(xhr.responseText);
                document.getElementById('progressText').textContent = response.message || 'Upload failed. Please try again.';
            } catch (e) {
                document.getElementById('progressText').textContent = 'Upload failed. Please try again.';
            }
            document.getElementById('progressBar').classList.remove('bg-blue-600');
            document.getElementById('progressBar').classList.add('bg-red-500');
            resetUploadButton();
        }
    });
    
    // Handle errors
    xhr.addEventListener('error', function() {
        document.getElementById('progressText').textContent = 'Upload failed. Please check your connection.';
        document.getElementById('progressBar').classList.remove('bg-blue-600');
        document.getElementById('progressBar').classList.add('bg-red-500');
        resetUploadButton();
    });
    
    // Handle timeout
    xhr.addEventListener('timeout', function() {
        document.getElementById('progressText').textContent = 'Upload timed out. Please try again.';
        document.getElementById('progressBar').classList.remove('bg-blue-600');
        document.getElementById('progressBar').classList.add('bg-red-500');
        resetUploadButton();
    });
    
    // Set timeout to 15 minutes
    xhr.timeout = 900000;
    
    // Start upload
    xhr.open('POST', this.action);
    
    // Get CSRF token from the form's hidden input (added by @csrf directive)
    const csrfInput = document.querySelector('input[name="_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '{{ csrf_token() }}';
    
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.send(formData);
});

function resetUploadButton() {
    document.getElementById('uploadBtn').disabled = false;
    document.getElementById('uploadText').textContent = 'Upload Video';
    document.getElementById('uploadSpinner').classList.add('hidden');
    document.getElementById('draftBtn').disabled = false;
    document.getElementById('draftText').textContent = 'Save as Draft';
    document.getElementById('draftSpinner').classList.add('hidden');
}

function saveAsDraft() {
    // Get the form
    const form = document.getElementById('uploadForm');
    
    // Validate required fields
    const title = document.getElementById('title').value.trim();
    if (!title) {
        alert('Please enter a video title before saving as draft.');
        document.getElementById('title').focus();
        return;
    }
    
    const videoFile = document.getElementById('video').files[0];
    if (!videoFile) {
        alert('Please select a video file before saving as draft.');
        return;
    }
    
    // Disable buttons and show loading state
    document.getElementById('draftBtn').disabled = true;
    document.getElementById('uploadBtn').disabled = true;
    document.getElementById('draftText').textContent = 'Saving...';
    document.getElementById('draftSpinner').classList.remove('hidden');
    
    // Show progress container
    document.getElementById('progressContainer').classList.remove('hidden');
    document.getElementById('progressText').textContent = 'Saving draft...';
    
    // Create FormData and add draft flag
    const formData = new FormData(form);
    formData.append('save_as_draft', '1');
    
    // Create XMLHttpRequest
    const xhr = new XMLHttpRequest();
    
    // Handle progress
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = Math.round((e.loaded / e.total) * 100);
            document.getElementById('progressBar').style.width = percentComplete + '%';
            document.getElementById('progressText').textContent = `Saving draft... ${percentComplete}%`;
        }
    });
    
    // Handle completion
    xhr.addEventListener('load', function() {
        console.log('Draft upload response status:', xhr.status);
        console.log('Draft upload response text:', xhr.responseText);
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                console.log('Parsed draft response:', response);
                if (response.success) {
                    document.getElementById('progressText').textContent = response.message;
                    document.getElementById('progressBar').style.width = '100%';
                    
                    // Redirect after a short delay
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    console.error('Draft save failed:', response.message);
                    throw new Error(response.message || 'Draft save failed');
                }
            } catch (e) {
                console.error('Error parsing draft response:', e);
                console.error('Raw response text:', xhr.responseText);
                document.getElementById('progressText').textContent = 'Draft save failed. Please try again.';
                document.getElementById('progressBar').classList.remove('bg-blue-600');
                document.getElementById('progressBar').classList.add('bg-red-500');
                resetUploadButton();
            }
        } else {
            console.error('Draft upload HTTP error:', xhr.status, xhr.statusText);
            console.error('Draft upload response:', xhr.responseText);
            document.getElementById('progressText').textContent = 'Draft save failed. Please try again.';
            document.getElementById('progressBar').classList.remove('bg-blue-600');
            document.getElementById('progressBar').classList.add('bg-red-500');
            resetUploadButton();
        }
    });
    
    // Handle errors
    xhr.addEventListener('error', function() {
        document.getElementById('progressText').textContent = 'Draft save failed. Please check your connection.';
        document.getElementById('progressBar').classList.remove('bg-blue-600');
        document.getElementById('progressBar').classList.add('bg-red-500');
        resetUploadButton();
    });
    
    // Set timeout
    xhr.timeout = 900000; // 15 minutes
    
    // Start upload
    xhr.open('POST', form.action);
    
    // Get CSRF token from the form's hidden input (added by @csrf directive)
    const csrfInput = document.querySelector('input[name="_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '{{ csrf_token() }}';
    
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.send(formData);
}
</script>
