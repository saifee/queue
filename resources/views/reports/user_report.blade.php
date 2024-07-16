@extends('layout.app')
@section('title','Reports')
@section('report','active')
@section('user_report','active')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/data-tables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/data-tables/css/jquery.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/data-tables/css/select.dataTables.min.css')}}">
@endsection
@section('content')
<div id="main">
    <div>
        <div id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12 m12 l12 pb-1">
                        <h5 class="breadcrumbs-title col s5"><b>{{__('messages.menu.user report')}}</b></h5>
                        <ol class="breadcrumbs col s7 right-align">

                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m12 l12">
                <div id="inline-form" class="card card card-default scrollspy">
                    <div class="card-content">
                        <form action="{{route('user_report')}}" id="user_report_form" autocomplete="off">
                            <div class="row">
                                <div class="input-field col s5">
                                    <select name="user_id" id="user_id" data-error=".user_id">
                                        <option value="" disabled selected>{{__('messages.reports.all users')}}</option>
                                        @foreach ($users as $user)
                                        <option value="{{$user->id}}" @if($selected_user_id==$user->id) selected @endif>{{$user->name}} </option>
                                        @endforeach
                                    </select>
                                    <label>{{__('messages.reports.select user')}}</label>
                                    <div class="user_id">
                                        @if ($errors->has('user_id'))
                                        <span class="text-danger errbk">{{ $errors->first('user_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="input-field col m5 s5">
                                    <input id="date" name="date" type="text" class="datepicker" value="{{$selected_date}}" data-error=".date">
                                    <label for="date">{{__('messages.reports.date')}}</label>
                                    <div class="date">
                                        @if ($errors->has('date'))
                                        <span class="text-danger errbk">{{ $errors->first('date') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="input-field col m2 s2">
                                    <div class="input-field col s12">
                                        <button class="btn waves-effect waves-light" id="gobtn" type="submit">
                                            {{__('messages.reports.go')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @if($reports)
        <div class="col s12">
            <div class="container" style="width: 99%;">
                <div class="section-data-tables">
                    <div class="row">
                        <div class="col s12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col s12">
                                            <table id="page-length-option" class="display dataTable">
                                                <thead>
                                                    <tr>
                                                        <th width="10px">#</th>
                                                        <th>{{__('messages.reports.service')}}</th>
                                                        <th>{{__('messages.reports.token number')}}</th>
                                                        <th>{{__('messages.reports.counter')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reports as $key=> $report)
                                                    <tr>
                                                        <td>{{ $key+1}}</td>
                                                        <td>{{$report->service_name}}</td>
                                                        <td>{{$report->token_letter}}-{{$report->token_number}}</td>
                                                        <td>{{$report->counter_name}}</td>

                                                    </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@section('js')
<script src="{{asset('app-assets/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/data-tables/js/dataTables.select.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/jquery-validation/jquery.validate.min.js')}}"></script>


<script>
    $(document).ready(function() {
        let a = $(".input-field").find('.select-wrapper')
        a.css('display','block');
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });


        $('body').addClass('loaded');
        var ndate = $('#date').val();

        if (ndate == "") {
            $('#gobtn').attr('disabled', 'disabled');
        } else {
            $('#gobtn').removeAttr('disabled');
        }

    });
    $('#date').change(function(event) {
        var date = $('#date').val();

        if (date == "") {
            $('#gobtn').attr('disabled', 'disabled');
        } else {
            $('#gobtn').removeAttr('disabled');
        }
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

    $('#user_report_form').validate({
        rules: {
            user_id: {
                required: true,
            },
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