<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'attendance_request_id',
        'new_break_start',
        'new_break_end',
    ];
    public function attendanceRequest()
    {
        return $this->belongsTo(AttendanceRequest::class);
    }
}
