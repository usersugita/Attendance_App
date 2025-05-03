<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ユーザーを10人作成
        User::factory(10)->create()->each(function ($user) {
            // 勤怠データを作成
            Attendance::factory(15)->create(['user_id' => $user->id])->each(function ($attendance) {
                // 各勤怠に対して休憩データを複数作成
                BreakTime::factory(rand(1, 3))->create(['attendance_id' => $attendance->id]);
            });
        });
    }
}
