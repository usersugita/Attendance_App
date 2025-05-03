<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $dates;

        if (!$dates) {
            $dates = collect(range(0, 49))->map(function ($i) {
                return now()->subDays($i)->format('Y-m-d');
            })->shuffle();
        }

        $clockIn = Carbon::createFromTime(rand(8, 10), 0, 0);
        $clockOut = Carbon::createFromTime(rand(17, 20), 0, 0);

        return [
            'user_id' => User::factory(),
            'date' => $dates->pop() ?? $this->faker->unique()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'clock_in' => $clockIn->format('H:i'),
            'clock_out' => $clockOut->format('H:i'),
            'total_work_time' => $clockIn->diffInMinutes($clockOut),
        ];
    }
}
