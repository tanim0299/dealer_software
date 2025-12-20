<?php

use App\Http\Controllers\BackendController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuSectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WebsiteSettingsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SubUnitController;
use App\Models\Item;
use App\Models\WebsiteSettings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', [BackendController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resources([
        'menu_section' => MenuSectionController::class,
        'menu' => MenuController::class,
        'role' => RoleController::class,
        'user' => UserController::class,
        'item' => ItemController::class,
        'category' => CategoryController::class,
        'brand' => BrandController::class,
        'website_settings' => WebsiteSettingsController::class,
        'product' => ProductController::class,
        'supplier' => SupplierController::class,

        'purchase' => PurchaseController::class,

        'unit' => UnitController::class,
        'sub_unit' => SubUnitController::class,

    ]);
    Route::get('/api/products', [ProductController::class, 'fetch']);

    Route::post('get_itemwise_category',[CategoryController::class,'itemWiseCategory'])->name('category.get_itemwise_category');

});
Route::post('role_permission/{id}',[RoleController::class,'permission'])->name('role.permission');

//menu section
Route::post('menu_section_status', [MenuSectionController::class, 'status'])->name('menu_section.status');

require __DIR__.'/auth.php';
