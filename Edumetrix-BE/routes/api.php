<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// ------------------------------------------ 註冊、登入、登出、忘記密碼、重設密碼、Google登入
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
// ------------------------------------------ 取得用戶資料、更新個人資料、用戶課程清單等功能
use App\Http\Controllers\UserController; 
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// (1) 公開路由：不需要驗證
Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

    // 第三方登入
    Route::get('/social-login/google', [SocialLoginController::class, 'redirectToGoogle']);
    Route::get('/social-login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);
    // 搜尋功能
});
// (2) 課程相關路由
Route::prefix('courses')->group(function () {
    Route::get('/search', [SearchController::class, 'search']);
});


// (2) 受保護路由：需要 Token
Route::middleware('auth:sanctum')->group(function () {
    // 建議統一前綴：'auth'
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout']);
        // 取得當前用戶資訊
        Route::get('/user', [UserController::class, 'getUser']);
        Route::get('/user/courses', [UserController::class, 'getUserCourses']);
        Route::post('/user/update-profile', [UserController::class, 'updateProfile']);
    });

    // 購物車相關路由
    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::get('/', [CartController::class, 'viewCart']);
        Route::delete('/{id}', [CartController::class, 'removeFromCart']);
        Route::delete('/clear', [CartController::class, 'clearCart']);
    });
    // 訂單相關路由
    Route::prefix('order')->group(function () {
        Route::post('/create', [OrderController::class, 'createOrder']);    
        Route::get('/', [OrderController::class, 'getUserOrders']); // 查詢用戶所有訂單
        Route::get('/{id}', [OrderController::class, 'getOrderDetails']); // 查詢單筆訂單
        Route::post('/{id}/cancel', [OrderController::class, 'cancelOrder']); // 取消訂單   

        Route::post('/callback', [OrderController::class, 'handlePaymentCallback']);
        Route::get('/success', [OrderController::class, 'paymentSuccess']);

    });

    // 收藏相關路由
    Route::prefix('favorite')->group(function() {
        Route::post('/{courseId}', [FavoriteController::class, 'store']);
        Route::delete('/{courseId', [FavoriteController::class, 'destory']);
        Route::get('/', [FavoriteController::class, 'index']);
    });
    // 老師後台
    Route::prefix('teacher')->group(function() {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
        Route::post('/courses/{id}/upload', [CourseController::class, 'uploadFile']);
    });

    // 管理員 API
    Route::prefix('teacher')->group(function() {
        Route::get('/admin/users', [AdminController::class, 'getUsers']);
        Route::get('/admin/courses', [AdminController::class, 'getCourses']);
        Route::get('/admin/orders', [AdminController::class, 'getOrders']);
        Route::delete('/admin/reviews/{id}', [AdminController::class, 'deleteReview']);

    });
    // 進度追蹤
    Route::get('/progress/{course_id}', [ProgressController::class, 'show']);
    Route::post('/progress/{course_id}', [ProgressController::class, 'update']);

    // 課程評價
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews/{course_id}', [ReviewController::class, 'index']);
});
