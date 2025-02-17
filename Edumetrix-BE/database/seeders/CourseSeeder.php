<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // 確保至少有一個老師
        $teacher = User::where('role', 'teacher')->first();
        if (!$teacher) {
            $teacher = User::factory()->create(['role' => 'teacher']);
        }

        // 確保至少有一個分類
        $category = Category::firstOrCreate([
            'name' => '程式設計',
            'parent_id' => null
        ]);

        // 新增 10 個假課程
        Course::factory(10)->create([
            'teacher_id' => $teacher->id,
            'category_id' => $category->id
        ]);
    }
}

