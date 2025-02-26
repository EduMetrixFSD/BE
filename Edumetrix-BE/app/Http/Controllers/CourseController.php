<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    // 新增課程
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'teacher_id' => auth()->id(),
        ]);

        return response()->json($course, 201);
    }

    // 更新課程
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $this->authorize('update', $course);

        $course->update($request->only(['title', 'description', 'price']));

        return response()->json($course);
    }

    // 刪除課程
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        $this->authorize('delete', $course);

        $course->delete();

        return response()->json(['message' => '課程已刪除']);
    }

    // 上傳課程影片/PDF
    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:mp4,pdf|max:50000'
        ]);

        $course = Course::findOrFail($id);
        $this->authorize('update', $course);

        $path = $request->file('file')->store('courses/' . $id, 'public');

        return response()->json(['file_path' => $path]);
    }
}
