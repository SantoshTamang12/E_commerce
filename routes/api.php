<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\SubCategoryApiController;
use App\Http\Controllers\Api\FieldApiController;
use App\Http\Controllers\Api\UserFavouriteApiController;
use App\Http\Controllers\Api\UserAdApiController;
use App\Http\Controllers\Api\AdApiController;
use App\Http\Controllers\Api\BannerApiController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\ImportApiController;


Route::group(['prefix' => 'v2'], function () {

    Route::get('categories', [ImportApiController::class, 'categories']);
    Route::post('categoriesImport', [ImportApiController::class, 'importCategories']);
    Route::get('subcategories', [ImportApiController::class, 'subcategories']);
    Route::post('subcategoriesImport', [ImportApiController::class, 'importSubCategories']);
    Route::get('ads', [ImportApiController::class, 'ads']);
    Route::post('adsImport', [ImportApiController::class, 'importAds']);
    Route::post('favrouitesAdsImport', [ImportApiController::class, 'importFavouriteAds']);
    Route::get('users', [ImportApiController::class, 'users']);
    Route::post('usersImport', [ImportApiController::class, 'importUsers']);

    Route::post('firebase-login', [AuthApiController::class, 'tokenLogin']);
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('signup', [AuthApiController::class, 'signUp']);

    // Category SubCategory
    Route::apiResource('categories', CategoryApiController::class)->except(['create', 'store', 'edit', 'update', 'show', 'destroy']);
    Route::get('categories/{category}', [SubCategoryApiController::class, 'index']);
    Route::get('fields/{category}/{subcategory}', [FieldApiController::class, 'index']);

    Route::get('ads-search', [AdApiController::class, 'search']);

    // Banners
    Route::get('banners', [BannerApiController::class, 'all']);

    // Browse Ads
    Route::get('freshAds', [AdApiController::class, 'freshAds']);
    Route::post('locationAds', [AdApiController::class, 'locationBasedAds']);
    Route::post('category-subcategory-ads', [AdApiController::class, 'getAdsWhichHasCategoryAndSubcategory']);

    // User
    Route::group(['prefix' => 'auth', 'middleware' => 'auth:api'], function () {

        Route::get('resend-phone-verification', [AuthApiController::class, 'resendVerificationCode']);
        Route::get('profile', [AuthApiController::class, 'profile']);

        Route::post('complete-profile', [AuthApiController::class, 'completeProfile']);
        Route::post('change-password', [AuthApiController::class, 'changePassword']);
        Route::post('update', [AuthApiController::class, 'update']);

        Route::post('update-profile', [AuthApiController::class, 'updateProfile']);
        Route::post('logout', [AuthApiController::class, 'logout']);

        // User Ads
        Route::post('user/ads', [UserAdApiController::class, 'index']);
        Route::post('ads', [UserAdApiController::class, 'store']);
        Route::post('ads/show', [UserAdApiController::class, 'show']);
        Route::post('ads/delete', [UserAdApiController::class, 'destroy']);
        Route::post('ads/renew', [UserAdApiController::class, 'renew']);

        Route::post('verify-phone', [AuthApiController::class, 'verifyPhone']);

        // Favuorites
        Route::get('user-favourites', [UserFavouriteApiController::class, 'index']);

        Route::post('is-ad-favourite', [UserFavouriteApiController::class, 'isFavourite']);
        Route::post('user-favourite', [UserFavouriteApiController::class, 'toggle']);
    });
    Route::group(['prefix' => 'chat', 'middleware' => 'auth:api'], function () {
        Route::post('initiate', [ChatApiController::class, 'initiate']);
        Route::post('send-message', [ChatApiController::class, 'send']);
        Route::post('fetch-messages', [ChatApiController::class, 'fetch']);
        Route::get('conversations', [ChatApiController::class, 'conversations']);
    });
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
