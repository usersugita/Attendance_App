<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'total_work_time'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            if (!$attendance->clock_in || !$attendance->clock_out) {
                $attendance->total_work_time = null;
                return;
            }

            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = Carbon::parse($attendance->clock_out);
            $workTime = $clockOut->diffInMinutes($clockIn);

            $breakTime = 0;

            if ($attendance->break1_start && $attendance->break1_end) {
                $breakTime += Carbon::parse($attendance->break1_end)->diffInMinutes(Carbon::parse($attendance->break1_start));
            }

            if ($attendance->break2_start && $attendance->break2_end) {
                $breakTime += Carbon::parse($attendance->break2_end)->diffInMinutes(Carbon::parse($attendance->break2_start));
            }

            $actualWorkTime = max(0, $workTime - $breakTime);

            $attendance->total_work_time = gmdate("H:i", $actualWorkTime * 60); // DBに保存
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }
    public function getTotalBreakTimeAttribute()
    {
        return $this->breaks->sum('total_break_time');
    }
    public function request()
    {
        return $this->hasOne(AttendanceRequest::class)->latest();
    }

}
