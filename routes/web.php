<?php

use App\Http\Controllers\BackendController;
use App\Http\Controllers\MenuSectionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [BackendController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resources([
        'menu_section' => MenuSectionController::class,
    ]);

});


//menu section
Route::post('menu_section_status', [MenuSectionController::class, 'status'])->name('menu_section.status');

require __DIR__.'/auth.php';
