@extends('layouts.app')
@section('content')
    <style>
        #juniors-error {
            position: absolute;
            top: 83%;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Employment Assignment :
            </h4>
        </div>
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create_assignment"
                data-bs-whatever="@getbootstrap">Create</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="dataTableExample" class="table">
                <thead>
                    <tr>
                        <th>Sr no</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($seniors as $key => $item)
                        <tr>
                            <td class="center">{{ $key + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->mobile ?? '-' }}</td>
                            <td>{{ $item->department->name ?? '-' }}</td>
                            <td>{{ $item->position->name ?? '-' }}</td>
                            <td><button onclick="show_assignment({{ $item->id }})" type="button"
                                    class="btn btn-primary btn-icon btn-xs"><i data-feather="eye"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="show_assignment" tabindex="-1" aria-labelledby="show_assignment" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel"> Assigned Junior's</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        Sr No
                                    </th>
                                    <th>
                                        Image
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Position
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="assigned_users">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create_assignment" tabindex="-1" aria-labelledby="add_department" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Staff Assignment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_assignement_form" method="post" action="{{ route('admin.assignement.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="gender" class="form-label">Department</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <select class="form-select" name="department" id="department">
                                    <option selected disabled>Select department</option>
                                    @foreach ($departments as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="gender" class="form-label">Seniors</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <select class="form-select" disabled name="senior" id="seniors">
                                    <option selected disabled>Select seniors</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="gender" class="form-label">Juniors</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <select id="juniors" name="juniors[]" disabled
                                    class="js-example-basic-multiple form-select" multiple="multiple" data-width="100%">

                                </select>
                            </div>
                        </div>
                        {{-- <input class="btn btn-primary" type="submit" value="Submit"> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
  'use strict'

  if ($(".js-example-basic-single").length) {
    $(".js-example-basic-single").select2();
  }
  if ($(".js-example-basic-multiple").length) {
    $(".js-example-basic-multiple").select2({
      placeholder:"Select juniors",
      dropdownParent: $("#create_assignment")
    });
  }
});
        $('#department').change(function() {
            $('#juniors').prop("disabled", true).empty();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'get_seniors/' + $(this).val(),
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#seniors').prop("disabled", false).empty().append($("<option></option>")
                            .attr("value", "")
                            .attr("selected", "")
                            .attr("disabled", "")
                            .text('Select seniors'))
                        $.each(data.seniors, function(key, value) {
                            $('#seniors').append($("<option></option>")
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
        $('#seniors').change(function() {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                // use show method for resource route
                url: 'assignement/' + $(this).val(),
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    console.log(data);
                    if (data.status == 200) {
                        $('#juniors').prop("disabled", false).empty();
                        $.each(data.juniors, function(key, value) {
                            $('#juniors').append($("<option></option>")
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
                // validate signup form on keyup and submit
                $("#add_assignement_form").validate({
                    rules: {
                        department: {
                            required: true
                        },
                        senior: {
                            required: true
                        },
                        juniors: {
                            required: true
                        },
                    },
                    messages: {
                        department: {
                            required: "Please select a department",
                        },
                        senior: {
                            required: "Please select senior",
                        },
                        juniors: {
                            required: "Please select a juniors",
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

        function show_assignment(id) {
            var imageUrl = '{{ asset('storage/') }}';

            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'show_assignement/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    if (data.users.length == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data Not Found',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                    if (data.status == 200 && data.users.length > 0) {
                        $('#show_assignment').modal("show");
                        $('#assigned_users').empty();
                        $.each(data.users, function(key, val) {
                            var image = (val.profile_photo_path) ? imageUrl + '/' + val
                                .profile_photo_path :
                                'https://static.vecteezy.com/system/resources/previews/008/442/086/original/illustration-of-human-icon-user-symbol-icon-modern-design-on-blank-background-free-vector.jpg';
                            $('#assigned_users').append(`<tr id="remove${val.id}">\
                                    <td>${key +1 }</td>\    
                                    <td class="py-1">\
                                        <img src="${image}"\
                                            alt="image">\
                                    </td>\
                                    <td>${val.name}</td>\
                                    <td>${val.position.name}</td>\
                                    <td><button data-id="${val.id}" onclick="remove_jun(${val.id})" data-id="${val.id}" class="btn btn-danger btn-xs">Remove</button></td>\
                                </tr>`)
                        });
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }

        function remove_jun(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'remove_assignement/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    if (data.status == 200) {
                        $('#remove' + id).remove();
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
    </script>
@endsection
