<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="keywords" content="ASTIN Admin template">
    <meta name="author" content="ThemeSelect">
    <!-- <meta name="_token" content="{!! csrf_token() !!}" /> -->
    <title>ADMIN LOGIN | Kings Token</title>
    <link rel="apple-touch-icon" href="{{asset('app-assets/images/icon/favicon.ico')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('app-assets/images/icon/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- BEGIN: VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/vendors.min.css')}}">
    <!-- END: VENDOR CSS-->
    <!-- BEGIN: Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/vertical-dark-menu-template/materialize.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/vertical-dark-menu-template/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/pages/login.css')}}">
    <!-- END: Page Level CSS-->
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/custom/custom.css')}}">
    <!-- END: Custom CSS-->
</head>
<!-- END: Head-->

<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 1-column    blank-page blank-page" data-open="click" data-menu="vertical-dark-menu" data-col="1-column" style="background-color: #73BCFF;">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div id="login-page" class="row">
                    <div 
                        class="
                            col 
                            s12 
                            l7
                            z-depth-4 
                            card 
                            horizontal
                            medium
                            border-radius-6 
                            login-card 
                            bg-opacity-8
                        " 
                        style="
                            background-color: #fff;
                            padding: 32px;
                        "
                    >
                        @if (isset($errors) && $errors->has('error'))
                        <div class="card-alert card red lighten-5">
                            <div class="card-content red-text">
                                <p>{{ $errors->first('error') }}</p>
                            </div>
                            <button type="button" class="close red-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        @endif
                        <form 
                            id="login_form" 
                            class="login-form card-stacked" 
                            method="post" 
                            action="{{route('post_login')}}"
                            style="background-color: #fff; width: 100%;"
                        >
                            @csrf

                            <div class="row">
                                <div class="input-field col s12">
                                    <h5 class="ml-4" style="font-weight: bold; color: #73BCFF;">Kings Token</h5>
                                </div>
                            </div>
                            <div class="row margin">
                                <i 
                                    class="col material-icons prefix pt-3"
                                    "
                                >person_outline</i>
                                <div
                                    class="col "
                                    style="width:75%;"
                                >  
                                    <input 
                                        id="email" 
                                        type="text" 
                                        name="email" 
                                        class="input-emailplaceholder" 
                                        value="Email" 
                                        onfocus="clearEmailPlaceholder(this)" 
                                        onblur="setEmailPlaceholder(this)"
                                        style="
                                            border: 1px solid #ccc; 
                                            border-radius: 100px;
                                            padding: 0 16px;
                                            box-sizing: border-box;
                                            
                                            color: #ccc;
                                        "
                                    >
                                </div>
                            </div>

                            <div 
                                class="row margin"
                            >
                                <i 
                                    class="col material-icons prefix pt-3"
                                    style=""
                                    
                                >lock_outline</i>
                                <div
                                    class="col "
                                    style="width:75%;"
                                > 
                                    <input 
                                        id="password" 
                                        type="password" 
                                        name="password" 
                                        class="input-passwordplaceholder" 
                                        value="Password" 
                                        onfocus="clearPasswordPlaceholder(this)" 
                                        onblur="setPasswordPlaceholder(this)"
                                        style="
                                            border: 1px solid #ccc; 
                                            border-radius: 100px;
                                            padding: 0 16px;
                                            box-sizing: border-box;
                                            color: #ccc;

                                        "
                                    >
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col s12 m12 l12 ml-2 mt-1">
                                    <p>
                                        <label>
                                            <input type="checkbox" />
                                            <span>Remember Me</span>
                                        </label>
                                    </p>
                                </div>
                            </div> -->
                            <div 
                                class="row"
                                style="
                                    margin: auto 0;  
                                "
                            >
                                <div 
                                    class="input-field col s12 "
                                    style="
                                        margin-top: 25%;
                                        width: 90%;
                                    "
                                >
                                    <button 
                                        type="submit" 
                                        class="btn waves-effect waves-light border-round  col s12" 
                                        style="background-color: #73BCFF;"
                                    >Login</button>
                                </div>
                            </div>
                            <div class="row"  style="text-align: center; padding-bottom: 7px; font-size: 13px">Version {{AppVersion::VERSION}}</div>
                        </form>
                        <div
                            class="card-image "
                            style="max-width: 40%; "

                        >
                            <img 
                                src="{{asset('app-assets/images/custom/loginAsset.png')}}" 
                                alt="Login Asset"
                                style="margin-top: 15%; width: auto; height: 75%; "
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-overlay"></div>
        </div>
    </div>

    <!-- BEGIN VENDOR JS-->
    <script src="{{asset('app-assets/js/vendors.min.js')}}"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN THEME  JS-->
    <script src="{{asset('app-assets/js/plugins.js')}}"></script>
    <script src="{{asset('app-assets/js/search.js')}}"></script>
    <script src="{{asset('app-assets/js/custom/custom-script.js')}}"></script>
    <!-- END THEME  JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <!-- END PAGE LEVEL JS-->
    <!-- validation -->
    <script src="{{asset('app-assets/js/vendors.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/jquery-validation/jquery.validate.min.js')}}"></script>
    <script>
        $(function() {
            $('#login_form').validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    email: {
                        required: "Please enter the email",
                        email: "enter your correct email address"
                    },
                    password: {
                        required: "Please enter the password",
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
        $(".card-alert .close").click(function() {
            $(this)
                .closest(".card-alert")
                .fadeOut("slow");
        });
    </script>
    <script>
        function clearEmailPlaceholder(input) {
            if (input.value === "Email") {
                input.value = "";
                input.classList.remove("input-emailplaceholder");
            }
        }

        function setEmailPlaceholder(input) {
            if (input.value === "") {
                input.value = "Email";
                input.classList.add("input-emailplaceholder");
            }
        }
    </script>
    <script>
        function clearPasswordPlaceholder(input) {
            if (input.value === "Password") {
                input.value = "";
                input.classList.remove("input-passwordplaceholder");
            }
        }

        function setPasswordPlaceholder(input) {
            if (input.value === "") {
                input.value = "Password";
                input.classList.add("input-passwordplaceholder");
            }
        }
    </script>
</body>

</html>