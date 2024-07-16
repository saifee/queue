@extends('layout.app')
@section('title','Counters')
@section('counter','active')
@section('content')
<div id="main" style="width:99%;">
    <div id="breadcrumbs-wrapper">
        <div class="container">
            <div class="row">
                <div class="col s12 m12 l12">
                    <h5 class="breadcrumbs-title col s5"><b>{{__('messages.counter_page.add counter')}}</b></h5>
                    <ol class="breadcrumbs col s7 right-align">
                        <a class="btn-floating waves-effect waves-light teal tooltipped" href="{{route('counters.index')}}" data-position=top data-tooltip="{{__('messages.common.go back')}}"><i class="material-icons">arrow_back</i></a>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col s12">
        <div class="container">
            <div class="row">
                <div class="col s12 m8  offset-m2">
                    <div class="card-panel">
                        <div class="row">
                            <form id="counter_form" method="post" action="{{route('counters.store')}}">
                                {{@csrf_field()}}
                                <div class="row">
                                    <div class="row form_align">
                                        <div class="input-field col s12">
                                            <label for="name">{{__('messages.counter_page.counter name')}}</label>
                                            <input id="name" name="name" type="text" value="{{old('name')}}" data-error=".name">
                                            <div class="name">
                                                @if ($errors->has('name'))
                                                <span class="text-danger errbk">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                    <div class="input-field col s12">
                                        <button class="btn waves-effect waves-light right submit" type="submit">{{__('messages.common.submit')}}
                                            <i class="mdi-content-send right"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
<script src="{{asset('app-assets/js/vendors.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/jquery-validation/jquery.validate.min.js')}}"></script>
<script>
    $(function() {
        $(document).ready(function() {
            $('body').addClass('loaded');
        });
        $('#counter_form').validate({
            rules: {
                name: {
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
    });
</script>
@endsection
@endsection