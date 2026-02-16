<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allReports = DailyReport::with('project')
            ->where('user_id', Auth::id())
            ->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // 日付ごとにグループ化
        $groupedReports = $allReports->groupBy('report_date');
        
        // ページネーション用に配列に変換
        $page = request()->get('page', 1);
        $perPage = 10;
        $total = $groupedReports->count();
        
        // ページネーション用にスライス（キーを保持）
        $items = $groupedReports->slice(($page - 1) * $perPage, $perPage);
        
        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('daily-reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daily-reports.manage-livewire');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_date' => ['required', 'date'],
            'projects' => ['required', 'array', 'min:1'],
            'projects.*.project_name' => ['required', 'string', 'max:255'],
            'projects.*.start_time' => ['nullable', 'date_format:H:i'],
            'projects.*.end_time' => ['nullable', 'date_format:H:i'],
            'projects.*.work_hours' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'projects.*.work_content' => ['required', 'string'],
            'projects.*.notes' => ['nullable', 'string'],
        ], [
            'report_date.required' => '日付は必須です。',
            'projects.required' => '少なくとも1つの案件を入力してください。',
            'projects.*.project_name.required' => '案件名/顧客名は必須です。',
            'projects.*.start_time.date_format' => '開始時間の形式が正しくありません。',
            'projects.*.end_time.date_format' => '終了時間の形式が正しくありません。',
            'projects.*.work_hours.integer' => '作業時間は整数で入力してください。',
            'projects.*.work_hours.min' => '作業時間は0以上で入力してください。',
            'projects.*.work_hours.max' => '作業時間は1440分（24時間）以内で入力してください。',
            'projects.*.work_content.required' => '作業内容は必須です。',
        ]);

        $count = 0;
        foreach ($validated['projects'] as $project) {
            // 作業時間を計算（分単位）
            $workHours = $project['work_hours'] ?? null;
            if (!$workHours && isset($project['start_time']) && isset($project['end_time'])) {
                $start = \Carbon\Carbon::parse($project['start_time']);
                $end = \Carbon\Carbon::parse($project['end_time']);
                $workHours = $end->diffInMinutes($start);
            }

            DailyReport::create([
                'user_id' => Auth::id(),
                'report_date' => $validated['report_date'],
                'start_time' => $project['start_time'] ?? null,
                'end_time' => $project['end_time'] ?? null,
                'project_name' => $project['project_name'],
                'work_hours' => $workHours,
                'work_content' => $project['work_content'],
                'notes' => $project['notes'] ?? null,
            ]);
            $count++;
        }

        return redirect()->route('daily-reports.index')
            ->with('success', "{$count}件の日報を作成しました。");
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyReport $dailyReport)
    {
        // 自分の日報のみ表示
        if ($dailyReport->user_id !== Auth::id()) {
            abort(403);
        }

        // 同じ日付の全案件を取得
        $dailyReports = DailyReport::with('project')
            ->where('user_id', Auth::id())
            ->where('report_date', $dailyReport->report_date)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('daily-reports.show', compact('dailyReports', 'dailyReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyReport $dailyReport)
    {
        // 自分の日報のみ編集可能
        if ($dailyReport->user_id !== Auth::id()) {
            abort(403);
        }

        return view('daily-reports.manage-livewire', compact('dailyReport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyReport $dailyReport)
    {
        // 自分の日報のみ更新可能
        if ($dailyReport->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'report_date' => ['required', 'date'],
            'project_name' => ['required', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'work_hours' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'work_content' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ], [
            'report_date.required' => '日付は必須です。',
            'project_name.required' => '案件名/顧客名は必須です。',
            'start_time.date_format' => '開始時間の形式が正しくありません。',
            'end_time.date_format' => '終了時間の形式が正しくありません。',
            'work_hours.integer' => '作業時間は整数で入力してください。',
            'work_hours.min' => '作業時間は0以上で入力してください。',
            'work_hours.max' => '作業時間は1440分（24時間）以内で入力してください。',
            'work_content.required' => '作業内容は必須です。',
        ]);

        // 作業時間を計算（分単位）
        $workHours = $validated['work_hours'] ?? null;
        if (!$workHours && isset($validated['start_time']) && isset($validated['end_time'])) {
            $start = \Carbon\Carbon::parse($validated['start_time']);
            $end = \Carbon\Carbon::parse($validated['end_time']);
            $workHours = $end->diffInMinutes($start);
        }
        $validated['work_hours'] = $workHours;

        $dailyReport->update($validated);

        return redirect()->route('daily-reports.index')
            ->with('success', '日報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyReport $dailyReport)
    {
        // 自分の日報のみ削除可能
        if ($dailyReport->user_id !== Auth::id()) {
            abort(403);
        }

        // 同じ日付の全案件を削除
        $deletedCount = DailyReport::where('user_id', Auth::id())
            ->where('report_date', $dailyReport->report_date)
            ->delete();

        return redirect()->route('daily-reports.index')
            ->with('success', '日報を削除しました。');
    }
}