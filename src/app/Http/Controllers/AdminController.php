<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use App\Models\BreakRequest;
use App\Models\User;
use Illuminate\Support\Facades\Response;
class AdminController extends Controller {
    public function adminlogin()
    {
        return view('auth.admin-login');
    }
    
   
    public function dailyList(Request $request)
    {
        // 日付を取得（GETで渡す or デフォルトは今日）
        $targetDate = $request->input('date', Carbon::today()->format('Y-m-d'));

        $attendances = Attendance::with('user')
            ->whereDate('date', $targetDate)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendance-list', compact('attendances', 'targetDate'));
    }

    public function adminCorrection($id)
    {
        // 修正申請のデータ取得（ユーザー、勤怠、休憩情報を同時に取得）
        $requestData = AttendanceRequest::with(['user', 'attendance', 'breakRequests'])->findOrFail($id);

        // 例えば、申請内容から勤怠やユーザー情報を取り出す
        $attendance = $requestData->attendance;
        $user = $requestData->user;
        $breaks = $attendance ? $attendance->breaks : collect();

        // ビュー (resources/views/admin-correction.blade.php) に渡す
        return view('admin.admin-correction', compact('requestData', 'attendance', 'user', 'breaks'));
    }
    public function processCorrection(Request $request, $id)
    {
        $requestData = AttendanceRequest::with('attendance')->findOrFail($id);

        if (auth()->user()->role !== 'admin') {
            abort(403, '許可されていません');
        }

        $requestData->status = 'approved';
        $requestData->save();

        $attendance = $requestData->attendance;
        $attendance->update([
            'clock_in' => $request->input('new_clock_in'),
            'clock_out' => $request->input('new_clock_out'),
            'total_work_time' => \Carbon\Carbon::parse($request->input('new_clock_in'))
                ->diffInMinutes(\Carbon\Carbon::parse($request->input('new_clock_out'))),
        ]);

        $attendance->breaks()->delete();

        if ($request->has('breaks')) {
            foreach ($request->input('breaks') as $break) {
                if (!empty($break['new_break_start']) || !empty($break['new_break_end'])) {
                    $attendance->breaks()->create([
                        'break_start' => $break['new_break_start'],
                        'break_end' => $break['new_break_end'],
                    ]);
                }
            }
        }

        // 承認後、再度詳細画面を表示
        $requestData->load(['user', 'attendance', 'breakRequests']);
        $attendance = $requestData->attendance;
        $user = $requestData->user;
        $breaks = $requestData->breakRequests;

        return view('admin.admin-correction', compact('requestData', 'attendance', 'user', 'breaks'));
    }
    public function adminStaffList()
    {

        $users = User::where('role', 'staff')->get(); 

        return view('admin.staff-list', compact('users'));
    }
    public function adminAttendanceStaff(Request $request, $id)
    {
        // ユーザー情報を取得（存在チェックも兼ねる）
        $user = User::findOrFail($id);

        // 現在表示中の月を取得（クエリパラメータで切替可能）
        $currentDate = $request->has('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::today()->startOfMonth();

        // 前月・翌月を計算
        $previousMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        // 指定ユーザーの勤怠データを取得（指定月）
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [
                $currentDate->startOfMonth()->toDateString(),
                $currentDate->endOfMonth()->toDateString()
            ])
            ->with('breaks')
            ->orderBy('date')
            ->get();

        return view('admin.staff-attendancelist', compact(
            'user',
            'attendances',
            'currentDate',
            'previousMonth',
            'nextMonth'
        ));
    }
    public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::today()->startOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString()
            ])
            ->orderBy('date')
            ->get();

        $csvHeader = ['日付', '出勤', '退勤', '休憩', '労働時間'];
        $csvData = [];

        foreach ($attendances as $attendance) {
            $csvData[] = [
                Carbon::parse($attendance->date)->format('Y-m-d'),
                $attendance->clock_in ?? '-',
                $attendance->clock_out ?? '-',
                $attendance->total_break_time ? gmdate('H:i', $attendance->total_break_time * 60) : '-',
                $attendance->total_work_time ?? '-',
            ];
        }

        $filename = "{$user->name}_attendance_{$date->format('Y_m')}.csv";

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $csvHeader);
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        $content = mb_convert_encoding($content, 'SJIS-win', 'UTF-8');
        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}




    
        
    
