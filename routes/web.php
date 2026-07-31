<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WorkScheduleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Catalogs
    |--------------------------------------------------------------------------
    */

    Route::resource('document-types', DocumentTypeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::resource('employees', EmployeeController::class);

    /*
    |--------------------------------------------------------------------------
    | Work Schedules
    |--------------------------------------------------------------------------
    */

    Route::resource('work-schedules', WorkScheduleController::class);

    /*
    |--------------------------------------------------------------------------
    | Attendance Records
    |--------------------------------------------------------------------------
    */

    Route::resource('attendance-records', AttendanceRecordController::class);

    /*
    |--------------------------------------------------------------------------
    | Attendance Register
    |--------------------------------------------------------------------------
    */

    Route::prefix('attendance')
        ->name('attendance.')
        ->group(function () {

            Route::get('/register', [AttendanceController::class, 'index'])
                ->name('register');

            Route::post('/search', [AttendanceController::class, 'search'])
                ->name('search');

            Route::get('/select/{employee}', [AttendanceController::class, 'select'])
                ->name('select');

            Route::post('/store', [AttendanceController::class, 'store'])
                ->name('store');

        });

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    |
    | Se agregarán cuando implementemos el módulo de reportes.
    |
    */

    
    Route::prefix('reports')
        ->name('reports.')
        ->group(function () {

            Route::get('/attendance', [ReportController::class, 'attendance'])
                ->name('attendance');

        });
    
    /*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('settings', SettingController::class);
});

require __DIR__.'/auth.php';