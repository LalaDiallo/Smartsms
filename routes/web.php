<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReplyController;
use App\Http\Controllers\Auth\SocialiteController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('{provider}/redirect', [SocialiteController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])->name('social.callback');

Route::get('/reply/{token}', [ReplyController::class, 'show'])->name('reply.show');
Route::post('/reply/{token}', [ReplyController::class, 'store'])->name('reply.store');

Route::get('/{any}', function () {
    return file_get_contents(public_path('index.html'));
})->where('any', '.*');
