<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Livewire\EditVideoPage;
use App\Livewire\FrontPage;
use App\Livewire\UploadPage;
use App\Livewire\VideoViewPage;
use Illuminate\Support\Facades\Route;

Route::get('/', FrontPage::class)
  ->name('home');

Route::get('/videos', [VideoController::class, "index"])->name('videos.index');
Route::post('/videos', [VideoController::class, "store"])->name('videos.store');
Route::get('/videos/{video}', VideoViewPage::class)->name('videos.show');
Route::get('/videos/{video}/raw', [VideoController::class, "raw"])->name('videos.raw');
Route::get('/videos/{video}/edit', EditVideoPage::class)->name('videos.edit');
Route::put('/videos/{video}', [VideoController::class, "update"])->name('videos.update');
Route::get('/videos/{video}/thumbnail.jpg', [VideoController::class, "getThumbnail"])->name('videos.thumbnail');
Route::post('/videos/{video}/thumbnail.jpg', [VideoController::class, "setThumbnail"])->name('videos.thumbnail.upload');

Route::post('/users', [UserController::class, 'login'])
  ->name('login');
Route::get('/logout', [UserController::class, 'logout'])
  ->name('logout');