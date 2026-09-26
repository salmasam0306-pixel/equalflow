<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PersonalProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserStatusController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DepartmentController;
use App\Services\AIService;
use Illuminate\Support\Facades\Route;

// ============================================
// TEST ROUTES
// ============================================
Route::get('/test', fn () => '✅ Laravel is working!');

Route::get('/test-auth', function () {
    return auth()->check()
        ? '✅ Logged in as: ' . auth()->user()->name
        : '❌ <a href="/login">Login</a>';
})->middleware(['auth']);

Route::get('/ai-check', function () {
    try {
        $ai = app(AIService::class);
        return response()->json([
            'status'    => $ai->isRunning() ? 'connected' : 'disconnected',
            'timestamp' => now()->toDateTimeString(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

// ============================================
// WELCOME
// ============================================
Route::get('/', fn () => view('welcome'))->name('home');

// ============================================
// DASHBOARD  (auth + verified)
// ============================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// ============================================
// PROFILE  (auth only — reachable even if unverified)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/picture', [ProfileController::class, 'removeProfilePicture'])->name('profile.picture.remove');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile-setup', [ProfileSetupController::class, 'show'])->name('profile.setup');
    Route::post('/profile-setup', [ProfileSetupController::class, 'store'])->name('profile.setup.store');
    Route::post('/profile-setup/skip', [ProfileSetupController::class, 'skip'])->name('profile.setup.skip');
});

// ============================================
// COMPANY  (auth + verified)
// ============================================
Route::prefix('company')->name('company.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/create', [OrganizationController::class, 'create'])->name('create');
    Route::post('/store', [OrganizationController::class, 'store'])->name('store');
    Route::get('/join', [OrganizationController::class, 'showJoin'])->name('join');
    Route::post('/join', [OrganizationController::class, 'join'])->name('join.submit');
    Route::get('/settings', [OrganizationController::class, 'settings'])->name('settings');
    Route::put('/settings', [OrganizationController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/regenerate-invite', [OrganizationController::class, 'regenerateInviteCode'])->name('settings.regenerate-invite');
    Route::delete('/logo', [OrganizationController::class, 'removeLogo'])->name('logo.remove');
    Route::delete('/destroy', [OrganizationController::class, 'destroy'])->name('destroy');
});

// ============================================
// PROJECTS  (auth + verified)
// ============================================
Route::resource('projects', ProjectController::class)->middleware(['auth', 'verified']);

Route::get('/projects/{project}/report', [ProjectController::class, 'report'])
    ->name('projects.report')
    ->middleware(['auth', 'verified']);

Route::post('/projects/{project}/documents', [ProjectController::class, 'uploadDocument'])
    ->name('projects.documents.upload')
    ->middleware(['auth', 'verified']);

Route::delete('/projects/{project}/documents/{document}', [ProjectController::class, 'deleteDocument'])
    ->name('projects.documents.delete')
    ->middleware(['auth', 'verified']);

Route::get('/projects/{project}/documents/{document}/download', [ProjectController::class, 'downloadDocument'])
    ->name('projects.documents.download')
    ->middleware(['auth', 'verified']);

// ============================================
// DEPARTMENTS  (auth + verified)
// NOTE: /create must come before /{department} to avoid route collision.
// ============================================
Route::prefix('departments')->name('departments.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('index');

    // Static /create BEFORE any /{department} route
    Route::get('/create', [DepartmentController::class, 'create'])->name('create');
    Route::post('/', [DepartmentController::class, 'store'])->name('store');

    // Assignment routes (use /{user} — must come before /{department}/{anything} if it could collide)
    Route::get('/{user}/assign-department',  [DepartmentController::class, 'assignDepartment'])->name('assign.department');
    Route::put('/{user}/assign-department',  [DepartmentController::class, 'updateDepartment'])->name('assign.department.update');

    // Member routes (nested on {department})
    Route::post('/{department}/members',              [DepartmentController::class, 'addMember'])->name('members.add');
    Route::delete('/{department}/members/{user}',     [DepartmentController::class, 'removeMember'])->name('members.remove');

    // Show / edit / update / destroy last
    Route::get('/{department}',         [DepartmentController::class, 'show'])->name('show');
    Route::get('/{department}/edit',    [DepartmentController::class, 'edit'])->name('edit');
    Route::put('/{department}',         [DepartmentController::class, 'update'])->name('update');
    Route::delete('/{department}',      [DepartmentController::class, 'destroy'])->name('destroy');
});

// ============================================
// TASKS  (auth + verified)
// ============================================
Route::prefix('tasks')->name('tasks.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/create/{project}', [TaskController::class, 'create'])->name('create');
    Route::post('/', [TaskController::class, 'store'])->name('store');

    Route::get('/departments/{department}/members-with-workload',
        [TaskController::class, 'getDepartmentMembersWithWorkload']
    )->name('department.members');

    Route::get('/smart-date-preview', [TaskController::class, 'smartDatePreview'])->name('smart-date-preview');

    Route::get('/{parentTask}/subtask/create', [TaskController::class, 'createSubtask'])->name('subtask.create');
    Route::post('/{parentTask}/subtask',       [TaskController::class, 'storeSubtask'])->name('subtask.store');
    Route::get('/subtasks/{task}',             [TaskController::class, 'showSubtask'])->name('subtask.show');
    Route::get('/subtasks/{task}/edit',        [TaskController::class, 'editSubtask'])->name('subtask.edit');
    Route::put('/subtasks/{task}',             [TaskController::class, 'updateSubtask'])->name('subtask.update');
    Route::delete('/subtasks/{task}',          [TaskController::class, 'destroySubtask'])->name('subtask.destroy');

    Route::get('/submissions/{submission}/download', [TaskController::class, 'downloadSubmission'])->name('submissions.download');
    Route::post('/submissions/{submission}/comment', [TaskController::class, 'addComment'])->name('submissions.comment');
    Route::post('/submissions/{submission}/review',  [TaskController::class, 'reviewSubmission'])->name('submissions.review');
    Route::delete('/submissions/{submission}',       [TaskController::class, 'deleteSubmission'])->name('submissions.delete');

    Route::get('/{task}', [TaskController::class, 'show'])->name('show');
    Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
    Route::put('/{task}', [TaskController::class, 'update'])->name('update');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    Route::post('/{task}/status', [TaskController::class, 'updateStatus'])->name('status');
    Route::get('/{task}/dependency-status', [TaskController::class, 'getDependencyStatus'])->name('dependency-status');
    Route::get('/{task}/submit', [TaskController::class, 'submitForm'])->name('submit');
    Route::post('/{task}/submit', [TaskController::class, 'uploadSubmission'])->name('submit.upload');

    Route::get('/{task}/assign', [TaskController::class, 'showAssign'])->name('assign');
    Route::post('/{task}/assign', [TaskController::class, 'assignMember'])->name('assign.store');
});

// ============================================
// PERSONAL  (auth + verified)
// ============================================
Route::prefix('personal')->name('personal.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects', [PersonalProjectController::class, 'index'])->name('index');
    Route::get('/projects/create', [PersonalProjectController::class, 'create'])->name('create');
    Route::post('/projects', [PersonalProjectController::class, 'store'])->name('store');
    Route::get('/projects/{project}', [PersonalProjectController::class, 'show'])->name('show');
    Route::get('/projects/{project}/invite', [PersonalProjectController::class, 'invite'])->name('invite');
    Route::post('/projects/{project}/invite', [PersonalProjectController::class, 'sendInvite'])->name('invite.send');
    Route::delete('/projects/{project}/members/{user}', [PersonalProjectController::class, 'removeMember'])->name('members.remove');
    Route::post('/projects/{project}/leave', [PersonalProjectController::class, 'leave'])->name('leave');
    Route::get('/join', [PersonalProjectController::class, 'showJoinPage'])->name('join-page');
    Route::post('/join-by-code', [PersonalProjectController::class, 'joinByCode'])->name('join-by-code');
    Route::delete('/projects/{project}', [PersonalProjectController::class, 'destroy'])->name('destroy');
});

