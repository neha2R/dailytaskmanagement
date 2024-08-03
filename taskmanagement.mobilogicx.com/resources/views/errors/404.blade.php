<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Responsive Laravel Admin Dashboard Template based on Bootstrap 5">
    <meta name="author" content="NobleUI">
    <meta name="keywords"
        content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, laravel, theme, front-end, ui kit, web">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&amp;display=swap"
        rel="stylesheet">
    <!-- End fonts -->

    <!-- CSRF Token -->
    <meta name="_token" content="4PijOUm8C87B5KovCZiuLtn3frUh8ZkBlvUrWfEI">

    <link rel="shortcut icon" href="../favicon.ico">

    <!-- plugin css -->

    <link href="{{ asset('admin/assets/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
    <!-- end plugin css -->


    <!-- common css -->
    <link href="{{ asset('admin/css/app.css') }}" rel="stylesheet" />

    <!-- end common css -->

</head>

<body>

    <script src="{{ asset('admin/assets/js/spinner.js') }}"></script>


    <div class="main-wrapper" id="app">
        <div class="page-wrapper full-page">
            <div class="page-content d-flex align-items-center justify-content-center">

                <div class="row w-100 mx-0 auth-page">
                    <div class="col-md-8 col-xl-6 mx-auto d-flex flex-column align-items-center">
                        <img src="{{asset('admin/assets/images/others/404.svg')}}" class="img-fluid mb-2" alt="404">
                        <h1 class="fw-bolder mb-22 mt-2 tx-80 text-muted">404</h1>
                        <h4 class="mb-2">Page Not Found</h4>
                        <h6 class="text-muted mb-3 text-center">Oopps!! The page you were looking for doesn't exist.
                        </h6>
                        <a href="{{url()->previous()}}">Back to home</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- base js -->
    <script src="{{ asset('admin/js/app.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/feather-icons/feather.min.js') }}"></script>

    <!-- end base js -->

    <!-- plugin js -->
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('admin/assets/js/template.js') }}"></script>

    <!-- end common js -->

</body>
</html>
