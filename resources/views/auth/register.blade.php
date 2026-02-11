@extends('layouts.guest')

@section('content')
    <h3 class="text-center mb-4">新規登録</h3>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- アカウント名 -->
        <div class="mb-3">
            <label for="account_name" class="form-label">アカウント名</label>
            <input 
                type="text" 
                class="form-control @error('account_name') is-invalid @enderror" 
                id="account_name" 
                name="account_name" 
                value="{{ old('account_name') }}" 
                required 
                autofocus
            >
            @error('account_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">英数字、ダッシュ、アンダースコアのみ使用可能（重複不可）</div>
        </div>

        <!-- 表示名 -->
        <div class="mb-3">
            <label for="display_name" class="form-label">表示名</label>
            <input 
                type="text" 
                class="form-control @error('display_name') is-invalid @enderror" 
                id="display_name" 
                name="display_name" 
                value="{{ old('display_name') }}" 
                required
            >
            @error('display_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">画面に表示される名前</div>
        </div>

        <!-- パスワード -->
        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <div class="input-group">
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    required
                >
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                    <i class="bi bi-eye" id="password-icon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text">8文字以上で入力してください</div>
        </div>

        <!-- パスワード確認 -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">パスワード（確認）</label>
            <div class="input-group">
                <input 
                    type="password" 
                    class="form-control" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                >
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                    <i class="bi bi-eye" id="password_confirmation-icon"></i>
                </button>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">登録</button>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none">既にアカウントをお持ちの方</a>
        </div>
    </form>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
@endsection
