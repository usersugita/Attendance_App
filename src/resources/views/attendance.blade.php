@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')

<body>
    <main>
        <div class="item">
            @if(optional($attendance)->clock_out)
            退勤済

            @else
            {{ $isWorking ? '出勤中' : '勤務外' }}
            @endif

        </div>
        <div class="date">{{ $today }}（{{ $dayOfWeek }}）</div>
        <div class="time">
            {{ $now }}
        </div>
        <div class="attendance__button">
            @if(optional($attendance)->clock_out)
            <h3>お疲れさまでした。</h3>
            @else
            @if(!$isWorking)
            <form action="/attendance/start" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $today }}">
                
                <button type="submit">出勤</button>
            </form>
        </div>
        @else
        @if(optional($break)->break_start && !optional($break)->break_end)
        <!-- 休憩中なら「休憩戻る」ボタンを表示 -->
        <div class="break">
            <form action="/attendance/breakend" method="POST">
                @csrf
               
                <button type="submit">休憩戻</button>
            </form>
        </div>
        @else
        <!-- 休憩中でない場合 -->
        <div class="work__button">
            <div class="clock-out">
                <form action="/attendance/workend" method="POST">
                    @csrf
                    
                    <button type="submit">退勤</button>
                </form>
            </div>

            <div class="break">
                <form action="/attendance/breakstart" method="POST">
                    @csrf
                    
                    <button type="submit">休憩入</button>
                </form>
            </div>
        </div>

        @endif

        @endif
        @endif
        </div>
    </main>
</body>
@endsection