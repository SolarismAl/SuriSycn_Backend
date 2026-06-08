<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\ReservationController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ReportController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        // Reports
        Route::get('/reports/tasks/export', [ReportController::class, 'exportTasks']);
        Route::get('/reports/reservations/export', [ReportController::class, 'exportReservations']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread', [NotificationController::class, 'unread']);
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // Core Business Modules
        Route::get('/users', [\App\Http\Controllers\Api\V1\UserController::class, 'index']);
        Route::get('/departments', [\App\Http\Controllers\Api\V1\DepartmentController::class, 'index']);
        Route::apiResource('events', EventController::class);
        Route::apiResource('reservations', ReservationController::class);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('documents', \App\Http\Controllers\Api\V1\DocumentController::class);

        // CTO (Compensatory Time Off)
        Route::get('/cto', [\App\Http\Controllers\Api\V1\CtoController::class, 'index']);
        Route::get('/cto/balance', [\App\Http\Controllers\Api\V1\CtoController::class, 'balance']);
        Route::post('/cto', [\App\Http\Controllers\Api\V1\CtoController::class, 'store']);

        // Office Orders
        Route::apiResource('office-orders', \App\Http\Controllers\Api\V1\OfficeOrderController::class);

        // Announcements (Public read access if authenticated)
        Route::get('/announcements', [AnnouncementController::class, 'index']);
        Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);

        // Admin only routes
        Route::middleware(\App\Http\Middleware\CheckRole::class.':admin')->group(function () {
            Route::patch('/users/{id}/role', [\App\Http\Controllers\Api\V1\UserController::class, 'updateRole']);
            Route::post('/announcements', [AnnouncementController::class, 'store']);
            Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update']);
            Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy']);
            
            // Admin CTO Routes
            Route::get('/cto/overview', [\App\Http\Controllers\Api\V1\CtoController::class, 'overview']);
            Route::patch('/cto/bulk-status', [\App\Http\Controllers\Api\V1\CtoController::class, 'bulkUpdateStatus']);
            Route::patch('/cto/{id}/status', [\App\Http\Controllers\Api\V1\CtoController::class, 'updateStatus']);
        });

        // Staff and Admin routes
        Route::middleware(\App\Http\Middleware\CheckRole::class.':staff,admin')->group(function () {
            Route::post('/departments', [\App\Http\Controllers\Api\V1\DepartmentController::class, 'store']);
        });
    });
});
