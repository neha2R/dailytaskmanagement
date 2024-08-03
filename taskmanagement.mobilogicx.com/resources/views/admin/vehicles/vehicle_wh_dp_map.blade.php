@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Vehicle Mapping/Unmapping with Warehouse and Depot
            </h4>
        </div>
        <div class="d-flex justify-content-end">
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Map Vehicles
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulk_maping_warehouse"
                        data-bs-whatever="@getbootstrap">Map with Warehouse </button>
                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulk_maping_depo"
                        data-bs-whatever="@getbootstrap">Map with Depot </button>
                </div>
            </div>
            {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create_assignment"
                data-bs-whatever="@getbootstrap">Map Vehicles</button> --}}
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="dataTableExample" class="table">
                <thead>
                    <tr>
                        <th>Sr no</th>
                        <th>Vehicle Number</th>
                        <th>Driver Name</th>
                        <th>Mapped with</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vehicles as $key => $item)
                        <tr>
                            <td class="center">{{ $key + 1 }}</td>
                            <td>{{ $item->vehicle_number ?? '-' }}</td>
                            <td>{{ $item->user_vehicle ? $item->user_vehicle->user->name : '-' }}</td>
                            <td>
                                @if ($item->maped_warehouse_depo->last() && $item->maped_warehouse_depo->last()->deassigned_at === null)
                                    @if ($item->maped_warehouse_depo->last()->warehouse_id)
                                        {{ $item->maped_warehouse_depo->last()->warehouse->name }}
                                    @else
                                        {{ $item->maped_warehouse_depo->last()->depo->name }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($item->maped_warehouse_depo->last() && $item->maped_warehouse_depo->last()->deassigned_at === null)
                                    <span class="badge bg-success">Mapped</span>
                                @else
                                    <span class="badge bg-danger">Not Mapped</span>
                                @endif
                            </td>
                            <td>

                                <div class="dropdown mb-2">
                                    <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                        <a type="button" onclick="showHistory({{ $item->id }})"
                                            class="dropdown-item d-flex align-items-center"><i data-feather="eye"
                                                class="icon-sm me-2"></i> <span class="">View</span></a>

                                        @if ($item->maped_warehouse_depo->last() && $item->maped_warehouse_depo->last()->deassigned_at === null)
                                            <a type="button" onclick="unmap({{ $item->id }})"
                                                class="dropdown-item d-flex align-items-center"><i data-feather="truck"
                                                    class="icon-sm me-2"></i>
                                                <span class="">Unmap</span></a>
                                        @else
                                            <a type="button"
                                                onclick="openWhModel('{{ $item->vehicle_number }}',{{ $item->id }})"
                                                class="dropdown-item d-flex align-items-center"><i data-feather="truck"
                                                    class="icon-sm me-2"></i>
                                                <span class="">Map With Warehouse</span></a>
                                            <a type="button"
                                                onclick="openDpModel('{{ $item->vehicle_number }}',{{ $item->id }})"
                                                class="dropdown-item d-flex align-items-center"><i data-feather="truck"
                                                    class="icon-sm me-2"></i>
                                                <span class="">Map With Depo</span></a>
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
                        <div class="table-responsive pt-0">
                            <table class="table table-hover" id="history">
                                <thead>
                                    <th>Vehicle No</th>
                                    <th>Action</th>
                                    <th>Warehouse/Depo</th>
                                    <th>date</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="wh_maping" tabindex="-1" aria-labelledby="wh_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map Vehicle With Warehouse
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div id="maping-container">
                    <form id="wh_maping_form" method="post" action="{{ route('vehicle.map-warehouse-dep.store') }}">
                        <div class="modal-body">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Vehicle No.</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <input type="hidden" name="vc_id" id="vc_id" value="">
                                    <input type="text" id="vehicle_no" readonly class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Warehouse</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single" style="width: 100%;" name="warehouse_id"
                                        id="warehouse_id">
                                        <option selected disabled>Select Warehouse</option>
                                        @foreach ($warehouses as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
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
    </div>
    <div class="modal fade" id="dp_maping" tabindex="-1" aria-labelledby="dp_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map Vehicle With Depo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div id="maping-container">
                    <form id="dp_maping_form" method="post" action="{{ route('vehicle.map-warehouse-dep.store') }}">
                        <div class="modal-body">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Vehicle No.</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <input type="hidden" id="vc_id1" value="" name="vc_id">
                                    <input type="text" id="vehicle_no1" readonly class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Depot</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single " style="width: 100%;" name="depo_id"
                                        id="depo_id">
                                        <option selected disabled>Select Depot</option>
                                        @foreach ($depos as $item)
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
    </div>
    <div class="modal fade" id="bulk_maping_warehouse" tabindex="-1" aria-labelledby="bulk_maping_warehouse"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map Vehicles With Warehouse
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div id="maping-container">
                    <form id="dp_maping_form" method="post" action="{{ route('vehicle.bulk_store') }}">
                        <div class="modal-body">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Warehouse</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single " style="width: 100%;" name="warehouse_id"
                                        id="bulk_warehouse_id">
                                        <option selected disabled>Select Warehouse</option>
                                        @foreach ($warehouses as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Vehicles</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-multiple " name="vehicles[]" id="warehouse_vehicles"
                                        multiple="multiple" data-width="100%">
                                        {{-- <option selected disabled>Select vehicles</option> --}}
                                        @foreach ($avl_vehicles as $item)
                                            <option value="{{ $item->id }}">{{ $item->vehicle_number }}</option>
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
    </div>
    <div class="modal fade" id="bulk_maping_depo" tabindex="-1" aria-labelledby="bulk_maping_depo" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map Vehicles With Depo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div id="maping-container">
                    <form id="dp_maping_form" method="post" action="{{ route('vehicle.bulk_store') }}">
                        <div class="modal-body">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Depo</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single " style="width: 100%;" name="depo_id"
                                        id="bulk_depo_id">
                                        <option selected disabled>Select Depo</option>
                                        @foreach ($depos as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Vehicles</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-multiple " multiple="multiple" data-width="100%"
                                        name="vehicles[]" id="depo_vehicles">
                                        @foreach ($avl_vehicles as $item)
                                            <option value="{{ $item->id }}">{{ $item->vehicle_number }}</option>
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
    </div>
@endsection
@section('js')
    <script>
        // select 2 and flatepicker
        $(function() {
            'use strict';
            if ($("#warehouse_id").length) {
                $("#warehouse_id").select2({
                    dropdownParent: $("#wh_maping")
                });
            }
            if ($("#depo_id").length) {
                $("#depo_id").select2({
                    dropdownParent: $("#dp_maping")
                });
            }
            if ($("#bulk_warehouse_id").length) {
                $("#bulk_warehouse_id").select2({
                    dropdownParent: $("#bulk_maping_warehouse")
                });
            }
            if ($("#bulk_depo_id").length) {
                $("#bulk_depo_id").select2({
                    dropdownParent: $("#bulk_maping_depo")
                });
            }
            if ($("#warehouse_vehicles").length) {
                $("#warehouse_vehicles").select2({
                    dropdownParent: $("#bulk_maping_warehouse")
                });
            }
            if ($("#depo_vehicles").length) {
                $("#depo_vehicles").select2({
                    dropdownParent: $("#bulk_maping_depo")
                });
            }
        });

        function openWhModel(vehicle_no, vc_id) {
            var model = $('#wh_maping');
            if (vehicle_no && vc_id) {
                $('#vehicle_no').val(vehicle_no);
                $('#vc_id').val(vc_id);
                model.modal('show');
            }
        }

        function openDpModel(vehicle_no, vc_id) {
            var model = $('#dp_maping');
            if (vehicle_no && vc_id) {
                $('#vehicle_no1').val(vehicle_no);
                $('#vc_id1').val(vc_id);
                model.modal('show');
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
                        var url = "{{ route('vehicle.map-warehouse-dep.show', ['map_warehouse_dep' => ':id']) }}";
                        url = url.replace(':id', id);
                        window.location.href = url;
                    }
                }
            });
        }

        function showHistory(id) {

            var url = "{{ route('vehicle.historyMapUnmapWarehouseDepo', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: url,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
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
                        console.log(data);
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
                                    <td>${value.warehouse ? value.warehouse.name : value.depo.name}</td>
                                    <td>${value.date}</td>
                                </tr>
                                `);
                            })
                        } else {

                        }

                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }
    </script>
@endsection
