<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ImageFile;
use App\Models\SiteSetting;
use App\Traits\HandlesUploadLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    use HandlesUploadLimits;

    /**
     * Get current upload limits from admin settings
     */
    private function getUploadLimits(): array
    {
        return [
            'max_size' => (int) SiteSetting::get('max_image_size', 10240),     // KB, default 10MB
            'max_count' => (int) SiteSetting::get('max_image_count', 50),      // files, default 50
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = Image::with(['user', 'imageFiles'])
                      ->public()
                      ->published()
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        return response()->json($images);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be signed in to upload images.');
        }

        try {
            // Get current upload limits
            $limits = $this->getUploadLimits();
            
            // Determine if this is a draft
            $isDraft = $request->boolean('save_as_draft');

            // Validate the request
            $rules = [
                'images' => "required|array|min:1|max:{$limits['max_count']}",
                'images.*' => "image|mimes:jpeg,png,jpg,gif,webp|max:{$limits['max_size']}", // Dynamic size limit
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
                'images.max' => "You can upload a maximum of {$limits['max_count']} images per post.",
                'images.*.max' => "Each image must be smaller than " . number_format($limits['max_size'] / 1024, 1) . " MB.",
            ]);

            // Create image post record
            $image = new Image();
            $image->title = $request->title;
            $image->description = $request->description;
            $image->category = $request->category;
            
            // Determine visibility and publication status
            if ($isDraft) {
                $image->visibility = Image::VISIBILITY_UNPUBLISHED;
                $image->published_at = null;
            } else {
                $image->visibility = $request->visibility === 'public' ? Image::VISIBILITY_PUBLIC : 
                                    ($request->visibility === 'private' ? Image::VISIBILITY_PRIVATE : Image::VISIBILITY_UNLISTED);
                $image->published_at = $request->visibility === 'public' ? now() : null;
            }
            
            $image->user_id = auth()->id();
            
            // Process tags
            if ($request->tags) {
                $tags = array_map('trim', explode(',', $request->tags));
                $tags = array_filter($tags); // Remove empty tags
                $image->tags = $tags;
            }

            $image->save();

            // Ensure image directory exists
            $imageDir = storage_path('app/public/images');
            if (!file_exists($imageDir)) {
                mkdir($imageDir, 0755, true);
            }

            // Process and store each uploaded image
            $order = 0;
            foreach ($request->file('images') as $uploadedFile) {
                $originalName = $uploadedFile->getClientOriginalName();
                $storedFilename = (string) Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
                
                // Store the file
                $uploadedFile->storeAs('images', $storedFilename, 'public');
                
                // Get image dimensions
                $imagePath = storage_path('app/public/images/' . $storedFilename);
                $imageInfo = getimagesize($imagePath);
                $width = $imageInfo ? $imageInfo[0] : null;
                $height = $imageInfo ? $imageInfo[1] : null;

                // Create image file record
                ImageFile::create([
                    'image_id' => $image->iid,
                    'filename' => $originalName,
                    'stored_filename' => $storedFilename,
                    'mime_type' => $uploadedFile->getMimeType(),
                    'file_size' => $uploadedFile->getSize(),
                    'width' => $width,
                    'height' => $height,
                    'order' => $order++,
                ]);
            }

            // Return appropriate response based on request type
            $successMessage = $isDraft ? 'Images saved as draft!' : 'Images uploaded successfully!';
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'redirect' => route('images.show', ['image' => $image->iid])
                ]);
            }

            return redirect()->route('images.show', ['image' => $image->iid])
                           ->with('message', $successMessage);

        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            
            $errorMessage = 'An error occurred while uploading your images. Please try again.';
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return back()->withErrors(['images' => $errorMessage])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        // Check if user can view this image
        if (!$image->canView(auth()->user())) {
            abort(404, 'Image not found');
        }

        return view('livewire.image-view', [
            'image' => $image->load(['user', 'imageFiles', 'comments.user'])
        ]);
    }

    /**
     * Serve image file
     */
    public function serveFile(Image $image, string $filename)
    {
        // Check if user can view this image
        if (!$image->canView(auth()->user())) {
            abort(404, 'Image not found');
        }

        // Find the image file
        $imageFile = $image->imageFiles()->where('stored_filename', $filename)->first();
        
        if (!$imageFile) {
            abort(404, 'Image file not found');
        }

        $path = storage_path('app/public/images/' . $filename);
        
        if (!file_exists($path)) {
            abort(404, 'Image file not found on disk');
        }

        return response()->file($path, [
            'Content-Type' => $imageFile->mime_type,
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        // Check authorization - owner or user who can moderate content
        if (!auth()->check() || (auth()->id() !== $image->user_id && !auth()->user()->canModerateContent())) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:' . implode(',', [
                Image::VISIBILITY_PUBLIC,
                Image::VISIBILITY_PRIVATE,
                Image::VISIBILITY_UNLISTED,
                Image::VISIBILITY_UNPUBLISHED,
            ]),
        ]);

        // Update the image details
        $image->update($request->only('title', 'description', 'visibility'));

        return redirect()->route('images.edit', ['image' => $image->iid])
            ->with('success', 'Image post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        // Check authorization - owner or user who can moderate content
        if (!auth()->check() || (auth()->id() !== $image->user_id && !auth()->user()->canModerateContent())) {
            abort(403, 'Unauthorized');
        }

        try {
            // Delete image record from database (files will be deleted automatically via model event)
            $image->delete();

            return redirect()->route('channel.show', auth()->user())
                ->with('success', 'Image post deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Error deleting image: ' . $e->getMessage());
            return back()->with('error', 'Error deleting image. Please try again.');
        }
    }

    /**
     * Share functionality for images
     */
    public function shareRaw(string $token, string $filename)
    {
        // Find image by share token
        $image = Image::findByShareToken($token);
        
        if (!$image) {
            abort(404, 'Shared image not found or share link has expired');
        }

        // Find the image file
        $imageFile = $image->imageFiles()->where('stored_filename', $filename)->first();
        
        if (!$imageFile) {
            abort(404, 'Image file not found');
        }

        $path = storage_path('app/public/images/' . $filename);
        
        if (!file_exists($path)) {
            abort(404, 'Image file not found on disk');
        }

        return response()->file($path, [
            'Content-Type' => $imageFile->mime_type,
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}
