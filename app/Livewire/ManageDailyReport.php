<?php

namespace App\Livewire;

use App\Models\DailyReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageDailyReport extends Component
{
    public $report_date;
    public $projects = [];
    public $notes = '';
    public $dailyReportId = null;
    public $isEditMode = false;

    protected function rules()
    {
        return [
            'report_date' => ['required', 'date'],
            'projects' => ['required', 'array', 'min:1'],
            'projects.*.project_name' => ['required', 'string', 'max:255'],
            'projects.*.start_time' => ['nullable', 'date_format:H:i'],
            'projects.*.end_time' => ['nullable', 'date_format:H:i'],
            'projects.*.work_hours' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'projects.*.work_content' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function messages()
    {
        return [
            'report_date.required' => '日付は必須です。',
            'projects.required' => '少なくとも1つの案件を入力してください。',
            'projects.*.project_name.required' => '案件名/顧客名は必須です。',
            'projects.*.start_time.date_format' => '開始時間の形式が正しくありません。',
            'projects.*.end_time.date_format' => '終了時間の形式が正しくありません。',
            'projects.*.work_hours.integer' => '作業時間は整数で入力してください。',
            'projects.*.work_hours.min' => '作業時間は0以上で入力してください。',
            'projects.*.work_hours.max' => '作業時間は1440分（24時間）以内で入力してください。',
            'projects.*.work_content.required' => '作業内容は必須です。',
        ];
    }

    public function mount($dailyReportId = null)
    {
        if ($dailyReportId) {
            // 編集モード
            $this->isEditMode = true;
            $this->dailyReportId = $dailyReportId;
            
            // idから日報を取得
            $dailyReport = DailyReport::findOrFail($dailyReportId);
            
            // 自分の日報のみ編集可能
            if ($dailyReport->user_id !== Auth::id()) {
                abort(403);
            }
            
            // 同じ日付の全案件を取得
            $dailyReports = DailyReport::where('user_id', Auth::id())
                ->where('report_date', $dailyReport->report_date)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $this->report_date = $dailyReport->report_date->format('Y-m-d');
            $this->notes = $dailyReport->notes ?? '';
            
            foreach ($dailyReports as $report) {
                $this->projects[] = [
                    'id' => $report->id,
                    'project_name' => $report->project_name,
                    'start_time' => $report->start_time ? \Carbon\Carbon::parse($report->start_time)->format('H:i') : '',
                    'end_time' => $report->end_time ? \Carbon\Carbon::parse($report->end_time)->format('H:i') : '',
                    'work_hours' => $report->work_hours ?? 0,
                    'work_content' => $report->work_content,
                ];
            }
        } else {
            // 作成モード
            $this->isEditMode = false;
            $this->report_date = now()->format('Y-m-d');
            // 最初の案件に現在時間を設定
            $this->projects[] = [
                'id' => null,
                'project_name' => '',
                'start_time' => now()->format('H:i'),
                'end_time' => '',
                'work_hours' => 0,
                'work_content' => '',
            ];
        }
    }

    public function addProject()
    {
        $this->projects[] = [
            'id' => null,
            'project_name' => '',
            'start_time' => '',
            'end_time' => '',
            'work_hours' => 0,
            'work_content' => '',
        ];
    }

    public function removeProject($index)
    {
        unset($this->projects[$index]);
        $this->projects = array_values($this->projects);
        
        // 全て削除されたら1つ追加
        if (count($this->projects) === 0) {
            $this->addProject();
        }
    }

    public function calculateWorkHours($index)
    {
        if (isset($this->projects[$index]['start_time']) && 
            isset($this->projects[$index]['end_time']) && 
            $this->projects[$index]['start_time'] && 
            $this->projects[$index]['end_time']) {
            
            $start = \Carbon\Carbon::parse($this->projects[$index]['start_time']);
            $end = \Carbon\Carbon::parse($this->projects[$index]['end_time']);
            
            // 終了時間が開始時間より前の場合は翌日と見なす
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            
            $this->projects[$index]['work_hours'] = $start->diffInMinutes($end);
        }
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->isEditMode) {
            // 編集モード：元の日付の全日報を削除
            $dailyReport = DailyReport::findOrFail($this->dailyReportId);
            DailyReport::where('user_id', Auth::id())
                ->where('report_date', $dailyReport->report_date)
                ->delete();
        }

        // 新しい日報を作成
        $count = 0;
        foreach ($validated['projects'] as $project) {
            // 作業時間を計算（分単位）
            $workHours = $project['work_hours'] ?? null;
            if (!$workHours && isset($project['start_time']) && isset($project['end_time'])) {
                $start = \Carbon\Carbon::parse($project['start_time']);
                $end = \Carbon\Carbon::parse($project['end_time']);
                
                // 終了時間が開始時間より前の場合は翌日と見なす
                if ($end->lessThan($start)) {
                    $end->addDay();
                }
                
                $workHours = $start->diffInMinutes($end);
            }

            DailyReport::create([
                'user_id' => Auth::id(),
                'report_date' => $validated['report_date'],
                'start_time' => $project['start_time'] ?: null,
                'end_time' => $project['end_time'] ?: null,
                'project_name' => $project['project_name'],
                'work_hours' => $workHours,
                'work_content' => $project['work_content'],
                'notes' => $this->notes ?: null,
            ]);
            $count++;
        }

        $message = $this->isEditMode 
            ? '日報を更新しました。' 
            : "{$count}件の日報を作成しました。";
        
        session()->flash('success', $message);
        
        return redirect()->route('daily-reports.index');
    }

    public function render()
    {
        return view('livewire.manage-daily-report');
    }
}
