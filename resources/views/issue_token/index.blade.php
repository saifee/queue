@extends('layout.call_page')

@section('content')
<div class="container mt-5 noprint">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <h3 class="card-title mb-4">{{__('messages.issue_token.click one service to issue token')}}</h3>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center flex-wrap">
                        @foreach($services as $service)
                        <button class="btn btn-success btn-lg px-5 py-3" 
                                onclick="queueDept({{$service}})">
                            {{$service->name}}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="details_form">
                <div class="modal-body">
                    <div class="mb-3" id="name_tab">
                        <label class="form-label">{{__('messages.settings.name')}}</label>
                        <input id="name" name="name" type="text" class="form-control">
                        <div class="name-error text-danger small"></div>
                    </div>
                    <div class="mb-3" id="phone_tab">
                        <label class="form-label">{{__('messages.settings.phone')}}</label>
                        <input id="phone" name="phone" type="text" class="form-control">
                        <div class="phone-error text-danger small"></div>
                    </div>
                    <div class="mb-3" id="email_tab">
                        <label class="form-label">{{__('messages.settings.email')}}</label>
                        <input id="email" name="email" type="email" class="form-control">
                        <div class="email-error text-danger small"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="modal_button">{{__('messages.common.submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="printarea" style="display:none"></div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    var service;
    var tokenModal = new bootstrap.Modal(document.getElementById('tokenModal'));

    function queueDept(value) {
        if (value.ask_email == 1 || value.ask_name == 1 || value.ask_phone == 1) {
            $('#email_tab').toggle(value.ask_email == 1);
            $('#name_tab').toggle(value.ask_name == 1);
            $('#phone_tab').toggle(value.ask_phone == 1);
            
            service = value;
            $('#modal_button').removeAttr('disabled');
            tokenModal.show();
        } else {
            createToken({ service_id: value.id, with_details: false });
        }
    }

    // ... Update your issueToken and createToken functions to use Bootstrap classes ...
    // E.g. tokenModal.hide() instead of $('#modal1').modal('close')
</script>
@endsection
