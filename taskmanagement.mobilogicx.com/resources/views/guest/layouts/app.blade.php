<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Responsive Laravel Admin Dashboard Template based on Bootstrap 5">
    <meta name="author" content="Neologicx">
    <meta name="keywords"
        content="neologicx, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, laravel, theme, front-end, ui kit, web">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&amp;display=swap"
        rel="stylesheet">
    <!-- End fonts -->

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="shortcut icon" href="{{ asset('admin/favicon.svg') }}">

    <!-- plugin css -->
    <link href="{{ asset('admin/assets/fonts/feather-font/css/iconfont.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin//assets/plugins/%40mdi/css/materialdesignicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />
    <!-- end plugin css -->

    <!-- datable css-->
    <link href="{{ asset('admin/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('admin/assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
    <!-- common css -->
    <link href="{{ asset('admin/css/app.css') }}" rel="stylesheet" />
    <!-- end common css -->

</head>

<body>
    <script src="{{ asset('admin/assets/js/spinner.js') }}"></script>

    <div class="main-wrapper" id="app">
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="#" class="sidebar-brand">
                    {{-- Neo<span>logicx</span> --}}
                    <img style="width: 119px;" src="{{ asset('admin/neologicx.svg') }}" alt="neologo">
                </a>
                <div class="sidebar-toggler not-active">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="sidebar-body">
                <ul class="nav">
                    <li class="nav-item nav-category">Main</li>
                    <li class="nav-item ">
                        <a href="{{ route('guest-home') }}" class="nav-link">
                            <i class="link-icon" data-feather="home"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <nav class="settings-sidebar">
            <div class="sidebar-body">
                <a href="#" class="settings-sidebar-toggler">
                    <i data-feather="settings"></i>
                </a>
                <h6 class="text-muted mb-2">Sidebar:</h6>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="sidebarThemeSettings"
                                id="sidebarLight" value="sidebar-light" checked>
                            Light
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="sidebarThemeSettings"
                                id="sidebarDark" value="sidebar-dark">
                            Dark
                        </label>
                    </div>
                </div>
            </div>
        </nav>
        <div class="page-wrapper">
            <nav class="navbar">
                <a href="#" class="sidebar-toggler">
                    <i data-feather="menu"></i>
                </a>
                <div class="navbar-content">
                    <form class="search-form">
                        <div class="input-group">
                            <div class="input-group-text">
                                <i data-feather="search"></i>
                            </div>
                            <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
                        </div>
                    </form>
                    <ul class="navbar-nav">
                        {{-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="appsDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="grid"></i>
                            </a>
                            <div class="dropdown-menu p-0" aria-labelledby="appsDropdown">
                                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                    <p class="mb-0 fw-bold">Web Apps</p>
                                    <a href="javascript:;" class="text-muted">Edit</a>
                                </div>
                                <div class="row g-0 p-1">
                                    <div class="col-3 text-center">
                                        <a href="apps/chat.html"
                                            class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i
                                                data-feather="message-square" class="icon-lg mb-1"></i>
                                            <p class="tx-12">Chat</p>
                                        </a>
                                    </div>
                                    <div class="col-3 text-center">
                                        <a href="apps/calendar.html"
                                            class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i
                                                data-feather="calendar" class="icon-lg mb-1"></i>
                                            <p class="tx-12">Calendar</p>
                                        </a>
                                    </div>
                                    <div class="col-3 text-center">
                                        <a href="email/inbox.html"
                                            class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i
                                                data-feather="mail" class="icon-lg mb-1"></i>
                                            <p class="tx-12">Email</p>
                                        </a>
                                    </div>
                                    <div class="col-3 text-center">
                                        <a href="general/profile.html"
                                            class="dropdown-item d-flex flex-column align-items-center justify-content-center wd-70 ht-70"><i
                                                data-feather="instagram" class="icon-lg mb-1"></i>
                                            <p class="tx-12">Profile</p>
                                        </a>
                                    </div>
                                </div>
                                <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                    <a href="javascript:;">View all</a>
                                </div>
                            </div>
                        </li> 
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="mail"></i>
                            </a>
                            <div class="dropdown-menu p-0" aria-labelledby="messageDropdown">
                                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                    <p>9 New Messages</p>
                                    <a href="javascript:;" class="text-muted">Clear all</a>
                                </div>
                                <div class="p-1">
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="me-3">
                                            <img class="wd-30 ht-30 rounded-circle"
                                                src="assets/images/faces/face2.jpg" alt="userr">
                                        </div>
                                        <div class="d-flex justify-content-between flex-grow-1">
                                            <div class="me-4">
                                                <p>Leonardo Payne</p>
                                                <p class="tx-12 text-muted">Project status</p>
                                            </div>
                                            <p class="tx-12 text-muted">2 min ago</p>
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="me-3">
                                            <img class="wd-30 ht-30 rounded-circle"
                                                src="assets/images/faces/face3.jpg" alt="userr">
                                        </div>
                                        <div class="d-flex justify-content-between flex-grow-1">
                                            <div class="me-4">
                                                <p>Carl Henson</p>
                                                <p class="tx-12 text-muted">Client meeting</p>
                                            </div>
                                            <p class="tx-12 text-muted">30 min ago</p>
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="me-3">
                                            <img class="wd-30 ht-30 rounded-circle"
                                                src="assets/images/faces/face4.jpg" alt="userr">
                                        </div>
                                        <div class="d-flex justify-content-between flex-grow-1">
                                            <div class="me-4">
                                                <p>Jensen Combs</p>
                                                <p class="tx-12 text-muted">Project updates</p>
                                            </div>
                                            <p class="tx-12 text-muted">1 hrs ago</p>
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="me-3">
                                            <img class="wd-30 ht-30 rounded-circle"
                                                src="assets/images/faces/face5.jpg" alt="userr">
                                        </div>
                                        <div class="d-flex justify-content-between flex-grow-1">
                                            <div class="me-4">
                                                <p>Amiah Burton</p>
                                                <p class="tx-12 text-muted">Project deatline</p>
                                            </div>
                                            <p class="tx-12 text-muted">2 hrs ago</p>
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div class="me-3">
                                            <img class="wd-30 ht-30 rounded-circle"
                                                src="assets/images/faces/face6.jpg" alt="userr">
                                        </div>
                                        <div class="d-flex justify-content-between flex-grow-1">
                                            <div class="me-4">
                                                <p>Yaretzi Mayo</p>
                                                <p class="tx-12 text-muted">New record</p>
                                            </div>
                                            <p class="tx-12 text-muted">5 hrs ago</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                    <a href="javascript:;">View all</a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown"
                                role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="bell"></i>
                                <div class="indicator">
                                    <div class="circle"></div>
                                </div>
                            </a>
                            <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
                                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                    <p>6 New Notifications</p>
                                    <a href="javascript:;" class="text-muted">Clear all</a>
                                </div>
                                <div class="p-1">
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div
                                            class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="gift"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>New Order Recieved</p>
                                            <p class="tx-12 text-muted">30 min ago</p>
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div
                                            class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="alert-circle"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>Server Limit Reached!</p>
                                            <p class="tx-12 text-muted">1 hrs ago</p>
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div
                                            class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <img class="wd-30 ht-30 rounded-circle"
                                                src="assets/images/faces/face6.jpg" alt="userr">
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>New customer registered</p>
                                            <p class="tx-12 text-muted">2 sec ago</p>
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div
                                            class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="layers"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>Apps are ready for update</p>
                                            <p class="tx-12 text-muted">5 hrs ago</p>
                                        </div>
                                    </a>
                                    <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                        <div
                                            class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                            <i class="icon-sm text-white" data-feather="download"></i>
                                        </div>
                                        <div class="flex-grow-1 me-2">
                                            <p>Download completed</p>
                                            <p class="tx-12 text-muted">6 hrs ago</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                    <a href="javascript:;">View all</a>
                                </div>
                            </div>
                        </li> --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="wd-30 ht-30 rounded-circle"
                                    src="{{ imagePath(auth()->user()->profile_photo_path) ?? asset('admin/assets/images/faces/face1.jpg') }}"
                                    alt="profile">
                            </a>
                            <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                                <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                                    <div class="mb-3">
                                        <img class="wd-80 ht-80 rounded-circle"
                                            src="{{ imagePath(auth()->user()->profile_photo_path) ?? asset('admin/assets/images/faces/face1.jpg') }}"
                                            alt="">
                                    </div>
                                    <div class="text-center">
                                        <p class="tx-16 fw-bolder">{{ auth()->user()->name }}</p>
                                        <p class="tx-12 text-muted">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                <ul class="list-unstyled p-1">
                                    <a href="{{ route('admin.profile.index') }}"
                                        class="text-body ms-0 dropdown-item py-2">
                                        <li>
                                            <i class="me-2 icon-md" data-feather="user"></i>
                                            <span>Profile</span>
                                        </li>
                                    </a>
                                    <a href="#sign-out" data-bs-toggle="modal" data-target="#sign-out"
                                        role="button" class="text-body ms-0 dropdown-item py-2">
                                        <li>
                                            <i class="me-2 icon-md" data-feather="log-out"></i>
                                            <span>Log Out</span>
                                        </li>
                                    </a>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="page-content">
                @yield('content')
            </div>
            @component('components.logout')
            @endcomponent
            <footer
                class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
                <p class="text-muted mb-1 mb-md-0">Copyright © 2023 <a href="#" target="_blank">Neologicx</a>.
                </p>
            </footer>
        </div>
    </div>

    <!-- base js -->
    <script src="{{ asset('admin/js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(function() {
                var current1 = window.location.href.split("?")[0].split("#")[0];
                let current = new URL(current1).origin + '/' + current1.split("/")[3] + '/' + current1
                    .split("/")[4]
                // var current = window.location.href.split("?")[0].split("#")[0];
                $('.sidebar ul li a').each(function() {
                    var $this = $(this);
                    // if the current path is like this link, make it active
                    if ($this.attr('href').indexOf(current) !== -1) {
                        $this.parent('li').addClass('active');
                        $this.addClass('active');
                        $('.sidebar ul li ul li a').each(function() {
                            if ($(this).attr('href').indexOf(current) !== -1) {
                                var div = $(this).parent('li').parent('ul').closest('div')
                                    .addClass("show");
                                div.parent('li').find('a').first().attr("aria-expanded",
                                    "true");
                            }
                        })
                    }
                })
            })
        });
    </script>

    <script src="{{ asset('admin/assets/plugins/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    <script src="{{ asset('admin/assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/dropify/js/dropify.min.js') }}"></script>

    <!-- end plugin js -->

    <!-- common js -->

    <script src="{{ asset('admin/assets/js/template.js') }}"></script>
    <!-- end common js -->
    <script src="{{ asset('admin/assets/js/data-table.js') }}"></script>
    <script src="{{ asset('admin/assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('admin/assets/js/dropify.js') }}"></script>
    <script src="{{ asset('admin/custom.js') }}"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true,
        });

        @if ($msg = Session::get('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ $msg }}'
            });
            @php
                session()->forget('success');
            @endphp
        @endif


        @if ($msg = Session::get('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ $msg }}'
            });
            @php
                session()->forget('success');
            @endphp
        @endif


        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Toast.fire({
                    icon: 'error',
                    title: '{{ $error }}'
                });
            @endforeach
            @php
                session()->forget('errors');
            @endphp
        @endif
        // $(document).ready(function() {
        //     // When the modal is hidden, reset the form and validation
        //     $('.modal').on('hidden.bs.modal', function() {
        //         if ($(this).find('form').length) {
        //             $(this).find('form')[0].reset();
        //             $(this).find('form').validate().resetForm();
        //             $(this).find('.is-invalid').removeClass('is-invalid');
        //             $(this).find('.is-valid').removeClass('is-valid');
        //         }
        //     });
        // });
    </script>
    <script>
        const loader = document.getElementById("spin");

        $('#lineTab').on('show.bs.tab', 'a[data-bs-toggle="tab"]', function(e) {
            // Show the loader when the tab changes
            loader.classList.remove("d-none");

            // Simulate a delay (replace with your actual loading logic)
            setTimeout(() => {
                // Hide the loader after the loading is complete
                loader.classList.add("d-none");
            }, 100); // Change 2000 to the time it takes to load your content
        });


        bootstrap.Modal.Default.backdrop = 'static';
        // $(document).ready(function() {
        //     // Function to refresh the page
        //     function refreshPage() {
        //         location.reload(); // Reloads the current page
        //     }

        //     // Add a handler for modal close event for all modals
        //     $('.modal').on('hidden.bs.modal', function(e) {
        //         loader.classList.remove("d-none");
        //         refreshPage(); // Refresh the page when any modal is closed
        //     });
        // });

        function handleStatusChange(url, mode, id) {
            
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "status": mode,
                    'id': id
                },
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    
                    if (data.status == 200) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: false,
                        });
                        Toast.fire({
                            icon: 'success',
                            title: data.message
                        })
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }
        // for edit
        function getData(url, successCallback) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: url,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    if (successCallback) {
                        successCallback(data);
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }

        // form validation
        function initializeValidation(formId, rules, messages) {
            $(formId).validate({
                rules: rules,
                messages: messages,
                errorPlacement: function(error, element) {
                    error.addClass("invalid-feedback");
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else if (element.prop('type') === 'radio' && element.parent(
                            '.radio-inline').length) {
                        error.insertAfter(element.parent().parent());
                    } else if (element.prop('type') === 'checkbox' || element.prop(
                            'type') === 'radio') {
                        error.appendTo(element.parent().parent());
                    } else if (element.hasClass('select2-hidden-accessible')) {
                        element.parent().find('.select2-container').addClass(
                            'form-control p-0 is-invalid');
                        error.appendTo(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element, errorClass) {
                    if ($(element).prop('type') !== 'checkbox' && $(element).prop(
                            'type') !== 'radio') {
                        $(element).addClass("is-invalid").removeClass("is-valid");
                    }
                    if ($(element).hasClass("select2-hidden-accessible")) {
                        $(element).on('select2:select', function() {
                            $(element).parent().find('.select2-container')
                                .addClass("is-valid").removeClass("is-invalid");
                        });
                        $(element).on('select2:unselect', function() {
                            $(element).parent().find('.select2-container')
                                .addClass("is-invalid").removeClass("is-valid");
                        });
                    }
                },
                unhighlight: function(element, errorClass) {
                    if ($(element).prop('type') !== 'checkbox' && $(element).prop(
                            'type') !== 'radio') {
                        $(element).addClass("is-valid").removeClass("is-invalid");
                    }
                    // Add the following code to handle select2 changes
                    if ($(element).hasClass("select2-hidden-accessible")) {
                        $(element).on('select2:select', function() {
                            $(element).parent().find('.select2-container')
                                .addClass("is-valid").removeClass("is-invalid");
                        });
                        $(element).on('select2:unselect', function() {
                            $(element).parent().find('.select2-container')
                                .addClass("is-invalid").removeClass("is-valid");
                        });
                    }
                },
                ignore: [], // This line allows validation for hidden elements
            });
        }
    </script>
    @yield('js')
</body>

</html>
