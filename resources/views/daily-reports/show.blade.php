@extends('layouts.app')

@section('title', '日報詳細')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>日報詳細</h2>
                <div>
                    <a href="{{ route('daily-reports.edit', $dailyReport) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> 編集
                    </a>
                    <a href="{{ route('daily-reports.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> 戻る
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ $dailyReport->report_date->format('Y年m月d日') }}</h5>
                </div>
                <div class="card-body">
                    @foreach($dailyReports as $index => $report)
                        <div class="mb-4 {{ $loop->last ? '' : 'pb-4 border-bottom' }}">
                            <h6 class="text-primary mb-3">案件 #{{ $index + 1 }}</h6>
                            
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">案件名/顧客名</h6>
                                <p>{{ $report->project->name ?? '（案件情報なし）' }}</p>
                            </div>

                            @if($report->start_time || $report->end_time)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">作業時間帯</h6>
                                    <p>
                                        @if($report->start_time)
                                            {{ \Carbon\Carbon::parse($report->start_time)->format('H:i') }}
                                        @else
                                            --:--
                                        @endif
                                        ～
                                        @if($report->end_time)
                                            {{ \Carbon\Carbon::parse($report->end_time)->format('H:i') }}
                                        @else
                                            --:--
                                        @endif
                                    </p>
                                </div>
                            @endif

                            <div class="mb-3">
                                <h6 class="text-muted mb-2">作業時間</h6>
                                <p>{{ $report->work_hours }}分</p>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted mb-2">作業内容</h6>
                                <div class="border rounded p-3 bg-light">
                                    <p class="mb-0 white-space-pre-line">{{ $report->work_content }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($dailyReport->notes)
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="text-muted mb-2">備考欄</h6>
                            <div class="border rounded p-3 bg-light">
                                <p class="mb-0 white-space-pre-line">{{ $dailyReport->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="text-muted small mt-4">
                        <p class="mb-1">作成日時: {{ $dailyReport->created_at->format('Y年m月d日 H:i') }}</p>
                        <p class="mb-0">更新日時: {{ $dailyReport->updated_at->format('Y年m月d日 H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <form action="{{ route('daily-reports.destroy', $dailyReport) }}" method="POST" onsubmit="return confirm('この日付の日報を削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> 削除
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
