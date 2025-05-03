@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/adminStafflist.css') }}">
@endsection

@section('content')

<body>
    <main>
        <div class="title">
            <h2>　スタッフ一覧</h2>
        </div>
        <div class="content">

            <table class="content__table">
                <thead>
                    <tr>
                        <th></th>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>月次勤怠</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><a class="stafflist-link" href="/admin/attendance/staff/{{ $user->id }}">詳細</a></td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </main>
</body>
@endsection