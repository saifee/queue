@extends('vendor.installer.layouts.master')
@section('template_title')
JL Token Installer
@endsection

@section('title')
JL Token Installer
@endsection
@section('style')
<style>
    .box {
        width: 525px !important;
    }

    .left-form {
        width: 50%;
        margin-right: 10px;
    }

    .right-form {
        width: 50%;
    }

    .row {
        display: flex;
    }
</style>
@endsection
@section('container')
<div class="alert alert-danger" id="error_alert" v-if="error_message">
    <h6 style="margin: 0; color: #fff; font-size:15px; line-height:1.6em">
        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
        @{{error_message}}
        </h4>
</div>
<div id="verify_license" v-if="verify_page">
    <form class="tabs-wrap">
        <div class="tab" id="tab1content">
            <div class="form-group col-sm-8" :class="{'has-error' : errors['purchase_code']}">
                <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank" style="float: right;">How to get codecanyon purchase code</a>
                <label for="puchase_code">
                    Codecanyon Purchase Code
                </label>
                <input type="text" name="puchase_code" id="puchase_code" placeholder="Purchase Code" :readonly="show_details" v-model="purchase_code" />
                <span class="error-block" v-if="errors['purchase_code']">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    @{{errors['purchase_code']}}
                </span>
            </div>
            <div class="form-group col-sm-8" :class="{'has-error' : errors['codecanyon_username']}" style="margin-top:10px">
                <label for="name">
                    Codecanyon Username
                </label>
                <input type="text" name="codecanyon_username" id="codecanyon_username" :readonly="show_details" placeholder="Codecanyon Username" v-model="codecanyon_username" />
                <span class="error-block" v-if="errors['codecanyon_username']">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    @{{errors['codecanyon_username']}}
                </span>
            </div>
            <div v-if="show_details">
                <div class="row">
                    <div class="form-group left-form" :class="{'has-error' : errors['name']}">
                        <label for="name">
                            Name
                        </label>
                        <input type="text" name="name" id="name" placeholder="Name" v-model="name" />
                        <span class="error-block" v-if="errors['name']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['name']}}
                        </span>
                    </div>


                    <div class="form-group right-form" :class="{'has-error' : errors['company_name']}">
                        <label for="company_name">
                            Company Name
                        </label>
                        <input type="text" name="company_name" id="company_name" placeholder="Company Name" v-model="company_name" />
                        <span class="error-block" v-if="errors['company_name']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['company_name']}}
                        </span>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group left-form" :class="{'has-error' : errors['email']}">
                        <label for="Email">
                            Email
                        </label>
                        <input type="text" name="Email" id="Email" placeholder="Email" v-model="email" />
                        <span class="error-block" v-if="errors['email']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['email']}}
                        </span>
                    </div>
                    <div class="form-group right-form" :class="{'has-error' : errors['phone']}">
                        <label for="phone">
                            Phone
                        </label>
                        <input type="text" name="phone" id="phone" placeholder="Phone" v-model="phone" />
                        <span class="error-block" v-if="errors['phone']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['phone']}}
                        </span>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group left-form" :class="{'has-error' : errors['city']}">
                        <label for="city">
                            City
                        </label>
                        <input type="text" name="city" id="city" placeholder="City" v-model="city" />

                        <span class="error-block" v-if="errors['city']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['city']}}
                        </span>

                    </div>
                    <div class="form-group right-form" :class="{'has-error' : errors['country']}">
                        <label for="country">
                            Country
                        </label>
                        <input type="text" name="country" id="country" placeholder="Country" v-model="country" />

                        <span class="error-block" v-if="errors['country']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['country']}}
                        </span>

                    </div>
                </div>
            </div>
            <div class="loader" v-if="loader"></div>
            <p class="text-center" style="margin-top: 20px; margin-bottom:0">
                <button class="button" type="button" style=" margin-bottom:0px; font-size:20px" @click="verifyLicense()" v-if="!loader &&!show_details ">
                    Verify
                    <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                </button>
                <button class="button" type="button" style=" margin-bottom:0px; font-size:14px" @click="setLicenseDetails()" v-if="!loader && show_details">
                    Check Requirements
                    <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                </button>
            </p>
        </div>
    </form>
    <div style="padding: 5px; margin-top:15px; background-color:rgb(198 227 251);border-radius:4px;" v-if="!show_details">
        <h6 style="margin: 0; font-size:13px; line-height:1.6em;">Important:-</h6>
        <p style="margin-left:10px; margin-bottom:0;">Proper internet connection required</p>
        <p style="margin-left:10px; margin-bottom:0;" v-if="is_sub_directory=='true'">NOT Recommended to install in sub directory</p>
    </div>
</div>
<span v-html="page" v-if="requirements_page || permission_page"></span>
<div class="loader" v-if="loader && (requirements_page || permission_page)"></div>
<div class="buttons" v-if="requirements_page || permission_page">
    <a class="button" @click="permissions()" v-if="requirements_page && permissions_button && !loader">
        Check Permissions
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
    </a>
    <a class="button" @click="requirements()" v-if="requirements_page && !permissions_button && !loader" style="background-color: #fd0909;">
        Retry
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
    </a>
    <a @click="setEnvPage()" class="button" v-if="permission_page && set_env_button && !loader">
        Configure Environment
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
    </a>
    <a @click="permissions()" class="button" v-if="permission_page && !set_env_button && !loader" style="background-color: #fd0909;">
        Retry
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
    </a>
