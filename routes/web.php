<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\StandController as AdminStandController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\TopUpController as AdminTopUpController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\MenuController as SellerMenuController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\ReportController as SellerReportController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\XoftwareWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logout', function () {
    return redirect('/');
});

Route::get('/dashboard', function () {
    $stands = \App\Models\Stand::query()
        ->where('is_active', true)
        ->withCount(['menuItems as available_menu_count' => fn($q) => $q->where('is_available', true)])
        ->having('available_menu_count', '>', 0)
        ->orderBy('name')
        ->get();
    return view('dashboard', compact('stands'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
    
    // Cart routes
    Route::post('/cart/add/{menuItem}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
    Route::post('/cart/update/{itemId}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/cart/remove/{itemId}', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
    
    // Top Up routes
    Route::get('/topup', [TopUpController::class, 'index'])->name('topup.index');
    Route::post('/topup', [TopUpController::class, 'store'])->name('topup.store');
    Route::match(['get', 'post'], '/topup/qris', fn() => redirect()->route('topup.index'));
    Route::get('/topup/history', [TopUpController::class, 'history'])->name('topup.history');
    Route::get('/topup/{transaction}', [TopUpController::class, 'show'])->name('topup.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('menu', AdminMenuController::class)->except(['show']);
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::resource('stands', AdminStandController::class);
    Route::get('sellers', [AdminSellerController::class, 'index'])->name('sellers.index');
    Route::patch('sellers/{seller}', [AdminSellerController::class, 'update'])->name('sellers.update');
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/excel', [AdminReportController::class, 'exportExcel'])->name('reports.exportExcel');
    Route::get('reports/export/pdf', [AdminReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    // Admin Top Up management
    Route::get('topup', [AdminTopUpController::class, 'index'])->name('topup.index');
    Route::post('topup/{transaction}/approve', [AdminTopUpController::class, 'approve'])->name('topup.approve');
    Route::post('topup/{transaction}/reject', [AdminTopUpController::class, 'reject'])->name('topup.reject');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/seller/pending', function () {
        $profile = auth()->user()->sellerProfile;
        if ($profile?->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }
        if ($profile?->status === 'rejected') {
            return redirect()->route('seller.rejected');
        }
        return view('seller.pending');
    })->name('seller.pending');

    Route::get('/seller/rejected', function () {
        $profile = auth()->user()->sellerProfile;
        if ($profile?->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }
        return view('seller.rejected');
    })->name('seller.rejected');

    Route::get('/seller/status-check', function () {
        $profile = auth()->user()->sellerProfile;
        return response()->json(['status' => $profile?->status ?? 'pending']);
    })->name('seller.status-check');
});

Route::middleware(['auth', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

    Route::resource('menu', SellerMenuController::class);
    Route::get('orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('reports', [SellerReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/excel', [SellerReportController::class, 'exportExcel'])->name('reports.exportExcel');
    Route::get('reports/export/pdf', [SellerReportController::class, 'exportPdf'])->name('reports.exportPdf');
});

// Webhook route
Route::match(['get', 'post'], '/webhook/xoftware', [XoftwareWebhookController::class, 'handle']);

require __DIR__.'/auth.php';
