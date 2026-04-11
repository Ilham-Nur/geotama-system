<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Login')</title>

    <link rel="shortcut icon" href="{{ asset('template/assets/images/icon-geotama.ico') }}" type="image/x-icon" />

    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/lineicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/materialdesignicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/custom.css') }}" />

    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f7fb;
            padding: 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-logo h3 {
            margin: 0;
            font-weight: 700;
            color: #1e293b;
        }

        .auth-logo p {
            margin-top: 8px;
            color: #64748b;
            font-size: 14px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            min-height: 48px;
            border-radius: 10px;
        }

        .btn-login {
            min-height: 48px;
            border-radius: 10px;
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="auth-wrapper">
        @yield('content')
    </div>

    <script src="{{ asset('template/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>
