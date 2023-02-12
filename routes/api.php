<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('count-row', [App\Http\Controllers\Admin\UploadController::class, 'countRow'])->name('count-row');
Route::get('get-row', [App\Http\Controllers\Admin\UploadController::class, 'getRow'])->name('get-row');
Route::get('get-folder', [App\Http\Controllers\Admin\FolderController::class, 'getFolder'])->name('get-folder');
Route::post('add-folder', [App\Http\Controllers\Admin\FolderController::class, 'addFolder'])->name('add-folder');
Route::get('get-row-name', [App\Http\Controllers\Admin\UploadController::class, 'getRowName'])->name('get-row-name');
//Route::get('upload', [App\Http\Controllers\Admin\UploadController::class, 'getRowName'])->name('get-row-name');

