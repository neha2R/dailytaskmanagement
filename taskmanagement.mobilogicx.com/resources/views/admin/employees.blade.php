@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Staffs
            </h4>
        </div>
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_emp"
                data-bs-whatever="@getbootstrap" onclick="dropyfyAppend()">Add staff</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->mobile }}</td>
                                <td>{{ $item->role->name ?? "-" }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                            {{ $item->is_active ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td>
                                    <button onclick="getEmpData({{ $item->id }})" type="button"
                                        class="btn btn-primary btn-icon btn-xs" data-bs-toggle="modal"
                                        data-bs-target="#edit_emp" data-bs-whatever="@getbootstrap"><i
                                            data-feather="edit"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_emp" tabindex="-1" aria-labelledby="add_emp" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_emp_form" method="post" action="{{ route('admin.employees.store') }}"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Image</label>
                            </div>
                            <div class="col-lg-8 col-sm-8" id="storeDropyfyInput">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input value="{{ old('name') }}" id="name" class="form-control" name="name"
                                    type="text" placeholder="Enter name">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="email" class="form-label">Email</label>

                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input value="{{ old('email') }}" id="email" class="form-control" name="email"
                                    type="text" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="mobile" class="form-label">Mobile No</label>

                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input value="{{ old('mobile') }}" id="mobile" class="form-control" name="mobile"
                                    type="number" min="10" placeholder="Enter mobile no">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="gender" class="form-label">Gender</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <select class="form-select" name="gender" id="gender">
                                    <option selected disabled>Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <!--<div class="row mb-3">-->
                        <!--    <div class="col-lg-3 col-sm-3">-->
                        <!--        <label for="emp_type" class="form-label">Employee Type</label>-->
                        <!--    </div>-->
                        <!--    <div class="col-lg-8 col-sm-8">-->
                        <!--        <select class="form-select" name="emp_type" id="emp_type">-->
                        <!--            <option selected disabled>Select Employee Type</option>-->
                        <!--            <option value="daily">Daily</option>-->
                        <!--            <option value="monthly">Monthly</option>-->
                        <!--            <option value="regular">Regular</option>-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                          <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="department" class="form-label">Department</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <select required class="form-select" name="department" id="emp_type">
                                    <option selected disabled>Select department</option>
                                    @foreach ($department as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                        <div class="col-lg-3 col-sm-3">
                                <label for="role" class="form-label">Role</label>

                            </div>  
                            <div class="col-lg-8 col-sm-8">

                            <select required style="width: 100%;"
                                        class="form-select js-example-basic-single" disabled name="role_id" id="role_id">
                                    <option selected disabled>Select Role</option>
                                     </select>
                                </div>
                        </div>
                        <!--<div class="row mb-3">-->
                        <!--    <div class="col-lg-3 col-sm-3">-->
                        <!--        <label for="department" class="form-label">Position</label>-->

                        <!--    </div>-->
                        <!--    <div class="col-lg-8 col-sm-8">-->
                        <!--        <select class="form-select" name="position" id="emp_type">-->
                        <!--            <option selected disabled>Select position</option>-->
                        <!--            @foreach ($position as $item)-->
                        <!--                <option value="{{ $item->id }}">{{ $item->name }}</option>-->
                        <!--            @endforeach-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_emp" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_emp_form" method="post" action="{{ route('admin.employees.update', '1') }}"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        @method('PATCH')
                        <input name="id" type="hidden" id="edit_id" value="">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Image</label>
                            </div>
                            <div class="col-lg-8 col-sm-8" id="dropyfyInput">
                                <input type="file" id="edit_myDropify" data-default-file="" data-height="80"
                                    name="image" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input value="{{ old('name') }}" id="edit_name" class="form-control" name="name"
                                    type="text" placeholder="Enter name">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="email" class="form-label">Email</label>

                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input value="{{ old('email') }}" id="edit_email" class="form-control" name="email"
                                    type="text" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="mobile" class="form-label">Mobile No</label>

                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input value="{{ old('mobile') }}" id="edit_mobile" class="form-control" name="mobile"
                                    type="number" min="10" placeholder="Enter mobile no">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="gender" class="form-label">Gender</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <select class="form-select" name="gender" id="edit_gender">
                                    <option selected disabled>Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <!--<div class="row mb-3">-->
                        <!--    <div class="col-lg-3 col-sm-3">-->
                        <!--        <label for="emp_type" class="form-label">Employee Type</label>-->
                        <!--    </div>-->
                        <!--    <div class="col-lg-8 col-sm-8">-->
                        <!--        <select class="form-select" name="emp_type" id="edit_emp_type">-->
                        <!--            <option selected disabled>Select Employee Type</option>-->
                        <!--            <option value="daily">Daily</option>-->
                        <!--            <option value="monthly">Monthly</option>-->
                        <!--            <option value="regular">Regular</option>-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                       <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="department" class="form-label">Department</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <select required class="form-select" name="department" id="edit_department">
                                    <option selected disabled>Select department</option>
                                    @foreach ($department as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="role" class="form-label">Role</label>

                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <select required class="form-select" name="role_id" id="edit_role">
                                    <option selected disabled>Select role</option>
                                  
                                </select>
                            </div>
                        </div>
                        <!--<div class="row mb-3">-->
                        <!--    <div class="col-lg-3 col-sm-3">-->
                        <!--        <label for="department" class="form-label">Position</label>-->

                        <!--    </div>-->
                        <!--    <div class="col-lg-8 col-sm-8">-->
                        <!--        <select class="form-select" name="position" id="edit_position">-->
                        <!--            <option selected disabled>Select position</option>-->
                        <!--            @foreach ($position as $item)-->
                        <!--                <option value="{{ $item->id }}">{{ $item->name }}</option>-->
                        <!--            @endforeach-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        // form
        $(function() {
            'use strict';

            $.validator.setDefaults({
                submitHandler: function(form) {
                    form.sumbit();
                }
            });
            // add 
            $(function() {
                $("#add_emp_form").validate({
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
                        gender: {
                            required: true,
                        },
                        emp_type: {
                            required: true,
                        },
                        department: {
                            required: true,
                        },
                        role: {
                            required: true,
                        },
                        position: {
                            required: true,
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
                            maxlength: "Please enter a valid mobile no",
                        },
                        gender: "Please select gender",
                        emp_type: "Please select employee type",
                        department: "Please select department",
                        role: "Please select role",
                        position: "Please select role",

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
            // edit
            $(function() {
                $("#edit_emp_form").validate({
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
                        gender: {
                            required: true,
                        },
                        emp_type: {
                            required: true,
                        },
                        department: {
                            required: true,
                        },
                        role: {
                            required: true,
                        },
                        position: {
                            required: true,
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
                            maxlength: "Please enter a valid mobile no",
                        },
                        gender: "Please select gender",
                        emp_type: "Please select employee type",
                        department: "Please select department",
                        role: "Please select role",
                        position: "Please select role",

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
                    },
                });
            });
        });
        // change status
        $('.item_status').change(function() {
            var mode = $(this).prop('checked');
            var id = $(this).data("id");

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: '{{ route('admin.emp.status') }}',
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
                    console.log(data);
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
        });
        $('#emp_type').change(function() {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'get_roles/' + $(this).val(),
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#role_id').prop("disabled", false).empty().append($("<option></option>")
                            .attr("value", "")
                            .attr("selected", "")
                            .attr("disabled", "")
                            .text('Select roles'))
                        $.each(data.roles, function(key, value) {
                            $('#role_id').append($("<option></option>")
                                .attr("value", value.id)
                                .text(value.name))
                        })
                    }
                },

                complete: function() {
                    $('#spin').addClass('d-none');
                },
            });

        });
        $('#edit_department').change(function() {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'get_roles/' + $(this).val(),
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#edit_role').prop("disabled", false).empty().append($("<option></option>")
                            .attr("value", "")
                            .attr("selected", "")
                            .attr("disabled", "")
                            .text('Select roles'))
                        $.each(data.roles, function(key, value) {
                            $('#edit_role').append($("<option></option>")
                                .attr("value", value.id)
                                .text(value.name))
                        })
                    }
                },

                complete: function() {
                    $('#spin').addClass('d-none');
                },
            });

        });
        // get data for edit
        function getEmpData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'employees/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#edit_id').val(data.resp.id);

                    $('#edit_myDropify, .dropify-wrapper').remove();
                    html = `<input type="file" id="edit_myDropify" name="updateImage" class="dropify form-control" data-max-file-size="1M" data-height="80" 
                            data-default-file='{{ asset('storage') }}/${data.resp.profile_photo_path}'/>`;
                    $('#dropyfyInput').append(html);
                    $('#edit_myDropify').dropify();

                    $('#edit_name').val(data.resp.name);
                    $('#edit_email').val(data.resp.email);
                    $('#edit_mobile').val(data.resp.mobile);
                    $('#edit_gender').val(data.resp.gender);
                    $('#edit_emp_type').val(data.resp.emp_type);
                    $('#edit_department').val(data.resp.department_id);
                    $('#edit_role').empty();
                    $.each(data.roles1, function(key, value) {
                    $('#edit_role').append($("<option></option>")
                        .attr("value", value.id)
                        .text(value.name))
                    });
                    $('#edit_role').val(data.resp.role_id);
                    $('#edit_position').val(data.resp.position_id);
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }

        function dropyfyAppend() {
            $('#myDropify, .dropify-wrapper').remove();
            html =
                `<input type="file" id="myDropify" name="image" class="dropify form-control" data-max-file-size="1M" id="imageInput" data-height="80"/>`;
            $('#storeDropyfyInput').append(html);
            $('#myDropify').dropify();
        }
    </script>
@endsection
