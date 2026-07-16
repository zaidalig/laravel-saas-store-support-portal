<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminResourceController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\OrderPlacementController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/products', [PublicController::class, 'products'])->name('products');
Route::get('/products/{slug}', [PublicController::class, 'productDetail'])->name('products.show');
Route::get('/pricing', [PublicController::class, 'pricing'])->name('pricing');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'submitContact'])->name('contact.store');
Route::get('/support', [PublicController::class, 'support'])->name('support');
Route::get('/track-order', [PublicController::class, 'trackOrder'])->name('track-order');
Route::post('/track-order', [PublicController::class, 'trackOrderResult'])->name('track-order.result');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'customer.access'])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::get('/', [CustomerDashboardController::class, 'index']);
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('.orders');
    Route::get('/orders/{order}', [CustomerDashboardController::class, 'order'])->name('.orders.show');
    Route::get('/invoices', [CustomerDashboardController::class, 'invoices'])->name('.invoices');
    Route::get('/payments', [CustomerDashboardController::class, 'payments'])->name('.payments');
    Route::get('/tickets', [CustomerDashboardController::class, 'tickets'])->name('.tickets');
    Route::get('/tickets/create', [CustomerDashboardController::class, 'createTicket'])->name('.tickets.create');
    Route::post('/tickets', [CustomerDashboardController::class, 'storeTicket'])->name('.tickets.store');
    Route::get('/tickets/{ticket}', [CustomerDashboardController::class, 'ticket'])->name('.tickets.show');
    Route::post('/tickets/{ticket}/reply', [CustomerDashboardController::class, 'reply'])->name('.tickets.reply');
    Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('.profile');
    Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('.profile.update');
});

Route::middleware(['auth', 'customer.access'])->group(function () {
    Route::get('/order/{product?}', [OrderPlacementController::class, 'create'])->name('orders.place');
    Route::post('/order', [OrderPlacementController::class, 'store'])->name('orders.store');
});

Route::middleware(['auth', 'admin.access'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    $staffResources = ['categories', 'products', 'pricing-plans', 'contact-messages'];
    $adminOnlyResources = ['users', 'teams', 'team-members', 'settings'];

    foreach ($staffResources as $resource) {
        Route::get("/{$resource}", [AdminResourceController::class, 'index'])->defaults('resource', $resource)->name("{$resource}.index");
        Route::get("/{$resource}/create", [AdminResourceController::class, 'create'])->defaults('resource', $resource)->name("{$resource}.create");
        Route::post("/{$resource}", [AdminResourceController::class, 'store'])->defaults('resource', $resource)->name("{$resource}.store");
        Route::get("/{$resource}/{id}/edit", [AdminResourceController::class, 'edit'])->defaults('resource', $resource)->name("{$resource}.edit");
        Route::put("/{$resource}/{id}", [AdminResourceController::class, 'update'])->defaults('resource', $resource)->name("{$resource}.update");
        Route::delete("/{$resource}/{id}", [AdminResourceController::class, 'destroy'])->defaults('resource', $resource)->name("{$resource}.destroy");
    }

    Route::middleware('admin.only')->group(function () use ($adminOnlyResources) {
        foreach ($adminOnlyResources as $resource) {
            Route::get("/{$resource}", [AdminResourceController::class, 'index'])->defaults('resource', $resource)->name("{$resource}.index");
            Route::get("/{$resource}/create", [AdminResourceController::class, 'create'])->defaults('resource', $resource)->name("{$resource}.create");
            Route::post("/{$resource}", [AdminResourceController::class, 'store'])->defaults('resource', $resource)->name("{$resource}.store");
            Route::get("/{$resource}/{id}/edit", [AdminResourceController::class, 'edit'])->defaults('resource', $resource)->name("{$resource}.edit");
            Route::put("/{$resource}/{id}", [AdminResourceController::class, 'update'])->defaults('resource', $resource)->name("{$resource}.update");
            Route::delete("/{$resource}/{id}", [AdminResourceController::class, 'destroy'])->defaults('resource', $resource)->name("{$resource}.destroy");
        }
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::resource('orders', AdminOrderController::class);
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [AdminPaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{payment}', [AdminPaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');
    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [AdminInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/print', [AdminInvoiceController::class, 'print'])->name('invoices.print');
    Route::put('/invoices/{invoice}', [AdminInvoiceController::class, 'update'])->name('invoices.update');
    Route::get('/support-tickets', [AdminTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('/support-tickets/{supportTicket}', [AdminTicketController::class, 'show'])->name('support-tickets.show');
    Route::put('/support-tickets/{supportTicket}', [AdminTicketController::class, 'update'])->name('support-tickets.update');
    Route::post('/support-tickets/{supportTicket}/reply', [AdminTicketController::class, 'reply'])->name('support-tickets.reply');
});
