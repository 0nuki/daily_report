<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // nameをaccount_nameに変更
            $table->renameColumn('name', 'account_name');
            
            // email関連のカラムを削除
            $table->dropColumn(['email', 'email_verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 元に戻す
            $table->renameColumn('account_name', 'name');
            
            // emailカラムを復元
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
        });
    }
};
