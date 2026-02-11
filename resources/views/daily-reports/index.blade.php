@extends('layouts.app')

@section('title', '日報一覧')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>日報一覧</h2>
                <a href="{{ route('daily-reports.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> 新規作成
                </a>
            </div>

            @if($reports->count() > 0)
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>日付</th>
                                        <th>案件名/顧客名</th>
                                        <th>作業時間</th>
                                        <th>作業内容</th>
                                        <th class="text-end">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr>
                                            <td>{{ $report->report_date->format('Y年m月d日') }}</td>
                                            <td>{{ $report->project_name }}</td>
                                            <td>{{ $report->work_hours }}分</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    {{ Str::limit($report->work_content, 50) }}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('daily-reports.show', $report) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('daily-reports.edit', $report) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('daily-reports.destroy', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('本当に削除しますか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted text-center py-5 mb-0">
                            まだ日報が登録されていません。<br>
                            「新規作成」ボタンから日報を作成してください。
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
