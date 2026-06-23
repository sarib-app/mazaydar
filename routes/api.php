<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, AddressController, MenuController, PackageController, SubscriptionController};
use App\Http\Controllers\Admin\{AdminAuthController, AdminController};
use App\Http\Middleware\{AuthMiddleware, AdminAuthMiddleware};

// ── Auth ──────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('send-otp',    [AuthController::class, 'sendOtp']);
    Route::post('verify-otp',  [AuthController::class, 'verifyOtp']);
    Route::middleware(AuthMiddleware::class)->group(function () {
        Route::get('me',           [AuthController::class, 'me']);
        Route::patch('profile',    [AuthController::class, 'updateProfile']);
        Route::post('logout',      [AuthController::class, 'logout']);
    });
});

// ── Public ────────────────────────────────────────────────────────────────
Route::get('menu',            [MenuController::class, 'index']);
Route::get('menu/weekly',     [MenuController::class, 'weekly']);
Route::get('menu/{id}',       [MenuController::class, 'show']);
Route::get('packages',        [PackageController::class, 'index']);
Route::get('packages/{id}',   [PackageController::class, 'show']);

// ── Protected (user) ──────────────────────────────────────────────────────
Route::middleware(AuthMiddleware::class)->group(function () {
    Route::get('addresses',              [AddressController::class, 'index']);
    Route::post('addresses',             [AddressController::class, 'store']);
    Route::patch('addresses/{id}',       [AddressController::class, 'update']);
    Route::delete('addresses/{id}',      [AddressController::class, 'destroy']);

    Route::get('subscriptions',                          [SubscriptionController::class, 'index']);
    Route::post('subscriptions',                         [SubscriptionController::class, 'store']);
    Route::get('subscriptions/{id}',                     [SubscriptionController::class, 'show']);
    Route::patch('subscriptions/{id}/pause',             [SubscriptionController::class, 'pause']);
    Route::patch('subscriptions/{id}/resume',            [SubscriptionController::class, 'resume']);
    Route::post('subscriptions/{id}/delivery-days',      [SubscriptionController::class, 'setDeliveryDays']);
    Route::post('subscriptions/{id}/selections',         [SubscriptionController::class, 'setSelection']);
});

// ── Admin Auth ────────────────────────────────────────────────────────────
Route::prefix('admin/auth')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::middleware(AdminAuthMiddleware::class)->group(function () {
        Route::get('admins',                  [AdminAuthController::class, 'list']);
        Route::post('admins',                 [AdminAuthController::class, 'create']);
        Route::delete('admins/{id}',          [AdminAuthController::class, 'remove']);
        Route::patch('admins/{id}/password',  [AdminAuthController::class, 'changePassword']);
    });
});

// ── Admin ─────────────────────────────────────────────────────────────────
Route::prefix('admin')->middleware(AdminAuthMiddleware::class)->group(function () {
    Route::get('stats',   [AdminController::class, 'stats']);
    Route::get('users',   [AdminController::class, 'users']);

    Route::get('menu',           [AdminController::class, 'menuIndex']);
    Route::post('menu',          [AdminController::class, 'menuStore']);
    Route::patch('menu/{id}',    [AdminController::class, 'menuUpdate']);
    Route::delete('menu/{id}',   [AdminController::class, 'menuDestroy']);

    Route::get('weekly',         [AdminController::class, 'weeklyIndex']);
    Route::patch('weekly/{id}',  [AdminController::class, 'weeklyUpdate']);

    Route::get('package-menus',  [AdminController::class, 'packageMenus']);
    Route::post('package-menus', [AdminController::class, 'packageMenuSet']);

    Route::get('packages',           [AdminController::class, 'packagesIndex']);
    Route::post('packages',          [AdminController::class, 'packagesStore']);
    Route::patch('packages/{id}',    [AdminController::class, 'packagesUpdate']);
    Route::delete('packages/{id}',   [AdminController::class, 'packagesDestroy']);

    Route::get('subscriptions',                             [AdminController::class, 'subscriptions']);
    Route::patch('subscriptions/{id}/status',               [AdminController::class, 'subscriptionStatus']);
    Route::patch('subscriptions/{id}/delivery-days',        [AdminController::class, 'subscriptionDeliveryDays']);
    Route::get('subscriptions/{id}/week',                   [AdminController::class, 'subscriptionWeek']);
    Route::post('subscriptions/{id}/week-selection',        [AdminController::class, 'subscriptionWeekSelection']);
});
