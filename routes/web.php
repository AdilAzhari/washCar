<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BayController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\WashController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicQueueController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Public queue routes (no authentication required)
Route::get('/queue/join/{branchCode}', [PublicQueueController::class, 'join'])->name('queue.join');
Route::post('/queue/join/{branchCode}', [PublicQueueController::class, 'submitJoin'])->name('queue.submit');
Route::get('/queue/status/{branchCode}/{queueId}', [PublicQueueController::class, 'status'])->name('queue.status');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Branches
    Route::resource('branches', BranchController::class);
    Route::get('/branches/{branch}/qrcode', [BranchController::class, 'qrcode'])->name('branches.qrcode');

    // Bays
    Route::resource('bays', BayController::class)->except(['create', 'edit']);
    Route::patch('/bays/{bay}/status', [BayController::class, 'updateStatus'])->name('bays.update-status');
    Route::get('/bays/{bay}/activity-log', [BayController::class, 'activityLog'])->name('bays.activity-log');

    // Customers
    Route::resource('customers', CustomerController::class)->except(['create', 'edit']);

    // Packages
    Route::resource('packages', PackageController::class)->except(['create', 'edit', 'show']);

    // Washes
    Route::resource('washes', WashController::class)->except(['create', 'edit']);

    // Queue
    Route::resource('queue', QueueController::class)->except(['create', 'edit']);
    Route::get('/queue-view', [QueueController::class, 'viewQueue'])->name('queue.view');
    Route::post('/queue/{queue}/start', [QueueController::class, 'start'])->name('queue.start');
    Route::post('/queue/{queue}/cancel', [QueueController::class, 'cancel'])->name('queue.cancel');
    Route::post('/wash/{wash}/complete', [QueueController::class, 'completeWash'])->name('wash.complete');
    Route::post('/wash/{wash}/cancel', [QueueController::class, 'cancelWash'])->name('wash.cancel');

    // Staff
    Route::resource('staff', StaffController::class)->except(['create', 'edit', 'show']);

    // Inventory
    Route::resource('inventory', InventoryController::class)->except(['create', 'edit', 'show']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
