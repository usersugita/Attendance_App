<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="120">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">

                <div class="header-utilities__img">
                    <a href="/admin/login">
                        <img src="{{ asset('/storage/images/logo (2).svg') }}" alt=""></a>
                </div>
                @if (Auth::check())
                <div class="header-utilities__nav">
                    <div class="header-utilities__item">
                        <a href="/attendance">勤怠</a>
                    </div>
                    <div class="header-utilities__item">
                        <a href="/attendance/list">勤怠一覧</a>
                    </div>

                    <div class="header-utilities__item">
                        <a href="/stamp_correction_request/list">申請</a>
                    </div>
                    <div class="header-utilities__item">
                        <form action="/logout" method="post">
                            @csrf
                            <button class="header-nav__button">ログアウト</button>

                        </form>
                    </div>
                    <!-- @if (Auth::check()) 
                    @else
                    <div style="color: white;">
                        <a href="/login">ログインはこちら</a>
                    </div>
                    @endif-->
                </div>
                @endif
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>