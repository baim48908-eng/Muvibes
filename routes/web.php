<?php

use App\Http\Controllers\MusicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute utama aplikasi
Route::get('/', [MusicController::class, 'index']);

// Rute khusus untuk AJAX Live Search (Pencarian Instan)
Route::get('/api/search', [MusicController::class, 'searchAjax']);
Route::get('/stream/{videoId}', [MusicController::class, 'streamAudio'])->name('music.stream');
Route::get('/get-video-id', [MusicController::class, 'getVideoId']);
Route::get('/get-direct-stream', [MusicController::class, 'getDirectStream']);
Route::get('/stream-proxy', [MusicController::class, 'streamProxy']);