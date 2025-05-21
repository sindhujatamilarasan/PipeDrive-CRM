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
Route::get('/panel', [PipedriveController::class, 'showPanel']);
Route::get('/pipedrive/user', [PipedriveController::class, 'getPipedriveUser']);

// Route::get('/panel', function () {
//     return response()->json([
//         'title' => 'Transaction Panel',
//         'iframe' => [
//             'url' => 'https://44b0-2401-4900-1f2d-4aa6-dc73-bf7c-1b58-f56b.ngrok-free.app/panel/view',
//             'height' => 500, // adjust height as needed
//         ]
//     ]);
// });

// routes/web.php
Route::get('/pipedrive-panel', function () {
    return view('panel');
});
