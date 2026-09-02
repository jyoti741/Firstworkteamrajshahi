<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\ExpenseManager;
use App\Livewire\Admin\InventoryManager;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\SalesList;
use App\Livewire\Admin\SellerManager;
use App\Livewire\Admin\SellerOverview;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Seller\QuickSell;
use App\Livewire\Seller\TodaySales;
use Illuminate\Support\Facades\Route;

// Root / Redirection Dispatcher based on authenticated role
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('seller.quick-sell');
})->name('home');

// Authenticated Home / Dashboard Dispatcher
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('seller.quick-sell');
})->middleware(['auth'])->name('dashboard');

// ==========================================
// SELLER / STAFF ROUTES (Protected by 'seller' and 'seller.locale' middleware)
// ==========================================
Route::middleware(['auth', 'seller', 'seller.locale'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/quick-sell', QuickSell::class)->name('quick-sell');
    Route::get('/today-sales', TodaySales::class)->name('today-sales');
});

// ==========================================
// ADMIN / OWNER ROUTES (Strictly Protected by 'admin' middleware)
// ==========================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/sales', SalesList::class)->name('sales');
    Route::get('/expenses', ExpenseManager::class)->name('expenses');
    Route::get('/products', ProductManager::class)->name('products');
    Route::get('/inventory', InventoryManager::class)->name('inventory');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/sellers', SellerManager::class)->name('sellers');
    Route::get('/sellers/overview/{user?}', SellerOverview::class)->name('sellers.overview');
    Route::get('/settings', AdminSettings::class)->name('settings');
});

require __DIR__.'/settings.php';
