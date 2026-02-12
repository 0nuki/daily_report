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
                @foreach($reports as $date => $dailyReports)
                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ \Carbon\Carbon::parse($date)->format('Y年m月d日') }}</h5>
                            <div>
                                <a href="{{ route('daily-reports.show', $dailyReports->first()->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> 詳細
                                </a>
                                <a href="{{ route('daily-reports.edit', $dailyReports->first()->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> 編集
                                </a>
                                <form action="{{ route('daily-reports.destroy', $dailyReports->first()->id) }}" method="POST" class="d-inline" onsubmit="return confirm('この日付の日報を削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> 削除
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

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
