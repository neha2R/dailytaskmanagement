@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Vehicle Master
            </h4>
        </div>
    </div>
    <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-line-tab" data-bs-toggle="tab" data-bs-target="#vehicle_mf" role="tab"
                aria-controls="home" aria-selected="true">Vehicle Manufacturer</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-line-tab" data-bs-toggle="tab" data-bs-target="#vehicle_model" role="tab"
                aria-controls="profile" aria-selected="false">Vehicle Model</a>
        </li>
    </ul>
    <div class="tab-content mt-3" id="lineTabContent">
        <div class="tab-pane fade show active" id="vehicle_mf" role="tabpanel" aria-labelledby="home-line-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#create_vehicle_mf" data-bs-whatever="@getbootstrap">Add Manufacturer</button>
                </div>
                <div class="card-body">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Manufacturer Name</th>
                                <th>Sort Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicle_mf as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ucwords( $item->name )}}</td>
                                    <td>{{ ucwords($item->sort_name) }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item_status" type="checkbox" role="switch"
                                                data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                                {{ $item->is_active ? 'checked' : '' }} />
                                        </div>
                                    </td>
                                    <td>
                                        <button onclick="getVMFData({{ $item->id }})" type="button"
                                            class="btn btn-primary btn-icon btn-xs"><i data-feather="edit"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="vehicle_model" role="tabpanel" aria-labelledby="profile-line-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#create_vehicle_model" data-bs-whatever="@getbootstrap">Add Model</button>
                </div>
                <div class="card-body">
                    <table id="dataTableExample1" class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Modal Name</th>
                                <th>Manufacturer Name</th>
                                <th>Fuel Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicle_models as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ucwords($item->name) }}</td>
                                    <td>{{ ucwords($item->manufacturer->sort_name) }}</td>
                                    <td>{{ $item->fule_type }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item_status1" type="checkbox" role="switch"
                                                data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                                {{ $item->is_active ? 'checked' : '' }} />
                                        </div>
                                    </td>
                                    <td>
                                        <button onclick="getVMData({{ $item->id }})" type="button"
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
    <div class="modal fade" id="create_vehicle_mf" tabindex="-1" aria-labelledby="create_vehicle_mf" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Vehicle Manufacturer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="create_vMf_form" method="post" action="{{ route('vehicle.vehicleMaster.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-md-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="name" placeholder="Manufacturer Name"
                                    class="form-control" name="name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-md-3">
                                <label for="sort_name" class="form-label">Sort Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('sort_name') }}" id="sort_name"
                                    placeholder="Manufacturer Sort Name" class="form-control" name="sort_name"
                                    type="text">
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
    <div class="modal fade" id="edit_vehicle_mf" tabindex="-1" aria-labelledby="edit_vehicle_mf" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Vehicle Manufacturer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_vMf_form" method="post" action="{{ route('vehicle.vehicleMaster.update','1') }}">
                    <div class="modal-body">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" value="" id="id" name="id">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-md-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="edit_name" placeholder="Manufacturer Name"
                                    class="form-control" name="name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-md-3">
                                <label for="sort_name" class="form-label">Sort Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('sort_name') }}" id="edit_sort_name"
                                    placeholder="Manufacturer Sort Name" class="form-control" name="sort_name"
                                    type="text">
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
    <div class="modal fade" id="create_vehicle_model" tabindex="-1" aria-labelledby="create_vehicle_model"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Vehicle Model
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="create_vModel_form" method="post" action="{{ route('vehicle.storeVehicleModel') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Model Name</label>
                                <input value="{{ old('name') }}" id="model_name" placeholder="Model Name"
                                    class="form-control" name="name" type="text">
                            </div>
                            <div class="col-md-6 mb-2 form-group">
                                <label for="name" class="form-label mb-2 ms-1">Manufacturer</label>
                                <select class="js-example-basic-single form-select" id="manufacturer"
                                    name="manufacturer_id" data-width="100%">
                                    <option selected disabled>Select Manufacturer</option>
                                    @foreach (getVehicleManufacturer() as $item)
                                        <option value="{{ $item->id }}">{{ $item->sort_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Batteries</label>
                                <input value="{{ old('NoOfbatteries') }}" id="batteries" placeholder="No Of Batteries"
                                    class="form-control" name="NoOfbatteries" type="number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Tyres</label>
                                <input value="{{ old('NoOfTyres') }}" id="tyres" placeholder="No Of Tyres"
                                    class="form-control" name="NoOfTyres" type="number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Tyre Type</label>
                                <select class="js-example-basic-single form-select" id="tyre_type" name="tyre_type"
                                    data-width="100%">
                                    <option selected disabled>Select Tyre Type</option>
                                    <option value="radial">Radial</option>
                                    <option value="nylon">Nylon</option>
                                    <option value="mining">Mining</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Fuel Type</label>
                                <select class="js-example-basic-single form-select" id="fule_type" name="fule_type"
                                    data-width="100%">
                                    <option selected disabled>Select Fuel Type</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Petrol">Petrol</option>
                                    <option value="Electric">Electric Vehicle</option>
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
    <div class="modal fade" id="edit_vehicle_model" tabindex="-1" aria-labelledby="edit_vehicle_model"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Vehicle Model
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_vModel_form" method="post" action="{{ route('vehicle.updateVehicleModel') }}">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" value="" id="model_id" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Model Name</label>
                                <input value="{{ old('name') }}" id="edit_model_name" placeholder="Model Name"
                                    class="form-control" name="name" type="text">
                            </div>
                            <div class="col-md-6 mb-2 form-group">
                                <label for="name" class="form-label mb-2 ms-1">Manufacturer</label>
                                <select class="js-example-basic-single form-select" id="edit_manufacturer"
                                    name="manufacturer_id" data-width="100%">
                                    <option selected disabled>Select Manufacturer</option>
                                    @foreach (getVehicleManufacturer() as $item)
                                        <option value="{{ $item->id }}">{{ $item->sort_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Batteries</label>
                                <input value="{{ old('NoOfbatteries') }}" id="edit_batteries" placeholder="No Of Batteries"
                                    class="form-control" name="NoOfbatteries" type="number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Tyres</label>
                                <input value="{{ old('NoOfTyres') }}" id="edit_tyres" placeholder="No Of Tyres"
                                    class="form-control" name="NoOfTyres" type="number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Tyre Type</label>
                                <select class="js-example-basic-single form-select" id="edit_tyre_type" name="tyre_type"
                                    data-width="100%">
                                    <option selected disabled>Select Tyre Type</option>
                                    <option value="radial">Radial</option>
                                    <option value="nylon">Nylon</option>
                                    <option value="mining">Mining</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Fuel Type</label>
                                <select class="js-example-basic-single form-select" id="edit_fule_type" name="fule_type"
                                    data-width="100%">
                                    <option selected disabled>Select Fuel Type</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Petrol">Petrol</option>
                                    <option value="Electric">Electric Vehicle</option>
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
            'use strict'
            if ($("#tyre_type").length) {
                $("#tyre_type").select2({
                    dropdownParent: $("#create_vehicle_model"),
                    placeholder: "Select juniors"
                });
            }
            if ($("#fule_type").length) {
                $("#fule_type").select2({
                    dropdownParent: $("#create_vehicle_model"),
                    placeholder: "Select juniors"
                });
            }
            if ($("#manufacturer").length) {
                $("#manufacturer").select2({
                    placeholder: "Select a state",
                    dropdownParent: $("#create_vehicle_model")
                });
            }
        });
        $(function() {
            'use strict';

            $.validator.setDefaults({
                submitHandler: function(form) {
                    form.sumbit();
                }
            });
            $(function() {
                $("#create_vMf_form").validate({
                    rules: {
                        name: {
                            required: true,

                        },
                        sort_name: {
                            required: true,

                        }
                    },
                    messages: {
                        name: {
                            required: "Please enter manufacturer name",
                        },
                        sort_name: {
                            required: "Please enter short name",
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
                $("#create_vModel_form").validate({
                    rules: {
                        name: {
                            required: true,
                        },
                        manufacturer_id: {
                            required: true,
                        },
                        NoOfbatteries: {
                            required: true,
                        },
                        NoOfTyres: {
                            required: true,
                        },
                        fule_type: {
                            required: true,
                        },
                    },
                    messages: {
                        name: {
                            required: "Please enter model name",
                        },
                        manufacturer_id: {
                            required: "Please select manufacturer",
                        },
                        NoOfbatteries: {
                            required: "Please enter batteries number",
                        },
                        NoOfTyres: {
                            required: "Please enter tyre number",
                        },
                        fule_type: {
                            required: "Please select fuel type",
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
                        } else if (element.hasClass('select2-hidden-accessible')) {
                            element.parent().find('.select2-container').addClass(
                                'form-control p-0 is-invalid');
                            error.appendTo(element.parent());
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
                $("#edit_vMf_form").validate({
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
                $("#edit_vModel_form").validate({
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

        function getVMFData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'vehicleMaster/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#edit_vehicle_mf').modal('show');
                    $('#id').val(data.data.id);
                    $('#edit_name').val(data.data.name);
                    $('#edit_sort_name').val(data.data.sort_name);
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }

        function getVMData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'vehicleMaster/showVehicleModel/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#edit_vehicle_model').modal('show');
                    $('#model_id').val(data.data.id);
                    $('#edit_model_name').val(data.data.name);
                    $('#edit_manufacturer').val(data.data.manufacturer_id).select2({
                        dropdownParent: $("#edit_vehicle_model")
                    });
                    $('#edit_batteries').val(data.data.no_of_batteries);
                    $('#edit_tyres').val(data.data.no_of_tyres);
                    $('#edit_tyre_type').val(data.data.tyre_type).select2({
                        dropdownParent: $("#edit_vehicle_model")
                    });
                    $('#edit_fule_type').val(data.data.fule_type).select2({
                        dropdownParent: $("#edit_vehicle_model")
                    });
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
                url: '{{ route('vehicle.changeVMFstatus') }}',
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
        $('.item_status1').change(function() {
            var mode = $(this).prop('checked');
            var id = $(this).data("id");

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: '{{ route('vehicle.changeVMstatus') }}',
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
