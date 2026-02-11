<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * ログイン画面を表示
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'account_name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'account_name.required' => 'アカウント名は必須です。',
            'password.required' => 'パスワードは必須です。',
        ]);

        $user = User::where('account_name', $credentials['account_name'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('daily-reports.index'))
                ->with('success', 'ログインしました。');
        }

        return back()->withErrors([
            'account_name' => 'アカウント名またはパスワードが正しくありません。',
        ])->onlyInput('account_name');
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'ログアウトしました。');
    }
}
