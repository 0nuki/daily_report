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

            <div class="card">
                <div class="card-body">
                    <p class="text-muted text-center py-5">
                        まだ日報が登録されていません。<br>
                        「新規作成」ボタンから日報を作成してください。
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
