@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendancelist.css') }}">
@endsection

@section('content')

<body>
    <main>
        <div class="title">
            <h2>{{ \Carbon\Carbon::parse($targetDate)->format('Y年n月j日') }}の勤怠</h2>
        </div>

        <div class="content">

            <div class="pagination">
                <div class="paginatoion_item">
                    <a href="{{ route('attendance.daily', ['date' => \Carbon\Carbon::parse($targetDate)->subDay()->format('Y-m-d')]) }}">
                        ← 前日
                    </a>
                </div>

                <div class="pagination_form">
                    <label for="date-input" style="display: inline-block; width: 100%;">
                        <form action="{{ route('attendance.daily') }}" method="GET">
                            <input
                                type="date"
                                id="date-input"
                                name="date"
                                style="width: 100%; padding: 8px;"
                                value="{{ $targetDate }}"
                                onchange="this.form.submit()">
                        </form>
                    </label>
                </div>

                <div class="paginatoion_item">
                    <a href="{{ route('attendance.daily', ['date' => \Carbon\Carbon::parse($targetDate)->addDay()->format('Y-m-d')]) }}">
                        翌日 →
                    </a>
                </div>
            </div>
            <table class="content__table">
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>

                @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->clock_in ?? '-' }}</td>
                    <td>{{ $attendance->clock_out ?? '-' }}</td>
                    <td>
                        @if ($attendance->total_break_time > 0)
                        {{ gmdate("H:i", $attendance->total_break_time * 60) }}
                        @else
                        -
                        @endif
                    </td>
                    <td>{{ $attendance->total_work_time ?? '-' }}</td>
                    <td>
                        <a class="detail-link" href="/attendance/{{ $attendance->id }}">詳細</a>
                    </td>
                </tr>
                
                @endforeach
            </table>

        </div>
    </main>
</body>

@endsection