</div>
<div class="tabs tabs-full" v-if="set_env_page">
    <div v-if="finished">
        <h6 style="margin: 0; padding: 0; font-size:17px" v-if="finished"> Installation Finished</h6>
        <p><strong><small>Migration &amp; Seed Console Output:</small></strong></p>
        <pre><code>@{{dbOutputLog }}</code></pre>

        <p><strong><small>Application Console Output:</small></strong></p>
        <pre><code>@{{ finalMessages }}</code></pre>

        <p><strong><small>Installation Log Entry:</small></strong></p>
        <pre><code>@{{ finalStatusMessage }}</code></pre>

        <p><strong><small>Final .env File:</small></strong></p>
        <pre><code>@{{ finalEnvFile }}</code></pre>

        <div class="buttons">
            <a href="{{ route('login') }}" class="button">Click here to exit</a>
        </div>
    </div>
    <div v-if="!finished">
        <input id="tab1" type="radio" name="tabs" class="tab-input" checked />
        <div class="alert alert-danger" id="error_alert" style="background:#6fe373" v-if="connection_exists">
            </button>
            <h6 style="margin: 0; color: #fff; font-size:15px; line-height:1.6em">
                Database connection tested successfully
                </h4>
        </div>

        <form class="tabs-wrap">
            <div class="tab" id="tab1content">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group" :class="{'has-error' : errors['app_name']}">
                    <label for="app_name">
                        App Name
                    </label>
                    <input type="text" name="app_name" id="app_name" v-model="app_name" placeholder="App Name" />
                    <span class="error-block" v-if="errors['app_name']">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        @{{errors['app_name']}}
                    </span>
                </div>

                <div class="form-group" :class="{'has-error' : errors['app_timezone']}">
                    <label for="app_timezone">
                        App Timezone
                    </label>
                    <select name="app_timezone" id="app_timezone" v-model="app_timezone">
                        <option :value="timezone" v-for="timezone in timezones">@{{timezone}}</option>
                    </select>
                    <span class="error-block" v-if="errors['app_timezone']">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        @{{errors['app_timezone']}}
                    </span>
                </div>

                <div class="form-group " :class="{'has-error' : errors['database_connection']}">
                    <label for="database_connection">
                        Database Connection
                    </label>
                    <select name="database_connection" id="database_connection" v-model="database_connection" @change="changeDBConnection()">
                        <option value="1">mysql</option>
                        <option value="2">pgsql</option>
                    </select>
                    <span class="error-block" v-if="errors['database_connection']">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        @{{errors['database_connection']}}
                    </span>

                </div>

                <div class="form-group " :class="{'has-error' : errors['database_host']}">
                    <label for="database_hostname">
                        Database Host
                    </label>
                    <input type="text" name="database_hostname" id="database_hostname" v-model="database_host" placeholder="Database Host" @input="valuesChangedAfterTest()" />
                    <span class="error-block" v-if="errors['database_host']">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        @{{errors['database_host']}}
                    </span>
                </div>

                <div class="form-group" :class="{'has-error' : errors['database_port']}">
                    <label for="database_port">
                        Database Port
                    </label>
                    <input type="number" name="database_port" id="database_port" v-model="database_port" placeholder="Database Port" @input="valuesChangedAfterTest()" />
                    <span class="error-block" v-if="errors['database_port']">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        @{{errors['database_port']}}
                    </span>
                </div>

                <div class="form-group" :class="{'has-error' : errors['database_name']}">
                    <label for="database_name">
                        Database Name
                    </label>
                    <input type="text" name="database_name" id="database_name" v-model="database_name" placeholder="Database Name" @input="valuesChangedAfterTest()" />
                    <span class="error-block" v-if="errors['database_name']">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        @{{errors['database_name']}}
                    </span>
                </div>

                <div class="form-group " :class="{'has-error' : errors['database_user_name']}">
                    <label for="database_username">
                        Database User Name
                    </label>
                    <input type="text" name="database_username" id="database_username" v-model="database_user_name" placeholder="Database User Name" @input="valuesChangedAfterTest()" />
                    <span class="error-block" v-if="errors['database_user_name']">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        @{{errors['database_user_name']}}
                    </span>
                </div>

                <div class="form-group" :class="{'has-error' : errors['database_password']}">
                    <label for="database_password">
                        Database Password
                    </label>
                    <input type="password" name="database_password" id="database_password" v-model="database_password" placeholder="Database Password" @input="valuesChangedAfterTest()" />
                    <span class="error-block" v-if="errors['database_password']">
                        <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                        @{{errors['database_password']}}
                    </span>
                </div>
                <div class="loader" v-if="loader"></div>
                <div class="buttons" v-if="!loader">
                    <button class="button" type="button" style="background-color: #66cd66; font-size:14px" @click="testConnection()" v-if="!connection_exists">
                        Test DB Connection
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                    <button class="button" type="button" @click="setEnv()" v-if="connection_exists">
                        Install
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>


@endsection
@section('app-version',$app_version)
@section('b-script')
<script>
    window.JLToken = {
        verify_license_url: "{{config('services.key_verification.verify_purchase_code')}}",
        set_license_details_url: "{{config('services.key_verification.set_customer_details')}}",
        requirements_url: "{{route('requirements')}}",
        permissions_url: "{{route('permissions')}}",
        set_env_url: "{{route('set-env')}}",
        test_connection_url: "{{route('test-connection')}}",
        is_sub_directory: "{{$sub_directory}}",
        timezones: JSON.parse('{!!$timezones!!}'),
        host: "{{$host}}",
    }
</script>
@endsection