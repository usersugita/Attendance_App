@extends('layouts.app')

@section('content')
<div class="container">
    <h1>メール認証をお願いします</h1>
    <p>登録したメールアドレスに認証リンクを送付しました。</p>

    <div style="margin-top: 20px;">
        <a href="http://localhost:8025" class="btn btn-primary">認証はこちら</a>
    </div>

    <form method="POST" action="{{ route('verification.send') }}" style="margin-top: 20px;">
        @csrf
        <button type="submit" class="btn btn-secondary">
            メールを再送する
        </button>
    </form>

    @if (session('message'))
    <div style="margin-top: 20px; color: green;">
        {{ session('message') }}
    </div>
    @endif
</div>
@endsection