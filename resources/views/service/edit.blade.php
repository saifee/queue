@extends('layout.app')
@section('title','Services')
@section('service','active')
@section('content')
<div id="main" style="width:99%;">
    <div id="breadcrumbs-wrapper">
        <div class="container">
            <div class="row">
                <div class="col s12 m12 l12">
                    <h5 class="breadcrumbs-title col s5"><b>{{__('messages.service_page.edit service')}}</b></h5>
                    <ol class="breadcrumbs col s7 right-align">
                        <a class="btn-floating waves-effect waves-light teal tooltipped" href="{{route('services.index')}}" data-position=top data-tooltip="{{__('messages.common.go back')}}"><i class="material-icons">arrow_back</i></a>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col s12">
        <div class="container">
            <div class="row">
                <div class="col  s12 m8  offset-m2">
                    <div class="card-panel">
                        <div class="row">
                            <form id="service_form" method="post" action="{{route('services.update',[$service->id])}}">
                                {{@csrf_field()}}
                                {{method_field('PATCH')}}
                                <div class="row">
                                    <div class="row form_align">
                                        <div class="input-field col s12">
                                            <label for="name">{{__('messages.service_page.service name')}}</label>
                                            <input id="name" name="name" type="text" value="{{$service->name}}" data-error=".name">
                                            <div class="name">
                                                @if ($errors->has('name'))
                                                <span class="text-danger errbk">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form_align">
                                        <div class="input-field col s6">
                                            <label for="letter">{{__('messages.service_page.letter')}}</label>
                                            <input id="letter" name="letter" type="text" value="{{$service->letter}}" data-error=".letter">
                                            <div class="letter">
                                                @if ($errors->has('letter'))
                                                <span class="text-danger errbk">{{ $errors->first('letter') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="start_number">{{__('messages.service_page.starting number')}}</label>
                                            <input id="start_number" name="start_number" type="number" value="{{$service->start_number}}" data-error=".start_number">
                                            <div class="start_number">
                                                @if ($errors->has('start_number'))
                                                <span class="text-danger errbk">{{ $errors->first('start_number') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form_align">
                                        <div class="input-field col s3">
                                            <select name="ask_name" id="ask_name" data-error=".ask_name" onchange="enableRequired()">
                                                <option value="0" @if($service->ask_name==0) selected @endif>No</option>
                                                <option value="1" @if($service->ask_name==1) selected @endif>Yes</option>
                                            </select>
                                            <label>{{__('messages.service_page.Ask Name For Token')}}</label>
                                            <div class="ask_name">
                                                @if ($errors->has('ask_name'))
                                                <span class="text-danger errbk">{{ $errors->first('ask_name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s3">
                                            <select name="name_required" id="name_required" data-error=".name_required">
                                                <option value="0" @if($service->name_required==0) selected @endif>No</option>
                                                <option value="1" @if($service->name_required==1) selected @endif>Yes</option>
                                            </select>
                                            <label>{{__('messages.service_page.Name is required')}}</label>
                                            <div class="name_required">
                                                @if ($errors->has('name_required'))
                                                <span class="text-danger errbk">{{ $errors->first('name_required') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s3">
                                            <select name="ask_email" id="ask_email" data-error=".ask_email" onchange="enableRequired()">
                                                <option value="0" @if($service->ask_email==0) selected @endif>No</option>
                                                <option value="1" @if($service->ask_email==1) selected @endif>Yes</option>
                                            </select>
                                            <label>{{__('messages.service_page.Ask Email For Token')}}</label>
                                            <div class="ask_email">
                                                @if ($errors->has('ask_email'))
                                                <span class="text-danger errbk">{{ $errors->first('ask_email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s3">
                                            <select name="email_required" id="email_required" data-error=".email_required">
                                                <option value="0" @if($service->email_required==0) selected @endif>No</option>
                                                <option value="1" @if($service->email_required==1) selected @endif>Yes</option>
                                            </select>
                                            <label>{{__('messages.service_page.Email is required')}}</label>
                                            <div class="email_required">
                                                @if ($errors->has('email_required'))
                                                <span class="text-danger errbk">{{ $errors->first('email_required') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form_align">
                                        <div class="input-field col s3">
                                            <select name="ask_phone" id="ask_phone" data-error=".ask_phone" onchange="enableRequired()">
                                                <option value="0" @if($service->ask_phone==0) selected @endif>No</option>
                                                <option value="1" @if($service->ask_phone==1) selected @endif>Yes</option>
                                            </select>
                                            <label>{{__('messages.service_page.Ask Phone For Token')}}</label>
                                            <div class="ask_phone">
                                                @if ($errors->has('ask_phone'))
                                                <span class="text-danger errbk">{{ $errors->first('ask_phone') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="input-field col s3">
                                            <select name="phone_required" id="phone_required" data-error=".phone_required">
                                                <option value="0" @if($service->phone_required==0) selected @endif>No</option>
                                                <option value="1" @if($service->phone_required==1) selected @endif>Yes</option>
                                            </select>
                                            <label>{{__('messages.service_page.Phone is required')}}</label>
                                            <div class="phone_required">
                                                @if ($errors->has('phone_required'))
                                                <span class="text-danger errbk">{{ $errors->first('phone_required') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($settings->sms_enabled)
                                        <div class="input-field col s6">
                                            <select name="sms" id="sms" data-error=".sms" onchange="changeSMS()">
                                                <option value="0" @if($service->sms_enabled==0) selected @endif>No</option>
                                                <option value="1" @if($service->sms_enabled==1) selected @endif>Yes</option>
                                            </select>
                                            <label>{{__('messages.settings.SMS Enabled')}}</label>
                                            <div class="sms">
                                                @if ($errors->has('sms'))
                                                <span class="text-danger errbk">{{ $errors->first('sms') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @if($settings->sms_enabled)
                                    <div id="sms_tab">
                                        <div class="row form_align">
                                            <h3 style="margin-left: 16px;">{{__('messages.settings.SMS Settings')}}</h3>
                                        </div>
                                        <div class="row form_align">
                                            <div class="input-field col s6">
                                                <select name="optin_message" id="optin_message" data-error=".optin_message" onchange="changeOptinMessage()">
                                                    <option value="0" @if($service->optin_message_enabled== 0) selected @endif>No</option>
                                                    <option value="1" @if($service->optin_message_enabled== 1) selected @endif>Yes</option>
                                                </select>
                                                <label>{{__('messages.service_page.Optin Message')}}</label>
                                                <div class="optin_message">
                                                    @if ($errors->has('optin_message'))
                                                    <span class="text-danger errbk">{{ $errors->first('optin_message') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="input-field col s6">
                                                <select name="call_message" id="call_message" data-error=".call_message" onchange="changeCallMessage()">
                                                    <option value="0" @if($service->call_message_enabled== 0) selected @endif>No</option>
                                                    <option value="1" @if($service->call_message_enabled== 1) selected @endif>Yes</option>
                                                </select>
                                                <label>{{__('messages.service_page.Call Message')}}</label>
                                                <div class="call_message">
                                                    @if ($errors->has('call_message'))
                                                    <span class="text-danger errbk">{{ $errors->first('call_message') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form_align">
                                            <div class="input-field col s6" style="margin-top: 0;" id="optin_message_tab">
                                                <textarea id="optin_message_format" name="optin_message_format" class="materialize-textarea" data-error=".optin_message_format">@if($service->optin_message_format){{$service->optin_message_format}} @else Your token number is '$token_number$'. You are #$position$ in the '$service_name$' queue @endif</textarea>
                                                <label for="optin_message_format">{{__('messages.service_page.Optin Message Format')}}</label>
                                                <div class="optin_message_format">
                                                    @if ($errors->has('optin_message_format'))
                                                    <span class="text-danger errbk">{{ $errors->first('optin_message_format') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="input-field col s6" style="margin-top: 0; float: right;" id="call_message_tab">
                                                <textarea id="call_message_format" name="call_message_format" class="materialize-textarea" data-error=".call_message_format">@if($service->call_message_format){{$service->call_message_format}} @else Your token number '$token_number$' is next to see to '$service_name$' queue @endif</textarea>
                                                <label for="call_message_format">{{__('messages.service_page.Call Message Format')}}</label>
                                                <div class="call_message_format">
                                                    @if ($errors->has('call_message_format'))
                                                    <span class="text-danger errbk">{{ $errors->first('call_message_format') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form_align">
                                            <div class="input-field col s6">
                                                <select name="noshow_message" id="noshow_message" data-error=".noshow_message" onchange="changeNoshowMessage()">
                                                    <option value="0" @if($service->noshow_message_enabled== 0) selected @endif>No</option>
                                                    <option value="1" @if($service->noshow_message_enabled== 1) selected @endif>Yes</option>
                                                </select>
                                                <label>{{__('messages.service_page.No Show Message')}}</label>
                                                <div class="noshow_message">
                                                    @if ($errors->has('noshow_message'))
                                                    <span class="text-danger errbk">{{ $errors->first('noshow_message') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="input-field col s6">
                                                <select name="completed_message" id="completed_message" data-error=".completed_message" onchange="changeCompletedMessage()">
                                                    <option value="0" @if($service->completed_message_enabled== 0) selected @endif>No</option>
                                                    <option value="1" @if($service->completed_message_enabled== 1) selected @endif>Yes</option>
                                                </select>
                                                <label>{{__('messages.service_page.Service Completed Message')}}</label>
                                                <div class="completed_message">
                                                    @if ($errors->has('completed_message'))
                                                    <span class="text-danger errbk">{{ $errors->first('completed_message') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form_align">
                                            <div class="input-field col s6" style="margin-top: 0;" id="noshow_message_tab">
                                                <textarea id="noshow_message_format" name="noshow_message_format" class="materialize-textarea" data-error=".noshow_message_format">{{$service->noshow_message_format ? $service->noshow_message_format : 'Your token number `$token_number$` is marked as no show'}}</textarea>
                                                <label for="noshow_message_format">No Show Message Format</label>
                                                <div class="noshow_message_format">
                                                    @if ($errors->has('noshow_message_format'))
                                                    <span class="text-danger errbk">{{ $errors->first('noshow_message_format') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="input-field col s6" style="margin-top: 0;float: right;" id="completed_message_tab">
                                                <textarea id="completed_message_format" name="completed_message_format" class="materialize-textarea" data-error=".completed_message_format">{{$service->completed_message_format ? $service->completed_message_format : 'Thank you for using our service, Have a good day'}}</textarea>
                                                <label for="completed_message_format">{{__('messages.service_page.No Show Message Format')}}</label>
                                                <div class="completed_message_format">
                                                    @if ($errors->has('completed_message_format'))
                                                    <span class="text-danger errbk">{{ $errors->first('completed_message_format') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row form_align">
                                                <div class="input-field col s6">
                                                    <select name="status_message" id="status_message" data-error=".status_message" onchange="changeStatusMessage()">
                                                        <option value="0" @if($service->status_message_enabled== 0) selected @endif>No</option>
                                                        <option value="1" @if($service->status_message_enabled== 1) selected @endif>Yes</option>
                                                    </select>
                                                    <label>{{__('messages.service_page.Status Message')}}</label>
                                                    <div class="status_message">
                                                        @if ($errors->has('status_message'))
                                                        <span class="text-danger errbk">{{ $errors->first('status_message') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="status_message_tab">
                                                <div class="row form_align">
                                                    <div class="input-field col s6">
                                                        <select name="status_message_positions[]" id="status_message_positions" data-error=".status_message_positions" multiple>

                                                            <option value="3" @if(($service->status_message_positions && in_array('3',$service->status_message_positions)) || !$service->status_message_positions) selected @endif>3</option>
                                                            <option value="5" @if($service->status_message_positions && in_array('5',$service->status_message_positions)) selected @endif>5</option>
                                                            <option value="10" @if($service->status_message_positions && in_array('10',$service->status_message_positions)) selected @endif>10</option>
                                                            <option value="15" @if($service->status_message_positions && in_array('15',$service->status_message_positions)) selected @endif>15</option>
                                                            <option value="20" @if($service->status_message_positions && in_array('20',$service->status_message_positions)) selected @endif>20</option>
                                                        </select>
                                                        <label>{{__('messages.service_page.Send status message when position is')}}</label>
                                                        <div class="status_message_positions">
                                                            @if ($errors->has('status_message_positions'))
                                                            <span class="text-danger errbk">{{ $errors->first('status_message_positions') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form_align">
                                                    <div class="input-field col s6" style="margin-top: 0;">
                                                        <textarea id="status_message_format" name="status_message_format" class="materialize-textarea" data-error=".status_message_format">{{$service->status_message_format ? $service->status_message_format : ' Your token number `$token_number$` is at #$position$ in the `$service_name$` queue'}}</textarea>
                                                        <label for="status_message_format">{{__('messages.service_page.Status Message Format')}}</label>
                                                        <div class="status_message_format">
                                                            @if ($errors->has('status_message_format'))
                                                            <span class="text-danger errbk">{{ $errors->first('status_message_format') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-left: 10px;">
                                                <div class="col s12">
                                                    <h6 style="font-size: 1.2rem; margin:10px 0">{{__('messages.service_page.Message Keywords')}}</h6>
                                                </div>
                                                <div class="col s4">$token_number$</div>
                                                <div class="col s8">{{__('messages.service_page.Token Number')}}</div>
                                                <div class="col s4">$service_name$</div>
                                                <div class="col s8">{{__('messages.service_page.Name of the service from which user has taken the token')}}</div>
                                                <div class="col s4">$date$</div>
                                                <div class="col s8">{{__('messages.service_page.Token Date')}}</div>
                                                <div class="col s4">$counter_name$</div>
                                                <div class="col s8">{{__('messages.service_page.Name of the counter')}}</div>
                                                <div class="col s4">$position$</div>
                                                <div class="col s8">{{__('messages.service_page.Current position of user in the queue')}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
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
        if ($('#sms').val() == 1) {
            $('#sms_tab').show();
        } else $('#sms_tab').hide();
        changeOptinMessage();
        changeCallMessage();
        changeNoshowMessage();
        changeCompletedMessage();
        enableRequired();
        changeStatusMessage();
    });
    $(function() {
        $('#service_form').validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                },
                letter: {
                    required: true,
                    maxlength: 2
                },
                start_number: {
                    required: true,
                    min: 0
                },
                call_message_format: {
                    required: function(element) {
                        return $("#call_message").val() == "1";
                    },
                },
                optin_message_format: {
                    required: function(element) {
                        return $("#optin_message").val() == "1";
                    },
                },
                noshow_message_format: {
                    required: function(element) {
                        return $("#noshow_message").val() == "1";
                    },
                },
                completed_message_format: {
                    required: function(element) {
                        return $("#completed_message").val() == "1";
                    },
                },
                'status_message_positions[]': {
                    required: true
                },
                status_message_format: {
                    required: function(element) {
                        return $("#status_message").val() == "1";
                    },
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

    function changeSMS() {
        if ($('#sms').val() == 1) {
            $('#sms_tab').show();
        } else {
            $('#sms_tab').hide();
        }
    }

    function changeOptinMessage() {
        if ($('#optin_message').val() == 1) {
            $('#optin_message_tab').show();
        } else {
            $('#optin_message_tab').hide();
        }
    }

    function changeCallMessage() {
        if ($('#call_message').val() == 1) {
            $('#call_message_tab').show();
        } else {
            $('#call_message_tab').hide();
        }
    }

    function changeNoshowMessage() {
        if ($('#noshow_message').val() == 1) {
            $('#noshow_message_tab').show();
        } else {
            $('#noshow_message_tab').hide();
        }
    }

    function changeCompletedMessage() {
        if ($('#completed_message').val() == 1) {
            $('#completed_message_tab').show();
        } else {
            $('#completed_message_tab').hide();
        }
    }

    function changeStatusMessage() {
        if ($('#status_message').val() == 1) {
            $('#status_message_tab').show();
        } else {
            $('#status_message_tab').hide();
        }
    }

    function enableRequired() {
        if ($('#ask_name').val() == 1) {
            $('#name_required').prop('disabled', false);
        } else {
            $('#name_required').val(0);
            $('#name_required').prop('disabled', true);
        }
        if ($('#ask_email').val() == 1) {
            $('#email_required').prop('disabled', false);
        } else {
            $('#email_required').val(0);
            $('#email_required').prop('disabled', true);
        }
        if ($('#ask_phone').val() == 1) {
            $('#phone_required,#sms').prop('disabled', false);
        } else {
            $('#sms,#phone_required').val(0);
            $('#phone_required,#sms').prop('disabled', true);
            $('#sms_tab').hide();
        }
        $('select').formSelect();
    }

    // function enableOrDisableSms() {
    //     if ($('#phone_required').val() == 1) {
    //         $('#sms').prop('disabled', false);
    //     } else {
    //         $('#sms').prop('disabled', true);
    //     }
    //     $('select').formSelect();
    // }
</script>
@endsection