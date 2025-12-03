@extends('layout.app')
@section('title','Dashboard')
@section('dashboard','active')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<style>
    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.25rem;
    }

    .chart-wrapper {
        min-height: 320px;
    }

    @media (max-width: 576px) {
        .stat-card-title {
            font-size: .95rem;
        }

        .stat-card-number {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<!-- BEGIN: Page Main-->
<div id="main">
    <div class="container-fluid py-3">
        <div class="row g-3">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase text-muted mb-1 stat-card-title">{{__('messages.dashboard.today queue')}}</p>
                            <h4 class="fw-bold stat-card-number mb-0">{{$today_queue}}</h4>
                        </div>
                        <span class="stat-card-icon" style="background: linear-gradient(135deg,#00bcd4,#26c6da);">
                            <i class="material-icons">queue</i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase text-muted mb-1 stat-card-title">{{__('messages.dashboard.today served')}}</p>
                            <h4 class="fw-bold stat-card-number mb-0">{{$today_served}}</h4>
                        </div>
                        <span class="stat-card-icon" style="background: linear-gradient(135deg,#ff5252,#ef5350);">
                            <i class="material-icons">check_circle</i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase text-muted mb-1 stat-card-title">{{__('messages.dashboard.today noshow')}}</p>
                            <h4 class="fw-bold stat-card-number mb-0">{{$today_noshow}}</h4>
                        </div>
                        <span class="stat-card-icon" style="background: linear-gradient(135deg,#ff9f43,#ffb74d);">
                            <i class="material-icons">highlight_off</i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase text-muted mb-1 stat-card-title">{{__('messages.dashboard.today serving')}}</p>
                            <h4 class="fw-bold stat-card-number mb-0">{{$today_serving}}</h4>
                        </div>
                        <span class="stat-card-icon" style="background: linear-gradient(135deg,#66bb6a,#81c784);">
                            <i class="material-icons">play_circle_filled</i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-1">{{__('messages.dashboard.today')}}</h5>
                        <p class="text-muted small mb-0">{{__('messages.dashboard.number of tokens')}}</p>
                    </div>
                    <div class="card-body chart-wrapper">
                        <canvas id="avg" class="w-100 h-100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-1">{{__('messages.dashboard.today vs yesterday')}}</h5>
                        <p class="text-muted small mb-0">{{__('messages.dashboard.time')}}</p>
                    </div>
                    <div class="card-body chart-wrapper">
                        <canvas id="chart2" class="w-100 h-100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Page Main-->
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="{{asset('app-assets/chart.js')}}"></script>
<script>
    var ChartData = {
        labels: [
            "{{__('messages.common.queue')}}",
            "{{__('messages.common.served')}}",
            "{{__('messages.common.noshow')}}",
            "{{__('messages.common.serving')}}",
        ],
        datasets: [{
            label: 'Today',
            backgroundColor: [
                'rgb(0, 188, 212)',
                'rgb(255, 82, 82)',
                'rgb(255, 167, 38)',
                'rgb(102, 187, 106)',
            ],
            data: ['{{$today_queue}}', '{{$today_served}}', '{{$today_noshow}}', '{{$today_serving}}'],
            hoverOffset: 4,
        }]
    };

    const LineChartData = {
        labels: ['00:00', '6:00', '12:00', '18:00', '24:00'],
        datasets: [{
            label: "{{__('messages.dashboard.today')}}",
            data: [@foreach($chart_data['today'] as $indx => $data)
                @if($indx == 0) <?php echo "'$data'"; ?>
                @else <?php echo ", '$data'"; ?>
                @endif
                @endforeach
            ],
            borderColor: ['rgb(54, 162, 235)',],
            backgroundColor: ['rgb(255,255,255)'],
            pointStyle: 'circle',
            pointRadius: 6,
            pointHoverRadius: 9,
        },
        {
            label: "{{__('messages.dashboard.yesterday')}}",
            data: [@foreach($chart_data['yesterday'] as $indx => $data)
                @if($indx == 0) <?php echo "'$data'"; ?>
                @else <?php echo ", '$data'"; ?>
                @endif
                @endforeach
            ],
            borderColor: ['rgb(255, 99, 132)',],
            backgroundColor: ['rgb(255,255,255)'],
            pointStyle: 'circle',
            pointRadius: 6,
            pointHoverRadius: 9
        }
        ]
    };

    $(document).ready(function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

        var ctx = document.getElementById("avg").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'pie',
            data: ChartData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
            }
        });

        var c2 = document.getElementById("chart2").getContext("2d");
        window.myBar2 = new Chart(c2, {
            type: 'line',
            data: LineChartData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        min: 0,
                        title: {
                            display: true,
                            text: "{{__('messages.dashboard.number of tokens')}}"
                        },
                        ticks: {
                            stepSize: 1,
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: "{{__('messages.dashboard.time')}}"
                        },
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true
                        }
                    }
                }
            }
        });

    });
</script>
@endsection
