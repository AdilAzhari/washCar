<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BayController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\Customer\CustomerAppointmentController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerLoyaltyController;
use App\Http\Controllers\Customer\CustomerWashHistoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicQueueController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WashController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public queue routes (no authentication required)
Route::get('/queue/join/{branchCode}', [PublicQueueController::class, 'join'])->name('queue.join');
Route::post('/queue/join/{branchCode}', [PublicQueueController::class, 'submitJoin'])->name('queue.submit');
Route::get('/queue/status/{branchCode}/{queueId}', [PublicQueueController::class, 'status'])->name('queue.status');

// ============================================================================
// ADMIN ROUTES - Full system access, all branches
// ============================================================================
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Branches
    Route::resource('branches', BranchController::class);
    Route::get('/branches/{branch}/qrcode', [BranchController::class, 'qrcode'])->name('branches.qrcode');

    // Bays (all branches)
    Route::resource('bays', BayController::class)->except(['create', 'edit']);
    Route::patch('/bays/{bay}/status', [BayController::class, 'updateStatus'])->name('bays.update-status');
    Route::get('/bays/{bay}/activity-log', [BayController::class, 'activityLog'])->name('bays.activity-log');

    // Customers (all branches)
    Route::resource('customers', CustomerController::class)->except(['create', 'edit']);

    // Packages (all branches)
    Route::resource('packages', PackageController::class)->except(['create', 'edit', 'show']);

    // Washes (all branches)
    Route::resource('washes', WashController::class)->except(['create', 'edit']);

    // Queue (all branches)
    Route::resource('queue', QueueController::class)->except(['create', 'edit']);
    Route::get('/queue-view', [QueueController::class, 'viewQueue'])->name('queue.view');
    Route::get('/queue-waiting', [QueueController::class, 'waitingQueue'])->name('queue.waiting');
    Route::get('/queue-in-progress', [QueueController::class, 'inProgress'])->name('queue.in-progress');
    Route::post('/queue/{queue}/start', [QueueController::class, 'start'])->name('queue.start');
    Route::post('/queue/{queue}/cancel', [QueueController::class, 'cancel'])->name('queue.cancel');
    Route::post('/queue/{queue}/confirm-payment', [QueueController::class, 'confirmPayment'])->name('queue.confirm-payment');
    Route::post('/queue/{queue}/update-package', [QueueController::class, 'updatePackage'])->name('queue.update-package');
    Route::post('/wash/{wash}/complete', [QueueController::class, 'completeWash'])->name('wash.complete');
    Route::post('/wash/{wash}/cancel', [QueueController::class, 'cancelWash'])->name('wash.cancel');

    // Staff management (all branches)
    Route::resource('staff', StaffController::class)->except(['create', 'edit', 'show']);

    // Inventory (all branches)
    Route::resource('inventory', InventoryController::class)->except(['create', 'edit', 'show']);

    // Appointments (all branches)
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/start', [AppointmentController::class, 'start'])->name('appointments.start');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('/appointments/{appointment}/no-show', [AppointmentController::class, 'markNoShow'])->name('appointments.no-show');

    // Transactions (all branches)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});

