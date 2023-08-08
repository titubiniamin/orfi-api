<?php

use App\Http\Controllers\Admin\UserCrudController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('system-user', 'SystemUserCrudController');
    Route::crud('user', 'UserCrudController');
    Route::get('/user-ban/{id}', [UserCrudController::class, 'userBan'])->name('user-ban');
//    Route::get('user-ban', 'UserCrudController');
    Route::crud('transaction', 'TransactionCrudController');
    Route::crud('testimonial', 'TestimonialCrudController');
    Route::crud('subscription-plan', 'SubscriptionPlanCrudController');
    Route::crud('subscription-plan-content', 'SubscriptionPlanContentCrudController');
    Route::crud('subscription', 'SubscriptionCrudController');
    Route::crud('home-page-setting', 'HomePageSettingCrudController');
    Route::crud('block-user', 'BlockUserCrudController');
}); // this should be the absolute last line of this file




Route::get('admin/register', function (){
    return view('errors.401');
})->name('backpack.auth.register');

//Route::get('admin/register', function (){
//    return view('errors.401');
//});