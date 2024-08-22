<!DOCTYPE html>
<html class="loading" data-textdirection="{{\App::currentLocale() == 'sa' ? 'rtl' : 'ltr'}}">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Kings Token | @yield('title')</title>
    <link rel="apple-touch-icon" href="{{asset('app-assets/images/icon/favicon.ico')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('app-assets/images/icon/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- BEGIN: VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/vendors.min.css')}}">
    <!-- END: VENDOR CSS-->
    <!-- BEGIN: Page Level CSS-->
    @if(\App::currentLocale() == 'sa')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/style-rtl.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/themes/vertical-dark-menu-template/materialize.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/themes/vertical-dark-menu-template/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/loader/main.css')}}">
    @else
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/vertical-dark-menu-template/materialize.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/vertical-dark-menu-template/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/loader/main.css')}}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/loader/normalize.css')}}">



    <!-- vue js -->
    @yield('css')

    <!-- END: Custom CSS-->
</head>
<!-- END: Head-->

<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns noprint ">

    <!-- BEGIN: Header-->
    <header class="page-topbar" id="header">
        <div class="navbar navbar-fixed">
            <nav class="navbar-main navbar-color nav-collapsible sideNav-lock">
                <div class="nav-wrapper">
                    <ul class="navbar-list left" style="padding-left: 60px;">
                        <li><span style="font-weight: bold; font-size: x-large; ">King Token</span></li>
                    </ul>
                    <ul class="navbar-list right">
                        @if(isset(session()->get("settings")->logo) && Storage::disk('public')->exists(session()->get("settings")->logo))
                        <li style="padding: 5px 0;">
                            <img style="max-height:50px" src="{{session()->get('settings')->logo_url}}" alt="avatar">
                        </li>
                        @endif
                        <li class="dropdown-language"><a class="waves-effect waves-block waves-light translation-button" href="#" data-target="translation-dropdown"><span class="flag-icon flag-icon-{{\App::currentLocale()}}"></span></a></li>
                        <li class="hide-on-med-and-down"><a class="waves-effect waves-block waves-light toggle-fullscreen" href="javascript:void(0);"><i class="material-icons">settings_overscan</i></a></li>
                        @if( Auth::user()->can('view issue token') || Auth::user()->can('view display'))
                        <li class="navbar-list left"><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="extra-dropdown"><i class="material-icons">attachment</i></a></li>
                        @endif
                        <li class="navbar-list left"><a href="{{route('profile')}}"><b>{{session()->get("settings")->name}},{{session()->get("settings")->location}}</b></a></li>

                        <!-- <li><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="profile-dropdown"><span class="avatar-status "><img src="{{Auth::user()->image_url}}" alt="avatar"></span></a></li> -->

                        <li><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="profile-dropdown"><span class="avatar-status ">
                                    @if(isset(Auth::user()->image) && Storage::disk('public')->exists(Auth::user()->image))
                                    <img style="width:28px;height:28px" src="{{Auth::user()->image_url}}" alt="avatar">
                                    @else
                                    <img src="{{asset('app-assets/images/avatar/avatar.png')}}" alt="avatar">
                                    @endif
                                </span></a>
                        </li>

                        <!-- <li><a class="waves-effect waves-block waves-light sidenav-trigger" href="#" data-target="slide-out-right"><i class="material-icons">format_indent_increase</i></a></li> -->
                    </ul>

                    <!-- <ul class="dropdown-content" id="translation-dropdown">
                        <li class="dropdown-item" onclick="changeLanguage(1)" ontouchstart="changeLanguage(1)"><a class="grey-text text-darken-1" href="#!" data-language="en"><i class="flag-icon flag-icon-gb"></i> English</a></li>
                        <li class="dropdown-item" onclick="changeLanguage(2)" ontouchstart="changeLanguage(2)"><a class="grey-text text-darken-1" href="#!" data-language="fr"><i class="flag-icon flag-icon-fr"></i> French</a></li>
                        <li class="dropdown-item" onclick="changeLanguage(3)" ontouchstart="changeLanguage(3)"><a class="grey-text text-darken-1" href="#!" data-language="in"><i class="flag-icon flag-icon-in"></i> Hindi</a></li>
                        <li class="dropdown-item" onclick="changeLanguage(4)" ontouchstart="changeLanguage(4)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-sa"></i> Arabic</a></li>
                        <li class="dropdown-item" onclick="changeLanguage(5)" ontouchstart="changeLanguage(5)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-es"></i> Spanish</a></li>
                        <li class="dropdown-item" onclick="changeLanguage(6)" ontouchstart="changeLanguage(6)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-pt"></i> Portuguese</a></li>
                        <li class="dropdown-item" onclick="changeLanguage(7)" ontouchstart="changeLanguage(7)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-it"></i> Italian</a></li>
                        <li class="dropdown-item" onclick="changeLanguage(8)" ontouchstart="changeLanguage(8)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-id"></i> Indonesian</a></li>
                        <li class="dropdown-item" onclick="changeLanguage(9)" ontouchstart="changeLanguage(9)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-de"></i> German</a></li> 
                    </ul> -->

                    <ul class="dropdown-content" id="profile-dropdown">
                        @can('view profile')
                        <li><a class="grey-text text-darken-1" href="{{route('profile')}}" ontouchstart="viewProfile()"><i class="material-icons">person_outline</i> {{__('messages.common.profile')}}</a></li>
                        <li class="divider"></li>
                        @endcan
                        <li><a class="grey-text text-darken-1" href="{{route('logout')}}" ontouchstart="logout()"><i class="material-icons">keyboard_tab</i> {{__('messages.common.logout')}}</a></li>
                    </ul>

                    <!-- <ul class="dropdown-content" id="extra-dropdown">
                        <li><a href="" style="font-weight: 600; color:black">{{__('messages.common.links')}}</a></li>
                        <li class="divider"></li>
                        @can('issue token')
                        <li><a class="grey-text text-darken-1" ontouchstart="kioskUrl()" href="{{route('issue_token')}}" target="_blank"> {{__('messages.menu.issue token url')}}</a></li>
                        @endcan
                        @can('view display')
                        <li><a class="grey-text text-darken-1" ontouchstart="displayUrl()" href="{{route('display')}}" target="_blank"> {{__('messages.menu.display url')}}</a></li>
                        @endcan
                        <li class="divider"></li>
                        <li><a class="grey-text text-darken-1" href="{{route('logout')}}"><i class="material-icons">keyboard_tab</i> Logout</a></li>
                    </ul> -->

                </div>

            </nav>
        </div>
    </header>
    <!-- END: Header-->
    <!-- BEGIN: SideNav-->
    @include('layout.menu')
    <!-- END: SideNav-->

    <!-- BEGIN: Page Main-->
    <div id="loader-wrapper">
        <div id="loader"></div>

        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>

    </div>
    @yield('content')
    <!-- END: Page Main-->

    <!-- BEGIN: Footer-->
    <footer class="page-footer footer footer-static navbar-border navbar-shadow">
        <div class="footer-copyright">
        <div class="container">
            <span>Powered by&nbsp;<a href="https://www.kingslee.net" target="_blank" style="color:#ffffff;font-weight: bolder;">Kings Technologies</a>&nbsp;All rights reserved</span>
            @if(\App::currentLocale() == 'sa')
            <span style="text-align: left; float:left; font-size:13px">Version {{AppVersion::VERSION}}</span>
            @else
            <span style="text-align: right; float:right; font-size:13px">Version {{AppVersion::VERSION}}</span>
            @endif
    </div>
        </div>
    </footer>

    <!-- BEGIN VENDOR JS-->
    <script src="{{asset('app-assets/js/loader/modernizr-2.6.2.min.js')}}"></script>
    <script src="{{asset('app-assets/js/vendors.min.js')}}"></script>
    <script src="{{asset('app-assets/js/plugins.js')}}"></script>

    <script>
        $(document).ready(function() {
            $('body').addClass('loaded');
        });
        $(document).on("click", 'a.frmsubmit', function(e) {
            var message = '';
            if (e.currentTarget.attributes.message != undefined) {
                message = e.currentTarget.attributes.message.value;
            } else {
                message = 'Are you sure you want delete ?';
            }
            if (message != 'false') {
                if (confirm(message)) {
                    e.preventDefault();
                    var myForm = '<form id="hidfrm" action="' + e.currentTarget.attributes.href.value + '" method="post">{{@csrf_field()}}<input type="hidden" name="_method" value="' + e.currentTarget.attributes.method.value + '"></form>';
                    $('body').append(myForm);
                    myForm = $('#hidfrm');
                    myForm.submit();
                }
            } else {
                e.preventDefault();
                var myForm = '<form id="hidfrm" action="' + e.currentTarget.attributes.href.value + '" method="post">{{@csrf_field()}}<input type="hidden" name="_method" value="' + e.currentTarget.attributes.method.value + '"></form>';
                $('body').append(myForm);
                myForm = $('#hidfrm');
                myForm.submit();
            }
            return false;
        });

        function changeLanguage(id) {
            $('body').removeClass('loaded');
            var data = "language=" + id + '&_token={{csrf_token()}}';
            $.ajax({
                type: "POST",
                url: "{{Route('change_session_language')}}",
                data: data,
                cache: false,
                success: function(response) {
                    location.reload(true);
                },
                error: function() {
                    $('body').addClass('loaded');
                    M.toast({
                        html: 'something went wrong',
                        classes: "toast-error"
                    });
                }
            });
        }

        function kioskUrl(){
            window.open("{{route('issue_token')}}", '_blank');
        }

        function displayUrl(){
            window.open("{{route('display')}}", '_blank');
        }

        function viewProfile(){
            window.location.href = "{{route('profile')}}"
        }

        function logout(){
            window.location.href = "{{route('logout')}}"
        }
    </script>
    @yield('js')
    @include('common.message')
</body>

</html>