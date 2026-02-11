@extends('layouts.app')

@section('title', '日報編集')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>日報編集</h2>
                <a href="{{ route('daily-reports.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> 戻る
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('daily-reports.update', $dailyReport) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- 日付 -->
                        <div class="mb-3">
                            <label for="report_date" class="form-label fw-bold">日付 <span class="text-danger">*</span></label>
                            <input 
                                type="date" 
                                class="form-control @error('report_date') is-invalid @enderror" 
                                id="report_date" 
                                name="report_date"
                                value="{{ old('report_date', $dailyReport->report_date) }}"
                                required
                            >
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 案件名/顧客名 -->
                        <div class="mb-3">
                            <label for="project_name" class="form-label">案件名/顧客名 <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('project_name') is-invalid @enderror" 
                                id="project_name" 
                                name="project_name"
                                value="{{ old('project_name', $dailyReport->project_name) }}"
                                required
                            >
                            @error('project_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 作業開始時間 -->
                        <div class="mb-3">
                            <label for="start_time" class="form-label">作業開始時間</label>
                            <input 
                                type="time" 
                                class="form-control @error('start_time') is-invalid @enderror" 
                                id="start_time" 
                                name="start_time"
                                value="{{ old('start_time', $dailyReport->start_time) }}"
                            >
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 作業終了時間 -->
                        <div class="mb-3">
                            <label for="end_time" class="form-label">作業終了時間</label>
                            <input 
                                type="time" 
                                class="form-control @error('end_time') is-invalid @enderror" 
                                id="end_time" 
                                name="end_time"
                                value="{{ old('end_time', $dailyReport->end_time) }}"
                            >
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 作業時間 -->
                        <div class="mb-3">
                            <label for="work_hours" class="form-label">作業時間（分）</label>
                            <input 
                                type="number" 
                                class="form-control @error('work_hours') is-invalid @enderror" 
                                id="work_hours" 
                                name="work_hours"
                                value="{{ old('work_hours', $dailyReport->work_hours) }}"
                                min="0"
                                max="1440"
                            >
                            @error('work_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 作業内容 -->
                        <div class="mb-3">
                            <label for="work_content" class="form-label">作業内容 <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control @error('work_content') is-invalid @enderror" 
                                id="work_content" 
                                name="work_content"
                                rows="8"
                                required
                            >{{ old('work_content', $dailyReport->work_content) }}</textarea>
                            @error('work_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 備考欄 -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">備考欄</label>
                            <textarea 
                                class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" 
                                name="notes"
                                rows="4"
                            >{{ old('notes', $dailyReport->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> 更新
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
