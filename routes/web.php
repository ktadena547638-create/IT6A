<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Task Management System - RESTful Web Routes
| Authentication: Session-based
| Authorization: Database policies + role-based middleware
|
*/

// Public routes
Route::get('/', [PageController::class, 'landing'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // ✅ HARDENED: Added aggressive rate limiting to prevent brute-force attacks
    Route::post('/login', [AuthController::class, 'handleLogin'])->middleware('throttle:5,1');
});

// Protected routes - requires authentication + email verification
// ✅ HARDENED: Enforced 'verified' middleware to prevent unverified users
Route::middleware(['auth', 'verified'])->group(function () {

    // Home Command Center
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');

    // Dashboard routes
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/tasks/{status?}', [DashboardController::class, 'tasks'])->name('tasks');
        Route::get('/projects', [DashboardController::class, 'projects'])->name('projects');
    });

    // Project management routes (full resource with custom actions)
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        
        // Create/Store - Admin ONLY (The Sovereign forges Projects)
        // ✅ HIERARCHICAL: Only the sovereign (Admin) can create new Projects
        Route::middleware(['checkRole:admin', 'throttle:100,1'])->group(function () {
            Route::get('/create', [ProjectController::class, 'create'])->name('create');
            Route::post('/', [ProjectController::class, 'store'])->name('store');
        });
        
        // Project-specific routes
        Route::prefix('{project}')->middleware('authorizeProject')->group(function () {
            Route::get('/', [ProjectController::class, 'show'])->name('show');
            Route::get('/statistics', [ProjectController::class, 'statistics'])->name('statistics');
            
            // Edit/Update/Delete - Admins and Project Managers only
            Route::middleware('checkRole:admin,project_manager')->group(function () {
                Route::get('/edit', [ProjectController::class, 'edit'])->name('edit');
                Route::put('/', [ProjectController::class, 'update'])->name('update');
                Route::delete('/', [ProjectController::class, 'destroy'])->name('destroy');
            });
        });
    });

    // Task management routes (full resource with custom actions)
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        
        // Kanban board view
        Route::get('/projects/{project}/kanban', [TaskController::class, 'kanban'])->name('kanban');
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('update-status');
        
        // Create/Store - Admins and Project Managers only
        // ✅ HARDENED: Added rate limiting to prevent spam/DoS
        Route::middleware(['checkRole:admin,project_manager', 'throttle:100,1'])->group(function () {
            Route::get('/create', [TaskController::class, 'create'])->name('create');
            Route::post('/', [TaskController::class, 'store'])->name('store');
        });
        
        // Task filtering routes - All authenticated users can view
        Route::get('/priority/{priority}', [TaskController::class, 'byPriority'])->name('priority');
        Route::get('/overdue', [TaskController::class, 'overdue'])->name('overdue');
        
        // Task-specific routes
        Route::prefix('{task}')->middleware('authorizeTask')->group(function () {
            Route::get('/', [TaskController::class, 'show'])->name('show');
            
            // Edit/Update/Delete - Only for task assignee, project manager, or admin
            Route::get('/edit', [TaskController::class, 'edit'])->name('edit');
            Route::put('/', [TaskController::class, 'update'])->name('update');
            Route::delete('/', [TaskController::class, 'destroy'])->name('destroy');
            Route::post('/complete', [TaskController::class, 'complete'])->name('complete');
            
            // Task comments (nested resource) - Any authenticated user on authorized task
            Route::post('/comments', [TaskCommentController::class, 'store'])->name('comments.store');
            Route::delete('/comments/{comment}', [TaskCommentController::class, 'destroy'])->name('comments.destroy');

            // Task attachments (nested resource) - Any authenticated user on authorized task
            Route::post('/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
            Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
            Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
        });
    });

    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/recent', [NotificationController::class, 'recent'])->name('recent');
        Route::put('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Global search
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    // Profile & preferences
    Route::get('/profile/preferences', [PageController::class, 'preferences'])->name('profile.preferences');
    Route::patch('/profile/update-theme', [PageController::class, 'updateTheme'])->name('profile.update-theme');

    // Reports and analytics (admin/manager only)
    Route::middleware('checkRole:admin,project_manager')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/tasks', [ReportController::class, 'tasks'])->name('tasks');
        Route::get('/projects', [ReportController::class, 'projects'])->name('projects');
        Route::get('/activities', [ReportController::class, 'activities'])->name('activities');
    });

    // Analytics routes (admin/manager only)
    Route::middleware('checkRole:admin,project_manager')->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/task-status', [AnalyticsController::class, 'taskStatusData'])->name('task-status');
        Route::get('/completion-trend', [AnalyticsController::class, 'completionTrendData'])->name('completion-trend');
    });

    // Admin routes - User Management (admin only)
    // ✅ SOVEREIGN'S DECREE: Only admins can manage users
    Route::middleware('checkRole:admin')->group(function () {
        Route::resource('users', UserController::class);
        
        // Audit logs (admin only)
        Route::get('/audit-logs', [UserController::class, 'auditLogs'])->name('audit-logs');
    });

    // Client routes (clients only)
    // ✅ SELECTIVE SCRYING: Clients can only see their own projects
    Route::middleware('checkRole:client')->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
        Route::get('/projects/{project}', [ClientController::class, 'viewProject'])->name('project');
        Route::get('/projects/{project}/tasks', [ClientController::class, 'viewProjectTasks'])->name('project-tasks');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

