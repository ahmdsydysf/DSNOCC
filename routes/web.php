<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('projects', ProjectController::class);

    // Modern Projects Display
    Route::get('/projects-display', [ProjectController::class, 'display'])->name('projects.display');

    Route::post('/projects/{project}/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');
    Route::get('/folders/{folder}', [FolderController::class, 'show'])->name('folders.show');
    Route::post('/folders/{folder}/files', [FileController::class, 'store'])->middleware('large.file.upload')->name('files.store');
    Route::post('/projects/{project}/files', [FileController::class, 'storeForProject'])->middleware('large.file.upload')->name('projects.files.store');
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/folders/{folder}/download-zip', [FolderController::class, 'downloadZip'])->name('folders.downloadZip');
    Route::get('/projects/{project}/download-zip', [ProjectController::class, 'downloadZip'])->name('projects.downloadZip');
});

require __DIR__.'/auth.php';
