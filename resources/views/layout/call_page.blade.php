<!DOCTYPE html>
<html class="loading" data-textdirection="{{\App::currentLocale() == 'sa' ? 'rtl' : 'ltr'}}">
<!-- BEGIN: Head-->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  @if(Route::current()->getName() == 'issue_token')
  <title>Kiosk</title>
  @elseif(Route::current()->getName() == 'display')
  <title>Display</title>
  @else
  <title>Kings Token</title>
  @endif
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
  <link rel="stylesheet" type="text/css" href="{{ asset('public/css/app.css') }}">
  <script src="{{asset('app-assets/js/voice.js?key=WfWmvaX0')}}"></script>

  <!-- vue js -->
  @yield('css')

  <!-- END: Custom CSS-->
</head>
<!-- END: Head-->

<body id="page-layout-body" class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns ">

  <!-- BEGIN: Header-->

  <header class="page-topbar noprint" id="header">
    <div class="navbar navbar-fixed">
      <nav class="navbar-main navbar-color nav-collapsible sideNav-lock">
        <div class="nav-wrapper">
          @if(\App::currentLocale() == 'sa')
          <ul class="navbar-list left" style="padding-right: 60px;">
            <li><span style="font-weight: bold; font-size: x-large; ">Kings Token</span></li>
          </ul>
          @else
          <ul class="navbar-list left" style="padding-left: 60px;">
            <li><span style="font-weight: bold; font-size: x-large; ">Kings Token</span></li>
          </ul>
          @endif
          <ul class="navbar-list right">
            @if(isset($settings->logo) && Storage::disk('public')->exists($settings->logo))
            <li style="padding: 5px 0;">
              <img style="max-height:50px" src="{{$settings->logo_url}}" alt="avatar">
            </li>
            @endif
            <!-- <li class="navbar-list left"><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="extra-dropdown"><i class="material-icons">attachment</i></a></li> -->
            <li class="navbar-list left"><a><b>{{$settings->name}},{{$settings->location}}</b></a></li>
            @if(\Request::route()->getName() == 'show_call_page')<li class="dropdown-language"><a class="waves-effect waves-block waves-light translation-button" href="#" data-target="translation-dropdown"><span class="flag-icon flag-icon-{{\App::currentLocale()}}"></span></a></li>@endif
            <li id="side-menu-icon-attachment" class="navbar-list left"><a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="extra-dropdown"><i class="material-icons">attachment</i></a></li>
            <li class="hide-on-med-and-down"><a class="waves-effect waves-block waves-light toggle-fullscreen" href="javascript:void(0);"><i class="material-icons">settings_overscan</i></a></li>
            <li id="side-menu-icon"><a class="waves-effect waves-block waves-light sidenav-trigger" href="#" data-target="slide-out-right"><i class="material-icons">format_indent_increase</i></a></li>
          </ul>



          <ul class="dropdown-content" id="extra-dropdown">
            <li><a href="" style="font-weight: 600; color:black">{{__('messages.common.links')}}</a></li>
            <li class="divider"></li>
            <li><a class="grey-text text-darken-1" ontouchstart="kioskUrl()" href="{{route('issue_token')}}" target="_blank"> {{__('messages.menu.issue token url')}}</a></li>
            <li><a class="grey-text text-darken-1" ontouchstart="displayUrl()" href="{{route('display')}}" target="_blank"> {{__('messages.menu.display url')}}</a></li>
          </ul>

          <ul class="dropdown-content" id="translation-dropdown">
            <li class="dropdown-item" onclick="changeLanguage(1)" ontouchstart="changeLanguage(1)"><a class="grey-text text-darken-1" href="#!" data-language="en"><i class="flag-icon flag-icon-gb"></i> English</a></li>
            <li class="dropdown-item" onclick="changeLanguage(2)" ontouchstart="changeLanguage(2)"><a class="grey-text text-darken-1" href="#!" data-language="fr"><i class="flag-icon flag-icon-fr"></i> French</a></li>
            <li class="dropdown-item" onclick="changeLanguage(3)" ontouchstart="changeLanguage(3)"><a class="grey-text text-darken-1" href="#!" data-language="in"><i class="flag-icon flag-icon-in"></i> Hindi</a></li>
            <li class="dropdown-item" onclick="changeLanguage(4)" ontouchstart="changeLanguage(4)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-sa"></i> Arabic</a></li>
            <li class="dropdown-item" onclick="changeLanguage(5)" ontouchstart="changeLanguage(5)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-es"></i> Spanish</a></li>
            <li class="dropdown-item" onclick="changeLanguage(6)" ontouchstart="changeLanguage(6)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-pt"></i> Portuguese</a></li>
            <li class="dropdown-item" onclick="changeLanguage(7)" ontouchstart="changeLanguage(7)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-it"></i> Italian</a></li>
            <li class="dropdown-item" onclick="changeLanguage(8)" ontouchstart="changeLanguage(8)"><a class="grey-text text-darken-1" href="#!" data-language="sa"><i class="flag-icon flag-icon-id"></i> Indonesian</a></li>
          </ul>

        </div>

      </nav>
    </div>
  </header>
  @if(isset($show_menu) && $show_menu)
  @include('layout.menu')
  @endif
  <!-- END: Header-->
  <!-- BEGIN: SideNav-->
  <!-- END: SideNav-->

  <!-- BEGIN: Page Main-->
  @yield('content')
  <!-- END: Page Main-->
  <div id="mount">
    <aside id="right-sidebar-nav">
      <div id="slide-out-right" class="slide-out-right-sidenav sidenav 
       side ">
        <div class="row" id="call-page-sidebar">
          <div class="slide-out-right-title">
            <div class="col s12 border-bottom-1 pb-0 pt-1 pr-0" style="border-bottom: 2px solid #000">
              <div class="row">
                <div class="col s2 pr-0 pl-0 center" style="width: 11.66667%;">
                  <i class="material-icons vertical-text-middle"><a href="#" @click="closeRightMenu()" style="color: #009688;">clear</a></i>
                </div>
                <div class="col s10 pl-0">
                  <ul class="tabs">
                    <li class="tab col s12 p-0">
                      <a href="#messages" style="color: #000000;">
                        <span>{{__('messages.call_page.walk in')}}</span>
                      </a>
                    </li>

                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="slide-out-right-body row">
            <div id="messages" class="col s12 pb-0">
              <div class="collection border-none mt-0 mb-0">
                <div class="progress" v-if="loader">
                  <div class="indeterminate"></div>
                </div>
                <ul class="collection right-sidebar-chat p-0 m-0" v-if="!loader">
                  <li class="right-sidebar-chat-item pb-0 waves-effect waves-light next-to-call" :class="{'selected-token': show_next_to_call }" style="padding:0px; align-items:center; min-height:40px" @click="showNextToCall()">
                    <div class="user-content" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                      <h6 style="margin: 0px; font-size:14px; font-weight:600;">{{__('messages.call_page.next to call')}}</h6>
                      <i v-if="show_next_to_call" class="material-icons" style="font-size: 25px; color:#b3b3b3">expand_more</i>
                      <i v-if="!show_next_to_call" class="material-icons" style="font-size: 25px; color:#b3b3b3">chevron_right</i>
                    </div>
                  </li>
                  <li class="right-sidebar-chat-item  pb-0 " style="padding:0px; align-items:center; min-height:40px; cursor:auto" data-target="slide-out-chat" v-for=" token in tokens_for_next_to_call" v-if="show_next_to_call">
                    <div class="user-content" style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: .1rem; margin-top: .7em;">
                      <div @click="!isCalled ? callNext(token.id): ''" style="border-radius:50%; display:flex; align-items:center; padding:5px;" :class="{'recall-active':  !isCalled, 'disabled-color': isCalled }"><i class="material-icons" style="font-size: 22px; color:#fff">call</i></div>
                      <h6 style="margin: 0px; font-size:20px; font-weight:600;">@{{token.letter}}-@{{token.number}}</h6>
                      <!-- <i class="material-icons" style="font-size: 25px; color:#b3b3b3">expand_more</i> -->
                      <div></div>
                    </div>
                  </li>
                  <li class="right-sidebar-chat-item  pb-0 waves-effect waves-light next-to-call" :class="{'selected-token': show_called }" style="padding:0px; align-items:center; min-height:40px" @click="showCalled()">
                    <div class="user-content" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                      <h6 style="margin: 0px; font-size:14px; font-weight:600;">{{__('messages.call_page.called')}}</h6>
                      <i v-if="show_called" class="material-icons" style="font-size: 25px; color:#b3b3b3;">expand_more</i>
                      <i v-if="!show_called" class="material-icons" style="font-size: 25px; color:#b3b3b3;">chevron_right</i>
                    </div>
                  </li>
                  <li class="right-sidebar-chat-item  pb-0" style="padding:0px; align-items:center; min-height:40px; cursor:auto" data-target="slide-out-chat" v-for=" token in called_tokens" v-if="show_called">
                    <div class="user-content" style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: .1rem; margin-top: .7em;">
                      <div @click="(token.call_status_id == {{CallStatuses::NOSHOW}} && !isCalled) ? recallToken(token.id) : ''" style="border-radius:50%; display:flex; align-items:center; padding:5px;" :class="{'recall-active': token.call_status_id == {{CallStatuses::NOSHOW}} && !isCalled, 'disabled-color': token.call_status_id != {{CallStatuses::NOSHOW}} || isCalled }"><i class="material-icons" style="font-size: 22px; color:#fff">replay</i></div>
                      <h6 style="margin: 0px; font-size:20px; font-weight:600;">@{{token.token_letter}}-@{{token.token_number}}</h6>
                      <span style="background-color: #009688; padding: 0 5px; color:#fff; font-weight:600; min-width:22px" v-if="token.call_status_id == {{CallStatuses::SERVED}}">S</span>
                      <span style="background-color: #ff0000; padding: 0 5px; color:#fff; font-weight:600; min-width:22px" v-if="token.call_status_id != {{CallStatuses::SERVED}}">N</span>
                      <!-- <i class="material-icons" style="font-size: 25px; color:#b3b3b3">expand_more</i> -->
                    </div>
                  </li>
              </div>
            </div>
    </aside>
  </div>

  <!-- </div> -->
  <!-- BEGIN: Footer-->
  @if((Route::current()->getName() == 'issue_token' || Route::current()->getName() == 'display') && \App::currentLocale() == 'sa')
  <footer class="page-footer footer footer-static navbar-border navbar-shadow noprint" style="padding-right: 10px">
    <div class="footer-copyright">
    <div class="container">
            <span>Powered by&nbsp;<a href="https://www.kingslee.net" target="_blank" style="color:#ffffff;font-weight: bolder;">Kings Technologies</a>&nbsp;All rights reserved</span>
            <span style="text-align: left; float:left; font-size:13px">Version {{AppVersion::VERSION}}</span>
    </div>
    </div>
  </footer>
  @elseif((Route::current()->getName() == 'issue_token' || Route::current()->getName() == 'display') && \App::currentLocale() != 'sa')
  <footer class="page-footer footer footer-static navbar-border navbar-shadow noprint" style="padding-left: 10px">
    <div class="footer-copyright">
    <div class="container">
            <span>Powered by&nbsp;<a href="https://www.kingslee.net" target="_blank" style="color:#ffffff;font-weight: bolder;">Kings Technologies</a>&nbsp;All rights reserved</span>
            <span style="text-align: right; float:right; font-size:13px">Version {{AppVersion::VERSION}}</span>
    </div>
    </div>
  </footer>
  @else
  <footer class="page-footer footer footer-static navbar-border navbar-shadow noprint">
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
  @endif

  @if(Route::current()->getName() == 'issue_token')
  <div id="printarea" class="printarea" style="text-align:center;margin-top: 20px; display:none"></div>
  @endif

  @yield('b-js')
  <!-- BEGIN VENDOR JS-->
  <script src="{{asset('app-assets/js/loader/modernizr-2.6.2.min.js')}}"></script>
  <script src="{{asset('app-assets/js/vendors.min.js')}}"></script>
  <script src="{{ asset('public/js/app.js') }}"></script>
  <script src="{{asset('app-assets/js/plugins.js')}}"></script>
  <script src="{{asset('app-assets/js/voice.js?key=WfWmvaX0')}}"></script>
  <script src="{{asset('app-assets/vendors/jquery-validation/jquery.validate.min.js')}}"></script>

  <!-- BEGIN VENDOR JS-->
  @yield('js')
  <script>
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
  </script>
  <script>
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
  </script>
  @include('common.message')
</body>

</html>