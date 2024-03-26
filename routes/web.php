<?php

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Guest\HomeController as GuestHomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Guest\ProjectController as GuestProjectController;
use App\Http\Controllers\Admin\TypeController;
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

Route::get('/', GuestHomeController::class)->name('guest.home');
Route::get('/projects/{slug}', [GuestProjectController::class, 'show'])->name('guest.projects.show');

Route::prefix('/admin')->name('admin.')->middleware('auth')->group(function () {
    // Rotta admin Home
    Route::get('', AdminHomeController::class)->name('home');

    // Rotte admin projects
    Route::get('/projects/trash', [AdminProjectController::class, 'trash'])->name('projects.trash');
    Route::patch('/projects/{project}/restore', [AdminProjectController::class, 'restore'])->name('projects.restore')->withTrashed();
    Route::delete('/projects/{project}/drop', [AdminProjectController::class, 'drop'])->name('projects.drop')->withTrashed();
    Route::delete('/projects/massive-drop', [AdminProjectController::class, 'massiveDrop'])->name('projects.massive-drop');
    Route::patch('/projects/{project}/toggle-status', [AdminProjectController::class, 'toggleStatus'])->name('projects.toggle-status');
    Route::resource('projects', AdminProjectController::class)->withTrashed(['show', 'edit']);

    // Rotte admin types
    Route::resource('/types', TypeController::class)->except('show');
});


// Rotte profilo
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
