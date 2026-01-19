<!DOCTYPE html>
<html lang="{{\App::currentLocale()}}" dir="{{\App::currentLocale() == 'sa' ? 'rtl' : 'ltr'}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>JL Token | @yield('title')</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('app-assets/images/icon/favicon.ico')}}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="{{asset('resources/css/vue_style/style.css')}}">

    <style>
        /* GUI Improvement: Modern Sidebar Layout */
        body { min-height: 100vh; display: flex; flex-direction: column; background-color: #f4f6f9; }
        #wrapper { display: flex; flex: 1; overflow: hidden; }
        #sidebar-wrapper { width: 250px; background: #343a40; color: #fff; transition: all 0.3s; flex-shrink: 0; }
        #page-content-wrapper { flex: 1; overflow-y: auto; padding: 20px; }
        
        /* Sidebar Link Styles */
        .sidebar-nav li a { color: rgba(255,255,255,.75); padding: 15px 20px; display: flex; align-items: center; text-decoration: none; }
        .sidebar-nav li a:hover, .sidebar-nav li a.active { color: #fff; background: rgba(255,255,255,0.1); }
        .sidebar-nav li a i { margin-right: 10px; }
        
        /* RTL Support */
        html[dir="rtl"] #sidebar-wrapper { border-left: 1px solid #dee2e6; border-right: none; }
        html[dir="rtl"] .sidebar-nav li a i { margin-left: 10px; margin-right: 0; }
    </style>
    @yield('css')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold ps-3" href="#">JL Token</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item me-3">
                        <span class="text-light small">
                            {{session()->get("settings")->name ?? 'App'}}, {{session()->get("settings")->location ?? 'Loc'}}
                        </span>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="flag-icon flag-icon-{{\App::currentLocale()}}">Lang</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="changeLanguage(1)">English</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeLanguage(4)">Arabic</a></li>
                            </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            @if(isset(Auth::user()->image) && Storage::disk('public')->exists(Auth::user()->image))
                                <img src="{{Auth::user()->image_url}}" class="rounded-circle" width="30" height="30" alt="user">
                            @else
                                <img src="{{asset('app-assets/images/avatar/avatar.png')}}" class="rounded-circle" width="30" height="30" alt="user">
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @can('view profile')
                            <li><a class="dropdown-item" href="{{route('profile')}}">{{__('messages.common.profile')}}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @endcan
                            <li><a class="dropdown-item" href="{{route('logout')}}">{{__('messages.common.logout')}}</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="wrapper">
        @include('layout.menu')
        
        <main id="page-content-wrapper">
            @yield('content')
        </main>
    </div>

    <footer class="bg-white text-center py-3 mt-auto border-top text-muted small">
        <div class="container">
            Powered by <a href="https://www.kingslee.net" target="_blank" class="text-decoration-none fw-bold">KingsLee Inc. Technologies</a>
            <span class="ms-2">Version {{AppVersion::VERSION}}</span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function changeLanguage(id) {
            $.post("{{Route('change_session_language')}}", { language: id, _token: '{{csrf_token()}}' })
             .done(() => location.reload());
        }
    </script>
    @yield('js')
</body>
</html>
