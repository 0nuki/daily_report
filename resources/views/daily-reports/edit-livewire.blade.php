@extends('layouts.app')

@section('title', '日報編集')

@section('content')
    @livewire('edit-daily-report', ['dailyReportId' => $dailyReport->id])
@endsection
