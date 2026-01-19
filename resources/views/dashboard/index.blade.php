@extends('layout.app')
@section('title','Dashboard')
@section('dashboard','active')

@section('css')
<style>
    /* GUI Tweaks specific to dashboard */
    .stat-card-icon {
        width: 50px; height: 50px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white;
    }
    .chart-wrapper { position: relative; height: 300px; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase mb-2">{{__('messages.dashboard.today queue')}}</h6>
                        <h3 class="fw-bold mb-0">{{$today_queue}}</h3>
                    </div>
                    <div class="stat-card-icon bg-info bg-gradient">
                        <i class="material-icons">queue</i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase mb-2">{{__('messages.dashboard.today served')}}</h6>
                        <h3 class="fw-bold mb-0">{{$today_served}}</h3>
                    </div>
                    <div class="stat-card-icon bg-danger bg-gradient">
                        <i class="material-icons">check_circle</i>
                    </div>
                </div>
            </div>
        </div>
        </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">{{__('messages.dashboard.today')}}</h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="avg"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">{{__('messages.dashboard.today vs yesterday')}}</h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="chart2"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{asset('app-assets/chart.js')}}"></script>
<script>
    // ... Keep your existing chart initialization code here ...
    // Note: ensure your Chart.js config matches the version you are loading.
</script>
@endsection
