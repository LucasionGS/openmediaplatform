<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
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
        
        // Return the thumbnail URL
        return response()->file($video->getThumbnailPath(), [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . $video->vid . '.jpg"',
        ]);
    }

    public function setThumbnail(Request $request, string $videoId)
    {
        // Validate the request
        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Fetch the video by ID
        $video = Video::find($videoId);

        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        // Store the thumbnail
        $request->file('thumbnail')->storeAs('thumbnails/', $video->vid, 'public');

        return response()->json(['message' => 'Thumbnail uploaded successfully'], 200);
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
                return back()->withErrors(['video' => $message])->withInput();
            }

            // Validate the request
            $request->validate([
                'video' => 'required|file|mimes:mp4,mov,avi,wmv,mkv,flv,webm|max:' . (500 * 1024), // 500MB in KB
                'title' => 'required|string|max:100',
                'description' => 'nullable|string|max:1000',
                'category' => 'nullable|string|max:50',
                'tags' => 'nullable|string|max:500',
                'visibility' => 'required|in:public,private,unlisted',
            ], [
                'video.max' => 'The video file must not be larger than 500MB.',
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
            $video->duration = $video->calculateDuration(save: true);
            $video->generateThumbnail($video->duration / 2);

            $video->save();

            return redirect()->route('videos.edit', ['video' => $video->vid])
                ->with('message', 'Video uploaded successfully! You can now edit the details.');

        } catch (\Exception $e) {
            \Log::error('Video upload failed: ' . $e->getMessage());
            
            // Check if it's a file size error
            if (strpos($e->getMessage(), 'file is too large') !== false || 
                strpos($e->getMessage(), 'POST Content-Length') !== false) {
                return back()->withErrors(['video' => 'The video file is too large. Please ensure your video is under 500MB and try again.'])->withInput();
            }
            
            return back()->withErrors(['video' => 'An error occurred while uploading your video. Please try again.'])->withInput();
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
        //
    }
}
