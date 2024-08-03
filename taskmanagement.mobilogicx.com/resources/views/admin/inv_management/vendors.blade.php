@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
        <div>
            <h4 class="mb-3 mb-md-0">Vendors</h4>
        </div>
        <div class="flex">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create_vendor"
                data-bs-whatever="@getbootstrap"> Add Vendor</button>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#bulk_upload"
                data-bs-whatever="@getbootstrap"> Bulk Upload</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="dataTableExample1" class="table w-100">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendors as $key => $item)
                        <tr>
                            <td class="center">{{ $key + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->contact_no }}</td>
                            <td style="white-space: normal; width:25%">
                                <p>{{ $item->address }}</p>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input item_status" type="checkbox" role="switch"
                                        data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                        {{ $item->is_active ? 'checked' : '' }} />
                                </div>
                            </td>
                            <td>
                                <button onclick="getVendorData({{ $item->id }})" type="button"
                                    class="btn btn-primary btn-icon btn-xs"><i data-feather="edit"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="create_vendor" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Vendor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_vendor_form" method="post" action="{{ route('admin.vendor.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="vendor_name" placeholder="Vendor Name"
                                    class="form-control" name="name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Email</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('portfolio') }}" id="email" placeholder="Email"
                                    class="form-control" name="email" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Contact</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('contact_no') }}" id="contact" placeholder="Contect No"
                                    class="form-control" name="contact_no" type="number">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Address</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <textarea name="address" class="form-control" id="exampleFormControlTextarea1" rows="4" spellcheck="false"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_Vendor" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Vendor Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_Vendor_form" method="post" action="{{ route('admin.vendor.update', '1') }}">
                    <div class="modal-body">
                        @csrf
                        @method('PATCH')
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="edit_name" placeholder="Vendor Name"
                                    class="form-control" name="name" type="text">
                                <input type="hidden" value="" name="id" id="edit_id">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Email</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('portfolio') }}" id="edit_email" placeholder="Email"
                                    class="form-control" name="email" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Contact</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('contact_no') }}" id="edit_contact" placeholder="Contect No"
                                    class="form-control" name="contact_no" type="number">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Address</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <textarea name="address" class="form-control" id="edit_address" rows="4" spellcheck="false"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bulk_upload" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Bulk Upload
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="bulk-upload" method="post" action="{{ route('admin.vendor.bulkUpload') }}" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Upload File</label>
                            </div>
                            <div class="col-lg-8 col-sm-8" id="dropyfyInput">
                                <input type="file" id="myDropify" data-default-file="" data-height="80"
                                    name="uploaded_file" type="text">
                            </div>
                        </div>
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
        $(function() {
            $("#add_vendor_form").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    contact_no: {
                        required: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    address: {
                        required: true,
                    },
                    email: {
                        email: true,
                    },
                },
                messages: {
                    name: {
                        required: "Please enter Vendor name",
                        minlength: "Vendor name must consist of at least 3 characters "
                    },
                    contact_no: {
                        required: "Please enter mobile number",
                        minlength: "Please enter a valid mobile no",
                        maxlength: "Please enter a valid mobile no"
                    },

                    email: "Please enter a valid email address",
                    address: {
                        required: "Please enter address",
                    }
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
            $("#edit_Vendor_form").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    contact_no: {
                        required: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    address: {
                        required: true,
                    },
                    email: {
                        email: true,
                    },
                },
                messages: {
                    name: {
                        required: "Please enter Vendor name",
                        minlength: "Vendor name must consist of at least 3 characters "
                    },
                    contact_no: {
                        required: "Please enter mobile number",
                        minlength: "Please enter a valid mobile no",
                        maxlength: "Please enter a valid mobile no"
                    },

                    email: "Please enter a valid email address",
                    address: {
                        required: "Please enter address",
                    }
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

        function getVendorData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'vendor/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#edit_Vendor').modal('show');
                    $('#edit_id').val(data.data.id);
                    $('#edit_name').val(data.data.name);
                    $('#edit_email').val(data.data.email);
                    $('#edit_contact').val(data.data.contact_no);
                    $('#edit_address').val(data.data.address);
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }
        $('.item_status').change(function() {
            var mode = $(this).prop('checked');
            var id = $(this).data("id");

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: '{{ route('admin.vendor.status') }}',
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
        });

        $(function() {
            'use strict';

            $('#myDropify').dropify();
        });
    </script>
@endsection
