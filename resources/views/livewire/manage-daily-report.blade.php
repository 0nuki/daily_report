<div>
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ $isEditMode ? '日報編集' : '日報作成' }}</h2>
                <a href="{{ route('daily-reports.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> 戻る
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <!-- 日付 -->
                        <div class="mb-4 pb-3 border-bottom">
                            <label for="report_date" class="form-label fw-bold">日付 <span class="text-danger">*</span></label>
                            <input 
                                type="date" 
                                class="form-control @error('report_date') is-invalid @enderror" 
                                id="report_date" 
                                wire:model="report_date"
                                required
                            >
                            @error('report_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 案件リスト -->
                        <div class="mb-4">
                            @foreach($projects as $index => $project)
                                <div class="project-item card mb-3" wire:key="project-{{ $index }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 text-primary">案件 #{{ $index + 1 }}</h6>
                                            @if(count($projects) > 1)
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    wire:click="removeProject({{ $index }})"
                                                >
                                                    <i class="bi bi-trash"></i> 削除
                                                </button>
                                            @endif
                                        </div>

                                        <!-- 案件名/顧客名 -->
                                        <div class="mb-3">
                                            <label class="form-label">案件名/顧客名 <span class="text-danger">*</span></label>
                                            
                                            <!-- 既存の案件を選択 -->
                                            @if(count($availableProjects) > 0)
                                                <!-- 検索用入力フィールド -->
                                                <input 
                                                    type="text" 
                                                    class="form-control mb-2" 
                                                    wire:model.live="projects.{{ $index }}.project_search"
                                                    placeholder="案件名で検索..."
                                                >
                                                
                                                @php
                                                    $filteredProjects = $this->getFilteredProjects($index);
                                                @endphp
                                                
                                                <select 
                                                    class="form-select mb-2 @error('projects.'.$index.'.project_id') is-invalid @enderror" 
                                                    wire:model="projects.{{ $index }}.project_id"
                                                >
                                                    <option value="">-- 既存の案件を選択 --</option>
                                                    @forelse($filteredProjects as $availableProject)
                                                        <option value="{{ $availableProject->id }}">{{ $availableProject->name }}</option>
                                                    @empty
                                                        <option value="" disabled>検索結果なし</option>
                                                    @endforelse
                                                </select>
                                                @error('projects.'.$index.'.project_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                
                                                <div class="text-center text-muted small mb-2">または</div>
                                            @endif
                                            
                                            <!-- 新規案件名を入力 -->
                                            <input 
                                                type="text" 
                                                class="form-control @error('projects.'.$index.'.new_project_name') is-invalid @enderror" 
                                                wire:model="projects.{{ $index }}.new_project_name"
                                                placeholder="新しい案件名を入力"
                                            >
                                            @error('projects.'.$index.'.new_project_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- 作業時間情報（横並び） -->
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">開始時間</label>
                                                <input 
                                                    type="time" 
                                                    class="form-control @error('projects.'.$index.'.start_time') is-invalid @enderror" 
                                                    wire:model="projects.{{ $index }}.start_time"
                                                    wire:change="calculateWorkHours({{ $index }})"
                                                    onclick="this.showPicker()"
                                                >
                                                @error('projects.'.$index.'.start_time')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">終了時間</label>
                                                <input 
                                                    type="time" 
                                                    class="form-control @error('projects.'.$index.'.end_time') is-invalid @enderror" 
                                                    wire:model="projects.{{ $index }}.end_time"
                                                    wire:change="calculateWorkHours({{ $index }})"
                                                    onclick="this.showPicker()"
                                                >
                                                @error('projects.'.$index.'.end_time')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">作業時間</label>
                                                <div class="pt-2">
                                                    <span class="fs-5 fw-bold">{{ $projects[$index]['work_hours'] ?? 0 }}</span>
                                                    <span class="text-muted">分</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 作業内容 -->
                                        <div class="mb-3">
                                            <label class="form-label">作業内容 <span class="text-danger">*</span></label>
                                            <textarea 
                                                class="form-control @error('projects.'.$index.'.work_content') is-invalid @enderror" 
                                                rows="6" 
                                                wire:model="projects.{{ $index }}.work_content"
                                                required
                                            ></textarea>
                                            @error('projects.'.$index.'.work_content')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- 案件追加ボタン -->
                        <div class="mb-4">
                            <button 
                                type="button" 
                                class="btn btn-outline-primary w-100"
                                wire:click="addProject"
                            >
                                <i class="bi bi-plus-circle"></i> 案件を追加
                            </button>
                        </div>

                        <!-- 備考欄 -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">備考欄</label>
                            <textarea 
                                class="form-control @error('notes') is-invalid @enderror"
                                rows="4"
                                wire:model="notes"
                                placeholder="全案件共通の備考を入力してください"
                            ></textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 送信ボタン -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> {{ $isEditMode ? '更新' : '登録' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
