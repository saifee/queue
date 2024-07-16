@extends('layout.app')
@section('title','Dashboard')
@section('profile','active')
@section('content')
<div id="main" style="width:99%;">
    <div id="breadcrumbs-wrapper">
        <div class="container">
            <div class="row">
                <div class="col s12 m12 l12">
                    <h5 class="breadcrumbs-title col s5"><b>{{__('messages.common.profile')}}</b></h5>
                    <ol class="breadcrumbs col s7 right-align">
                        <a class="btn-floating waves-effect waves-light teal tooltipped" href="{{route('dashboard')}}" data-position=top data-tooltip="{{__('messages.common.go back')}}"><i class="material-icons">arrow_back</i></a>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col s12">
        <div class="container">
            <div class="row">
                <div class="col  s10 m8  offset-m2">
                    <div class="card-panel">
                        <div class="row">
                            <form id="user_form" method="post" action="{{route('update_profile',[$profile->id])}}" enctype="multipart/form-data">
                                {{@csrf_field()}}
                                <div class="row">
                                    <div class="row form_align">
                                        <div class="input-field col s6">
                                            <label for="name">{{__('messages.user_page.name')}}</label>
                                            <input id="name" name="name" type="text" value="{{$profile->name}}" data-error=".name">
                                            <div class="name">
                                                @if ($errors->has('name'))
                                                <span class="text-danger errbk">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="email">{{__('messages.user_page.email')}}</label>
                                            <input id="email" name="email" type="text" value="{{$profile->email}}" data-error=".email">
                                            <div class="email">
                                                @if ($errors->has('email'))
                                                <span class="text-danger errbk">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form_align">
                                        <div class="file-field input-field col s6">
                                            <div class="btn">
                                                <span>{{__('messages.user_page.image')}}</span>
                                                <input type="file" name="image">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text">
                                            </div>
                                            @if ($errors->has('image'))
                                            <span class="text-danger errbk">{{ $errors->first('image') }}</span>
                                            @endif
                                        </div>
                                         @if(isset($profile->image) && Storage::disk('public')->exists($profile->image))       
                                        <div class="col s6 ">
                                            <img class="responsive-img circle z-depth-5" width="80" style="height: 80px;" src="{{$profile->image_url}}" alt="">
                                        </div>
                                        @endif
                                    </div>
                                    <div class="input-field col s12">
                                        <button class="btn waves-effect waves-light right submit" type="submit" name="action">{{__('messages.common.update')}}
                                            <i class="mdi-content-send right"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-panel">
                        <div class="row">
                            <h4>{{__('messages.profile.change password')}}</h4>
                            <form id="password_form" method="post" action="{{route('change_password')}}">
                                {{@csrf_field()}}
                                <div class="row">
                                    <div class="row form_align">
                                        <div class="input-field col s6">
                                            <label for="password">{{__('messages.profile.new password')}}</label>
                                            <input id="newpassword" name="newpassword" type="password" data-error=".newpassword">
                                            <div class="newpassword">
                                                @if ($errors->has('newpassword'))
                                                <span class="text-danger errbk">{{ $errors->first('newpassword') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="cpassword">{{__('messages.profile.confirm password')}}</label>
                                            <input id="confirmpassword" name="confirmpassword" type="password" data-error=".confirmpassword">
                                            <div class="confirmpassword">
                                                @if ($errors->has('confirmpassword'))
                                                <span class="text-danger errbk">{{ $errors->first('confirmpassword') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-field col s12">
                                        <button class="btn waves-effect waves-light right submit" type="submit" name="action">{{__('messages.common.submit')}}
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
@endsection
@section('js')
<script src="{{asset('app-assets/js/vendors.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/jquery-validation/jquery.validate.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('body').addClass('loaded');
    });
    $(function() {
        $('#user_form').validate({
            rules: {
                name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
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

        $('#password_form').validate({
            rules: {
                newpassword: {
                    required: true,
                },
                confirmpassword: {
                    required: true,
                    equalTo: "#newpassword"
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