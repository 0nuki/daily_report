<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * 登録画面を表示
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * ユーザー登録処理
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'display_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'account_name.required' => 'アカウント名は必須です。',
            'account_name.unique' => 'このアカウント名は既に使用されています。',
            'account_name.alpha_dash' => 'アカウント名は英数字、ダッシュ、アンダースコアのみ使用できます。',
            'display_name.required' => '表示名は必須です。',
            'password.required' => 'パスワードは必須です。',
            'password.confirmed' => 'パスワードが一致しません。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
        ]);

        $user = User::create([
            'account_name' => $validated['account_name'],
            'display_name' => $validated['display_name'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('daily-reports.index')
            ->with('success', '登録が完了しました。');
    }
}
