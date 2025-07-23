<?php

namespace App\Livewire;

use App\Models\Image;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class EditImagePage extends Component
{
    public Image $image;
    
    public $title;
    public $description;
    public $visibility;
    
    public function mount(Image $image)
    {
        // Check if user is authorized to edit this image
        if (!auth()->check() || (!auth()->user()->canModerateContent() && auth()->id() !== $image->user_id)) {
            abort(403, 'Unauthorized');
        }
        
        $this->image = $image->load('imageFiles');
        $this->title = $image->title;
        $this->description = $image->description;
        $this->visibility = $image->visibility;
    }
    
    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'visibility' => 'required|in:public,unlisted,private',
        ]);
        
        $this->image->update([
            'title' => $this->title,
            'description' => $this->description,
            'visibility' => $this->visibility,
        ]);
        
        session()->flash('message', 'Image updated successfully!');
        
        return redirect()->route('images.show', $this->image);
    }
    
    public function delete()
    {
        // Check if user is authorized to delete this image
        if (!auth()->check() || (!auth()->user()->canModerateContent() && auth()->id() !== $this->image->user_id)) {
            abort(403, 'Unauthorized');
        }
        
        $this->image->delete();
        
        session()->flash('message', 'Image deleted successfully!');
        
        return redirect()->route('library');
    }
    
    public function deleteImageFile($fileId)
    {
        // Check if user is authorized to edit this image
        if (!auth()->check() || (!auth()->user()->canModerateContent() && auth()->id() !== $this->image->user_id)) {
            abort(403, 'Unauthorized');
        }
        
        $imageFile = $this->image->imageFiles()->findOrFail($fileId);
        
        // Don't allow deleting the last file
        if ($this->image->imageFiles()->count() <= 1) {
            session()->flash('error', 'Cannot delete the last image file. Delete the entire post instead.');
            return;
        }
        
        // Delete the physical file
        $storagePath = storage_path('app/public/images/' . $imageFile->stored_filename);
        if (file_exists($storagePath)) {
            unlink($storagePath);
        }
        
        // Delete the database record
        $imageFile->delete();
        
        // Reload the image with files
        $this->image = $this->image->fresh('imageFiles');
        
        session()->flash('message', 'Image file deleted successfully!');
    }
    
    public function reorderFiles($orderedIds)
    {
        // Check if user is authorized to edit this image
        if (!auth()->check() || (!auth()->user()->canModerateContent() && auth()->id() !== $this->image->user_id)) {
            abort(403, 'Unauthorized');
        }
        
        // Debug logging
        \Log::info('Reorder attempt', [
            'orderedIds' => $orderedIds,
            'orderedIds_count' => count($orderedIds),
            'orderedIds_type' => gettype($orderedIds),
            'current_files_count' => $this->image->imageFiles()->count(),
            'current_file_ids' => $this->image->imageFiles()->pluck('id')->toArray(),
            'image_id' => $this->image->iid
        ]);
        
        // Just try to update directly for now - bypass validation
        try {
            // Update the order for each file
            foreach ($orderedIds as $index => $fileId) {
                $updated = $this->image->imageFiles()->where('id', $fileId)->update(['order' => $index + 1]);
                \Log::info('Updated file order', ['file_id' => $fileId, 'new_order' => $index + 1, 'rows_updated' => $updated]);
            }
            
            // Reload the image with files
            $this->image = $this->image->fresh('imageFiles');
            
            session()->flash('message', 'Image order updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Reorder failed', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to reorder files: ' . $e->getMessage());
        }
    }
    
    public function moveFileUp($fileId)
    {
        // Check if user is authorized to edit this image
        if (!auth()->check() || (!auth()->user()->canModerateContent() && auth()->id() !== $this->image->user_id)) {
            abort(403, 'Unauthorized');
        }
        
        \Log::info('Move file up called', ['file_id' => $fileId, 'image_id' => $this->image->iid]);
        
        $file = $this->image->imageFiles()->findOrFail($fileId);
        $previousFile = $this->image->imageFiles()->where('order', '<', $file->order)->orderBy('order', 'desc')->first();
        
        \Log::info('Files found for move up', [
            'current_file_order' => $file->order,
            'previous_file_exists' => $previousFile ? true : false,
            'previous_file_order' => $previousFile ? $previousFile->order : null
        ]);
        
        if ($previousFile) {
            // Swap the orders
            $tempOrder = $file->order;
            $file->update(['order' => $previousFile->order]);
            $previousFile->update(['order' => $tempOrder]);
            
            \Log::info('Orders swapped', [
                'file_id' => $fileId,
                'old_order' => $tempOrder,
                'new_order' => $previousFile->order
            ]);
            
            // Reload the image with files
            $this->image = $this->image->fresh('imageFiles');
            
            session()->flash('message', 'Image moved up successfully!');
        } else {
            session()->flash('error', 'Cannot move up - already at the top.');
        }
    }
    
    public function moveFileDown($fileId)
    {
        // Check if user is authorized to edit this image
        if (!auth()->check() || (!auth()->user()->canModerateContent() && auth()->id() !== $this->image->user_id)) {
            abort(403, 'Unauthorized');
        }
        
        $file = $this->image->imageFiles()->findOrFail($fileId);
        $nextFile = $this->image->imageFiles()->where('order', '>', $file->order)->orderBy('order', 'asc')->first();
        
        if ($nextFile) {
            // Swap the orders
            $tempOrder = $file->order;
            $file->update(['order' => $nextFile->order]);
            $nextFile->update(['order' => $tempOrder]);
            
            // Reload the image with files
            $this->image = $this->image->fresh('imageFiles');
            
            session()->flash('message', 'Image moved down successfully!');
        }
    }
    
    public function testReverse()
    {
        \Log::info('Test reverse called');
        
        // Get all files ordered by current order
        $files = $this->image->imageFiles()->orderBy('order')->get();
        
        \Log::info('Files before reverse', [
            'count' => $files->count(),
            'orders' => $files->pluck('order', 'id')->toArray()
        ]);
        
        // Reverse the order
        $totalFiles = $files->count();
        foreach ($files as $index => $file) {
            $newOrder = $totalFiles - $index;
            \DB::table('image_files')->where('id', $file->id)->update(['order' => $newOrder]);
            \Log::info('Updated file order', ['file_id' => $file->id, 'old_order' => $file->order, 'new_order' => $newOrder]);
        }
        
        // Reload the image with files
        $this->image = $this->image->fresh('imageFiles');
        
        session()->flash('message', 'Order reversed successfully!');
    }
    
    #[Title('Edit Image')]
    public function render()
    {
        return view('livewire.edit-image-page');
    }
}
