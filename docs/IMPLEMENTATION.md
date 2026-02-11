# 日報作成システム - 実装手順書

## セットアップ済み項目

✅ Laravel プロジェクト作成  
✅ データベース接続設定（MySQL）  
✅ 初期マイグレーション実行  

## 実装方針

**スターターキット不使用**: 全ての機能を一つずつ手動で実装し、ファイル管理を明確にします。

## 実装手順

### Step 1: Bootstrapの統合

#### 1.1 package.jsonの更新
```bash
npm install bootstrap @popperjs/core
```

#### 1.2 resources/css/app.cssの編集
```css
@import 'bootstrap/dist/css/bootstrap.min.css';
```

#### 1.3 resources/js/bootstrap.jsの確認（既存ファイル）
Bootstrapの読み込みを追加：
```javascript
import 'bootstrap';
```

#### 1.4 ビルド
```bash
npm run dev
```

### Step 2: 基本レイアウトファイルの作成

#### 2.1 resources/views/layouts/app.blade.php
共通レイアウトを作成（認証前後で使用）

#### 2.2 resources/views/layouts/guest.blade.php
ゲスト用レイアウト（ログイン・登録画面用）

### Step 3: 認証機能の実装（手動）

#### 3.1 Usersテーブルの確認
既存のusersマイグレーションから不要なカラムを削除：
- name → account_name に変更
- email_verified_at 削除
- remember_token は残す（ログイン状態保持用）

#### 3.2 認証コントローラーの作成
```bash
# ユーザー登録
php artisan make:controller Auth/RegisterController

# ログイン
php artisan make:controller Auth/LoginController

# ログアウト（Loginコントローラーにメソッド追加）
```

#### 3.3 ルート設定（routes/web.php）
認証関連のルートを追加

#### 3.4 ビューファイルの作成
- resources/views/auth/register.blade.php（登録画面）
- resources/views/auth/login.blade.php（ログイン画面）

#### 3.5 バリデーションの実装
FormRequestクラスを作成してバリデーションルールを定義

### Step 4: Livewireのセットアップ

```bash
composer require livewire/livewire
```

Livewireの基本的な使い方：
- コンポーネント作成時に一度に1つずつ作成
- 日報機能で使用

### Step 5: 日報機能の実装

#### 5.1 マイグレーションファイル作成
```bash
php artisan make:migration create_daily_reports_table
```

マイグレーションファイルを編集：
```php
Schema::create('daily_reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->date('report_date');
    $table->string('title');
    $table->text('work_content');
    $table->text('reflection')->nullable();
    $table->text('tomorrow_plan')->nullable();
    $table->enum('status', ['draft', 'submitted'])->default('draft');
    $table->timestamps();
    
    $table->index(['user_id', 'report_date']);
    $table->index('status');
});
```

マイグレーション実行：
```bash
php artisan migrate
```

#### 5.2 モデル作成
```bash
php artisan make:model DailyReport
```

#### 5.3 コントローラー作成
```bash
php artisan make:controller DailyReportController --resource
```

#### 5.4 Livewireコンポーネント作成（一つずつ）
```bash
# 日報一覧
php artisan make:livewire DailyReportList

# 日報作成
php artisan make:livewire DailyReportCreate

# 日報編集
php artisan make:livewire DailyReportEdit
```

#### 5.5 ルート設定（routes/web.php）
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/daily-reports', [DailyReportController::class, 'index'])->name('daily-reports.index');
    Route::get('/daily-reports/create', [DailyReportController::class, 'create'])->name('daily-reports.create');
    Route::get('/daily-reports/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('daily-reports.edit');
});
```

### Step 6: フォームリクエストバリデーション作成

```bash
php artisan make:request StoreDailyReportRequest
php artisan make:request UpdateDailyReportRequest
```

### Step 7: ビューファイル作成（一つずつ）

作成するビューファイル：
1. `resources/views/layouts/app.blade.php` - 認証後の共通レイアウト
2. `resources/views/layouts/guest.blade.php` - 認証前の共通レイアウト
3. `resources/views/auth/register.blade.php` - 登録画面
4. `resources/views/auth/login.blade.php` - ログイン画面
5. `resources/views/daily-reports/index.blade.php` - 日報一覧
6. `resources/views/daily-reports/create.blade.php` - 日報作成
7. `resources/views/daily-reports/edit.blade.php` - 日報編集

### Step 8: 実装の優先順位

#### Phase 1: 基本レイアウトとBootstrap統合
1. Bootstrapインストール
2. 共通レイアウトファイル作成
3. 動作確認用の簡単なページ作成

#### Phase 2: 認証機能
1. Usersマイグレーション修正
2. 登録機能実装
3. ログイン機能実装
4. ログアウト機能実装
5. 認証ミドルウェア設定

#### Phase 3: 日報機能の基本
1. daily_reportsテーブル作成
2. DailyReportモデル作成
3. 日報一覧表示（Livewire使用）
4. 日報作成機能
5. 日報編集機能
6. 日報削除機能

## 開発サーバーの起動

Laravel Herdを使用している場合、自動的に `https://daily_report.test` でアクセス可能です。

