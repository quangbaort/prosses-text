<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UploadController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::prefix('/api')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::post('count-row', [App\Http\Controllers\Admin\UploadController::class, 'countRow'])->name('count-row');
        Route::get('get-folder', [App\Http\Controllers\Admin\FolderController::class, 'getFolder'])->name('get-folder');
        Route::post('add-folder', [App\Http\Controllers\Admin\FolderController::class, 'addFolder'])->name('add-folder');
        Route::get('get-row/{idFolder}', [App\Http\Controllers\Admin\UploadController::class, 'getRowName'])->name('get-row-name');
        Route::post('upload', [App\Http\Controllers\Admin\UploadController::class, 'upload'])->name('upload');
        Route::post('delete-text/{idFolder}', [App\Http\Controllers\Admin\UploadController::class, 'deleteText'])->name('delete-text');

    });
    Route::get('get-row', [App\Http\Controllers\Admin\UploadController::class, 'getRow'])->name('get-row');



});

