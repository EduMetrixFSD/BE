<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Order;
use App\Models\Review;

class AdminController extends Controller
{
    // 管理所有用戶
    public function getUsers()
    {
        $this->authorize('admin');
        return response()->json(User::all());
    }

    // 管理所有課程
    public function getCourses()
    {
        $this->authorize('admin');
        return response()->json(Course::all());
    }

    // 管理訂單
    public function getOrders()
    {
        $this->authorize('admin');
        return response()->json(Order::all());
    }

    // 刪除課程評價
    public function deleteReview($id)
    {
        $this->authorize('admin');
        Review::findOrFail($id)->delete();
        return response()->json(['message' => '評價已刪除']);
    }
}
