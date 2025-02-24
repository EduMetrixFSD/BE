<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // 收藏課程
    public function store(Request $request, $courseId)
    {
        // 確認使用者已登入
        if (!Auth::check()) {
            return response()->json(['error' => '請先登入'], 401);
        }

        $user = Auth::user();

        // 檢查課程是否存在
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json(['error' => '課程不存在'], 404);
        }

        // 檢查是否已經收藏
        $favorite = Favorite::where('user_id', $user->id)
                            ->where('course_id', $courseId)
                            ->first();

        if ($favorite) {
            return response()->json(['message' => '此課程已經收藏'], 400);
        }

        // 建立收藏
        $favorite = new Favorite();
        $favorite->user_id = $user->id;
        $favorite->course_id = $courseId;
        $favorite->save();

        return response()->json(['message' => '課程收藏成功'], 200);
    }

    // 取消收藏課程
    public function destroy(Request $request, $courseId)
    {
        // 確認使用者已登入
        if (!Auth::check()) {
            return response()->json(['error' => '請先登入'], 401);
        }

        $user = Auth::user();

        // 檢查收藏是否存在
        $favorite = Favorite::where('user_id', $user->id)
                            ->where('course_id', $courseId)
                            ->first();

        if (!$favorite) {
            return response()->json(['error' => '尚未收藏此課程'], 404);
        }

        // 刪除收藏
        $favorite->delete();

        return response()->json(['message' => '課程已取消收藏'], 200);
    }

    // 查看用戶的收藏課程
    public function index(Request $request)
    {
        // 確認使用者已登入
        if (!Auth::check()) {
            return response()->json(['error' => '請先登入'], 401);
        }

        $user = Auth::user();

        // 查詢用戶的收藏課程
        $favorites = $user->favorites()->with('course')->get();

        return response()->json($favorites, 200);
    }
}
