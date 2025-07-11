<?php

namespace App\Livewire;

use App\Models\Video;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class EditVideoPage extends Component
{
    use WithFileUploads;

    public Video $video;
    public string $error = '';
    public string $success = '';

    public string $title = '';
    public ?string $description = '';
    public string $visibility = Video::VISIBILITY_PUBLIC;
    public ?string $category = '';
    public string $tags = '';
    
    public $thumbnail;
    public bool $showDeleteConfirm = false;

    public function mount(Video $video)
    {
        // Check if user is authorized to edit this video
        if (!auth()->check() || (!auth()->user()->canModerateContent() && auth()->id() !== $video->user_id)) {
            abort(403, 'Unauthorized');
        }

        $this->video = $video;
        $this->title = $video->title;
        $this->description = $video->description ?? '';
        $this->visibility = $video->visibility;
        $this->category = $video->category ?? '';
        $this->tags = is_array($video->tags) ? implode(', ', $video->tags) : '';
    }

    public function updateVideo()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'category' => 'nullable|string|max:50',
            'tags' => 'nullable|string|max:500',
            'visibility' => 'required|in:' . implode(',', [
                Video::VISIBILITY_PUBLIC,
                Video::VISIBILITY_PRIVATE,
                Video::VISIBILITY_UNLISTED,
                Video::VISIBILITY_UNPUBLISHED,
            ]),
        ]);

        try {
            // Process tags
            $tagsArray = [];
            if ($this->tags) {
                $tagsArray = array_map('trim', explode(',', $this->tags));
                $tagsArray = array_filter($tagsArray); // Remove empty tags
            }

            $this->video->update([
                'title' => $this->title,
                'description' => $this->description,
                'category' => $this->category,
                'tags' => $tagsArray,
                'visibility' => $this->visibility,
                'published_at' => $this->visibility === Video::VISIBILITY_PUBLIC ? ($this->video->published_at ?? now()) : null,
            ]);

            $this->success = 'Video updated successfully!';
            $this->error = '';

        } catch (\Exception $e) {
            $this->error = 'Error updating video: ' . $e->getMessage();
            $this->success = '';
        }
    }

    public function publishVideo()
    {
        try {
            // Validate required fields for publishing
            $this->validate([
                'title' => 'required|string|max:255',
                'visibility' => 'required|in:' . implode(',', [
                    Video::VISIBILITY_PUBLIC,
                    Video::VISIBILITY_PRIVATE,
                    Video::VISIBILITY_UNLISTED,
                ]),
            ]);

            // Process tags
            $tags = [];
            if (!empty($this->tags)) {
                $tags = array_map('trim', explode(',', $this->tags));
                $tags = array_filter($tags); // Remove empty tags
            }

            // Update video with current form data and publish it
            $this->video->update([
                'title' => $this->title,
                'description' => $this->description,
                'category' => $this->category,
                'tags' => $tags,
                'visibility' => $this->visibility,
                'published_at' => now(),
            ]);

            $this->success = 'Video published successfully!';
            $this->error = '';

            // Redirect to the video page after publishing
            return redirect()->route('videos.show', $this->video)
                ->with('message', 'Video published successfully!');

        } catch (\Exception $e) {
            $this->error = 'Error publishing video: ' . $e->getMessage();
            $this->success = '';
        }
    }

    public function uploadThumbnail()
    {
        $this->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB max
        ]);

        try {
            // Ensure thumbnail directory exists
            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Delete old thumbnail if exists
            $oldThumbnailPath = $this->video->getThumbnailPath();
            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath);
            }

            // Save new thumbnail
            $thumbnailPath = $this->video->getThumbnailPath();
            $this->thumbnail->storeAs('thumbnails', $this->video->vid . '.jpg', 'public');

            $this->success = 'Thumbnail updated successfully!';
            $this->error = '';
            $this->thumbnail = null;

        } catch (\Exception $e) {
            $this->error = 'Error uploading thumbnail: ' . $e->getMessage();
            $this->success = '';
        }
    }

    public function confirmDelete()
    {
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteConfirm = false;
    }

    public function deleteVideo()
    {
        try {
            // Delete video record from database (files will be deleted automatically via model event)
            $this->video->delete();

            // Redirect to user's channel
            return redirect()->route('channel.show', auth()->user())
                ->with('success', 'Video deleted successfully!');

        } catch (\Exception $e) {
            $this->error = 'Error deleting video: ' . $e->getMessage();
            $this->success = '';
            $this->showDeleteConfirm = false;
        }
    }
    
    public function render()
    {
        return view('livewire.edit-video-page');
    }
}
