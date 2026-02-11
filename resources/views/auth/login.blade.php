@extends('layouts.guest')

@section('content')
    <h3 class="text-center mb-4">ログイン</h3>

    <form method="POST" action="{{ route('login') }}">
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
        </div>

        <!-- ログイン状態を保持 -->
        <div class="mb-4">
            <div class="form-check">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="remember" 
                    name="remember"
                >
                <label class="form-check-label" for="remember">
                    ログイン状態を保持
                </label>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">ログイン</button>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('register') }}" class="text-decoration-none">アカウントをお持ちでない方</a>
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
