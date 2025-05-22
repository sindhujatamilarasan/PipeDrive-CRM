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

Route::middleware([])->group(function () {
    Route::get('/panel', [PipedriveController::class, 'showPanel']);
});
Route::get('/pipedrive/user', [PipedriveController::class, 'getPipedriveUser']);

