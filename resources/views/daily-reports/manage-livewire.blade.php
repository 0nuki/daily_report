@extends('layouts.app')

@section('title', isset($dailyReport) ? '日報編集' : '日報作成')

@section('content')
    @if(isset($dailyReport))
        @livewire('manage-daily-report', ['dailyReportId' => $dailyReport->id])
    @else
        @livewire('manage-daily-report')
    @endif
@endsection
