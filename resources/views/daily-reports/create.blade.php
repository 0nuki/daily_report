@extends('layouts.app')

@section('title', '日報作成')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>日報作成</h2>
                <a href="{{ route('daily-reports.index') }}" class="btn btn-outline-secondary">
                    戻る
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <p class="text-muted text-center py-5">
                        日報作成フォーム（今後実装予定）
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
