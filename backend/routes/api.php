<?php
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\PosController;
use App\Http\Controllers\Api\V1\PurchaseController;
use App\Http\Controllers\Api\V1\ImportController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;
Route::prefix('v1')->group(function () {
    Route::get('health', HealthController::class);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::get('dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view');
        Route::get('medicines', [InventoryController::class, 'medicines'])->middleware('permission:inventory.view');
        Route::get('medicines/low-stock', [InventoryController::class, 'lowStock'])->middleware('permission:inventory.view');
        Route::get('medicines/near-expiry', [InventoryController::class, 'nearExpiry'])->middleware('permission:inventory.view');
        Route::post('pos/sales', [PosController::class, 'store'])->middleware('permission:pos.use');
        Route::get('sales', [PosController::class, 'index'])->middleware('permission:pos.use');
        Route::get('suppliers', [PurchaseController::class, 'suppliers'])->middleware('permission:purchases.view');
        Route::get('reports/daily-sales', [ReportController::class, 'dailySales'])->middleware('permission:reports.view');
        Route::post('import/medicines', [ImportController::class, 'medicines'])->middleware('permission:inventory.manage');
        Route::get('export/inventory', [ImportController::class, 'exportInventory'])->middleware('permission:inventory.view');
        Route::get('users', [UserController::class, 'index'])->middleware('permission:users.view');
        Route::get('settings', [SettingController::class, 'index'])->middleware('permission:settings.view');
        Route::put('settings', [SettingController::class, 'update'])->middleware('permission:settings.manage');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->middleware('permission:audit.view');
    });
});
