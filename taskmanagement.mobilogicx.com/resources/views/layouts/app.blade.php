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
    <link href="{{ asset('admin/assets/plugins/jquery-steps/jquery.steps.css') }}" rel="stylesheet" />

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
                    <li class="nav-item nav-category">Explore</li>
                    <li class="nav-item ">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="link-icon" data-feather="home"></i>
                            <span class="link-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" data-bs-toggle="collapse" href="#employee_management" role="button"
                            aria-expanded="false" aria-controls="employee_management">
                            <i class="link-icon" data-feather="users"></i>
                            <span class="link-title">Employee Management</span>
                            <i class="link-arrow" data-feather="chevron-down"></i>
                        </a>
                        <div class="collapse" id="employee_management">
                            <ul class="nav sub-menu">
                                <li class="nav-item">
                                    <a href="{{ route('admin.department.index') }}" class="nav-link "> Departments </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.roles.index') }}" class="nav-link "> Roles </a>
                                </li>
                             
                                <li class="nav-item">
                                    <a href="{{ route('admin.employees.index') }}" class="nav-link ">Staffs</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.attendance.index') }}" class="nav-link "> Attendance
                                        Management </a>
                                </li>
                            
                            </ul>
                        </div>
                    </li>
                  <li class="nav-item ">
                        <a class="nav-link collapsed" data-bs-toggle="collapse" href="#advanced-ui" role="button"
                            aria-expanded="false" aria-controls="advanced-ui">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-anchor link-icon">
                                <circle cx="12" cy="5" r="3"></circle>
                                <line x1="12" y1="22" x2="12" y2="8"></line>
                                <path d="M5 12H2a10 10 0 0 0 20 0h-3"></path>
                            </svg>
                            <span class="link-title">Task Management</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-chevron-down link-arrow">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                        <div class="collapse" id="advanced-ui" style="">
                            <ul class="nav sub-menu">
                                <li class="nav-item">
                                    <a href="{{ route('admin.manage-tasks.index') }}" class="nav-link ">Daily Tasks</a>
                                </li>
                             
                            </ul>
                        </div>
                    </li>
                  
                   
                   
                 
                  
                </ul>
            </div>
        </nav>

        <div class="page-wrapper">
            <nav class="navbar">
                <a href="#" class="sidebar-toggler">
                    <i data-feather="menu"></i>
                </a>
                <div class="navbar-content">
                    {{-- <form class="search-form">
                        <div class="input-group">
                            <div class="input-group-text">
                                <i data-feather="search"></i>
                            </div>
                            <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
                        </div>
                    </form> --}}
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown"
                                role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="bell"></i>
                                <div class="indicator">
                                    <div class="circle"></div>
                                </div>
                            </a>
                            <div class="dropdown-menu p-0 " aria-labelledby="notificationDropdown"
                                id="notificationsContainer">
                                <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                    <p class="me-3" id="notificationCount">Loading Notifications...</p>
                                    <a href="javascript:;" class="text-muted" id="clearNotifications">Clear all</a>
                                </div>
                                <div class="p-1 notification_height" id="notificationList">
                                    <!-- Notifications will be dynamically added here -->
                                </div>
                            </div>
                        </li>
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
    <script src="{{ asset('admin/assets/plugins/jquery-steps/jquery.steps.min.js') }}"></script>

    <script src="{{ asset('admin/assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/typeahead-js/typeahead.bundle.min.js') }}"></script>
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
        function handleCancelled(url,id) {
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
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
                            timer: 4000,
                            timerProgressBar: false,
                        });
                        Toast.fire({
                            icon: 'success',
                            title: data.message
                        })
                        setTimeout(function () { 
   //     alert('Reloading Page'); 
        location.reload(true); 
      }, 900); 
                      //  datatable1.ajax.reload();

                     //$("#dataTableExample").dataTable().fnReloadAjax();

                   //     $("#container-fluid1").load(location.href + " #container-fluid1");

                      //  $("#container-fluid1").load($(this).attr(href));

                    }
                    else if (data.status == 202) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: false,
                        });
                        Toast.fire({
                            icon: 'error',
                            title: data.message
                        })
                      
                    }
                },
                complete: function() {

                    $('#spin').addClass('d-none');
                }
            });
        }
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
                    else if (data.status == 202) {
                    $('.item_status').prop('checked',true);
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: false,
                        });
                        Toast.fire({
                            icon: 'error',
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
            $.validator.addMethod("noDoubleSpaces", function(value, element) {
                return !/\s{2,}/.test(value);
            }, "Please avoid using double spaces between words.");
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
                    } else if (element.hasClass('dropify')) {
                        element.parent().addClass("has-error");
                        element.parent().find('.dropify-error').html(error);
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
        // Function to initialize Select2
        function initializeSelect2(selectId, dropdownParentId) {
            if ($(selectId).length) {
                $(selectId).select2({
                    dropdownParent: $(dropdownParentId)
                });
            }
        }

        function initializeFlatpickr(inputId) {
            if ($(inputId).length) {
                flatpickr(inputId, {
                    dateFormat: "d M Y",
                    // Additional flatpickr configurations if needed
                });
            }
        }

        function initializeAndSetValue(selector, parent, values) {
            initializeSelect2(selector, parent);
            $(selector).val(values).trigger('change');
        }
        // Function to initialize flatpickr
        function initFlatpickrWithMinDate(inputId, relatedInputId, defaultDate = '') {
            if ($(inputId).length) {
                flatpickr(inputId, {
                    dateFormat: "d M Y",
                    minDate: "today",
                    defaultDate: defaultDate,
                    onChange: function(selectedDates) {
                        if (selectedDates.length > 0 && $(relatedInputId).length) {
                            setMinMaxDate(relatedInputId, selectedDates[0], 'min');
                        }
                    }
                });
            }
        }

        function initFlatpickrWithMaxDate(inputId, relatedInputId, defaultDate = '') {
            if ($(inputId).length) {
                flatpickr(inputId, {
                    dateFormat: "d M Y",
                    minDate: "today",
                    defaultDate: defaultDate,
                    onChange: function(selectedDates) {
                        if (selectedDates.length > 0 && $(relatedInputId).length) {
                            setMinMaxDate(relatedInputId, selectedDates[0], 'max');
                        }
                    }
                });
            }
        }

        function setMinMaxDate(inputId, date, type) {
            var fp = flatpickr(inputId, {
                dateFormat: "d M Y"
            });
            if (type === 'min') {
                fp.set("minDate", date);
            } else if (type === 'max') {
                fp.set("maxDate", date);
                fp.set("minDate", "today");
            }
        }

        // Function to initialize Dropify
        function initializeDropify(inputId) {
            if ($(inputId).length) {
                $(inputId).dropify({
                    messages: {
                        'default': 'Drag and drop a file here or click',
                        'replace': 'Change Document',
                    }
                    // Additional Dropify configurations if needed
                });
            }
        }

        function initializeFlatpickrWithTime(selector) {
            $(document).ready(function() {
                flatpickr(selector, {
                    enableTime: true, // Enable time selection
                    dateFormat: "d M Y H:i", // Customize the date and time format
                });
            });
        }


        function showDocument(documentUrl) {
            // You can open the document in a new tab or use any other method to display it
            window.open(`{{ asset('storage') }}` + '/' + documentUrl, '_blank');
        }
    </script>

    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
    https://firebase.google.com/docs/web/setup#available-libraries -->

    <script>
        // Your web app's Firebase configuration
        var firebaseConfig = {
            apiKey: "AIzaSyBa0_iGr6D7y_xqYiBG9ZemNXkkXcQ72bA",
            authDomain: "blb-infra.firebaseapp.com",
            projectId: "blb-infra",
            storageBucket: "blb-infra.appspot.com",
            messagingSenderId: "930773697230",
            appId: "1:930773697230:web:1ec6ae45509cefc4c2869f",
            measurementId: "G-37GZYMEY5C"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        const messaging = firebase.messaging();

        function initFirebaseMessagingRegistration() {
            messaging.requestPermission().then(function() {
                return messaging.getToken()
            }).then(function(token) {

                axios.post("{{ route('admin.store.token') }}", {
                    _method: "PATCH",
                    token
                }).then(({
                    data
                }) => {
                    console.log(data)
                }).catch(({
                    response: {
                        data
                    }
                }) => {
                    console.error(data)
                })

            }).catch(function(err) {
                console.log(`Token Error :: ${err}`);
            });
        }

        initFirebaseMessagingRegistration();

        messaging.onMessage(function({
            data: {
                body,
                title
            }
        }) {
            new Notification(title, {
                body
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to fetch and display notifications
            function fetchNotifications() {
                axios.get('/update-account-details/fetch-notifications')
                    .then(response => {
                        console.log(response);
                        const notifications = response.data.notifications;
                        const notificationCount = notifications.length;

                        document.getElementById('notificationCount').innerText = notificationCount +
                            ' New Notifications';

                        const indicator = document.querySelector(
                        '.indicator'); // Assuming there's only one indicator
                        if (notificationCount > 0) {
                            indicator.style.display = 'block'; // Show the indicator
                        } else {
                            indicator.style.display = 'none'; // Hide the indicator
                        }
                        const notificationList = document.getElementById('notificationList');
                        notificationList.innerHTML = ''; // Clear existing notifications

                        notifications.forEach(notification => {
                            const notificationItem = document.createElement('a');
                            notificationItem.href = notification.url;
                            notificationItem.className = 'dropdown-item d-flex align-items-center py-2';

                            const iconContainer = document.createElement('div');
                            iconContainer.className =
                                'wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3';

                            const icon = document.createElement('i');
                            icon.className = 'icon-sm text-white mdi mdi-bell-outline';

                            const contentContainer = document.createElement('div');
                            contentContainer.className = 'flex-grow-1 me-2';

                            const title = document.createElement('p');
                            title.innerText = notification.title;
                            title.className = 'text-wrap notification_p';

                            const timestamp = document.createElement('p');
                            timestamp.className = 'tx-12 text-muted';
                            timestamp.innerText = notification.created_at;

                            iconContainer.appendChild(icon);
                            contentContainer.appendChild(title);
                            contentContainer.appendChild(timestamp);

                            notificationItem.appendChild(iconContainer);
                            notificationItem.appendChild(contentContainer);

                            notificationList.appendChild(notificationItem);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                    });
            }

            // Function to clear all notifications
            function clearNotifications() {
                axios.post('/update-account-details/clear-notifications')
                    .then(response => {
                        fetchNotifications(); // Refresh the notification dropdown
                    })
                    .catch(error => {
                        console.error('Error clearing notifications:', error);
                    });
            }

            // Event listener for the "Clear all" button
            document.getElementById('clearNotifications').addEventListener('click', function() {
                clearNotifications();
            });

            // Fetch notifications on page load
            fetchNotifications();
        });
    </script>
    @yield('js')
</body>

</html>
