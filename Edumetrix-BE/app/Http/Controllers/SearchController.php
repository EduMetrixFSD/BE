<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class SearchController extends Controller
{
    /**
     * 搜尋課程
     */
    public function search(Request $request)
    {
        // 取得查詢參數
        $query = Course::query();

        // 關鍵字搜尋 (標題 & 描述)
        if ($request->has('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->keyword . '%');
            });
        }

        // 分類篩選
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 標籤篩選
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        // 排序 (最新, 最熱門, 最高評價, 最便宜)
        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('enrolled_students', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('average_rating', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
            }
        }

        // 取得結果
        $courses = $query->paginate(10);

        return response()->json($courses);
    }
}