// ============================================================================
// MANAGER ROUTES - Branch-scoped management (own branch + read-only all)
// ============================================================================
Route::middleware(['auth', 'verified', 'role:manager', 'branch'])->prefix('manager')->name('manager.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ManagerDashboardController::class, 'reports'])->name('reports');
    Route::get('/analytics/all-branches', [ManagerDashboardController::class, 'allBranchesAnalytics'])->name('analytics.all-branches');

    // Staff management (own branch only)
    Route::resource('staff', StaffController::class)->except(['create', 'edit', 'show']);

    // Packages (own branch)
    Route::resource('packages', PackageController::class)->except(['create', 'edit', 'show']);

    // Inventory (own branch)
    Route::resource('inventory', InventoryController::class)->except(['create', 'edit', 'show']);

    // Appointments (own branch)
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/start', [AppointmentController::class, 'start'])->name('appointments.start');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('/appointments/{appointment}/no-show', [AppointmentController::class, 'markNoShow'])->name('appointments.no-show');

    // Queue management (own branch)
    Route::resource('queue', QueueController::class)->except(['create', 'edit']);
    Route::get('/queue-view', [QueueController::class, 'viewQueue'])->name('queue.view');
    Route::get('/queue-waiting', [QueueController::class, 'waitingQueue'])->name('queue.waiting');
    Route::get('/queue-in-progress', [QueueController::class, 'inProgress'])->name('queue.in-progress');
    Route::post('/queue/{queue}/start', [QueueController::class, 'start'])->name('queue.start');
    Route::post('/queue/{queue}/cancel', [QueueController::class, 'cancel'])->name('queue.cancel');
    Route::post('/queue/{queue}/confirm-payment', [QueueController::class, 'confirmPayment'])->name('queue.confirm-payment');
    Route::post('/queue/{queue}/update-package', [QueueController::class, 'updatePackage'])->name('queue.update-package');
    Route::post('/wash/{wash}/complete', [QueueController::class, 'completeWash'])->name('wash.complete');
    Route::post('/wash/{wash}/cancel', [QueueController::class, 'cancelWash'])->name('wash.cancel');

    // Bays (own branch)
    Route::resource('bays', BayController::class)->except(['create', 'edit']);
    Route::patch('/bays/{bay}/status', [BayController::class, 'updateStatus'])->name('bays.update-status');
    Route::get('/bays/{bay}/activity-log', [BayController::class, 'activityLog'])->name('bays.activity-log');

    // Customers (own branch)
    Route::resource('customers', CustomerController::class)->except(['create', 'edit']);

    // Washes (own branch)
    Route::resource('washes', WashController::class)->except(['create', 'edit']);

    // Transactions (own branch)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});

// ============================================================================
// STAFF ROUTES - Branch-scoped operations (own branch only)
// ============================================================================
Route::middleware(['auth', 'verified', 'role:staff', 'branch'])->prefix('staff')->name('staff.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Queue management (own branch only)
    Route::resource('queue', QueueController::class)->only(['index', 'show']);
    Route::post('/queue/{queue}/start', [QueueController::class, 'start'])->name('queue.start');
    Route::post('/queue/{queue}/cancel', [QueueController::class, 'cancel'])->name('queue.cancel');
    Route::post('/queue/{queue}/confirm-payment', [QueueController::class, 'confirmPayment'])->name('queue.confirm-payment');
    Route::post('/wash/{wash}/complete', [QueueController::class, 'completeWash'])->name('wash.complete');
    Route::post('/wash/{wash}/cancel', [QueueController::class, 'cancelWash'])->name('wash.cancel');

    // Bays (own branch, limited to status updates)
    Route::resource('bays', BayController::class)->only(['index', 'update']);
    Route::patch('/bays/{bay}/status', [BayController::class, 'updateStatus'])->name('bays.update-status');

    // Appointments (own branch, view and manage)
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/start', [AppointmentController::class, 'start'])->name('appointments.start');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');

    // Customers (read-only for own branch)
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    // Inventory (read-only for own branch)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
});

// ============================================================================
// CUSTOMER ROUTES - Customer portal
// ============================================================================
Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Appointments
    Route::resource('appointments', CustomerAppointmentController::class)->except(['edit']);

    // Wash history
    Route::get('/history', [CustomerWashHistoryController::class, 'index'])->name('history');

    // Loyalty points
    Route::get('/loyalty', [CustomerLoyaltyController::class, 'index'])->name('loyalty');
    Route::post('/loyalty/redeem', [CustomerLoyaltyController::class, 'redeem'])->name('loyalty.redeem');
});

// ============================================================================
// SHARED ROUTES - Available to all authenticated users
// ============================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

require __DIR__ . '/auth.php';
