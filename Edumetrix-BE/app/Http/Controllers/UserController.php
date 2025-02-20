<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;

class UserController extends Controller
{
    /**
     * 取得當前用戶資訊
     */
    public function getUser(Request $request)
    {
        return response()->json([
            'user' => Auth::user(),
        ]);
    }

    /**
     * 取得用戶購買的課程
     */
    public function getUserCourses()
    {
        $user = Auth::user();

        // 假設 orders 是購買紀錄，order_items 存儲購買的課程
        $courses = $user->orders()->with('orderItems.course')->get()->pluck('orderItems.*.course')->flatten();

        return response()->json([
            'courses' => $courses,
        ]);
    }

    /**
     * 更新用戶個人資料
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            // 更新資料
        ]);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
        'message' => '個人資料更新成功',
        'user' => $user
        ]);
    }
    /**
     * 上傳頭像
     */
    public function uploadAvatar(Request $request)
    {
        $user = auth()->user();

        // 驗證檔案
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 儲存檔案
        $path = $request->file('avatar')->store('public/avatars');
        $filename = str_replace('public/', '', $path); // 移除 `public/` 方便存取

        // 更新用戶頭像
        $user->update(['avatar' => $filename]);

        return response()->json([
            'message' => '頭像更新成功',
            'avatar_url' => asset('storage/' . $filename)
        ]);
    }


    /**
     * 修改密碼
     */
    public function changePassword(Request $request)
    {
        // 驗證輸入
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // 驗證舊密碼
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => '舊密碼不正確'], 400);
        }

        // 更新密碼
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => '密碼更新成功']);
    }

}
