<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PlaylistController;
use App\Livewire\ChannelPage;
use App\Livewire\EditImagePage;
use App\Livewire\EditVideoPage;
use App\Livewire\Homepage;
use App\Livewire\ImageUploadPage;
use App\Livewire\ImageViewPage;
use App\Livewire\LibraryPage;
use App\Livewire\LoginComponent;
use App\Livewire\ProfileSettings;
use App\Livewire\RegisterComponent;
use App\Livewire\SubscriptionsPage;
use App\Livewire\UploadPage;
use App\Livewire\VideoWatch;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::get('/login', LoginComponent::class)->name('login');
Route::get('/register', RegisterComponent::class)->name('register');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Public share route (no authentication required)
Route::get('/share/{token}', VideoWatch::class)->name('videos.share');

// Public video assets for shared videos (no authentication required)
Route::get('/share/{token}/raw', [VideoController::class, "shareRaw"])->name('videos.share.raw');
Route::get('/share/{token}/thumbnail.jpg', [VideoController::class, "shareThumbnail"])->name('videos.share.thumbnail');

// Embed route for social media players
Route::get('/share/{token}/embed', [VideoController::class, "shareEmbed"])->name('videos.share.embed');

// oEmbed endpoint for rich embeds
Route::get('/oembed', [VideoController::class, "oEmbed"])->name('oembed');

// Routes (for authenticated users)
Route::middleware('auth')->group(function () {
    // Homepage
    Route::get('/', Homepage::class)->name('home');

    // Search
    Route::get('/search', Homepage::class)->name('search');
  
    // Video routes
    Route::get('/videos', [VideoController::class, "index"])->name('videos.index');
    Route::get('/videos/upload-config', [VideoController::class, "getUploadConfig"])->name('videos.upload-config');
    Route::post('/videos', [VideoController::class, "store"])->name('videos.store');
    Route::get('/watch/{video}', VideoWatch::class)->name('videos.show');
    Route::get('/videos/{video}/raw', [VideoController::class, "raw"])->name('videos.raw');
    Route::get('/videos/{video}/edit', EditVideoPage::class)->name('videos.edit')->middleware('auth');
    Route::put('/videos/{video}', [VideoController::class, "update"])->name('videos.update')->middleware('auth');
    Route::delete('/videos/{video}', [VideoController::class, "destroy"])->name('videos.destroy')->middleware('auth');
    Route::get('/videos/{video}/thumbnail.jpg', [VideoController::class, "getThumbnail"])->name('videos.thumbnail');
    Route::post('/videos/{video}/thumbnail.jpg', [VideoController::class, "setThumbnail"])->name('videos.thumbnail.upload')->middleware('auth');

    // Upload page (protected)
    Route::get('/upload', UploadPage::class)->name('videos.upload');
    
    // Image routes
    Route::get('/images', [ImageController::class, "index"])->name('images.index');
    Route::post('/images', [ImageController::class, "store"])->name('images.store')->middleware('auth');
    Route::get('/images/upload', ImageUploadPage::class)->name('images.upload')->middleware('auth');
    Route::get('/images/{image}', ImageViewPage::class)->name('images.show');
    Route::get('/images/{image}/edit', EditImagePage::class)->name('images.edit')->middleware('auth');
    Route::get('/images/{image}/{filename}', [ImageController::class, "serveFile"])->name('images.file');
    Route::put('/images/{image}', [ImageController::class, "update"])->name('images.update')->middleware('auth');
    Route::delete('/images/{image}', [ImageController::class, "destroy"])->name('images.destroy')->middleware('auth');
    Route::get('/shared/images/{shareToken}', [ImageController::class, "shareRaw"])->name('images.share.raw');
    
    // Channel routes
    Route::get('/channel/{user}', ChannelPage::class)->name('channel.show');

    Route::get('/library', LibraryPage::class)->name('library');
    Route::get('/library/{tab}', LibraryPage::class)->name('library.tab');
    Route::get('/subscriptions', SubscriptionsPage::class)->name('subscriptions');
    Route::post('/subscribe/{user}', [UserController::class, 'subscribe'])->name('subscribe');
    Route::delete('/unsubscribe/{user}', [UserController::class, 'unsubscribe'])->name('unsubscribe');
    Route::get('/profile/settings', ProfileSettings::class)->name('profile.settings');
    
    // Admin routes (protected by role middleware)
    Route::get('/admin/settings', \App\Livewire\AdminSettings::class)->name('admin.settings')->middleware('role:admin');
    
    // Playlist management routes
    Route::get('/playlists/user', [PlaylistController::class, 'getUserPlaylists'])->name('playlists.user');
    Route::post('/playlists', [PlaylistController::class, 'store'])->name('playlists.store');
    Route::put('/playlists/{playlist}', [PlaylistController::class, 'update'])->name('playlists.update');
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy'])->name('playlists.destroy');
    Route::post('/playlists/{playlist}/videos', [PlaylistController::class, 'addVideo'])->name('playlists.add-video');
    Route::delete('/playlists/{playlist}/videos', [PlaylistController::class, 'removeVideo'])->name('playlists.remove-video');
    Route::put('/playlists/{playlist}/reorder', [PlaylistController::class, 'reorderVideos'])->name('playlists.reorder');
    
    // Public playlist routes (viewable by anyone if public/unlisted)
    Route::get('/playlist/{playlist}', App\Livewire\PlaylistViewPage::class)->name('playlists.show');

    // Static file serving from storage
    Route::get('/sf/{path}', function ($path) {
        // Decode the path to handle URL encoding
        $path = urldecode($path);
        
        // Security: Prevent directory traversal attacks
        $path = str_replace(['../', '..\\', '../', '..\\'], '', $path);
        
        // Build the full file path
        $filePath = storage_path('app/public/' . $path);
        
        // Check if file exists and is within the storage/app directory
        if (!file_exists($filePath)) {
            \Log::warning('File not found: ' . $filePath);
            abort(404, 'File not found');
        }
        
        if (realpath($filePath) && !str_starts_with(realpath($filePath), realpath(storage_path('app/')))) {
            \Log::warning('File outside storage directory: ' . realpath($filePath));
            abort(403, 'File access denied');
        }
        
        // Get file extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Additional security: Only allow certain file types for direct serving
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'avi', 'mkv', 'webm', 'flv', 'wmv', 'pdf', 'txt', "ico"];
        
        if (!in_array($extension, $allowedExtensions)) {
            \Log::warning('File type not allowed: ' . $extension . ' for file: ' . $filePath);
            abort(403, 'File type not allowed for direct access: ' . $extension);
        }
        
        // Get mime type for proper content type header
        $mimeType = mime_content_type($filePath);
        
        // For video files, support range requests for better streaming
        if (str_starts_with($mimeType, 'video/')) {
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }
        
        // For other files, serve normally with caching headers
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    })->where('path', '.*')->name('storage.file');
});

Route::get('/favicon', function () {
    $siteIcon = \App\Models\SiteSetting::get('site_icon');
    if ($siteIcon) {
        return response()->file(storage_path('app/public/' . $siteIcon));
    }

    // Fallback to default favicon if no site icon is set
    return response()->file(public_path('favicon.ico'));
});