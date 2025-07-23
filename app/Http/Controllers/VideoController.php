<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Traits\HandlesUploadLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        Log::info('Upload started', [
            'file_size' => $request->file('video')->getSize(),
            'memory_usage' => $startMemory,
            'time' => $startTime
        ]);
        
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

            // Determine if this is a draft
            $isDraft = $request->boolean('save_as_draft');

            // Validate the request with conditional visibility requirement
            $rules = [
                'video' => 'required|file|mimes:mp4,mov,avi,wmv,mkv,flv,webm|max:' . $maxUploadSizeKB,
                'title' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'category' => 'nullable|string|max:50',
                'tags' => 'nullable|string|max:500',
                'save_as_draft' => 'nullable|boolean',
            ];

            // Only require visibility if not saving as draft
            if (!$isDraft) {
                $rules['visibility'] = 'required|in:public,private,unlisted';
            }

            $request->validate($rules, [
                'video.max' => "The video file must not be larger than {$maxUploadSizeMB}.",
                'video.mimes' => 'The video must be a file of type: mp4, mov, avi, wmv, mkv, flv, webm.',
            ]);

            // Create video record
            $video = new Video();
            $video->title = $request->title;
            $video->description = $request->description;
            $video->category = $request->category;
            
            // Determine visibility and publication status
            
            if ($isDraft) {
                // Save as draft - set visibility to unpublished regardless of form selection
                $video->visibility = Video::VISIBILITY_UNPUBLISHED;
                $video->published_at = null;
            } else {
                // Normal upload - use the selected visibility
                $video->visibility = $request->visibility === 'public' ? Video::VISIBILITY_PUBLIC : 
                                    ($request->visibility === 'private' ? Video::VISIBILITY_PRIVATE : Video::VISIBILITY_UNLISTED);
                $video->published_at = $request->visibility === 'public' ? now() : null;
            }
            
            $video->user_id = auth()->id();
            
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

            // Log after file move
            Log::info('After file move', [
                'memory_usage' => memory_get_usage(true),
                'elapsed' => microtime(true) - $startTime
            ]);

            // Return appropriate response based on request type
            $successMessage = $isDraft ? 'Video saved as draft!' : 'Video uploaded successfully!';
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'redirect' => $isDraft ? route('library.tab', 'videos') : route('videos.show', ['video' => $video->vid])
                ]);
            }

            $redirectRoute = $isDraft ? route('library.tab', 'videos') : route('videos.show', ['video' => $video->vid]);
            return redirect($redirectRoute)->with('message', $successMessage);

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
        } finally {
            // Log upload completion
            Log::info('Upload completed', [
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
                'total_time' => microtime(true) - $startTime
            ]);
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

    /**
     * Stream raw video for shared videos using share token
     */
    public function shareRaw(string $token)
    {
        // Find video by share token
        $video = Video::findByShareToken($token);
        
        if (!$video) {
            abort(404, 'Shared video not found or share link has expired');
        }

        // Stream the video file using the same logic as the authenticated raw method
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
     * Get thumbnail for shared videos using share token
     */
    public function shareThumbnail(string $token)
    {
        // Find video by share token
        $video = Video::findByShareToken($token);
        
        if (!$video) {
            abort(404, 'Shared video not found or share link has expired');
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
        
        // Return the thumbnail file with proper caching headers
        return response()->file($thumbnailPath, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    /**
     * Get embed player for shared videos (for social media embeds)
     */
    public function shareEmbed(string $token)
    {
        // Find video by share token
        $video = Video::findByShareToken($token);
        
        if (!$video) {
            abort(404, 'Shared video not found or share link has expired');
        }

        return view('embed.video-player', [
            'video' => $video,
            'token' => $token
        ]);
    }

    /**
     * oEmbed endpoint for rich embeds
     */
    public function oEmbed(Request $request)
    {
        $url = $request->input('url');
        $maxwidth = $request->input('maxwidth', 1280);
        $maxheight = $request->input('maxheight', 720);
        $format = $request->input('format', 'json');
        
        // Extract token from URL
        if (preg_match('/\/share\/([a-zA-Z0-9]+)/', $url, $matches)) {
            $token = $matches[1];
            $video = Video::findByShareToken($token);
            
            if (!$video) {
                abort(404, 'Video not found');
            }
            
            $embedUrl = route('share.video.embed', ['token' => $token]);
            $thumbnailUrl = route('share.video.thumbnail', ['token' => $token]);
            
            $response = [
                'version' => '1.0',
                'type' => 'video',
                'width' => min($maxwidth, 1280),
                'height' => min($maxheight, 720),
                'title' => $video->title,
                'author_name' => $video->user->getChannelName(),
                'provider_name' => \App\Models\SiteSetting::get('site_title', 'Open Media Platform'),
                'provider_url' => url('/'),
                'thumbnail_url' => $thumbnailUrl,
                'thumbnail_width' => 1280,
                'thumbnail_height' => 720,
                'html' => '<iframe src="' . $embedUrl . '" width="' . min($maxwidth, 1280) . '" height="' . min($maxheight, 720) . '" frameborder="0" allowfullscreen></iframe>'
            ];
            
            if ($format === 'xml') {
                return response()->view('oembed.xml', $response)->header('Content-Type', 'application/xml');
            }
            
            return response()->json($response);
        }
        
        abort(400, 'Invalid URL format');
    }
}
