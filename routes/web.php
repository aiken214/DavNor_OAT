<?php

use App\Http\Controllers\AccomplishmentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DTRController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/attendance/record', [DashboardController::class, 'recordAttendance'])->name('attendance.record');

    Route::get('/my-dtr', [DTRController::class, 'index'])->name('dtr.index');
    Route::get('/my-dtr/download', [DTRController::class, 'download'])->name('dtr.download');

    Route::get('/photo/{path}', function ($path) {
        if (Storage::disk('synology')->exists($path)) {
            return Storage::disk('synology')->response($path);
        }
        if (Storage::disk('public')->exists('photos/' . $path)) {
            return Storage::disk('public')->response('photos/' . $path);
        }
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }
        abort(404);
    })->where('path', '.*')->name('photo.show');

    Route::get('/accomplishments', [AccomplishmentController::class, 'index'])->name('accomplishments.index');
    Route::post('/accomplishments', [AccomplishmentController::class, 'store'])->name('accomplishments.store');
    Route::delete('/accomplishments/{accomplishment}', [AccomplishmentController::class, 'destroy'])->name('accomplishments.destroy');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('/employees', [AdminController::class, 'employees'])->name('employees.index');
        Route::get('/employees/{user}/dtr', [AdminController::class, 'employeeDTR'])->name('employees.dtr');
        Route::get('/employees/{user}/accomplishments', [AdminController::class, 'employeeAccomplishments'])->name('employees.accomplishments');
    });
});