// ============================================
// TEAM (legacy)  (auth + verified)
// ============================================
Route::prefix('team')->name('team.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('index');
    Route::get('/{id}', [TeamController::class, 'show'])->name('show');
    Route::delete('/remove/{userId}', [TeamController::class, 'removeMember'])->name('remove');
    Route::post('/leave', [TeamController::class, 'leaveCompany'])->name('leave');
});

// ============================================
// STATUS  (auth only — account setting)
// ============================================
Route::prefix('status')->name('status.')->middleware(['auth'])->group(function () {
    Route::get('/edit', [UserStatusController::class, 'edit'])->name('edit');
    Route::put('/update', [UserStatusController::class, 'update'])->name('update');
    Route::post('/quick', [UserStatusController::class, 'quickUpdate'])->name('quick');
    Route::get('/user/{id}', [UserStatusController::class, 'getStatus'])->name('user');
    Route::get('/team', [UserStatusController::class, 'getTeamStatus'])->name('team');
});

// ============================================
// NOTIFICATIONS  (auth only — should work even if unverified)
// ============================================
Route::prefix('notifications')->name('notifications.')->middleware(['auth'])->group(function () {

    // Static /data route MUST come before /{id} catch-alls
    Route::get('/data', [NotificationController::class, 'index'])->name('data');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');

    // Preferences — GET (read) + POST (write) to match your controller
    Route::get('/preferences',  [NotificationController::class, 'preferences'])->name('preferences');
    Route::post('/preferences', [NotificationController::class, 'updatePreferences'])->name('preferences.update');

    // Single-item actions — {id} last
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::delete('/{id}',     [NotificationController::class, 'destroy'])->name('destroy');

    // The Blade page — must be the LAST route in this group
    Route::get('/', [NotificationController::class, 'page'])->name('index');
});

// ============================================
// AI  (auth only — diagnostic)
// ============================================
Route::prefix('ai')->name('ai.')->middleware(['auth'])->group(function () {
    Route::get('/status', function () {
        $ai = app(AIService::class);
        return response()->json(['status' => $ai->isRunning() ? 'online' : 'offline']);
    })->name('status');

    Route::get('/start', function () {
        return back()->with('info', 'AI service start requested.');
    })->name('start');
});

// ============================================
// BREEZE (auth routes — includes Google OAuth)
// ============================================
require __DIR__.'/auth.php';