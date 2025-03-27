@extends('vendor.installer.layouts.master')

@section('template_title')
    JL Token | Environment Settings
@endsection

@section('title')
    JL Token Installer
@endsection

@section('environment','active')

@section('container')
    <div class="tabs tabs-full" id="installer-wizard" v-cloak>
        <h6 style="margin: 0; padding: 0; font-size:17px" v-if="finished"> Installation Finished</h6>
    <div v-if="finished">
		<p><strong><small>Migration &amp; Seed Console Output:</small></strong></p>
		<pre><code>@{{dbOutputLog }}</code></pre>

	<p><strong><small>Application Console Output:</small></strong></p>
	<pre><code>@{{ finalMessages }}</code></pre>

	<p><strong><small>Installation Log Entry:</small></strong></p>
	<pre><code>@{{ finalStatusMessage }}</code></pre>

	<p><strong><small>Final .env File:</small></strong></p>
	<pre><code>@{{ finalEnvFile }}</code></pre>

    <div class="buttons">
        <a href="{{ url('/') }}" class="button">Click here to exit</a>
    </div>
    </div>
    <div v-if="!finished">
    <input id="tab1" type="radio" name="tabs" class="tab-input" checked />
        <div class="alert alert-danger" id="error_alert" v-if="error_message">
        <button type="button" class="close" id="close_alert" data-dismiss="alert" aria-hidden="true">
            <i class="fa fa-close" aria-hidden="true"></i>
        </button>
        <h6 style="margin: 0; color: #fff; font-size:15px; line-height:1.6em">
            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
            @{{error_message}}
            </h4>
    </div>
        
        <!-- <input id="tab3" type="radio" name="tabs" class="tab-input" />
        <label for="tab3" class="tab-label">
            <i class="fa fa-cogs fa-2x fa-fw" aria-hidden="true"></i>
            <br />
            {{ trans('installer_messages.environment.wizard.tabs.application') }}
        </label> -->

        <form  class="tabs-wrap">
            <div class="tab" id="tab1content">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group" :class="{'has-error' : errors['app_name']}">
                    <label for="app_name">
                        App Name
                    </label>
                    <input type="text" name="app_name" id="app_name" v-model ="app_name" placeholder="App Name" />
                    <span class="error-block" v-if="errors['app_name']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['app_name']}}
                        </span>
                </div>

                <div class="form-group " :class="{'has-error' : errors['database_connection']}" >
                    <label for="database_connection">
                       Database Connection
                    </label>
                    <select name="database_connection" id="database_connection" v-model="database_connection" :disabled="connection_exists">
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
                    <input type="text" name="database_hostname" id="database_hostname" v-model="database_host" placeholder="Database Host" />
                    <span class="error-block" v-if="errors['database_host']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['database_host']}}
                        </span>
                </div>

                <div class="form-group" :class="{'has-error' : errors['database_port']}">
                    <label for="database_port">
                        Database Port
                    </label>
                    <input type="number" name="database_port" id="database_port" v-model="database_port" placeholder="Database Port" />
                    <span class="error-block" v-if="errors['database_port']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['database_port']}}
                        </span>
                </div>

                <div class="form-group" :class="{'has-error' : errors['database_name']}">
                    <label for="database_name">
                        Database Name
                    </label>
                    <input type="text" name="database_name" id="database_name" v-model="database_name" placeholder="Database Name" />
                    <span class="error-block" v-if="errors['database_name']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['database_name']}}
                        </span>
                </div>

                <div class="form-group " :class="{'has-error' : errors['database_user_name']}">
                    <label for="database_username">
                        Database User Name
                    </label>
                    <input type="text" name="database_username" id="database_username" v-model="database_user_name" placeholder="Database User Name" />
                    <span class="error-block" v-if="errors['database_user_name']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['database_user_name']}}
                        </span>
                </div>

                <div class="form-group" :class="{'has-error' : errors['database_password']}">
                    <label for="database_password">
                        Database Password
                    </label>
                    <input type="password" name="database_password" id="database_password" v-model="database_password" placeholder="Database Password" />
                    <span class="error-block" v-if="errors['database_password']">
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            @{{errors['database_password']}}
                        </span>
                </div>
                <div class="loader" v-if="loader"></div>
                <div class="buttons" v-if="!loader">
                    <button class="button" type="button" style="background-color: #66cd66;" @click="checkForm()" v-if="!connection_exists">
                        Test
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                    <button class="button" type="button" @click="checkForm()" v-if="connection_exists">
                        Install
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                </div>

            </div>
        </form>
    </div>
    </div>
@endsection
@section('b-script')
<script>
    window.JLToken = {
        set_env_url: "{{route('set-env')}}",
        test_connection_url: "{{route('test-connection')}}",
    }
</script>
@endsection
