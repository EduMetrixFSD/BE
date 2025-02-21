<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// ------------------------------------------ 註冊、登入、登出、忘記密碼、重設密碼、Google登入
// Google登入還沒有設置金鑰
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
// ------------------------------------------ 取得用戶資料、更新個人資料、用戶課程清單等功能
// use App\Http\Controllers\UserController; 
use App\Http\Controllers\CourseController;
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
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

    // 第三方登入
    Route::get('/social-login/google', [SocialLoginController::class, 'redirectToGoogle']);
    Route::get('/social-login/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);
    // 搜尋功能
    Route::get('/courses/search', [CourseController::class, 'search']);
});


// (2) 受保護路由：需要 Token
Route::middleware('auth:sanctum')->group(function () {
    // 建議統一前綴：'auth'
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout']);
        // 取得當前用戶資訊
        // Route::get('/user', [UserController::class, 'getUser']);
        // Route::get('/user/courses', [UserController::class, 'getUserCourses']);
        // Route::post('/user/update-profile', [UserController::class, 'updateProfile']);
    });
});
