@extends(Auth::check() ? (Auth::user()->role ? (Auth::user()->role->name === 'Warehouse Head' ? 'warehouse_head.layouts.app' : (Auth::user()->role->name === 'Depot Head' ? 'depot_head.layouts.app' : 'guest.layouts.app')) : 'layouts.app') : 'guest.layouts.app')


@section('content')
    <style>
        .dropify-filename {
            display: none;
        }

        .dropify-wrapper .dropify-preview .dropify-infos .dropify-infos-inner p.dropify-infos-message::before {
            display: none;
        }
    </style>
    <div class="row">
        <div class="col-md-4 col-lg-3">
            <form method="POST" action="{{ route('admin.profile.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" id="myDropify" data-default-file="{{ imagePath($user->profile_photo_path) }}"
                    data-height="200" data-show-remove="false" name="image" type="text">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Update Image</button>
                </div>
            </form>
        </div><!-- col-3 -->
        <div class="col-md-8 col-lg-9 mg-t-30 mg-md-t-0">
            <h5 class="card-title mb-3">Personal Information</h5>
            <div class="card">
                <div class="card-body">
                    <form id="per_info" method="POST" action="{{ route('admin.profile.update', $user->id) }}">
                        @method('PATCH')
                        @csrf
                        <div class="row mb-3">
                            <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="exampleInputUsername2" name="name"
                                    placeholder="Name" value="{{ $user->name }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                                    value="{{ $user->email }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="email" class="col-sm-3 col-form-label">Mobile</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="mobile" name="mobile"
                                    placeholder="Mobile" value="{{ $user->mobile }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary btn-sm">Update Information</button>
                        </div>
                    </form>
                </div>
            </div>
            <h5 class="card-title my-3">Update Password</h5>
            <div class="card">
                <div class="card-body">
                    <form id="pw_update" method="POST" action="{{ route('admin.profile.update', $user->id) }}">
                        @method('PATCH')
                        @csrf
                        <div class="row mb-3">
                            <label for="exampleInputUsername2" class="col-sm-3 col-form-label">Current Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="current_password" name="current_password"
                                    placeholder="Current Password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="email" class="col-sm-3 col-form-label">New Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="password" name="new_password"
                                    placeholder="New Password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="password" class="col-sm-3 col-form-label">Confirm Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="cnf_password"
                                    name="new_password_confirmation" placeholder="Confirm password">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary btn-sm">Update password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            'use strict';

            $('#myDropify').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Edit Photo',
                }
            });
        });
        $(function() {
            'use strict';

            $.validator.setDefaults({
                submitHandler: function(form) {
                    form.sumbit();
                }
            });
            // add 
            $(function() {
                $("#per_info").validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3
                        },
                        email: {
                            required: true,
                            email: true
                        },
                        mobile: {
                            required: true,
                            minlength: 10,
                            maxlength: 10
                        },
                    },
                    messages: {
                        name: {
                            required: "Please enter a name",
                            minlength: "Name must consist of at least 3 characters"
                        },
                        email: "Please enter a valid email address",
                        mobile: {
                            required: "Please enter mobile number",
                            minlength: "Please enter a valid mobile no",
                            maxlength: "Please enter a valid mobile no"
                        },
                    },
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
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function(element, errorClass) {
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-invalid").removeClass("is-valid");
                        }
                    },
                    unhighlight: function(element, errorClass) {
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-valid").removeClass("is-invalid");
                        }
                    }
                });
            });
            $(function() {
                $("#pw_update").validate({
                    rules: {
                        current_password: {
                            required: true,
                        },
                        new_password: {
                            required: true,
                            minlength: 5
                        },
                        new_password_confirmation: {
                            required: true,
                            minlength: 5,
                            equalTo: "#password"
                        },
                    },
                    messages: {
                        current_password: {
                            required: "Please enter your current password",
                        },
                        new_password: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long"
                        },
                        new_password_confirmation: {
                            required: "Please confirm your password",
                            minlength: "Your password must be at least 5 characters long",
                            equalTo: "Please enter the same password as above"
                        },
                    },
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
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function(element, errorClass) {
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-invalid").removeClass("is-valid");
                        }
                    },
                    unhighlight: function(element, errorClass) {
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-valid").removeClass("is-invalid");
                        }
                    }
                });
            });
        });
        $(document).ready(function() {
            // Remove the image name on hover
            $("#myDropify").on("dropify:hover", function() {
                $(this).find(".dropify-filename").text("");
            });
        });
    </script>
@endsection
