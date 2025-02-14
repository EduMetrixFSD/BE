<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    /**
     * 取得所有分類清單 (可含大類、小類)
     */
    public function index()
    {
        // 若要包含子分類，可用巢狀關係
        // 例如 $categories = Category::with('children')->get();
        // 若只要平面資料:
        $categories = Category::all();

        return response()->json($categories);
    }

    /**
     * 取得單一分類詳細資訊
     */
    public function show($id)
    {
        $category = Category::with('children')->findOrFail($id);
        return response()->json($category);
    }

    /**
     * 建立新分類 (大類或小類)
     * 大類 -> parent_id = null
     * 小類 -> parent_id = 目標大類別 ID
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id'  // 確保 parent_id 存在
        ]);

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'message' => '分類已建立',
            'category' => $category
        ], 201);
    }

    /**
     * 更新分類資料
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $category = Category::findOrFail($id);

        if ($request->has('name')) {
            $category->name = $request->name;
        }
        if ($request->has('parent_id')) {
            $category->parent_id = $request->parent_id;
        }

        $category->save();

        return response()->json([
            'message' => '分類已更新',
            'category' => $category
        ], 200);
    }

    /**
     * 刪除分類
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        // 若分類下有子分類或課程，可能需要檢查後再決定是否允許刪除
        // 這裡先示範直接刪除
        $category->delete();

        return response()->json([
            'message' => '分類已刪除'
        ], 200);
    }
}
