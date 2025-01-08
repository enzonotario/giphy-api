<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Gif\CreateFavoriteGifController;
use App\Http\Controllers\Gif\GetGifController;
use App\Http\Controllers\Gif\SearchGifsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/register', RegisterController::class)->name('auth.register');
Route::post('/auth/login', LoginController::class)->name('auth.login');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/auth/user', UserController::class)->name('auth.user');

    Route::post('/gifs/favorites', CreateFavoriteGifController::class)->name('gif.favorite.create');

    Route::get('/gifs/search', SearchGifsController::class)->name('gif.search');
    Route::get('/gifs/{id}', GetGifController::class)->name('gif.get');
});
