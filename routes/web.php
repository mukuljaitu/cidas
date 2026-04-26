<?php

use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\VariantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/admin/notifications', [AdminNotificationController::class, 'store'])->name('admin.notifications.store');
    Route::post('/notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{notificationId}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::put('/employees/{employee}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy']);

    Route::get('/parties', [PartyController::class, 'index']);
    Route::post('/parties', [PartyController::class, 'store']);
    Route::put('/parties/{party}', [PartyController::class, 'update']);
    Route::delete('/parties/{party}', [PartyController::class, 'destroy']);

    Route::get('/transports', [TransportController::class, 'index']);
    Route::post('/transports', [TransportController::class, 'store']);
    Route::put('/transports/{transport}', [TransportController::class, 'update']);
    Route::delete('/transports/{transport}', [TransportController::class, 'destroy']);

    Route::get('/cities', [CityController::class, 'index']);
    Route::post('/cities', [CityController::class, 'store']);
    Route::put('/cities/{city}', [CityController::class, 'update']);
    Route::delete('/cities/{city}', [CityController::class, 'destroy']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}/variants', [ProductController::class, 'variants']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::get('/variants', [VariantController::class, 'index']);
    Route::post('/variants', [VariantController::class, 'store']);
    Route::put('/variants/{variant}', [VariantController::class, 'update']);
    Route::delete('/variants/{variant}', [VariantController::class, 'destroy']);

    Route::get('/view-tours', [TourController::class, 'index'])->name('tours.index');
    Route::put('/view-tours/{tour}', [TourController::class, 'update'])->name('tours.update');
    Route::delete('/view-tours/{tour}', [TourController::class, 'destroy'])->name('tours.destroy');

    Route::get('/view-employees', [EmployeeController::class, 'analyze'])->name('employees.analyze');

    Route::get('/view-orders', [OrderController::class, 'analyze'])->name('orders.analyze');

    Route::get('/banks', [BankController::class, 'index'])->name('banks.index');
    Route::post('/banks', [BankController::class, 'store'])->name('banks.store');
    Route::put('/banks/{bank}', [BankController::class, 'update'])->name('banks.update');
    Route::delete('/banks/{bank}', [BankController::class, 'destroy'])->name('banks.destroy');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/api/list', [OrderController::class, 'apiList'])->name('orders.api.list');
    Route::get('/orders/api/details/{order}', [OrderController::class, 'apiDetails'])->name('orders.api.details');
    Route::post('/orders/api/items/bulk', [OrderController::class, 'apiItemsBulk'])->name('orders.api.items.bulk');
    Route::get('/orders/api/salesmen', [OrderController::class, 'apiSalesmen'])->name('orders.api.salesmen');
    Route::get('/orders/api/parties', [OrderController::class, 'apiParties'])->name('orders.api.parties');
    Route::get('/orders/api/transports', [OrderController::class, 'apiTransports'])->name('orders.api.transports');
    Route::get('/orders/api/transports/{transport}', [OrderController::class, 'apiTransportDetails'])->name('orders.api.transports.details');
    Route::get('/orders/api/products', [OrderController::class, 'apiProducts'])->name('orders.api.products');
    Route::get('/orders/api/product-packings', [OrderController::class, 'apiProductPackings'])->name('orders.api.product-packings');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');
    Route::put('/order-items/{item}', [OrderController::class, 'updateItemQty'])->name('orders.items.update');
    Route::delete('/order-items/{item}', [OrderController::class, 'deleteItem'])->name('orders.items.delete');

    Route::get('/create-tour', [TourController::class, 'create'])->name('tours.create');
    Route::get('/create-tour/cities', [TourController::class, 'cities'])->name('tours.cities');
    Route::post('/create-tour', [TourController::class, 'store'])->name('tours.store');
    Route::post('/quick-add-city', [TourController::class, 'quickAddCity'])->name('tours.quick-add-city');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
