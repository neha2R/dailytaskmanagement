@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
        <div>
            <h4 class="mb-3 mb-md-0">Companies</h4>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create_category"
            data-bs-whatever="@getbootstrap"> Add Company</button>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="dataTableExample1" class="table">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Name</th>
                        <th>Portfolio</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companies as $key => $item)
                        <tr>
                            <td class="center">{{ $key + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->portfolio }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input item_status" type="checkbox" role="switch"
                                        data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                        {{ $item->is_active ? 'checked' : '' }} />
                                </div>
                            </td>
                            <td>
                                <button onclick="getCompanyData({{ $item->id }})" type="button"
                                    class="btn btn-primary btn-icon btn-xs"><i data-feather="edit"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="create_category" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Company
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_company_form" method="post" action="{{ route('admin.companies.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="category_name" placeholder="Category Name"
                                    class="form-control" name="name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Portfolio</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('portfolio') }}" id="portfolio" placeholder="Portfolio"
                                    class="form-control" name="portfolio" type="text">
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
                                <label for="categories" class="form-label">Categories</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <select id="categories_id" name="categories_id[]"
                                    class="js-example-basic-multiple form-select" multiple="multiple" data-width="100%">
                                    @foreach (getCategories() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
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
    <div class="modal fade" id="edit_company" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Company Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_company_form" method="post" action="{{ route('admin.companies.update', '1') }}">
                    <div class="modal-body">
                        @csrf
                        @method('PATCH')
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="edit_name" placeholder="Category Name"
                                    class="form-control" name="name" type="text">
                                <input type="hidden" value="" name="id" id="edit_id">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Portfolio</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('portfolio') }}" id="edit_portfolio" placeholder="Portfolio"
                                    class="form-control" name="portfolio" type="text">
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
                                <label for="categories" class="form-label">Categories</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <select id="edit_categories" name="categories_id[]"
                                    class="js-example-basic-multiple1 form-select" multiple="multiple" data-width="100%">
                                    @foreach (getCategories() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
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
@endsection
@section('js')
    <script>
        $(function() {
            $("#add_company_form").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    contact_no: {
                        required: true,
                        minlength: 10
                    },
                },
                messages: {
                    name: {
                        required: "Please enter company name",
                        minlength: "Company name must consist of at least 3 characters "
                    },
                    contact_no: {
                        required: "Please enter mobile number",
                        minlength: "Please enter a valid mobile no"
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
            $("#edit_company_form").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    contact_no: {
                        required: true,
                        minlength: 10
                    },
                },
                messages: {
                    name: {
                        required: "Please enter company name",
                        minlength: "Company name must consist of at least 3 characters "
                    },
                    contact_no: {
                        required: "Please enter mobile number",
                        minlength: "Please enter a valid mobile no"
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
        $(function() {
            'use strict'

            if ($(".js-example-basic-multiple").length) {
                $(".js-example-basic-multiple").select2({
                    placeholder: "Select Categories",
                    dropdownParent: $("#create_category")
                });
            }
        });

        function getCompanyData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'companies/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#edit_company').modal('show');
                    $('#edit_id').val(data.data.id);
                    $('#edit_name').val(data.data.name);
                    $('#edit_portfolio').val(data.data.portfolio);
                    $('#edit_contact').val(data.data.contact_no);
                    $('#edit_categories').val(data.categories);
                    $('#edit_categories').select2({
                        placeholder: "Select Categories",
                        dropdownParent: $("#edit_company")
                    });
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
                url: '{{ route('admin.companies.status') }}',
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
        
    </script>
@endsection
