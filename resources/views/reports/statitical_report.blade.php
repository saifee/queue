@extends('layout.app')
@section('title','Reports')
@section('report','active')
@section('statitical_report','active')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/data-tables/css/jquery.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/data-tables/css/select.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/data-tables.css')}}">
@endsection
@section('content')
<div id="main">
    <div id="breadcrumbs-wrapper">
        <div class="container">
            <div class="row">
                <div class="col s12 m12 l12">
                    <h5 class="breadcrumbs-title col s5"><b>Services</b></h5>
                    <ol class="breadcrumbs col s7 right-align">
                        <a class="btn-floating waves-effect waves-light tooltipped" href="{{route('services.create')}}" data-position="top" data-tooltip="Add service">
                            <i class="material-icons">add</i>
                        </a>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col s12 m12 l12">
            <div id="inline-form" class="card card card-default scrollspy">
                <div class="card-content">
                    <form action="" id="statitical_report_form">
                        <div class="row">
                            <div class="input-field col s2">
                                <input id="date" name="date" type="text" class="datepicker" value="">
                                <label for="date">Date</label>
                            </div>
                            <div class="input-field col s3">
                                <select name="user_id" id="user_id">
                                    <option value="" disabled selected>All Users</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}} </option>
                                    @endforeach
                                </select>
                                <label>User</label>
                            </div>
                            <div class="input-field col s3">
                                <select name="user_id" id="user_id">
                                    <option value="" disabled selected>All Services</option>
                                    @foreach ($services as $service)
                                    <option value="{{$service->id}}">{{$service->name}}</option>
                                    @endforeach
                                </select>
                                <label>Service</label>
                            </div>
                            <div class="input-field col s2">
                                <select name="user_id" id="user_id">
                                    <option value="" disabled selected>All Couners</option>
                                    @foreach($counters as $counter)
                                    <option value="{{$counter->id}}">{{$counter->name}}</option>
                                    @endforeach
                                </select>
                                <label>Counter</label>
                            </div>
                            <div class="input-field col m2 s2">
                                <div class="input-field col s12">
                                    <button class="btn waves-effect waves-light" type="submit" name="action">
                                        Go</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class=row>
        <div class="col s12">
            <div class=card-panel>
                <span style="line-height:0;font-size:22px;font-weight:300">Average Waiting Time</span>
                <div class=divider style="margin:15px 0 10px 0"></div>
                <div><canvas id="avg" style="height:249px;width:547px" height=249 width=547></canvas></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="{{asset('app-assets/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/chart.js')}}"></script>
<script src="{{asset('app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/data-tables/js/dataTables.select.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/jquery-validation/jquery.validate.min.js')}}"></script>
<script>
    var year = ['2013', '2014', '2015', '2016'];
    var data_click = [1, 2, 3, 4, 5];
    var data_viewer = [1, 2, 3, 4, 5];

    var barChartData = {
        labels: year,
        datasets: [{
            label: 'Click',
            backgroundColor: "rgba(220,220,220,0.5)",
            data: data_click
        }, {
            label: 'View',
            backgroundColor: "rgba(151,187,205,0.5)",
            data: data_viewer
        }]
    };

    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

        var ctx = document.getElementById("avg").getContext("2d");

        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgb(0, 255, 0)',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                title: {
                    display: true,
                    text: 'Yearly Website Visitor'
                }
            }
        });


    });
    $('#page-length-option').DataTable({
        "responsive": true,
        "autoHeight": false,
        "scrollX": true,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ]
    });

    $('#statitical_report_form').validate({
        rules: {
            date: {
                required: true,
            },
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        }
    });
</script>
@endsection