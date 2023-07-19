<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\InAppAdController;


Route::get('/', function () {
    return redirect('/login');
});

Auth::routes([
    'register' => false,
    'verify'   => false,
    'reset'    => false,
]);

Route::group(['middleware' => 'auth'], function(){

    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Ads
    Route::resource('ads', AdController::class);
    Route::post('ads/bulkDelete', [AdController::class, 'bulkDelete'])
            ->name('ads.bulkDelete');

    // Banners
    Route::resource('banners', BannerController::class)->except(['store',  'update']);
    Route::post('banners/bulkStore', [BannerController::class, 'bulkStore'])
            ->name('banners.bulkStore');
    Route::post('banners/bulkDelete', [BannerController::class, 'bulkDelete'])
            ->name('banners.bulkDelete');

    // Category & Sub Categories
    Route::resource('categories', CategoryController::class)->except('create', 'show', 'update');
    Route::post('categories/bulkStore', [CategoryController::class, 'bulkStore'])
            ->name('categories.bulkStore');
    Route::post('categories/bulkDelete', [CategoryController::class, 'bulkDelete'])
            ->name('categories.bulkDelete');

    Route::resource('subcategories', SubCategoryController::class);
    Route::get('categories/{category}/subcategories', [SubCategoryController::class, 'index'])
        ->name('categories.subcategories');
    Route::post('categories/{category}/subcategories/bulkStore', [SubCategoryController::class, 'bulkStore'])
            ->name('subcategories.bulkStore');
    Route::post('subcategories/bulkDelete', [SubCategoryController::class, 'bulkDelete'])
            ->name('subcategories.bulkDelete');
    

    // Fields
    Route::resource('fields', FieldController::class)->except('index', 'store');
    Route::get('categories/{category}/subcategories/{subcategory}/fields', [FieldController::class, 'index'])
        ->name('fields');
    Route::post('categories/{category}/subcategories/{subcategory}/fields', [FieldController::class, 'store'])
            ->name('fields.store');
    Route::post('fields/bulkDelete', [FieldController::class, 'bulkDelete'])
            ->name('fields.bulkDelete');

    // InAppAdd
    Route::resource('inapp-ads', InAppAdController::class)->except('store');
    Route::post('inapp-ads/bulkStore', [InAppAdController::class, 'bulkStore'])
            ->name('inapp-ads.bulkStore');
    Route::post('inapp-ads/bulkDelete', [InAppAdController::class, 'bulkDelete'])
            ->name('inapp-ads.bulkDelete');

    // Users
    Route::resource('users', UserController::class);
   
    // AdmiProfile
    Route::resource('profile', ProfileController::class);
    Route::patch('/profile/{admin}/updatePassword', [ProfileController::class, 'updatePassword'])->name('admin.password.update');

});

