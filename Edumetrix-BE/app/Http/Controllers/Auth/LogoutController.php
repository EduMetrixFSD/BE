<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class LogoutController extends Controller
{
    /**
     * 用戶登出
     */
    public function logout(Request $request)
    {
        // 刪除當前用戶所有 Token
        $request->user()->tokens()->delete();

        return response()->json(['message' => '已成功登出']);
    }
}
