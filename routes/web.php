<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminIssueController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Auth\AdminSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main_landing_page');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.signup');
})->name('register');

Route::redirect('/signup', '/register')->name('signup');

Route::redirect('/municipal-manager', '/admin');
Route::redirect('/system-manager', '/admin');

Route::post('/admin/session-login', [AdminSessionController::class, 'store'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('admin.session-login');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:municipal_manager'])
    ->group(function (): void {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [AdminAccountController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [AdminAccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/settings', [AdminAccountController::class, 'editSettings'])->name('settings.edit');
        Route::put('/settings', [AdminAccountController::class, 'updateSettings'])->name('settings.update');
        Route::post('/logout', [AdminSessionController::class, 'destroy'])->name('logout');
        Route::resource('departments', DepartmentController::class)->except(['show', 'edit']);
        Route::patch('issues/{issue}/assign', [AdminIssueController::class, 'assign'])->name('issues.assign');
        Route::get('issues/workers-by-department/{department}', [AdminIssueController::class, 'workersByDepartment'])->name('issues.workers-by-department');
        Route::resource('issues', AdminIssueController::class)->only(['index', 'update']);
        Route::patch('workers/{worker}/toggle-status', [WorkerController::class, 'toggleStatus'])->name('workers.toggle-status');
        Route::resource('workers', WorkerController::class)->except(['edit']);
    });

Route::prefix('citizen')->name('citizen.')->group(function () {
    Route::get('/', fn () => view('citizen.shell'))->name('dashboard');
    Route::get('/dashcode', fn () => view('citizen.dashcode'))->name('dashcode');
    Route::get('/profile', fn () => view('citizen.profile'))->name('profile');
    Route::get('/report', fn () => view('citizen.report'))->name('report');
    Route::get('/my-issues', fn () => view('citizen.my-issues'))->name('my_issues');
});

Route::prefix('worker')->name('worker.')->group(function () {
    Route::get('/', fn () => view('worker.shell'))->name('dashboard');
    Route::get('/overview', fn () => view('worker.overview'))->name('overview');
    Route::get('/assigned', fn () => view('worker.assigned'))->name('assigned');
    Route::get('/in-progress', fn () => view('worker.in-progress'))->name('in_progress');
    Route::get('/resolved', fn () => view('worker.resolved'))->name('resolved');
    Route::get('/performance', fn () => view('worker.performance'))->name('performance');
    Route::get('/profile', fn () => view('worker.profile'))->name('profile');
    Route::get('/settings', fn () => view('worker.settings'))->name('settings');
});
