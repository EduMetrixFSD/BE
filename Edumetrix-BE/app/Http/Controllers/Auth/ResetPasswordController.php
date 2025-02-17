<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * 重設用戶密碼
     */
    public function resetPassword(Request $request)
    {
        // 驗證請求的數據是否合法
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        // Laravel 提供的內建方法，用於處理密碼重設邏輯
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // 為用戶加密並更新密碼
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
        // 檢查密碼重設的狀態並返回響應
        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => '密碼已成功重設'])
            : response()->json(['message' => '重設密碼失敗'], 500);
    }
}
