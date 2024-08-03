@extends('layouts.app')
@section('content')
    <div class="tab-container">
        <div class="tab-menu">
            <ul>
                <li><a href="#" class=" btn btn-outline-secondary btn-sm tab-a active-a" data-id="tab1">Categories</a>
                </li>
                <li><a href="#" class="btn btn-outline-secondary btn-sm tab-a" data-id="tab2">Sub Categories</a></li>
            </ul>
        </div>
        <div class="tab tab-active" data-id="tab1">
            <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
                <div>
                    <h4 class="mb-3 mb-md-0">Categories
                    </h4>
                </div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#create_category" data-bs-whatever="@getbootstrap">Add Category</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item_status" type="checkbox" role="switch"
                                                data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                                {{ $item->is_active ? 'checked' : '' }} />
                                        </div>
                                    </td>
                                    <td>
                                        <button onclick="getCategoryData({{ $item->id }})" type="button"
                                            class="btn btn-primary btn-icon btn-xs"><i data-feather="edit"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab" data-id="tab2">
            <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
                <div>
                    <h4 class="mb-3 mb-md-0">Sub Categories
                    </h4>
                </div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#create_subcategory" data-bs-whatever="@getbootstrap"> Add Sub Category</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <table id="dataTableExample1" class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Name</th>
                                <th>Category Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sub_categories as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->category->name ?? '-' }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item_status" type="checkbox" role="switch"
                                                data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                                {{ $item->is_active ? 'checked' : '' }} />
                                        </div>
                                    </td>
                                    <td>
                                        <button onclick="getSubCategoryData({{ $item->id }})" type="button"
                                            class="btn btn-primary btn-icon btn-xs"><i data-feather="edit"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create_category" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_category_form" method="post" action="{{ route('admin.category.store') }}">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create_subcategory" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Sub Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_sub_category_form" method="post" action="{{ route('admin.category.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="sub_category_name"
                                    placeholder="Sub Category Name" class="form-control" name="name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Category</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <select class="form-select" name="category_id" id="category_id">
                                    <option selected disabled>Select Category</option>
                                    @foreach (getCategories() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
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
    <div class="modal fade" id="edit_category" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_category_form" method="post" action="{{ route('admin.category.update', '1') }}">
                    @method('PATCH')
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="edit_category_name" placeholder="Category Name"
                                    class="form-control" name="name" type="text">
                                <input type="hidden" id="edit_category_id" name="id" value="">
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
    <div class="modal fade" id="edit_sub_category" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Sub Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_sub_category_form" method="post" action="{{ route('admin.category.update', '1') }}">
                    <div class="modal-body">
                        @csrf
                        @method('PATCH')
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="edit_sub_category_name"
                                    placeholder="Sub Category Name" class="form-control" name="name" type="text">
                                <input type="hidden" id="edit_sub_category_id" name="id" value="">

                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Category</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <select class="form-select" name="category_id" id="edit_sub_category_cat_id">
                                    <option selected disabled>Select Category</option>
                                    @foreach (getCategories() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
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
@endsection
@section('js')
    <script>
        $(function() {
            'use strict';

            $.validator.setDefaults({
                submitHandler: function(form) {
                    form.sumbit();
                }
            });
            $(function() {
                $("#add_category_form").validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3
                        },
                    },
                    messages: {
                        name: {
                            required: "Please enter category name",
                            minlength: "Category name must consist of at least 3 characters "
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
                $("#add_sub_category_form").validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3
                        },
                        category_id: {
                            required: true,
                        },
                    },
                    messages: {
                        name: {
                            required: "Please enter sub category name",
                            minlength: "Sub category must consist of at least 3 characters "

                        },
                        category_id: {
                            required: "Please select category",
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
                $("#edit_category_form").validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3
                        },
                    },
                    messages: {
                        name: {
                            required: "Please enter category name",
                            minlength: "Category name must consist of at least 3 characters "
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
                $("#edit_sub_category_form").validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3
                        },
                        category_id: {
                            required: true,
                        },
                    },
                    messages: {
                        name: {
                            required: "Please enter sub category name",
                            minlength: "Sub category must consist of at least 3 characters "

                        },
                        category_id: {
                            required: "Please select category",
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
        });

        function getCategoryData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'category/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#edit_category').modal('show');
                    $('#edit_category_id').val(data.data.id);
                    $('#edit_category_name').val(data.data.name);
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }

        function getSubCategoryData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'category/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#edit_sub_category').modal('show');
                    $('#edit_sub_category_id').val(data.data.id);
                    $('#edit_sub_category_name').val(data.data.name);
                    $('#edit_sub_category_cat_id').val(data.data.parent_id);
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
                url: '{{ route('admin.category.status') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "status": mode,
                    'id': id
                },
                beforeSend : function(){
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
                complete : function(){
                    $('#spin').addClass('d-none');
                }
            });
        });
    </script>
@endsection