Viteの開発サーバーも起動：
```bash
npm run dev
```

## 作成するファイル一覧（実装順）

### Phase 1: Bootstrap統合
- [ ] `package.json` - Bootstrap追加
- [ ] `resources/css/app.css` - Bootstrap読み込み
- [ ] `resources/js/bootstrap.js` - Bootstrap JS読み込み

### Phase 2: 基本レイアウト
- [ ] `resources/views/layouts/app.blade.php`
- [ ] `resources/views/layouts/guest.blade.php`
- [ ] `resources/views/welcome.blade.php` - 動作確認用（既存を修正）

### Phase 3: 認証機能
- [ ] `database/migrations/xxxx_modify_users_table.php` - Users修正
- [ ] `app/Http/Controllers/Auth/RegisterController.php`
- [ ] `app/Http/Controllers/Auth/LoginController.php`
- [ ] `app/Http/Requests/RegisterRequest.php`
- [ ] `app/Http/Requests/LoginRequest.php`
- [ ] `resources/views/auth/register.blade.php`
- [ ] `resources/views/auth/login.blade.php`
- [ ] `routes/web.php` - 認証ルート追加

### Phase 4: 日報機能
- [ ] `database/migrations/xxxx_create_daily_reports_table.php`
- [ ] `app/Models/DailyReport.php`
- [ ] `app/Http/Controllers/DailyReportController.php`
- [ ] `app/Http/Livewire/DailyReportList.php`
- [ ] `app/Http/Livewire/DailyReportCreate.php`
- [ ] `app/Http/Livewire/DailyReportEdit.php`
- [ ] `resources/views/livewire/daily-report-list.blade.php`
- [ ] `resources/views/livewire/daily-report-create.blade.php`
- [ ] `resources/views/livewire/daily-report-edit.blade.php`
- [ ] `resources/views/daily-reports/index.blade.php`
- [ ] `resources/views/daily-reports/create.blade.php`
- [ ] `resources/views/daily-reports/edit.blade.php`
- [ ] `routes/web.php` - 日報ルート追加

## Git管理

### 初期コミット
```bash
git init
git config --local user.name "あなたの名前"
git config --local user.email "your.email@example.com"
git add .
git commit -m "Initial commit: Laravel project setup"
```

### 機能実装ごとのコミット例
```bash
git add .
git commit -m "Add authentication system with Laravel Breeze"

git add .
git commit -m "Add Bootstrap integration"

git add .
git commit -m "Create daily_reports table migration"

git add .
git commit -m "Implement daily report CRUD functionality"
```

## 機能チェックリスト

### Bootstrap統合
- [ ] Bootstrap インストール
- [ ] CSS/JSファイル設定
- [ ] ビルド確認
- [ ] 基本レイアウト作成

### 認証機能（手動実装）
- [ ] Usersテーブル修正（account_name、パスワード、ID）
- [ ] 登録コントローラー作成
- [ ] ログインコントローラー作成
- [ ] 登録画面作成
- [ ] ログイン画面作成
- [ ] バリデーション実装
- [ ] セッション管理実装
- [ ] ログアウト機能実装

### 日報機能
- [ ] daily_reports テーブル作成
- [ ] DailyReportモデル作成
- [ ] リレーション設定
- [ ] Livewire インストール
- [ ] 日報一覧Livewireコンポーネント
- [ ] 日報作成Livewireコンポーネント
- [ ] 日報編集Livewireコンポーネント
- [ ] 日報コントローラー作成
- [ ] ビューファイル作成
- [ ] バリデーション実装

### UI/UX
- [ ] Bootstrap スタイル適用
- [ ] レスポンシブデザイン対応
- [ ] フラッシュメッセージ表示
- [ ] エラーメッセージ表示
- [ ] ナビゲーションメニュー

## 参考リンク

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Breeze](https://laravel.com/docs/starter-kits#laravel-breeze)
- [Laravel Livewire](https://livewire.laravel.com/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
