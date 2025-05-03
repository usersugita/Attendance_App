<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;
    protected $table = 'breaks';
    protected $fillable = ['attendance_id', 'break_start', 'break_end', 'total_break_time'];
    protected $casts = [
        'total_break_time' => 'integer',
    ];
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($break) {
            // 休憩終了時間がある場合のみ total_break_time を計算
            if ($break->break_start && $break->break_end) {
                $break->total_break_time = Carbon::parse($break->break_end)
                    ->diffInMinutes(Carbon::parse($break->break_start));
            }
            // 🚀 休憩時間を計算して保存
            $break->total_break_time = Carbon::parse($break->break_start)
                ->diffInMinutes(Carbon::parse($break->break_end));
        });
       
    }
}
