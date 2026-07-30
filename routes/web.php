<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('document-types', DocumentTypeController::class);

Route::resource('positions', PositionController::class)
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/attendance/register', [AttendanceController::class, 'index'])
        ->name('attendance.register');
    Route::post('/attendance/search', [AttendanceController::class, 'search'])
        ->name('attendance.search');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])
        ->name('attendance.store');
});

Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/register', [AttendanceController::class, 'index'])
        ->name('register');
    Route::post('/search', [AttendanceController::class, 'search'])
        ->name('search');
    Route::get('/select/{employee}', [AttendanceController::class, 'select'])
        ->name('select');
    Route::post('/store', [AttendanceController::class, 'store'])
        ->name('store');
});

require __DIR__.'/auth.php';