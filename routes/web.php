<?php

use App\Http\Controllers\PipedriveController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/auth/redirect', [PipedriveController::class, 'redirectToPipedrive']);
Route::get('/auth/callback', [PipedriveController::class, 'handleCallback']);
Route::get('/panel', [PipedriveController::class, 'showPanel'])->withoutMiddleware([
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\StartSession::class, // optional
        \App\Http\Middleware\VerifyCsrfToken::class,       // optional
    ]);
Route::get('/pipedrive/user', [PipedriveController::class, 'getPipedriveUser']);

