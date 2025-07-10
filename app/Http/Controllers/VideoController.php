<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Traits\HandlesUploadLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    use HandlesUploadLimits;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all videos
        $videos = Video::all();

        // Return the view with the videos
        return $videos;
    }

    public function getThumbnail(string $videoId)
    {
        // Fetch the video by ID
        $video = Video::find($videoId);

        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }
        
        $thumbnailPath = $video->getThumbnailPath();
        
        // If thumbnail doesn't exist, try to generate it
        if (!file_exists($thumbnailPath)) {
            \Log::info("Thumbnail not found, attempting to generate: " . $thumbnailPath);
            $video->generateThumbnail($video->duration ? $video->duration / 2 : 1);
        }
        
        // If thumbnail still doesn't exist, return a default placeholder
        if (!file_exists($thumbnailPath)) {
            \Log::warning("Could not generate thumbnail, returning default");
            return $this->getDefaultThumbnail();
        }
        
        // Return the thumbnail file
        return response()->file($thumbnailPath, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . $video->vid . '.jpg"',
        ]);
    }

    private function getDefaultThumbnail()
    {
        // Create a simple default thumbnail in memory
        $width = 1280;
        $height = 720;
        $image = imagecreate($width, $height);
        
        // Set colors
        $background = imagecolorallocate($image, 45, 45, 45); // Dark gray
        $textColor = imagecolorallocate($image, 255, 255, 255); // White
        $accentColor = imagecolorallocate($image, 255, 0, 0); // Red
        
        // Fill background
        imagefill($image, 0, 0, $background);
        
        // Draw play button (triangle)
        $playButton = [
            $width/2 - 50, $height/2 - 30,
            $width/2 - 50, $height/2 + 30,
            $width/2 + 40, $height/2
        ];
        imagefilledpolygon($image, $playButton, 3, $accentColor);
        
        // Add text
        $text = "Video Thumbnail";
        $textX = ($width - strlen($text) * 10) / 2;
        imagestring($image, 3, $textX, $height/2 + 50, $text, $textColor);
        
        // Output as JPEG
        ob_start();
        imagejpeg($image, null, 85);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        
        return response($imageData, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="default-thumbnail.jpg"',
        ]);
    }

    public function setThumbnail(Request $request, string $videoId)
    {
        // Fetch the video by ID
        $video = Video::find($videoId);

        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        // Check authorization
        if (!auth()->check() || auth()->id() !== $video->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the request
        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Ensure thumbnail directory exists
            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Delete old thumbnail if exists
            $oldThumbnailPath = $video->getThumbnailPath();
            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath);
            }

            // Store the new thumbnail with proper filename
            $request->file('thumbnail')->storeAs('thumbnails', $video->vid . '.jpg', 'public');

            return response()->json(['message' => 'Thumbnail uploaded successfully'], 200);

        } catch (\Exception $e) {
            \Log::error('Error uploading thumbnail: ' . $e->getMessage());
            return response()->json(['message' => 'Error uploading thumbnail'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be signed in to upload videos.');
        }

        try {
            // Check for upload errors
            $uploadError = $request->file('video') ? $request->file('video')->getError() : UPLOAD_ERR_NO_FILE;
            
            if ($uploadError !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the maximum file size allowed by the server.',
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the maximum file size specified in the form.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                ];
                
                $message = $errorMessages[$uploadError] ?? 'An unknown upload error occurred.';
                
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                
                return back()->withErrors(['video' => $message])->withInput();
            }

            // Get maximum upload size from PHP configuration
            $maxUploadSizeKB = $this->getMaxUploadSizeInKB();
            $maxUploadSizeMB = $this->formatBytes($maxUploadSizeKB * 1024);

            // Validate the request
            $request->validate([
                'video' => 'required|file|mimes:mp4,mov,avi,wmv,mkv,flv,webm|max:' . $maxUploadSizeKB,
                'title' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'category' => 'nullable|string|max:50',
                'tags' => 'nullable|string|max:500',
                'visibility' => 'required|in:public,private,unlisted',
            ], [
                'video.max' => "The video file must not be larger than {$maxUploadSizeMB}.",
                'video.mimes' => 'The video must be a file of type: mp4, mov, avi, wmv, mkv, flv, webm.',
            ]);

            // Create video record
            $video = new Video();
            $video->title = $request->title;
            $video->description = $request->description;
            $video->category = $request->category;
            $video->visibility = $request->visibility === 'public' ? Video::VISIBILITY_PUBLIC : 
                                ($request->visibility === 'private' ? Video::VISIBILITY_PRIVATE : Video::VISIBILITY_UNLISTED);
            $video->user_id = auth()->id();
            $video->published_at = $request->visibility === 'public' ? now() : null;
            
            // Process tags
            if ($request->tags) {
                $tags = array_map('trim', explode(',', $request->tags));
                $tags = array_filter($tags); // Remove empty tags
                $video->tags = $tags;
            }

            $video->save();

            // Store the video file
            $videoFile = $request->file('video');
            $videoFile->storeAs('videos', $video->vid, 'public');
            
            // Calculate duration and generate thumbnail
            try {
                \Log::info("Starting duration calculation for video: {$video->vid}");
                $video->duration = $video->calculateDuration(save: true);
                \Log::info("Duration calculated: {$video->duration} seconds");
                
                \Log::info("Starting thumbnail generation for video: {$video->vid}");
                $thumbnailPath = $video->generateThumbnail($video->duration / 2);
                \Log::info("Thumbnail generation completed. Path: " . ($thumbnailPath ?? 'null'));
                
            } catch (\Exception $e) {
                \Log::error("Error during duration/thumbnail processing: " . $e->getMessage());
                // Continue with upload even if thumbnail generation fails
            }

            $video->save();

            // Return appropriate response based on request type
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Video uploaded successfully!',
                    'redirect' => route('videos.show', ['video' => $video->vid])
                ]);
            }

            return redirect()->route('videos.show', ['video' => $video->vid])
                ->with('message', 'Video uploaded successfully!');

        } catch (\Exception $e) {
            \Log::error('Video upload failed: ' . $e->getMessage());
            
            // Get maximum upload size for error message
            $maxUploadSizeKB = $this->getMaxUploadSizeInKB();
            $maxUploadSizeMB = $this->formatBytes($maxUploadSizeKB * 1024);
            
            // Check if it's a file size error
            if (strpos($e->getMessage(), 'file is too large') !== false || 
                strpos($e->getMessage(), 'POST Content-Length') !== false) {
                $errorMessage = "The video file is too large. Please ensure your video is under {$maxUploadSizeMB} and try again.";
            } else {
                $errorMessage = 'An error occurred while uploading your video. Please try again.';
            }
            
            // Return appropriate response based on request type
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return back()->withErrors(['video' => $errorMessage])->withInput();
        }
    }

    public function raw(Video $video)
    {
        // Fetch the video by ID
        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        // Stream the video file
        $path = $video->getPath();
        $size = $video->getSize();
        $stream = fopen($path, 'rb');

        $headers = [
            'Content-Type' => 'video/mp4',
            'Content-Disposition' => 'inline; filename="' . $video->vid . '.mp4"',
            'Accept-Ranges' => 'bytes',
        ];

        $start = 0;
        $length = $size;

        if (isset($_SERVER['HTTP_RANGE'])) {
            $range = $_SERVER['HTTP_RANGE'];
            list(, $range) = explode('=', $range, 2);
            list($start, $end) = explode('-', $range);
            $start = intval($start);
            $end = $end ? intval($end) : $size - 1;
            $length = $end - $start + 1;

            fseek($stream, $start);
            $headers['Content-Range'] = "bytes $start-$end/$size";
            $headers['Content-Length'] = $length;
        } else {
            $headers['Content-Length'] = $size;
        }

        return response()->stream(function () use ($stream, $length) {
            echo fread($stream, $length);
            fclose($stream);
        }, isset($headers['Content-Range']) ? 206 : 200, $headers);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        // Check authorization
        if (!auth()->check() || auth()->id() !== $video->user_id) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:' . implode(',', [
                Video::VISIBILITY_PUBLIC,
                Video::VISIBILITY_PRIVATE,
                Video::VISIBILITY_UNLISTED,
                Video::VISIBILITY_UNPUBLISHED,
            ]),
        ]);

        // Update the video details
        $video->update($request->only('title', 'description', 'visibility'));

        // Generate a thumbnail
        $video->calculateDuration(save: true);
        $video->generateThumbnail($video->duration / 2);

        return redirect()->route('videos.edit', ['video' => $video->vid])
            ->with('success', 'Video updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        // Check authorization
        if (!auth()->check() || auth()->id() !== $video->user_id) {
            abort(403, 'Unauthorized');
        }

        try {
            // Delete video record from database (files will be deleted automatically via model event)
            $video->delete();

            return redirect()->route('channel.show', auth()->user())
                ->with('success', 'Video deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Error deleting video: ' . $e->getMessage());
            return back()->with('error', 'Error deleting video. Please try again.');
        }
    }

    /**
     * Get upload configuration for frontend
     */
    public function getUploadConfig()
    {
        $maxUploadSizeBytes = $this->getMaxUploadSize();
        $maxUploadSizeKB = $this->getMaxUploadSizeInKB();
        $maxUploadSizeMB = $this->formatBytes($maxUploadSizeBytes);
        
        return response()->json([
            'max_size_bytes' => $maxUploadSizeBytes,
            'max_size_kb' => $maxUploadSizeKB,
            'max_size_formatted' => $maxUploadSizeMB,
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit')
        ]);
    }
}
