@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendancelist.css') }}">
@endsection

@section('content')

<body>
    <main>
        <div class="title">
            <h2>{{ $user->name }} さんの勤怠</h2>
        </div>
        <div class="content">
            <div class="pagination">
                <div class="paginatoion_item">
                    <a href="?date={{ $previousMonth->format('Y-m-d') }}">← 前月</a>
                </div>
                <div class="pagination_form">
                    <label for="date-input" style="display: inline-block; width: 100%;">
                        <form action="" method="GET">
                            <input type="month" id="date-input" name="date" style="width: 100%; padding: 8px;" value="{{ $currentDate->format('Y-m') }}"
                                onchange="this.form.submit()">
                        </form>
                    </label>
                </div>
                <div class="paginatoion_item">
                    <a href="?date={{ $nextMonth->format('Y-m-d') }}">翌月 →</a>
                </div>

            </div>
            <table class="content__table">

                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ Carbon\Carbon::parse($attendance->date)->translatedFormat('m/d（D）') }}</td>
                    <td>{{ $attendance->clock_in }}</td>
                    <td>{{ $attendance->clock_out}}</td>

                    <td> @if ($attendance->total_break_time > 0)
                        {{ gmdate("H:i", $attendance->total_break_time * 60) }} <!-- 分を時:分に変換 -->
                        @else
                        -
                        @endif
                    </td>
                    <td>{{ $attendance->total_work_time }}</td>
                    <td><a class="detail-link" href="/attendance/{{ $attendance->id }}">詳細</a></td>
                </tr>
                @endforeach

            </table>
            <div class="button__csv" style="text-align: right; margin-top: 20px;">
                <form action="{{ route('admin.attendance.csv', ['id' => $user->id]) }}" method="GET">
                    <input type="hidden" name="date" value="{{ $currentDate->format('Y-m-d') }}">
                    <button type="submit">CSV出力</button>
                </form>
            </div>
    </main>
</body>
@endsection