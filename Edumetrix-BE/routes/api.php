<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// ------------------------------------------ 配置 Google/Facebook 開發者平台
// 配置 Google OAuth 
// 登錄 Google Cloud Console
// 創建一個專案
// 啟用 API 和服務
// 在「API 和服務」中，啟用 Google+ API 或 OAuth 2.0
// 創建憑據
// 選擇 OAuth 2.0 用戶端 ID
// 配置授權的 回調 URL（例如：https://yourdomain.com/api/social-login/google/callback）。
// 添加憑據到 .env 文件

// Google 配置
// GOOGLE_CLIENT_ID=your-google-client-id
// GOOGLE_CLIENT_SECRET=your-google-client-secret
// GOOGLE_REDIRECT_URI=https://yourdomain.com/api/social-login/google/callback

// Facebook 配置
// FACEBOOK_CLIENT_ID=your-facebook-client-id
// FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
// FACEBOOK_REDIRECT_URI=https://yourdomain.com/api/social-login/facebook/callback



// ------------------------------------------ 註冊、登入、登出、忘記密碼、重設密碼、Google登入
// Google登入還沒有設置金鑰
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
// ------------------------------------------ 取得用戶資料、更新個人資料、用戶課程清單等功能
use App\Http\Controllers\UserController; 
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

// 公開路由：不需要驗證
Route::group(['prefix' => 'auth'], function () {
    // 註冊
    Route::post('/register', [RegisterController::class, 'register']);

    // 登入
    Route::post('/login', [LoginController::class, 'login']);

    // 忘記密碼
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);

    // 重設密碼
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

    // 第三方登入
    Route::post('/social-login/google', [SocialLoginController::class, 'googleLogin']);
    Route::post('/social-login/facebook', [SocialLoginController::class, 'facebookLogin']);
});


// 受保護的路由：需要登入 Token
Route::group(['middleware' => ['auth:sanctum']], function () {
    // 登出
    Route::post('/auth/logout', [LogoutController::class, 'logout']);

    // 獲取當前用戶信息
    Route::get('/user', [UserController::class, 'getUser']);

    // 其他需要身份驗證的 API
    // Route::get('/user/courses', [UserController::class, 'getUserCourses']);
    // Route::post('/user/update-profile', [UserController::class, 'updateProfile']);
});

