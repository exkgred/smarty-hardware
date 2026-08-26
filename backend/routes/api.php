<?php

use App\Http\Controllers\Admin\AdminChatbotController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSaleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CepController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SavedCardController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);

Route::post('/chat/send', [ChatbotController::class, 'send']);
Route::get('/chat/history/{sessionId}', [ChatbotController::class, 'history']);

Route::get('/payment/methods', [PaymentController::class, 'methods']);
Route::get('/cep/{cep}', [CepController::class, 'show'])->where('cep', '[0-9\-]+');
Route::post('/webhooks/stripe', [PaymentWebhookController::class, 'stripe']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me/cards', [SavedCardController::class, 'index']);
    Route::post('/me/cards', [SavedCardController::class, 'store']);
    Route::delete('/me/cards/{id}', [SavedCardController::class, 'destroy']);

    Route::post('/orders/checkout', [CartOrderController::class, 'checkout']);
    Route::get('/orders/my', [CartOrderController::class, 'myOrders']);
    Route::get('/orders/{id}', [CartOrderController::class, 'show']);
    Route::post('/payment/intent', [PaymentController::class, 'intent']);

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'show']);
        Route::get('/products', [AdminProductController::class, 'index']);
        Route::post('/products', [AdminProductController::class, 'store']);
        Route::put('/products/{id}', [AdminProductController::class, 'update']);
        Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);
        Route::get('/inventory/movements', [AdminInventoryController::class, 'recent']);
        Route::get('/inventory/{productId}/movements', [AdminInventoryController::class, 'movements']);
        Route::post('/inventory/adjust', [AdminInventoryController::class, 'adjust']);
        Route::post('/inventory/entries', [AdminInventoryController::class, 'entry']);
        Route::get('/inventory/alerts', [AdminInventoryController::class, 'alerts']);
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
        Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
        Route::post('/orders/{id}/refund', [AdminOrderController::class, 'refund']);
        Route::post('/orders/{id}/invoice', [AdminSaleController::class, 'issueInvoice']);
        Route::post('/sales', [AdminSaleController::class, 'store']);
        Route::get('/customers', [AdminSaleController::class, 'customers']);
        Route::get('/invoices', [AdminSaleController::class, 'invoices']);
        Route::get('/invoices/{id}', [AdminSaleController::class, 'showInvoice']);
        Route::get('/chatbot/knowledge', [AdminChatbotController::class, 'index']);
        Route::post('/chatbot/knowledge', [AdminChatbotController::class, 'store']);
        Route::delete('/chatbot/knowledge/{id}', [AdminChatbotController::class, 'destroy']);
    });
});
