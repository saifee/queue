<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JL Token| Installer</title>
    <link rel="icon" type="image/png" href="{{ asset('app-assets/installer/img/favicon/favicon-16x16.png') }}" sizes="16x16" />
    <link rel="icon" type="image/png" href="{{ asset('app-assets/installer/img/favicon/favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ asset('app-assets/installer/img/favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link href="{{ asset('app-assets/installer/css/style.min.css') }}" rel="stylesheet" />
    @yield('style')
    <script>
        window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>
    </script>
</head>

<body>
    <div class="master">
        <div class="box">
            <div class="header" style="background-color: #dd0606;">
                <h1 class="header__title"><i class="fa fa-exclamation-triangle"></i>
                    The files are corrupted and cannot be opened. Please reinstall</h1>
            </div>
           
            <div class="main" style="padding: 10px 10px 5px 10px;">
                @yield('container')
                <div style="display: flex; justify-content: center; margin-top:20px">Version @yield('app-version')</div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>