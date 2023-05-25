<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api.key'
], function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    });

    Route::middleware('auth:api')->group(function () {
        // Feed
        Route::prefix('feed')->group(function () {
            // Product
            Route::group([
                'prefix'        => 'product',
                'middleware'    => 'role:RESTO,ENTERPRISE'
            ], function () {
                Route::get('/', [FeedController::class, 'index_product']);
                Route::get('{product:id}', [FeedController::class, 'show_product']);
            });

            // Investment
            Route::group([
                'prefix'        => 'investment',
                'middleware'    => 'role:INVESTOR'
            ], function () {
                Route::get('/', [FeedController::class, 'index_investment']);
                Route::get('{invoice:id}', [FeedController::class, 'show_invoice']);
            });

            // Investment
            Route::group([
                'prefix'        => 'enterprise',
                'middleware'    => 'role:ENTERPRISE'
            ], function () {
                Route::get('/', [FeedController::class, 'index_enterprise']);
            });
        });

        // Transaction
        Route::prefix('transaction')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::get('{invoice:id}', [TransactionController::class, 'show']);
            Route::post('approve/{invoice:id}', [TransactionController::class, 'approve'])->middleware('role:ENTERPRISE');
            Route::post('cancel/{invoice:id}', [TransactionController::class, 'cancel'])->middleware('role:RESTO,ENTERPRISE');
            Route::post('update/{invoice:id}', [TransactionController::class, 'update_status'])->middleware('role:ADMIN');
        });

        // Checkout
        Route::prefix('checkout')->group(function () {
            // Product
            Route::group([
                'prefix'        => 'product',
                'middleware'    => 'role:RESTO'
            ], function () {
                Route::get('/', [CheckoutController::class, 'index_product']);
                Route::post('/', [CheckoutController::class, 'checkout_product']);
            });

            // Investment
            Route::group([
                'prefix'        => 'investment',
                'middleware'    => 'role:INVESTOR'
            ], function () {
                Route::get('/', [CheckoutController::class, 'index_investment']);
                Route::post('/', [CheckoutController::class, 'checkout_investment']);
            });
        });

        // Bank
        Route::group([
            'prefix'        => 'bank',
            'middleware'    => 'role:INVESTOR,RESTO'
        ], function () {
            Route::get('/', [BankController::class, 'index']);
        });

        // Profile
        Route::group([
            'prefix'        => 'profile',
            'middleware'    => 'role:INVESTOR,RESTO,ENTERPRISE'
        ], function () {
            Route::get('/', [ProfileController::class, 'index']);
        });

        // Dashboard
        Route::group([
            'prefix'        => 'dashboard',
            'middleware'    => 'role:ADMIN'
        ], function () {
            Route::get('/', [DashboardController::class, 'index']);
        });
    });
});
