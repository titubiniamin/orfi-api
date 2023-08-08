<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\SocialiteController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\MobileApp\Auth\MobileAppAuthController;
use App\Http\Controllers\Api\MobileApp\Auth\MobileAppSocialiteController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Payment\SslCommerzPaymentController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\SettingController;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Stevebauman\Location\Facades\Location;
use App\Http\Controllers\Api\SearchHistoryController;

Route::controller(AuthController::class)->group(function () {
    Route::get('check-token', 'checkToken');
    Route::post('signup', 'registration');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgetPassword');
    Route::post('reset-password', 'resetPassword');
});

Route::controller(SocialiteController::class)->group(function () {
    Route::get('auth/{provider}/redirect', 'redirectToProvider');
    Route::get('auth/{provider}/callback', 'handleProviderCallback');
    Route::post('facebook-data-deletion', 'facebookDataDeletion');
});
Route::get('testimonial', [TestimonialController::class, 'index']);
Route::post('newsletter', [NewsletterController::class, 'store']);

Route::get('/setting', [SettingController::class, 'homePageSetting']);

//planning
Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index']);


//Authenticate Routes
Route::middleware('auth:api')->group(function () {
    // Email Verification Routes...
    Route::controller(EmailVerificationController::class)->group(function () {
        Route::post('email-verification', 'sendVerificationEmail');
        Route::get('verify-email/{id}/{hash}', 'emailVerify')->name('verification.verify');

        Route::get('app/email-verification', 'appSendVerificationEmail'); // For Mobile App.
        Route::post('app/verify-email', 'appEmailVerify'); // For Mobile App.
    });


    Route::get('logout', [AuthController::class, 'logout']);

    Route::controller(SslCommerzPaymentController::class)->group(function () {
        Route::post('ssl-pay', 'sslPayment');
    });

    //User profile routes
    Route::controller(UserProfileController::class)->group(function () {
        Route::get('profile', 'profile');
        Route::post('profile/{id}', 'updateProfile');
    });
    //Testimonials Routes
    Route::controller(TestimonialController::class)->group(function () {
        Route::put('update-testimonial/{id}', 'update');
        Route::post('save-testimonial', 'store');
        Route::get('user-testimonials', 'getTestimonialByUser');
    });

    //planning
    Route::controller(SubscriptionPlanController::class)->group(function () {
        Route::get('/valid-subscription-plans', 'validSubscriptionPlans');
        Route::get('/subscription-plans-history', 'subscriptionPlansHistory');
        Route::put('/update-subscription-plan/{id}', 'updateSubscriptionPlan');
    });

    //Payment
    Route::controller(PaymentController::class)->group(function () {
        Route::get('payment-history', 'getPaymentHistory');
        Route::get('payment-status', 'getPaymentStatus');
    });

    //history
    Route::controller(SearchHistoryController::class)->group(function () {
        Route::get('search-histories', 'getHistories');
        Route::get('search-histories-suggestion', 'getHistoriesForSuggestion');
        Route::get('search-count', 'getSearchCount');
        Route::post('delete-histories', 'deleteHistories');
    });


    // Bookmark
    Route::controller(BookmarkController::class)->group(function () {
        Route::get('bookmarks', 'getBookmarks');
        Route::post('bookmark', 'saveBookmark');
        Route::delete('bookmark/{index_id}', 'deleteBookmark');
        Route::get('user-bookmark', 'userBookmark');
    });


});
// SSLCOMMERZ Start
Route::controller(SslCommerzPaymentController::class)->group(function () {
    Route::post('/success', 'success');
    Route::post('/fail', 'fail');
    Route::post('/cancel', 'cancel');
    Route::post('/ipn', 'ipn');
});
//SSLCOMMERZ END


// Mobile App Routes
Route::post('app-social/login-registration', [MobileAppSocialiteController::class, 'socialLoginRegistration']);
Route::controller(MobileAppAuthController::class)->group(function () {
    Route::post('app/forgot-password', 'forgotPassword');
    Route::post('app/reset-password', 'resetPassword');
});

Route::get('test', function () {
    return request()->getClientIp();
});

