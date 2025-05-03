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


class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y/m/d');
        $dayOfWeek = mb_substr(Carbon::today()->translatedFormat('l'), 0, 1); 
        
        $now = Carbon::now()->format('H:i');
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today) 
            ->first();
        $break = $attendance ? $attendance->breaks()->latest()->first() : null;
        // セッションから出勤状態を取得
        $isWorking = session('isWorking', $attendance ? true : false);
        if ($user->role === 'admin') {
            return redirect('/admin/attendance/list');
        }
        return view('attendance', compact('user', 'today', 'dayOfWeek', 'attendance', 'isWorking', 'break', 'now'));
    }

    public function startWork(Request $request)
    {
        $userId = Auth::id();
        $today = $request->input('date'); // フォームから送信された日付
        $now = Carbon::now()->format('H:i');

        // すでに出勤データがあるか確認
        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();
        if ($attendance) {
            return back()->with('error', '本日はすでに出勤済みです。');
        }
        // 出勤データがない場合のみ作成
        if (!$attendance) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'clock_in' => $now,
            ]);
        }

        return back();
    }
    public function endWork()
    {
        $userId = Auth::id();
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now()->format('H:i');
        //$endTime = $request->input('clock_out'); 

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();
        if ($attendance) {
            $attendance->update([
                'clock_out' => $now,
            ]);
        }
        return back();
    }
    public function startbreak()
    {
        $userId = Auth::id();
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now()->format('H:i');

        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();

        if ($attendance) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => $now,
            ]);
        }
        
        return back();
    }
    public function endbreak()
    {
        $userId = Auth::id();
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now(); // `Carbon` オブジェクトのまま使用

        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();

        if ($attendance) {
            $lastBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end') // 終了していない休憩を取得
                ->latest()
                ->first();

            if ($lastBreak && $lastBreak->break_start) { // `break_start` の `null` チェック
                $breakStart = Carbon::parse($lastBreak->break_start);

                // 🚀 `break_end` を `H:i` 形式で保存（秒なし）
                $breakEnd = $now->format('H:i');

                // 🚀 `diffInMinutes()` で分単位の差を計算
                $totalMinutes = $breakStart->diffInMinutes($now);

                $lastBreak->update([
                    'break_end' => $breakEnd,
                    'total_break_time' => $totalMinutes
                ]);
            }

            // 🚀 勤怠データの `total_break_time` も更新
            $attendance->update([
                'total_break_time' => $attendance->breaks->sum('total_break_time')
            ]);
        }

        return back();
    }
    public function attendancelist(Request $request)
    {
        // 現在の日付を取得（クエリパラメータがあれば使用）
        $currentDate = $request->has('date')
            ? Carbon::parse($request->input('date'))->startOfMonth()
            : Carbon::today()->startOfMonth();

        // 前月・翌月を計算
        $previousMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        // ユーザーの勤怠データを取得
        $attendances = Attendance::where('user_id', Auth::id())
            ->whereBetween('date', [
                $currentDate->startOfMonth()->toDateString(),
                $currentDate->endOfMonth()->toDateString()
            ])
            ->with('breaks') // 🚀 休憩データも一緒に取得
            ->orderBy('date')
            ->get();

        return view('attendancelist', compact(
            'attendances',
            'currentDate',
            'previousMonth',
            'nextMonth'
        ));
    }
    public function attendancedetail($id)
    {
        $userId = Auth::id();
        $user = Auth::user();

        if ($user->role === 'admin') {
            // admin は誰の出勤情報でも見られる
            $attendance = Attendance::with(['user', 'breaks', 'request.breakRequests'])->find($id);
        } else {
            // 通常ユーザーは自分のものだけ
            $attendance = Attendance::where('id', $id)
                ->where('user_id', $userId)
                ->with(['breaks', 'request.breakRequests'])
                ->first();
        }
        $attendanceRequest = $attendance->request;
        $breakRequests = $attendanceRequest ? $attendanceRequest->breakRequests : collect();

        return view('attendancedetail', compact('attendance', 'user', 'attendanceRequest', 'breakRequests'));
    }
    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);

        $attendance->update([
            'clock_in' => $request->input('clock_in'),
            'clock_out' => $request->input('clock_out'),
        ]);

        foreach ($request->input('breaks') as $breakId => $breakData) {
            BreakTime::where('id', $breakId)->update([
                'break_start' => $breakData['break_start'],
                'break_end' => $breakData['break_end'],
            ]);
        }

        return redirect();
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'new_clock_in' => 'nullable|date_format:H:i',
            'new_clock_out' => 'nullable|date_format:H:i',
            'note' => 'required|string|max:1000',
            'breaks.*.new_break_start' => 'nullable|date_format:H:i',
            'breaks.*.new_break_end' => 'nullable|date_format:H:i',
        ], [
            'new_clock_in.date_format' => '出勤時間の形式が正しくありません。',        
            'new_clock_out.date_format' => '退勤時間の形式が正しくありません。',         
            'break_start.date_format' => '休憩開始時間の形式が正しくありません。',
            'break_end.date_format' => '休憩終了時間の形式が正しくありません。',
            'note.required' => '備考を記入してください。',
        ]);
        if (strtotime($request->clock_in) > strtotime($request->clock_out)) {
            return back()->withErrors([
                'clock_in' => '出勤時間もしくは退勤時間が不適切な値です。',
            ])->withInput();
        }
        foreach ($request->breaks ?? [] as $index => $break) {
            if (!empty($break['new_break_start'])) {
                if (
                    strtotime($break['new_break_start']) < strtotime($request->new_clock_in) ||
                    strtotime($break['new_break_start']) > strtotime($request->new_clock_out)
                ) {
                    return back()->withErrors([
                        "breaks.$index.new_break_start" => '休憩時間が勤務時間外です。',
                    ])->withInput();
                }
            }
            if (!empty($break['new_break_end'])) {
                if (
                    strtotime($break['new_break_end']) < strtotime($request->new_clock_in) ||
                    strtotime($break['new_break_end']) > strtotime($request->new_clock_out)
                ) {
                    return back()->withErrors([
                        "breaks.$index.new_break_end" => '休憩時間が勤務時間外です。',
                    ])->withInput();
                }
            }
        }
        $validated['user_id'] = auth()->id();

        $attendanceRequest = AttendanceRequest::create($validated);

        if ($request->has('breaks')) {
            foreach ($request->breaks as $break) {
                if (!empty($break['new_break_start']) || !empty($break['new_break_end'])) {
                    $attendanceRequest->breakRequests()->create([
                        'new_break_start' => $break['new_break_start'],
                        'new_break_end' => $break['new_break_end'],
                    ]);
                }
            }
        }
        return redirect('/attendance/list');
    }
    public function correction(Request $request)
    {
        $status = $request->input('status'); // 'pending' or 'approved'
        $user = Auth::user(); // ログイン中のユーザー

        // 共通のクエリベース
        $query = AttendanceRequest::with(['user', 'attendance'])->latest();

        // 管理者(admin)以外なら自分の申請だけ
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // ステータスで絞り込み（あれば）
        if (in_array($status, ['pending', 'approved'])) {
            $query->where('status', $status);
        }

        $requests = $query->get();

        return view('stamp_correction_request', compact('requests', 'status'));
    }

    public function logout()
    {        
        Auth::logout();
        return redirect('/login');
    }
    
}
