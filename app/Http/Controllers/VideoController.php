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
        // Validate the request
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi,wmv',
        ]);

        // Video file only, populate with default values
        $video = new Video();
        $video->title = "New Video";
        $video->description = "";
        $video->visibility = Video::VISIBILITY_UPLOADING;
        $video->duration = 0;
        $video->save();

        // Store the video file
        $request->file('video')->storeAs('videos', $video->vid, 'public');
        $video->duration = $video->calculateDuration();
        $video->generateThumbnail($video->duration / 2);

        if ($video->visibility == Video::VISIBILITY_UPLOADING) {
            $video->visibility = Video::VISIBILITY_UNPUBLISHED;
        }

        $video->save();

        return redirect()->route('videos.edit', ['video' => $video->vid])
            ->with('success', 'Video uploaded successfully');
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
