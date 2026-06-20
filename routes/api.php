<?php

use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CitizenPortalController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\IssueController;
use App\Http\Controllers\Api\SystemManagerController;
use App\Http\Controllers\Api\UpvoteController;
use App\Http\Controllers\Api\WorkerController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/issues', [IssueController::class, 'index']);
Route::get('/stats', [IssueController::class, 'stats']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/citizen/dashboard', [CitizenPortalController::class, 'dashboard'])
        ->middleware('role:citizen');
    Route::get('/citizen/issues', [CitizenPortalController::class, 'issues'])
        ->middleware('role:citizen');
    Route::put('/citizen/profile', [CitizenPortalController::class, 'updateProfile'])
        ->middleware('role:citizen');
    Route::get('/citizen/settings', [CitizenPortalController::class, 'settings'])
        ->middleware('role:citizen');
    Route::put('/citizen/settings', [CitizenPortalController::class, 'updateSettings'])
        ->middleware('role:citizen');

    Route::post('/issues', [IssueController::class, 'store']);
    Route::get('/issues/{id}', [IssueController::class, 'show']);
    Route::put('/issues/{id}', [IssueController::class, 'update']);
    Route::delete('/issues/{id}', [IssueController::class, 'destroy']);

    Route::post('/municipal-manager/assign-issue', [AssignmentController::class, 'assignIssue'])
        ->middleware('role:municipal_manager');

    Route::get('/worker/dashboard', [WorkerController::class, 'dashboard'])
        ->middleware('role:worker');
    Route::get('/worker/issues', [WorkerController::class, 'issues'])
        ->middleware('role:worker');
    Route::post('/worker/issues/{issue}/status', [WorkerController::class, 'updateIssueStatus'])
        ->middleware('role:worker');
    Route::get('/worker/profile', [WorkerController::class, 'profile'])
        ->middleware('role:worker');
    Route::put('/worker/profile', [WorkerController::class, 'updateProfile'])
        ->middleware('role:worker');
    Route::get('/worker/settings', [WorkerController::class, 'settings'])
        ->middleware('role:worker');
    Route::put('/worker/settings', [WorkerController::class, 'updateSettings'])
        ->middleware('role:worker');
    Route::get('/worker/performance', [WorkerController::class, 'performance'])
        ->middleware('role:worker');

    Route::get('/system-manager/municipal-manager', [SystemManagerController::class, 'municipalManager'])
        ->middleware('role:system_manager');
    Route::post('/system-manager/municipal-manager', [SystemManagerController::class, 'storeMunicipalManager'])
        ->middleware('role:system_manager');
    Route::put('/system-manager/municipal-manager/{user}', [SystemManagerController::class, 'updateMunicipalManager'])
        ->middleware('role:system_manager');
    Route::get('/system-manager/escalations', [SystemManagerController::class, 'escalations'])
        ->middleware('role:system_manager');
    Route::get('/system-manager/analytics', [SystemManagerController::class, 'analytics'])
        ->middleware('role:system_manager');

    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/issues/{id}/comments', [CommentController::class, 'index']);

    Route::post('/issues/{id}/upvote', [UpvoteController::class, 'store']);
});

// Admin dashboard API (internal desktop tool)
use App\Http\Controllers\Api\AdminController;

Route::prefix('admin')->group(function (): void {
    Route::get('/stats', [AdminController::class, 'stats']);
    Route::get('/issues', [AdminController::class, 'issues']);
    Route::get('/issues/recent', [AdminController::class, 'recentIssues']);
    Route::get('/workers/top', [AdminController::class, 'topWorkers']);

    Route::get('/workers', [AdminController::class, 'workers']);
    Route::post('/workers', [AdminController::class, 'storeWorker']);
    Route::put('/workers/{id}', [AdminController::class, 'updateWorker']);
    Route::delete('/workers/{id}', [AdminController::class, 'destroyWorker']);

    Route::get('/departments', [AdminController::class, 'departments']);
    Route::post('/departments', [AdminController::class, 'storeDepartment']);
    Route::put('/departments/{id}', [AdminController::class, 'updateDepartment']);
    Route::delete('/departments/{id}', [AdminController::class, 'destroyDepartment']);
});
