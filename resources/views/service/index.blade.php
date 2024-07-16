@extends('layout.app')
@section('title','Services')
@section('service','active')
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
                    <h5 class="breadcrumbs-title col s5"><b>{{__('messages.service_page.services')}}</b></h5>
                    <ol class="breadcrumbs col s7 right-align">
                        <a class="btn-floating waves-effect waves-light tooltipped" href="{{route('services.create')}}" data-position="top" data-tooltip="{{__('messages.service_page.add service')}}">
                            <i class="material-icons">add</i>
                        </a>
                    </ol>
                </div>
            </div>
        </div>
    </div>
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
                                                    <th>{{__('messages.service_page.name')}}</th>
                                                    <th>{{__('messages.service_page.letter')}}</th>
                                                    <th>{{__('messages.service_page.starting number')}}</th>
                                                    <th>{{__('messages.service_page.status')}}</th>
                                                    <th>{{__('messages.service_page.action')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($services as $key=>$service)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>{{$service->name}}</td>
                                                    <td>{{$service->letter}}</td>
                                                    <td>{{$service->start_number}}</td>
                                                    <td>
                                                        <div class="switch">
                                                            <label>
                                                                <input type="checkbox" {{$service->status?'checked':''}} onchange="changeStatus({{$service->id}})">
                                                                <span class="lever"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a class="btn-floating btn-action waves-effect waves-light orange tooltipped" href="{{route('services.edit',[$service->id])}}" data-position=top data-tooltip="{{__('messages.common.edit')}}"><i class="material-icons">edit</i></a>

                                                        <a class="btn-floating btn-action waves-effect waves-light red tooltipped frmsubmit" href="{{route('services.destroy',[$service->id])}}" data-position=top data-tooltip="{{__('messages.common.delete')}}" method="DELETE"><i class="material-icons">delete</i></a>

                                                        <a class="btn-floating btn-action waves-effect waves-light green tooltipped" href="{{route('get_display_by_service',[\Crypt::encrypt($service->id)])}}" target="_blank" data-position=top data-tooltip="{{__('messages.display.display')}}"><i class="material-icons">desktop_windows</i></a>

                                                    </td>
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
</div>
@endsection
@section('js')
<script src="{{asset('app-assets/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/data-tables/js/dataTables.select.min.js')}}"></script>
<script>
    $('#page-length-option').DataTable({
        "responsive": true,
        "autoHeight": false,
        "scrollX": true,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ]
    });
    $(document).ready(function() {
        $('body').addClass('loaded');
    });
</script>
<script>
    function changeStatus(id) {
        $('body').removeClass('loaded');
        var data = "id=" + id + '&_token={{csrf_token()}}';
        $.ajax({
            type: "POST",
            url: "{{Route('service_change_status')}}",
            data: data,
            cache: false,
            success: function(response) {
                location.reload(true);
            }
        });
    }
</script>
@endsection