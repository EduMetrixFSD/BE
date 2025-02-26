<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;

class ProgressController extends Controller
{
    // 取得學員的課程進度
    public function show($courseId)
    {
        $progress = Progress::where('user_id', auth()->id())
            ->where('course_id', $courseId)
            ->first();

        return response()->json($progress);
    }

    // 更新學員的課程進度
    public function update(Request $request, $courseId)
    {
        $progress = Progress::updateOrCreate(
            ['user_id' => auth()->id(), 'course_id' => $courseId],
            ['percentage' => $request->percentage]
        );

        return response()->json($progress);
    }
}
