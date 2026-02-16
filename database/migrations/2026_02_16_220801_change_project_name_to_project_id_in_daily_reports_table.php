<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DailyReport;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. まずproject_idカラムを追加（nullableで）
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('user_id');
        });

        // 2. 既存のproject_nameデータをprojectsテーブルに移行
        $dailyReports = DB::table('daily_reports')->get();
        $projectMap = []; // user_id + project_name => project_id のマップ

        foreach ($dailyReports as $report) {
            $key = $report->user_id . '::' . $report->project_name;
            
            if (!isset($projectMap[$key])) {
                // Projectを作成または取得
                $project = DB::table('projects')
                    ->where('user_id', $report->user_id)
                    ->where('name', $report->project_name)
                    ->first();
                
                if (!$project) {
                    $projectId = DB::table('projects')->insertGetId([
                        'user_id' => $report->user_id,
                        'name' => $report->project_name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $projectMap[$key] = $projectId;
                } else {
                    $projectMap[$key] = $project->id;
                }
            }
            
            // daily_reportのproject_idを更新
            DB::table('daily_reports')
                ->where('id', $report->id)
                ->update(['project_id' => $projectMap[$key]]);
        }

        // 3. project_nameカラムを削除し、project_idに外部キー制約を追加
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn('project_name');
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. project_nameカラムを追加
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->string('project_name')->after('user_id');
        });

        // 2. project_idからproject_nameにデータを戻す
        $dailyReports = DB::table('daily_reports')
            ->join('projects', 'daily_reports.project_id', '=', 'projects.id')
            ->select('daily_reports.id', 'projects.name')
            ->get();

        foreach ($dailyReports as $report) {
            DB::table('daily_reports')
                ->where('id', $report->id)
                ->update(['project_name' => $report->name]);
        }

        // 3. 外部キー制約とproject_idカラムを削除
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
