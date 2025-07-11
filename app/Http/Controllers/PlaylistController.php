<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlaylistController extends Controller
{
    public function show(Playlist $playlist)
    {
        // Check if user can view this playlist
        if ($playlist->visibility === 'private' && $playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $playlist->load(['videos' => function($query) {
            $query->where('visibility', '!=', 'private')
                  ->orWhere('user_id', Auth::id());
        }, 'user']);

        return view('playlists.show', compact('playlist'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'visibility' => 'required|in:public,private,unlisted',
        ]);

        $playlist = Playlist::create([
            'title' => $request->title,
            'description' => $request->description,
            'visibility' => $request->visibility,
            'user_id' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'playlist' => $playlist->load('user'),
                'message' => 'Playlist created successfully!'
            ]);
        }

        return redirect()->route('playlists.show', $playlist)
            ->with('success', 'Playlist created successfully!');
    }

    public function update(Request $request, Playlist $playlist)
    {
        // Check if user owns this playlist
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'visibility' => 'required|in:public,private,unlisted',
        ]);

        $playlist->update($request->only(['title', 'description', 'visibility']));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'playlist' => $playlist->fresh(),
                'message' => 'Playlist updated successfully!'
            ]);
        }

        return redirect()->route('playlists.show', $playlist)
            ->with('success', 'Playlist updated successfully!');
    }

    public function destroy(Playlist $playlist)
    {
        // Check if user owns this playlist
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $playlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Playlist deleted successfully!'
        ]);
    }

    public function addVideo(Request $request, Playlist $playlist)
    {
        // Check if user owns this playlist
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'video_id' => 'required|exists:videos,vid',
        ]);

        $video = Video::findOrFail($request->video_id);

        // Check if video is already in playlist
        if ($playlist->videos()->where('vid', $video->vid)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Video is already in this playlist!'
            ], 422);
        }

        // Get the next position
        $nextPosition = $playlist->videos()->max('playlist_videos.position') + 1;

        $playlist->videos()->attach($video->vid, [
            'position' => $nextPosition,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video added to playlist successfully!'
        ]);
    }

    public function removeVideo(Request $request, Playlist $playlist)
    {
        // Check if user owns this playlist
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'video_id' => 'required|exists:videos,vid',
        ]);

        $playlist->videos()->detach($request->video_id);

        // Reorder remaining videos
        $this->reorderPlaylistVideos($playlist);

        return response()->json([
            'success' => true,
            'message' => 'Video removed from playlist successfully!'
        ]);
    }

    public function reorderVideos(Request $request, Playlist $playlist)
    {
        // Check if user owns this playlist
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'video_ids' => 'required|array',
            'video_ids.*' => 'exists:videos,vid',
        ]);

        DB::transaction(function() use ($playlist, $request) {
            foreach ($request->video_ids as $position => $videoId) {
                $playlist->videos()->updateExistingPivot($videoId, [
                    'position' => $position + 1,
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Playlist order updated successfully!'
        ]);
    }

    public function getUserPlaylists(Request $request)
    {
        $playlists = Playlist::where('user_id', Auth::id())
            ->with(['videos:vid,title']) // Include videos with just vid and title
            ->withCount('videos')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'playlists' => $playlists
        ]);
    }

    private function reorderPlaylistVideos(Playlist $playlist)
    {
        $videos = $playlist->videos()->orderBy('playlist_videos.position')->get();
        
        foreach ($videos as $index => $video) {
            $playlist->videos()->updateExistingPivot($video->vid, [
                'position' => $index + 1,
                'updated_at' => now(),
            ]);
        }
    }
}
