<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;            // 若需要使用 Str::random() 或其他字串工具
use Illuminate\Support\Facades\DB;     // 若需要 DB Facade 進行原生查詢
use App\Models\Course;                 // 引入 Course 模型
use App\Models\Hashtag;                // 引入 Hashtag 模型
use App\Models\Category;               // 引入 Category 模型

class CourseController extends Controller
{
    /**
     * 課程列表 / 搜尋
     */
    public function index(Request $request)
    {
        // 1. 取得查詢參數
        $search       = $request->input('search');       // 關鍵字
        $category     = $request->input('category');     // 大類別 ID
        $subcategory  = $request->input('subcategory');  // 小類別 ID
        $sort         = $request->input('sort');         // 排序參數
        $hashtag      = $request->input('hashtag');      // 單一標籤
        // 若需要多標籤，可以設計為 array，如 `hashtag=python,前端`

        $perPage      = $request->input('per_page', 10); // 每頁顯示數量

        // 2. 初始化查詢
        $query = Course::query();

        // 3. 關鍵字搜尋（模糊搜尋）
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // 4. 類別篩選
        if ($category) {
            $query->where('category_id', $category);
        }
        if ($subcategory) {
            $query->where('subcategory_id', $subcategory);
        }

        // 5. 標籤篩選
        if ($hashtag) {
        // 假設一次只篩選一個標籤
        // 如果是多標籤(陣列)，可使用 whereHas + whereIn 方式
            $query->whereHas('hashtags', function ($hashtagQuery) use ($hashtag) {
        // 如果前端傳的是 `#Python`，可視情況去掉 `#`
        // 例如 $cleanHashtag = ltrim($hashtag, '#');
            $cleanHashtag = ltrim($hashtag, '#');
            $hashtagQuery->where('name', $cleanHashtag);
            });
        }

        // 6. 排序
        switch ($sort) {
            case 'latest':
                // 新增時間最新 (created_at DESC)
                $query->orderBy('created_at', 'DESC');
                break;
            case 'popular':
                // 最熱門 (purchase_count DESC)
                $query->orderBy('purchase_count', 'DESC');
                break;
            case 'rating':
                // 評價最高 (rating DESC)
                $query->orderBy('rating', 'DESC');
                break;
            case 'price_asc':
                // 價格從低到高
                $query->orderBy('price', 'ASC');
                break;
            case 'price_desc':
                // 價格從高到低
                $query->orderBy('price', 'DESC');
                break;
            default:
                // 相關度 / 預設排序 (可自定義)
                // 這裡可以先簡單依 title 做排序，或維持原狀
                $query->orderBy('title', 'ASC');
                break;
        }

        // 7. 分頁
        $courses = $query->paginate($perPage);

        // 8. 返回 JSON
        return response()->json($courses);
    }

    /**
     * 顯示課程詳細資訊
     */
    public function show($id)
    {
        $course = Course::with(['teacher', 'category', 'subcategory'])->findOrFail($id);

        return response()->json([
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'cover_image' => $course->cover_image,
            'rating' => $course->rating,
            'purchase_count' => $course->purchase_count,
            'price' => $course->price,
            'teacher' => $course->teacher->name ?? null,
            'category' => $course->category->name ?? null,
            'subcategory' => $course->subcategory->name ?? null,
        ], 200);
    }

    public function updateHashtags(Request $request, $id)
    {
        // 根據課程 ID 取得課程
        $course = Course::findOrFail($id);

        // 接收傳入的標籤陣列（例如 ["Python", "前端"]）
        $hashtags = $request->input('hashtags', []);

        // 先將 hashtags 表中的標籤確認是否存在，若不存在就創建
        $hashtagIds = [];
        foreach ($hashtags as $ht) {
        // 如果有 `#` 開頭，可移除
        $cleanName = ltrim($ht, '#');

        // 先查詢是否有相同的 name
        $hashtagModel = Hashtag::firstOrCreate([
            'name' => $cleanName
        ]);
        $hashtagIds[] = $hashtagModel->id;
    }

        // 同步課程的標籤
        $course->hashtags()->sync($hashtagIds);

        return response()->json([
            'message' => '標籤更新成功',
            'course' => $course->load('hashtags')
        ]);
    }

}
