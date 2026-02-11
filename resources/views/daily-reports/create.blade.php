@extends('layouts.app')

@section('title', '日報作成')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>日報作成</h2>
                <a href="{{ route('daily-reports.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> 戻る
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('daily-reports.store') }}" id="dailyReportForm">
                        @csrf
                        
                        <!-- 日付 -->
                        <div class="mb-4 pb-3 border-bottom">
                            <label for="report_date" class="form-label fw-bold">日付 <span class="text-danger">*</span></label>
                            <input 
                                type="date" 
                                class="form-control @error('report_date') is-invalid @enderror" 
                                id="report_date" 
                                name="report_date"
                                value="{{ old('report_date', now()->format('Y-m-d')) }}"
                                required
                            >
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 案件リスト -->
                        <div id="projectsContainer" class="mb-4"></div>

                        <!-- 案件追加ボタン -->
                        <div class="mb-4">
                            <button 
                                type="button" 
                                class="btn btn-outline-primary w-100" 
                                id="addProjectBtn"
                            >
                                <i class="bi bi-plus-circle"></i> 案件を追加
                            </button>
                        </div>

                        <!-- 登録ボタン -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> 登録
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let projectIndex = 0;

// 案件カードのテンプレート
function createProjectCard(index) {
    return `
        <div class="project-item card mb-3" data-index="${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary">案件 #<span class="project-number">${index + 1}</span></h6>
                    <button 
                        type="button" 
                        class="btn btn-sm btn-outline-danger remove-project-btn"
                        data-index="${index}"
                    >
                        <i class="bi bi-trash"></i> 削除
                    </button>
                </div>

                <!-- 案件名/顧客名 -->
                <div class="mb-3">
                    <label class="form-label">案件名/顧客名 <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('projects.${index}.project_name') is-invalid @enderror" 
                        name="projects[${index}][project_name]"
                        value="{{ old('projects.${index}.project_name') }}"
                        required
                    >
                    @error('projects.${index}.project_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 作業開始時間 -->
                <div class="mb-3">
                    <label class="form-label">作業開始時間</label>
                    <input 
                        type="time" 
                        class="form-control start-time" 
                        name="projects[${index}][start_time]"
                        value="{{ old('projects.${index}.start_time') }}"
                    >
                </div>

                <!-- 作業終了時間 -->
                <div class="mb-3">
                    <label class="form-label">作業終了時間</label>
                    <input 
                        type="time" 
                        class="form-control end-time" 
                        name="projects[${index}][end_time]"
                        value="{{ old('projects.${index}.end_time') }}"
                    >
                </div>

                <!-- 作業時間 -->
                <div class="mb-3">
                    <label class="form-label">作業時間（分）</label>
                    <input 
                        type="number" 
                        class="form-control work-hours" 
                        name="projects[${index}][work_hours]"
                        value="{{ old('projects.${index}.work_hours', 0) }}"
                        min="0"
                        max="1440"
                    >
                </div>

                <!-- 作業内容 -->
                <div class="mb-3">
                    <label class="form-label">作業内容 <span class="text-danger">*</span></label>
                    <textarea 
                        class="form-control" 
                        rows="6" 
                        name="projects[${index}][work_content]"
                        required
                    >{{ old('projects.${index}.work_content') }}</textarea>
                </div>

                <!-- 備考欄 -->
                <div class="mb-0">
                    <label class="form-label">備考欄</label>
                    <textarea 
                        class="form-control" 
                        rows="3"
                        name="projects[${index}][notes]"
                    >{{ old('projects.${index}.notes') }}</textarea>
                </div>
            </div>
        </div>
    `;
}

// 案件番号を更新
function updateProjectNumbers() {
    document.querySelectorAll('.project-item').forEach((card, idx) => {
        card.querySelector('.project-number').textContent = idx + 1;
    });
    
    // 削除ボタンの表示制御
    const removeButtons = document.querySelectorAll('.remove-project-btn');
    removeButtons.forEach(btn => {
        btn.style.display = removeButtons.length > 1 ? 'inline-block' : 'none';
    });
}

// 案件追加
document.getElementById('addProjectBtn').addEventListener('click', function() {
    const container = document.getElementById('projectsContainer');
    const div = document.createElement('div');
    div.innerHTML = createProjectCard(projectIndex);
    container.appendChild(div.firstElementChild);
    projectIndex++;
    updateProjectNumbers();
});

// 案件削除（イベント委譲）
document.getElementById('projectsContainer').addEventListener('click', function(e) {
    if (e.target.closest('.remove-project-btn')) {
        const card = e.target.closest('.project-item');
        card.remove();
        updateProjectNumbers();
        
        // 全て削除されたら1つ追加
        if (document.querySelectorAll('.project-item').length === 0) {
            document.getElementById('addProjectBtn').click();
        }
    }
});

// 初期表示で1つ追加
document.getElementById('addProjectBtn').click();
</script>
@endpush
