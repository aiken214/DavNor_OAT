<?php

use App\Http\Controllers\AccomplishmentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DTRController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/icons/{file}', function ($file) {
    $path = public_path("icons/{$file}");
    if (file_exists($path)) {
        return response()->file($path, ['Cache-Control' => 'public, max-age=31536000']);
    }

    // Fallback: generate icon if file missing
    if (preg_match('/icon-(\d+)\.png/', $file, $m)) {
        $size = (int) $m[1];
        $img = imagecreatetruecolor($size, $size);
        $bg = imagecolorallocate($img, 37, 99, 235);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg);
        $tw = strlen('OAT') * imagefontwidth(5);
        imagestring($img, 5, (int)(($size - $tw) / 2), (int)(($size - imagefontheight(5)) / 2), 'OAT', $white);
        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);
        return response($data, 200, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=31536000']);
    }

    abort(404);
})->where('file', '.*');

Route::get('/debug-icons', function () {
    $pub = public_path();
    $iconsDir = public_path('icons');
    $icon192 = public_path('icons/icon-192.png');
    return response()->json([
        'public_path' => $pub,
        'icons_dir_exists' => is_dir($iconsDir),
        'icon_192_exists' => file_exists($icon192),
        'icon_192_size' => file_exists($icon192) ? filesize($icon192) : null,
        'files_in_icons' => is_dir($iconsDir) ? scandir($iconsDir) : 'directory not found',
        'gd_available' => extension_loaded('gd'),
    ]);
});

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
        // Employee management (all admin roles)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // View records (all admin roles, scoped)
        Route::get('/employees', [AdminController::class, 'employees'])->name('employees.index');
        Route::get('/employees/{user}/dtr', [AdminController::class, 'employeeDTR'])->name('employees.dtr');
        Route::get('/employees/{user}/accomplishments', [AdminController::class, 'employeeAccomplishments'])->name('employees.accomplishments');

        // Sections (super_admin only)
        Route::middleware('role:super_admin')->group(function () {
            Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
            Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
            Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
            Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

            Route::get('/districts', [DistrictController::class, 'index'])->name('districts.index');
            Route::post('/districts', [DistrictController::class, 'store'])->name('districts.store');
            Route::put('/districts/{district}', [DistrictController::class, 'update'])->name('districts.update');
            Route::delete('/districts/{district}', [DistrictController::class, 'destroy'])->name('districts.destroy');
            Route::post('/districts/{district}/schools', [DistrictController::class, 'storeSchool'])->name('districts.schools.store');
            Route::put('/schools/{school}', [DistrictController::class, 'updateSchool'])->name('schools.update');
            Route::delete('/schools/{school}', [DistrictController::class, 'destroySchool'])->name('schools.destroy');
        });
    });
});
