@extends('layout.app')
@section('title','Users')
@section('user','active')
@section('content')
<div id="main" style="width:99%;">
    <div id="breadcrumbs-wrapper">
        <div class="container">
            <div class="row">
                <div class="col s12 m12 l12">
                    <h5 class="breadcrumbs-title col s5"><b>{{__('messages.user_page.edit user')}}</b></h5>
                    <ol class="breadcrumbs col s7 right-align">
                        <a class="btn-floating waves-effect waves-light teal tooltipped" href="{{route('users.index')}}" data-position=top data-tooltip="{{__('messages.common.go back')}}"><i class="material-icons">arrow_back</i></a>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col s12">
        <div class="container">
            <div class="row">
                <div class="col  s12 m10  offset-m1">
                    <div class="card-panel">
                        <div class="row">
                            <form id="user_form" method="post" action="{{route('users.update',[$user->id])}}" enctype="multipart/form-data">
                                {{@csrf_field()}}
                                {{method_field('PATCH')}}
                                <div class="row">
                                    <div class="row form_align">
                                        <div class="input-field col s6">
                                            <label for="name">{{__('messages.user_page.name')}}</label>
                                            <input id="name" name="name" type="text" value="{{$user->name}}" data-error=".name">
                                            <div class="name">
                                                @if ($errors->has('name'))
                                                <span class="text-danger errbk">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="email">{{__('messages.user_page.email')}}</label>
                                            <input id="email" name="email" type="text" value="{{$user->email}}" data-error=".email">
                                            <div class="email">
                                                @if ($errors->has('email'))
                                                <span class="text-danger errbk">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form_align">
                                        <div class="input-field col s6">
                                            <label for="password">{{__('messages.user_page.password')}}</label>
                                            <input id="password" name="password" type="password" data-error=".password">
                                            <div class="password">
                                                @if ($errors->has('password'))
                                                <span class="text-danger errbk">{{ $errors->first('password') }}</span>
                                                @endif
                                            </div>
                                        </div>
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
                                    </div>
                                    <div class="row form_align">
                                        <div class="input-field col s6">
                                            <select name="role" id="user_id">
                                                <option value="" disabled selected>{{__('messages.user_page.select role')}}</option>
                                                @foreach ($roles as $role)
                                                <option value="{{$role->id}}" {{ $user->getRoleNames()->contains($role->name) ? 'selected' : '' }}>{{$role->name}} </option>
                                                @endforeach
                                            </select>
                                            <label>{{__('messages.user_page.role')}}</label>
                                        </div>
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
                role: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 5
                },
                image: {
                    accept: "jpg|jpeg|png"
                }
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