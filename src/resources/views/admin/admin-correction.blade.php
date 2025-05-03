@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendancedetail.css') }}">
@endsection

@section('content')

<body>
    <main>
        <div class="title">
            <h2>勤怠詳細</h2>
        </div>
        <div class="content">
            <form action="/stamp_correction_request/approve/{{ $requestData->id }}" method="post">
                @csrf
                <!-- 勤怠IDは隠しフィールドで送信 -->
                <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

                <table class="content__table">
                    <tr>
                        <th>名前</th>
                        <td colspan="4" class="content__table-name">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>日付</th>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</td>
                        <td></td>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</td>
                        <td></td>
                    </tr>
                    <!-- 出勤・退勤 -->
                    <tr>
                        <th>出勤・退勤</th>
                        <td>
                            <input type="time" name="new_clock_in"
                                value="{{ $requestData->new_clock_in }}"
                                style="border: none;" readonly>
                        <td>～</td>
                        <td>
                            <input type="time" name="new_clock_out"
                                value="{{ $requestData->new_clock_out }}"
                                style="border: none;" readonly>
                        <td></td>
                    </tr>
                    <!-- 休憩 （複数行） -->
                    @foreach ($requestData->breakRequests as $i => $break)
                    <tr>
                        <th>休憩{{ $i + 1 }}</th>
                        <td>
                            <input type="time" name="breaks[{{ $i }}][new_break_start]"
                                value="{{ $break->new_break_start }}"
                                style="border: none;" readonly>
                        </td>
                        <td>～</td>
                        <td>
                            <input type="time" name="breaks[{{ $i }}][new_break_end]"
                                value="{{ $break->new_break_end }}"
                                style="border: none;" readonly>
                        </td>
                        <td></td>
                    </tr>
                    @endforeach
                    <!-- 備考 -->
                    <tr>
                        <th>備考</th>
                        <td>
                            <input type="text" name="note" value="{{ $requestData->note ?? '' }}" style="border: none;" readonly>

                        </td>
                    </tr>
                </table>
                @if ($requestData->status === 'pending')
                <div class="content__button" style="text-align: right; margin-top: 30px;">
                    <button type="submit">承認</button>
                </div>
                @else
                <div class="content__approved" style="text-align: right; margin-top: 30px;">
                    <span>承認済み</span>
                </div>
                @endif
            </form>
        </div>

    </main>
</body>

@endsection