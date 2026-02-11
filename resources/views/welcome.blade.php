@extends('layouts.guest')

@section('content')
    <div class="text-center">
        <h2 class="mb-4">ようこそ</h2>
        <p class="mb-4 text-muted">日報の作成・管理を簡単に行えるシステムです</p>
        
        <div class="d-grid gap-2">
            <a href="{{ route('login') }}" class="btn btn-primary">ログイン</a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary">新規登録</a>
        </div>
    </div>
@endsection
