<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreakTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
   
    protected $model = BreakTime::class;
    public function definition()
    {
        // ランダムな休憩開始時間
        $breakStart = Carbon::createFromTime(rand(12, 14), rand(0, 59), 0);
        $breakEnd = (clone $breakStart)->addMinutes(rand(15, 60));

        return [
            'attendance_id' => Attendance::factory(),  // 勤怠データを関連付け
            'break_start' => $breakStart->format('H:i:s'),  // 休憩開始
            'break_end' => $breakEnd->format('H:i:s'),  // 休憩終了
            'total_break_time' => $breakStart->diffInMinutes($breakEnd),  // 休憩時間（分）
        ];
    }
    
}
