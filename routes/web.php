<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PlaylistController;
use App\Livewire\ChannelPage;
use App\Livewire\EditVideoPage;
use App\Livewire\Homepage;
use App\Livewire\LibraryPage;
use App\Livewire\LoginComponent;
use App\Livewire\ProfileSettings;
use App\Livewire\RegisterComponent;
use App\Livewire\SubscriptionsPage;
use App\Livewire\UploadPage;
use App\Livewire\VideoWatch;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', Homepage::class)->name('home');

// Search
Route::get('/search', Homepage::class)->name('search');

// Authentication routes
Route::get('/login', LoginComponent::class)->name('login');
Route::get('/register', RegisterComponent::class)->name('register');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

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

// Channel routes
Route::get('/channel/{user}', ChannelPage::class)->name('channel.show');

// Subscription routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/library', LibraryPage::class)->name('library');
    Route::get('/library/{tab}', LibraryPage::class)->name('library.tab');
    Route::get('/subscriptions', SubscriptionsPage::class)->name('subscriptions');
    Route::post('/subscribe/{user}', [UserController::class, 'subscribe'])->name('subscribe');
    Route::delete('/unsubscribe/{user}', [UserController::class, 'unsubscribe'])->name('unsubscribe');
    Route::get('/profile/settings', ProfileSettings::class)->name('profile.settings');
    
    // Playlist management routes
    Route::get('/playlists/user', [PlaylistController::class, 'getUserPlaylists'])->name('playlists.user');
    Route::post('/playlists', [PlaylistController::class, 'store'])->name('playlists.store');
    Route::put('/playlists/{playlist}', [PlaylistController::class, 'update'])->name('playlists.update');
    Route::delete('/playlists/{playlist}', [PlaylistController::class, 'destroy'])->name('playlists.destroy');
    Route::post('/playlists/{playlist}/videos', [PlaylistController::class, 'addVideo'])->name('playlists.add-video');
    Route::delete('/playlists/{playlist}/videos', [PlaylistController::class, 'removeVideo'])->name('playlists.remove-video');
    Route::put('/playlists/{playlist}/reorder', [PlaylistController::class, 'reorderVideos'])->name('playlists.reorder');
});

// Public playlist routes (viewable by anyone if public/unlisted)
Route::get('/playlist/{playlist}', App\Livewire\PlaylistViewPage::class)->name('playlists.show');