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
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WorkScheduleAdjustmentController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\DashboardController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([
    'auth',
    'verified',
    'permission:dashboard.view'
    ])->name('dashboard');

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

        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])
            ->name('attendance.export');

    });

    /*
    |--------------------------------------------------------------------------
    | Administration
    |--------------------------------------------------------------------------
    */

    Route::resource('users', UserController::class)
            ->middleware('permission:users.view');

    Route::get('settings', [SettingController::class, 'index'])
        ->name('settings.index');
    Route::get('settings/roles/{role}/edit', [SettingController::class, 'editRole'])
        ->name('settings.roles.edit');
    Route::put('settings/roles/{role}', [SettingController::class, 'updateRole'])
        ->name('settings.roles.update');
    Route::patch('settings/roles/{role}/status', [SettingController::class, 'toggleRole'])
        ->name('settings.roles.status');
    Route::patch('settings/permissions/{permission}/status', [SettingController::class, 'togglePermission'])
        ->name('settings.permissions.status');

    /*
    |--------------------------------------------------------------------------
    | Ajuste de jornada laboral
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'work-schedule-adjustments',
        WorkScheduleAdjustmentController::class
    );
    Route::post(
        'work-schedule-adjustments/{workScheduleAdjustment}/canceled',
        [WorkScheduleAdjustmentController::class, 'canceled']
    )->name('work-schedule-adjustments.canceled');
    Route::post(
        'work-schedule-adjustments/{workScheduleAdjustment}/complete',
        [WorkScheduleAdjustmentController::class, 'complete']
    )->name('work-schedule-adjustments.complete');

    Route::resource('holidays', HolidayController::class);
});

require __DIR__.'/auth.php';
