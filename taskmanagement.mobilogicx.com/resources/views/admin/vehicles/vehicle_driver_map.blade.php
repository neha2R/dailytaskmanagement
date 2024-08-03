@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Vehicle Maping/Unmaping
            </h4>
        </div>
        {{-- <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create_assignment"
                data-bs-whatever="@getbootstrap">Map Vehicle</button>
        </div> --}}
    </div>
    <div class="card">
        <div class="card-body">
            <table id="dataTableExample" class="table">
                <thead>
                    <tr>
                        <th>Sr no</th>
                        <th>Vehicle Number</th>
                        <th>Driver Name</th>
                        <th>Model</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $item)
                        <tr>
                            <td class="center">{{ $key + 1 }}</td>
                            <td>{{ $item->vehicle_number ?? '-' }}</td>
                            <td>{{ $item->user_vehicle ? $item->user_vehicle->user->name : '-' }}</td>
                            {{-- <td>{{ dateformat($item->created_at, 'd M Y h:i A') }}</td>
                            <td>{{ dateformat($item->updated_at, 'd M Y h:i A') }}</td> --}}

                            <td>{{ ucwords($item->model->name ?? '-') }}</td>
                            <td>{{ ucwords($item->vehicle_body_type) }}</td>
                            <td>
                                @if ($item->user_vehicle)
                                    <span class="badge bg-success">Mapped</span>
                                @else
                                    <span class="badge bg-danger">Not Mapped</span>
                                @endif
                            </td>
                            <td>
                                {{-- <button onclick="show_assignment({{ $item->id }})" type="button"
                                    class="btn btn-primary btn-icon btn-xs"><i data-feather="eye"></i>
                                </button> --}}
                                <div class="dropdown mb-2">
                                    <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                        <a type="button" onclick="showHistory({{ $item->id }})"
                                            class="dropdown-item d-flex align-items-center"><i data-feather="eye"
                                                class="icon-sm me-2"></i> <span class="">View</span></a>

                                        @if ($item->user_vehicle)
                                            <a type="button" onclick="unmap({{ $item->id }})"
                                                class="dropdown-item d-flex align-items-center"><i data-feather="truck"
                                                    class="icon-sm me-2"></i>
                                                <span class="">Unmap</span></a>
                                        @else
                                            <a type="button" onclick="mapModel({{ $item->id }})"
                                                class="dropdown-item d-flex align-items-center"><i data-feather="truck"
                                                    class="icon-sm me-2"></i>
                                                <span class="">Map</span></a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="show_assignment" tabindex="-1" aria-labelledby="show_assignment" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">History of Map/Unmap</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive pt-0">
                        <table class="table table-hover" id="history">
                            <thead>
                                <th>Vehicle No</th>
                                <th>Action</th>
                                <th>date</th>
                                <th>Driver</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create_assignment" tabindex="-1" aria-labelledby="add_department" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map Driver with vehicle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_assignement_form" method="post" action="{{ route('vehicle.driver.store') }}">
                    <div class="modal-body">
                        @csrf
                        {{-- <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="gender" class="form-label">Department</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <select class="form-select" name="vehicle" id="vehicle">
                                    <option selected disabled>Select Vehicle</option>
                                    @foreach ($available_vehicles as $item)
                                        <option value="{{ $item->id }}">{{ $item->vehicle_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="gender" class="form-label">Select Driver</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input type="hidden" name="vehicle_id" id="vehicle_id">
                                <select class="js-example-basic-single " style="width: 100%;" name="driver" id="driver">
                                    <option selected disabled>Select Driver</option>
                                    @foreach ($available_drivers as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
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
                $(".js-example-basic-single").select2({
                    placeholder: "Select juniors",
                    dropdownParent: $("#create_assignment")
                });
            }
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
                        driver: {
                            required: true
                        }
                    },
                    messages: {
                        driver: {
                            required: "Please select a driver",
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
                        if ($(element).hasClass("select2-hidden-accessible")) {
                            $(element).on('select2:select', function() {
                                $(element).parent().find('.select2-container').addClass(
                                    "is-valid").removeClass("is-invalid");
                            });
                            $(element).on('select2:unselect', function() {
                                $(element).parent().find('.select2-container').addClass(
                                    "is-invalid").removeClass("is-valid");
                            });
                            console.log($(element).parent().find('.invalid-feedback'));
                        }
                    },
                    unhighlight: function(element, errorClass) {
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-valid").removeClass("is-invalid");
                        }
                        // Add the following code to handle select2 changes
                        if ($(element).hasClass("select2-hidden-accessible")) {
                            $(element).on('select2:select', function() {
                                $(element).parent().find('.select2-container').addClass(
                                    "is-valid").removeClass("is-invalid");
                            });
                            $(element).on('select2:unselect', function() {
                                $(element).parent().find('.select2-container').addClass(
                                    "is-invalid").removeClass("is-valid");
                            });
                        }
                    },
                    ignore: [], // This line allows validation for hidden elements

                });
            });
        });


        // show add model
        function mapModel(id) {
            if (id) {
                $('#create_assignment').modal('show');
                $('#vehicle_id').val(id);
            }
        }

        function unmap(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger me-2'
                },
                buttonsStyling: false,
            })
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "you want to unmap this vehicle ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'me-2',
                confirmButtonText: 'Yes, Unmap it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    if (id) {
                        $('#spin').removeClass('d-none');
                        var url = "{{ route('vehicle.driver.destroy', ['driver' => ':id']) }}";
                        url = url.replace(':id', id);
                        window.location.href = url;
                    }
                }
            });
        }

        function showHistory(id) {
            var url = "{{ route('vehicle.historyMapUnmap', ['id' => ':id']) }}";
            url = url.replace(':id', id);

            // Use getData function for AJAX request
            getData(url, function(data) {
                console.log(data);
                if (data.data.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No history found for this vehicle.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
                if (data.status == 200 && data.data.length > 0) {
                    $('#show_assignment').modal("show");
                    if (data.data.length) {
                        $('#history tbody').empty();
                        $.each(data.data, function(key, value) {
                            $('#history tbody').append(`
                            <tr>
                                <td>${value.vehicle.vehicle_number}</td>
                                <td>
                                    ${
                                        value.type === 'map'
                                            ? `<span class="badge bg-primary xs">Map</span>`
                                            : value.type === 'unmap'
                                            ? `<span class="badge bg-secondary xs">Unmap</span>`
                                            : ''
                                    }
                                </td>
                                <td>${value.date}</td>
                                <td>${value.user.name}</td>
                            </tr>
                            `);
                        });
                    } else {

                    }
                }
            });
        }
    </script>
@endsection
