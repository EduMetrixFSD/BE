<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
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
        // 檢查 token 是否存在
        $resetEntry = DB::table('password_reset_tokens')
                        ->where('email', $request->email)
                        ->first();

        if (!$resetEntry || !Hash::check($request->token, $resetEntry->token)) {
            return response()->json(['message' => '無效的重設令牌'], 400);
        }

        // 更新密碼
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => '找不到該電子郵件的用戶'], 404);
        }

        // 更新用戶密碼
        $user->update(['password' => Hash::make($request->password)]);

        // 刪除 `password_resets` 記錄
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => '密碼已成功重設']);
    }
}
