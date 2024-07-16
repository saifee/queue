@extends('layout.app')
@section('title','Settings')
@section('settings','active')
@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/colorpicker.css')}}">
@endsection
@section('content')
<div id="main" style="width:99%;">
    <div id="breadcrumbs-wrapper" style="width:101%">
        <div class="container">
            <div class="row">
                <div class="col s12 m12 l12">
                    <h5 class="breadcrumbs-title col s5 pb-1" style="margin:10px 0 0"><b>{{__('messages.menu.settings')}}</b></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col s12" style="margin-top: 10px; padding-right:0">
            <ul class="tabs tabs-fixed-width tab-demo z-depth-1">
                <li class="tab"><a href="#test40" class="active">{{__('messages.settings.General Settings')}}</a></li>
                <li class="tab"><a class="" href="#test5">{{__('messages.settings.SMS Settings')}}</a></li>
                <li class="indicator" style="left: 0px; right: 796px;"></li>
            </ul>
        </div>
        <div class="col s12">
            <div id="test40" class="col s12 active" style="display: block;">
                <div class="row">
                    <div class="col s12 m12 l6 pl-3">
                        <div id="basic-form" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{__('messages.settings.company settings')}}</h4>
                                <form id="company_settings" method="post" action="{{route('update_settings')}}" enctype="multipart/form-data">
                                    {{@csrf_field()}}
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input type="text" id="name" name="name" value="{{$settings->name}}" data-error=".name">
                                            <label for="fn">{{__('messages.settings.name')}}</label>
                                            <div class="name">
                                                @if ($errors->has('name'))
                                                <span class="text-danger errbk">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="email" name="email" type="email" value="{{$settings->email}}" data-error=".email">
                                            <label for="email">{{__('messages.settings.email')}}</label>
                                            <div class="email">
                                                @if ($errors->has('email'))
                                                <span class="text-danger errbk">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <textarea id="address" name="address" class="materialize-textarea" data-error=".address">{{$settings->address}}</textarea>
                                            <label for="textarea2">{{__('messages.settings.address')}}</label>
                                            <div class="address">
                                                @if ($errors->has('address'))
                                                <span class="text-danger errbk">{{ $errors->first('address') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <input id="phone" name="phone" type="text" value="{{$settings->phone}}" data-error=".phone">
                                            <label for="password">{{__('messages.settings.phone')}}</label>
                                            <div class="phone">
                                                @if ($errors->has('phone'))
                                                <span class="text-danger errbk">{{ $errors->first('phone') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="location" name="location" type="text" value="{{$settings->location}}" data-error=".location">
                                            <label for="location">{{__('messages.settings.location')}}</label>
                                            <div class="location">
                                                @if ($errors->has('location'))
                                                <span class="text-danger errbk">{{ $errors->first('location') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <select name="timezone" id="timezone" data-error=".timezone">
                                                <option value="" disabled selected>{{__('messages.settings.select timezone')}}</option>
                                                @foreach($timezones as $timezone)
                                                <option value="{{$timezone}}" @if($timezone==$settings->timezone) selected @endif>{{$timezone}}</option>
                                                @endforeach
                                            </select>
                                            <p style="font-size: 12px; color:#e77676">{{__('messages.settings.timezone_message')}}</p>
                                            <label class="active">{{__('messages.settings.timezone')}}</label>
                                            <div class="timezone">
                                                @if ($errors->has('timezone'))
                                                <span class="text-danger errbk">{{ $errors->first('timezone') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @if($settings->logo && Storage::disk('public')->exists($settings->logo))<div class="pl-3"><img height="40px" width="60px" src="{{$settings->logo_url}}"></div>@endif
                                        <div class="file-field input-field col s9">
                                            <div class="btn">
                                                <span>{{__('messages.settings.logo')}}</span>
                                                <input type="file" name="logo" data-error=".logo">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text">
                                            </div>
                                            @if ($errors->has('logo'))
                                            <span class="text-danger errbk">{{ $errors->first('logo') }}</span>
                                            @endif
                                        </div>
                                        @if($settings->logo)
                                        <div class="col s3" style="padding-top: 15px;">
                                            <div class="btn" style="display: block; padding:0 1rem; background-color:#f15353;" onclick="removeLogo()">
                                                <span>{{__('messages.common.delete')}}</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <button class="btn  waves-effect waves-light right" type="submit" name="action">{{__('messages.common.update')}}
                                                <i class="material-icons right">import_export</i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col s12 m12 l6">
                        <div id="basic-form" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{__('messages.settings.change default language')}}</h4>
                                <form action="{{route('update_language_settings')}}" method="POST">
                                    {{@csrf_field()}}
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <select name="language" data-error=".language">
                                                <option value="" disabled selected>{{__('messages.settings.select language')}}</option>
                                                @foreach ($languages as $language)
                                                <option value="{{$language->id}}" {{ $language->id == $settings->language_id ?'selected':''}}>{{$language->name}}</option>
                                                @endforeach
                                            </select>
                                            <label>{{__('messages.settings.select language')}}</label>
                                            <div class="language">
                                                @if ($errors->has('language'))
                                                <span class="text-danger errbk">{{ $errors->first('language') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12" style="margin: 10px 0;">
                                            <button class="btn  waves-effect waves-light right" type="submit" name="action">{{__('messages.common.update')}}
                                                <i class="material-icons right">import_export</i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col s12 m12 l6">
                        <div id="basic-form" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h4 class="card-title">{{__('messages.settings.notification')}}</h4>
                                <h6 style="font-size: 1rem;">{{__('messages.settings.Preview')}}:</h6>
                                <div class="row">
                                    <span style="font-size:{{$settings->display_font_size}}px;color:{{$settings->display_font_color}}">
                                        <marquee>{{$settings->display_notification ? $settings->display_notification : 'Hello'}}</marquee>
                                    </span>
                                </div>
                                <form id="notification_settings" action="{{route('update_display_settings')}}" method="post">
                                    {{@csrf_field()}}
                                    <div class="row">
                                        <div class="input-field col s12" style="margin: 5px 0;">
                                            <input type="text" id="notification_text" name="notification_text" value="{{$settings->display_notification}}" data-error=".notification_text">
                                            <label for="fn">{{__('messages.settings.notification text')}}</label>
                                            <div class="notification_text">
                                                @if ($errors->has('notification_text'))
                                                <span class="text-danger errbk">{{ $errors->first('notification_text') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s6" style="margin: 5px 0;">
                                            <input id="font_size" type="number" name="font_size" value="{{$settings->display_font_size}}" data-error=".font_size">
                                            <label for="font_size">{{__('messages.settings.font size')}}</label>
                                            <div class="font_size">
                                                @if ($errors->has('font_size'))
                                                <span class="text-danger errbk">{{ $errors->first('font_size') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s6" style="margin: 5px 0;">
                                            <input id="color" type="text" name="color" value="{{$settings->display_font_color}}" data-error=".color">
                                            <label for="color" class="active">{{__('messages.settings.color')}}</label>
                                            <div class="color">
                                                @if ($errors->has('color'))
                                                <span class="text-danger errbk">{{ $errors->first('color') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <select name="display_voice" data-error=".display_voice">
                                                <option value="0" {{$settings->display_voice_enabled=='0' ? 'selected' : ''}}>Disabled</option>
                                                <option value="1" {{$settings->display_voice_enabled=='1' ? 'selected' : ''}}>Enabled</option>
                                            </select>
                                            <label>Display Voice</label>
                                            <div class="display_voice">
                                                @if ($errors->has('display_voice'))
                                                <span class="text-danger errbk">{{ $errors->first('display_voice') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s6">
                                            <select name="print_preview" data-error=".print_preview">
                                                <option value="0" {{$settings->print_preview_enabled=='0' ? 'selected' : ''}}>Disabled</option>
                                                <option value="1" {{$settings->print_preview_enabled=='1' ? 'selected' : ''}}>Enabled</option>
                                            </select>
                                            <label>Print Preview</label>
                                            <div class="print_preview">
                                                @if ($errors->has('print_preview'))
                                                <span class="text-danger errbk">{{ $errors->first('print_preview') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col s12">
                                            <h4>{{__('messages.settings.display voice settings')}}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s6" style="margin: 5px 0;">
                                            <input id="token_translation" type="text" name="token_translation" value="{{$settings->language->token_translation}}" data-error=".token_translation">
                                            <label for="token_translation">{{__('messages.settings.token translation')}}</label>
                                            <div class="token_translation">
                                                @if ($errors->has('token_translation'))
                                                <span class="text-danger errbk">{{ $errors->first('token_translation') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s6" style="margin: 5px 0;">
                                            <input id="please_proceed_to_translation" type="text" name="please_proceed_to_translation" value="{{$settings->language->please_proceed_to_translation}}" data-error=".please_proceed_to_translation">
                                            <label for="please_proceed_to_translation">{{__('messages.settings.please proceed to translation')}}</label>
                                            <div class="please_proceed_to_translation">
                                                @if ($errors->has('please_proceed_to_translation'))
                                                <span class="text-danger errbk">{{ $errors->first('please_proceed_to_translation') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12" style="margin: 10px 0;">
                                            <button class="btn  waves-effect waves-light right" type="submit" name="action">{{__('messages.common.update')}}
                                                <i class="material-icons right">import_export</i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="test5" class="col s12" style="display: none;">
                <div class="col s12">
                    <div class="card-panel" style="margin-top: 15px;">
                        <div class="row">
                            <form id="sms_settings" method="post" action="{{route('update_sms_settings')}}">
                                {{@csrf_field()}}
                                <div class="row">
                                    <div class="row form_align">
                                        <div class="input-field col s3">
                                            <select name="sms_enabled" id="sms_enabled" data-error=".sms_enabled" onchange="changeSmsEnabled()">
                                                <option value="0" @if($settings->sms_enabled== 0) selected @endif>No</option>
                                                <option value="1" @if($settings->sms_enabled== 1) selected @endif>Yes</option>
                                            </select>
                                            <label>{{__('messages.settings.SMS Enabled')}}</label>
                                            <div class="sms_enabled">
                                                @if ($errors->has('sms_enabled'))
                                                <span class="text-danger errbk">{{ $errors->first('sms_enabled') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s9" id="url_tab">
                                            <label for="sms_url">{{__('messages.settings.SMS URL')}}</label>
                                            <input id="sms_url" name="sms_url" type="text" value="{{$settings->sms_url}}" data-error=".sms_url">
                                            <div class="sms_url">
                                                @if ($errors->has('sms_url'))
                                                <span class="text-danger errbk">{{ $errors->first('sms_url') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form_align">
                                        <div class="input-field col s3" style="float:right;margin:0">
                                            <button class="btn waves-effect right waves-light submit" type="submit">{{__('messages.common.submit')}}
                                                <i class="mdi-content-send right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-left: 10px;" id="url_keywords">
                                        <div class="col s12">
                                            <h6 style="font-size: 1.2rem; margin:10px 0">{{__('messages.settings.URL Keywords')}}</h6>
                                        </div>
                                        <div class="col s2">$phone$</div>
                                        <div class="col s10">{{__('messages.settings.Phone Number')}}</div>
                                        <div class="col s2">$text$</div>
                                        <div class="col s10">{{__('messages.settings.SMS Text')}}</div>
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
<script src="{{asset('app-assets/colorpicker.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#color').colorpicker();
        changeSmsEnabled();
    })
    // $('body').addClass('loaded');
    $(function() {
        $('#company_settings').validate({
            rules: {
                name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true,
                    number: true
                },
                address: {
                    required: true
                },
                timezone: {
                    required: true
                },
                location: {
                    required: true
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

    $(function() {
        $('#notification_settings').validate({
            rules: {
                notification_text: {
                    required: true,
                    minlength: 5

                },
                font_size: {
                    required: true,
                    number: true,
                    min: 15,
                    max: 50
                },
                color: {
                    required: true,
                },
                token_tranlation: {
                    required: true,
                },
                please_proceed_to_translation: {
                    required: true,
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

    function changeSmsEnabled() {
        if ($('#sms_enabled').val() == 1) {
            $('#url_keywords,#url_tab').show();
        } else {
            $('#url_tab,#url_keywords').hide();
        }
    }

    $(function() {
        $('#sms_settings').validate({
            rules: {
                sms_enabled: {
                    required: true
                },
                sms_url: {
                    required: function(element) {
                        return $("#sms_enabled").val() == "1";
                    },
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
<script>
    function removeLogo() {
        $('body').removeClass('loaded');
        $.ajax({
            type: "GET",
            url: "{{Route('remove_logo')}}",
            cache: false,
            success: function(response) {
                if (response.status_code == 200) {
                    M.toast({
                        html: 'successfully removed'
                    });
                    location.reload(true);
                } else {
                    M.toast({
                        html: 'something went wrong',
                        classes: "toast-error"
                    });
                    $('body').addClass('loaded');
                }
            },
            error: function() {
                M.toast({
                    html: 'something went wrong',
                    classes: "toast-error"
                });
                $('body').addClass('loaded');
            }
        });
    }
</script>
@endsection