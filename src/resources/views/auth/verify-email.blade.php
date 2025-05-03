@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection
@section('content')
<div class="container">
    <h4>登録していただいたメールアドレスに認証メールを送付しました。
        <br>メール認証を完了してください。
    </h4>

    <div class="container-btn">
        <a href="http://localhost:8025" class="btn-primary">認証はこちら</a>
    </div>

    <form method="POST" action="{{ route('verification.send') }}" style="margin-top: 20px;">
        @csrf
        <div class="container-btn">
            <button type="submit" class="btn-secondary">
                認証メールを再送する
            </button>
        </div>
    </form>

    @if (session('message'))
    <div style="margin-top: 20px; color: green;">
        {{ session('message') }}
    </div>
    @endif
</div>
@endsection