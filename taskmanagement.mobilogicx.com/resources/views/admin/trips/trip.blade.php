@extends('layouts.app')
@section('content')
    <style>
        .filter {
            width: 18px;
            height: 18px;
        }


        .trip-card {
            border: 1px solid #000;
        }

        .warhouseadd {
            cursor: pointer;
        }

        /* .warhouseadd:hover .warhouse-content {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            display: block;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        .warhouse-content {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            display: none;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            width: 200px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            height: 125px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            overflow-y: scroll;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            color: black;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            position: absolute;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            top: -25%;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            background-color: white !important;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            border:1px solid gray;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            overflow-x: hidden;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            padding:5px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */



        .custom-popover {
            position: relative;
            /* display: inline-block; */
        }

        .popover-content {
            display: none;
            position: absolute;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
            z-index: 1;
            /* max-height: 150px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        overflow-y: scroll; */
            top: -15%;
        }

        .custom-popover:hover .popover-content {
            display: block;
        }


        .border-left {
            border-left: 1px solid #000 !important;
        }

        .view-constigment th {
            background: #6571ff;
            color: white !important;
            border: 1px solid #000 !important;
            border-bottom: 0 !important;
        }

        .view-constigment td {
            border: 1px solid #000 !important;
            border-top: 0 !important;
        }

        .view-consignment-card {
            border: 1px solid gray !important;
            border-radius: 0;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('admin.consignments.index') }}" class="nav-link  tab-heading"
                    aria-selected="false">Consignments</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('trips.trip.index') }}" class="nav-link active tab-heading" aria-selected="true">Trips</a>
            </li>
        </ul>
        <a type="button" href="{{ route('trips.trip.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
            <i class="mdi mdi-plus fs-6"></i> Create Trip
        </a>
    </div>

    <div class=" d-flex justify-content-between">
        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-line-tab" data-bs-toggle="tab" data-bs-target="#upcomingtrip"
                    role="tab" aria-controls="home" aria-selected="true">Upcoming Trip</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-line-tab" data-bs-toggle="tab" data-bs-target="#activeTrip" role="tab"
                    aria-controls="profile" aria-selected="false">Active Trip</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-line-tab" data-bs-toggle="tab" data-bs-target="#CompletedTrip"
                    role="tab" aria-controls="profile" aria-selected="false">Completed Trip</a>
            </li>
        </ul>
    </div>


    <div class="tab-content mt-3" id="lineTabContent">
        <div class="tab-pane fade show active" id="upcomingtrip" role="tabpanel" aria-labelledby="home-line-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center px-3 py-2 m-0">
                    <h5 class="info-heading">Manage Trips</h5>
                </div>
                {{-- <div class="d-flex justify-content-end me-4 mt-3">
                    <p class="text-primary mb-0 fw-bold">Fillter by <i class="filter" data-feather="filter"></i></p>
                    <div class="dropdown mb-2 ms-3">
                        <button class="btn btn-link p-0 fw-bold" type="button" id="dropdownMenuButton7"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Status <i data-feather="chevron-down"></i>


                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><span
                                    class="">Pending
                                </span></a>
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><span class="">Trip
                                    assigned
                                </span></a>
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"> <span
                                    class="">Delivered
                                </span></a>
                        </div>
                    </div>
                </div> --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th class="pe-3">Trip #</th>
                                    <th class="pe-3">Origin</th>
                                    <th class="pe-3">Last Delivery</th>
                                    <th class="pe-3">Delivery Type</th>
                                    <th class="pe-3">Consignments</th>
                                    <th class="pe-3">Vehicle</th>
                                    <th class="pe-3">Start Date</th>
                                    <th class="pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trips as $item)
                                    @if ($item->status == 'pending')
                                        <tr>
                                            <td>{{ env('PrefixTrip') . $item->id }}</td>
                                            <td>{{ $item->origin_source()->name }}</td>
                                            <td>{{ $item->destination_source()->name }}</td>
                                            <td>{{ $item->delivery_type == 'multi' ? 'Multi Point' : 'Single Point' }}</td>
                                            <td>
                                                {{ $item->trip_items->pluck('consignment_id')->slice(0, 2)->implode(', ') }}

                                                @if ($item->trip_items->count() > 2)
                                                    <span class="text-primary" style="cursor:pointer;"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="{{ implode(', ',$item->trip_items->slice(2)->pluck('consignment_id')->toArray()) }}">
                                                        ({{ $item->trip_items->count() - 2 }} more)
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $item->vehicle->vehicle_number }}</td>
                                            <td>{{ $item->start_date ? dateformat($item->start_date, 'd M Y') : '-' }}</td>
                                            <td>
                                                <div class="dropdown mb-2">
                                                    <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="icon-lg text-muted pb-3px"
                                                            data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                                        <a type="button" class="dropdown-item d-flex align-items-center"
                                                            onclick="window.location.href ='{{ route('trips.trip.show', encrypt($item->id)) }}'"><i
                                                                data-feather="eye" class="icon-sm me-2"></i> <span
                                                                class="">View
                                                                Trip</span></a>
                                                        {{-- <a type="button" onclick="getTrip({{ $item->id }})"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                data-feather="edit" class="icon-sm me-2"></i>
                                                            <span class="">Edit Trip</span></a> --}}
                                                        <a type="button" data-id="{{ $item->id }}"
                                                            onclick="deleteTrip(this)"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                data-feather="trash" class="icon-sm me-2"></i>
                                                            <span class="">Delete Trip</span></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade show " id="activeTrip" role="tabpanel" aria-labelledby="home-line-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center px-3 py-2 m-0">
                    <h5 class="info-heading">Manage Trips</h5>
                </div>
                {{-- <div class="d-flex justify-content-end me-4 mt-3">
                    <p class="text-primary mb-0 fw-bold">Fillter by <i class="filter" data-feather="filter"></i></p>
                    <div class="dropdown mb-2 ms-3">
                        <button class="btn btn-link p-0 fw-bold" type="button" id="dropdownMenuButton7"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Status <i data-feather="chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><span
                                    class="">Pending
                                </span></a>
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><span
                                    class="">Trip
                                    assigned
                                </span></a>
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"> <span
                                    class="">Delivered
                                </span></a>
                        </div>
                    </div>
                </div> --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTableExample2" class="table tabel-borderd">
                            <thead>
                                <tr>
                                    <th class="pe-3">Trip #</th>
                                    <th class="pe-3">Origin</th>
                                    <th class="pe-3">Last Delivery</th>
                                    <th class="pe-3">Delivery Type</th>
                                    <th class="pe-3">Consignments</th>
                                    <th class="pe-3">Vehicle</th>
                                    <th class="pe-3">Start Date</th>
                                    <th class="pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trips as $item)
                                    @if ($item->status == 'ongoing')
                                        <tr>
                                            <td>{{ env('PrefixTrip') . $item->id }}</td>
                                            <td>{{ $item->origin_source()->name }}</td>
                                            <td>{{ $item->destination_source()->name }}</td>
                                            <td>{{ $item->delivery_type == 'multi' ? 'Multi Point' : 'Single Point' }}</td>
                                            <td>
                                                {{ $item->trip_items->pluck('consignment_id')->slice(0, 2)->implode(', ') }}

                                                @if ($item->trip_items->count() > 2)
                                                    <span class="text-primary" style="cursor:pointer;"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="{{ implode(', ',$item->trip_items->slice(2)->pluck('consignment_id')->toArray()) }}">
                                                        ({{ $item->trip_items->count() - 2 }} more)
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $item->vehicle->vehicle_number }}</td>
                                            <td>{{ $item->start_date ? dateformat($item->start_date, 'd M Y') : '-' }}</td>
                                            <td>
                                                <div class="dropdown mb-2">
                                                    <button class="btn btn-link p-0" type="button"
                                                        id="dropdownMenuButton7" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="icon-lg text-muted pb-3px"
                                                            data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                                        <a type="button"  onclick="window.location.href ='{{ route('trips.trip.show', encrypt($item->id)) }}'"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                data-feather="eye" class="icon-sm me-2"></i> <span
                                                                class="">View
                                                                Trip</span></a>
                                                        {{-- <a type="button" onclick="getTrip({{ $item->id }})"
                                                        class="dropdown-item d-flex align-items-center"><i
                                                            data-feather="edit" class="icon-sm me-2"></i>
                                                        <span class="">Edit Trip</span></a>
                                                    <a type="button" data-id="{{ $item->id }}"
                                                        onclick="deleteTrip(this)"
                                                        class="dropdown-item d-flex align-items-center"><i
                                                            data-feather="trash" class="icon-sm me-2"></i>
                                                        <span class="">Delete Trip</span></a> --}}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade show " id="CompletedTrip" role="tabpanel" aria-labelledby="home-line-tab">
            <div class="card">
                {{-- <div class="d-flex justify-content-end me-4 mt-3">
                    <p class="text-primary mb-0 fw-bold">Fillter by <i class="filter" data-feather="filter"></i></p>
                    <div class="dropdown mb-2 ms-3">
                        <button class="btn btn-link p-0 fw-bold" type="button" id="dropdownMenuButton7"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Status <i data-feather="chevron-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><span
                                    class="">Pending
                                </span></a>
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"><span
                                    class="">Trip
                                    assigned
                                </span></a>
                            <a class="dropdown-item d-flex align-items-center" href="javascript:;"> <span
                                    class="">Delivered
                                </span></a>
                        </div>
                    </div>
                </div> --}}
                <div class="card-header d-flex justify-content-between align-items-center px-3 py-2 m-0">
                    <h5 class="info-heading">Manage Trips</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTableExample3" class="table">
                            <thead>
                                <tr>
                                    <th class="pe-3">Trip #</th>
                                    <th class="pe-3">Origin</th>
                                    <th class="pe-3">Last Delivery</th>
                                    <th class="pe-3">Delivery Type</th>
                                    <th class="pe-3">Consignments</th>
                                    <th class="pe-3">Vehicle</th>
                                    <th class="pe-3">Start Date</th>
                                    <th class="pe-3">Actions</th>
                            </thead>
                            <tbody>
                                @forelse ($trips as $item)
                                    @if ($item->status == 'completed')
                                        <tr>
                                            <td>{{ env('PrefixTrip') . $item->id }}</td>
                                            <td>{{ $item->origin_source()->name }}</td>
                                            <td>{{ $item->destination_source()->name }}</td>
                                            <td>{{ $item->delivery_type == 'multi' ? 'Multi Point' : 'Single Point' }}</td>
                                            <td>
                                                {{ $item->trip_items->pluck('consignment_id')->slice(0, 2)->implode(', ') }}

                                                @if ($item->trip_items->count() > 2)
                                                    <span class="text-primary" style="cursor:pointer;"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="{{ implode(', ',$item->trip_items->slice(2)->pluck('consignment_id')->toArray()) }}">
                                                        ({{ $item->trip_items->count() - 2 }} more)
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $item->vehicle->vehicle_number }}</td>
                                            <td>{{ $item->start_date ? dateformat($item->start_date, 'd M Y') : '-' }}</td>
                                            <td>
                                                <div class="dropdown mb-2">
                                                    <button class="btn btn-link p-0" type="button"
                                                        id="dropdownMenuButton7" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="icon-lg text-muted pb-3px"
                                                            data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                                        <a type="button" onclick="window.location.href ='{{ route('trips.trip.show', encrypt($item->id)) }}'"
                                                            class="dropdown-item d-flex align-items-center"><i
                                                                data-feather="eye" class="icon-sm me-2"></i> <span class="">ViewTrip</span></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_cons" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Trip
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form class="modal-body ajax-form" id="create_trip" method="post" action="{{ route('trips.trip.store') }}">
                    @csrf
                    <div class="">
                        <div class="card border-0 pt-0">
                            <div class="card-body py-2 px-3">
                                <div class="row ">
                                    <div class="col-md-6 mb-2">
                                        <label for="deliver_to" class="form-label mb-2 ms-1">Origin Location </label>
                                        <select onchange="getConsignments(this)"
                                            class="js-example-basic-single form-select" id="originLocation"
                                            name="originLocation" data-width="100%">
                                            <option selected disabled>Select Origin Location</option>
                                            @foreach (getWarehouses() as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="deliver_to" class="form-label mb-2 ms-1">Trip Start Date
                                        </label>
                                        <div class="input-group flatpickr" id="flate_input">
                                            <input name="start_date" type="text"
                                                class="form-control placeholde-size flatpickr-input"
                                                placeholder="Select date" data-input="" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="multi-point" class="card trip-card">
                            <div class="card-body  py-2 px-3">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody id="tbody">
                                                <td class="col-4 pt-0 ps-0"><label for="deliver_to"
                                                        class="form-label mb-2 ms-1 d-block">Consignment No.</label>
                                                    <select class="js-example-basic-single form-select consignments"
                                                        id="consignment" name="consignments[0]" data-width="100%">
                                                        <option selected disabled>Select Consignment</option>
                                                    </select>
                                                </td>
                                                <td class="col-4 pe-0 pt-0 ps-2"><label for="deliver_to"
                                                        class="form-label mb-2 ms-1 d-block">Delivery Location
                                                    </label>
                                                    <input type="text" class="form-control delivery-location " readonly
                                                        value="" placeholder="Delivery Location">
                                                </td>
                                                {{-- <td class="col-3 pe-0 pt-0 ps-2"><label for="deliver_to"
                                                        class="form-label mb-2 ms-1">Delivery By Date
                                                    </label>
                                                    <input name="" type="text"
                                                        class="form-control deliveryDate" placeholder="Delivery By Date"
                                                        readonly="readonly">
                                                </td> --}}
                                                <td class="col-4 pe-0 pt-0 ps-2">
                                                    <label for="deliver_to" class="form-label mb-2 ms-1">Delivery Date
                                                    </label>
                                                    <div class="input-group flatpickr" id="consignment_delivery">
                                                        <input type="text" name="deliveryDate[0]"
                                                            class="form-control placeholde-size flatpickr-input"
                                                            placeholder="Select date" data-input="" readonly="readonly">
                                                    </div>
                                                </td>

                                                <td class="pe-0"><button id="addBtn" type="button"
                                                        class="border-0 text-primary btn  mt-4 pe-0"><i
                                                            class="mdi mdi-plus-circle fs-4"></i></button>
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body py-2 px-3">
                            <div class="row">
                                <div class="col-md-6 mb-2 ">
                                    <label for="warehouse_from" class="form-label mb-2 ms-1">Vehicle NO.</label>
                                    <select onchange="getDriver(this)" class="js-example-basic-single form-select"
                                        id="vehicleno" name="vehicleno" data-width="100%">
                                        <option selected disabled>Select Vehicle</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2 ">
                                    <label for="warehouse_from" class="form-label mb-2 ms-1">Driver Name</label>
                                    <input type="text" readonly class="form-control driver_name" value=""
                                        placeholder="Driver Name">
                                </div>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="edit_cons" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Trip Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_trip" method="post" action="{{ route('trips.trip.update', 0) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="trip_id" value="" id="trip_id">
                        <div class="row py-2 px-3 ">
                            <div class="col-md-6 mb-2">
                                <label for="originLocation">Origin Location</label>
                                <input value="" readonly class="form-control" id="edit_originLocation"
                                    name="originLocation">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="deliver_to" class="">Trip Start Date
                                </label>
                                <div class="input-group flatpickr" id="edit_flate_input">
                                    <input name="start_date" type="text"
                                        class="form-control placeholde-size flatpickr-input" placeholder="Select date"
                                        data-input="" readonly="readonly">
                                </div>
                            </div>
                        </div>

                        <div id="edit_multi-point" class="card trip-card d-none">
                            <div class="card-body  py-2 px-3">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody id="edit_tbody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body py-2 px-3">
                            <div class="row">
                                <div class="col-md-6 mb-2 ">
                                    <label for="warehouse_from" class="form-label mb-2 ms-1">Vehicle NO.</label>
                                    <select onchange="getDriver(this)" class="js-example-basic-single form-select"
                                        id="edit_vehicleno" name="vehicleno" data-width="100%">
                                        <option selected disabled>Select Vehicle</option>
                                        @foreach (getActiveVehicles() as $item)
                                            <option value="{{ $item->vehicle_id }}">{{ $item->vehicle->vehicle_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2 ">
                                    <label for="warehouse_from" class="form-label mb-2 ms-1">Driver Name</label>
                                    <input type="text" id="driver_name" readonly class="form-control driver_name"
                                        placeholder="Driver Name">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="edit_trip">Save Changes</button>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('js')
    <script>
        var rowIdx = 1;
        var select2 = 1;
        // Validations in form
        $(function() {
            $.validator.setDefaults({
                submitHandler: function(form) {
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger me-2'
                        },
                        buttonsStyling: false,
                    })
                    swalWithBootstrapButtons.fire({
                        title: 'Are you sure?',
                        text: "you want to submit form",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'me-2',
                        confirmButtonText: 'Save',
                        cancelButtonText: 'check again',
                        reverseButtons: true
                    }).then((result) => {
                        // console.log(result);
                        if (result.value) {
                            $('#spin').removeClass('d-none');
                            $('#warehouse_from').prop('disabled', false);
                            $('#deliver_to').prop('disabled', false);
                            form.submit();
                        }
                    })

                }
            });


            $(document).ready(function() {
                const rules = {
                    delivery_Type: {
                        required: true,
                    },
                    originLocation: {
                        required: true,
                    },
                };

                for (let index = 0; index < 9; index++) {
                    rules[`consignments[${index}]`] = {
                        required: true,
                        uniqueSelection: true,
                    }
                };
                for (let index = 0; index < 9; index++) {
                    rules[`deliveryDate[${index}]`] = {
                        required: true,
                    }
                };
                rules.vehicleno = {
                    required: true,
                };

                rules.start_date = {
                    required: true,
                };
                $("#create_trip").validate({
                    rules: rules,
                    messages: {
                        delivery_Type: {
                            required: "Select Delivery Type"
                        },
                        originLocation: {
                            required: "Select Origin Location"
                        },
                        vehicleno: {
                            required: "Select Vehicle NO.",
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
                $.validator.addMethod('uniqueSelection', function(value, element) {
                    var selectedValues = [];
                    $('.consignments').not(element).each(function() {
                        selectedValues.push($(this).val());
                    });

                    var currentValue = $(element).val();

                    return $.inArray(currentValue, selectedValues) === -1;
                }, "Consignments cannot be the same");
            });
        });
        // add and remove tr 
        $(function() {
            $('#tbody').on('click', '#addBtn', function() {
                if (rowIdx < 10) {
                    var parent = $('#tbody tr:last td:first');
                    $('#tbody').append(`<tr>
                                                <td class="col-4 pt-0 ps-0"><label for="deliver_to"
                                                        class="form-label mb-2 ms-1 d-block">Consignment No.</label>
                                                    <select class="js-example-basic-single1 form-select consignments"
                                                        id="consignment${select2}" name="consignments[${rowIdx}]" data-width="100%">
                                                        <option selected disabled>Select Consignment</option>
                                                        
                                                        <option data-delivery_location="" value=""></option>
                                                        
                                                    </select>
                                                </td>
                                                <td class="col-4 pe-0 pt-0 ps-2"><label for="deliver_to"
                                                        class="form-label mb-2 ms-1 d-block">Delivery Location
                                                    </label>
                                                    <input type="text" class="form-control delivery-location " readonly value=""
                                                        placeholder="Delivery Location">
                                                </td>
                                                <td class="col-4 pe-0 pt-0 ps-2"><label for="deliver_to"
                                                        class="form-label mb-2 ms-1">Delivery Date
                                                    </label>
                                                    <div class="input-group flatpickr" id="consignment_delivery${rowIdx}">
                                                        <input name="deliveryDate[${rowIdx}]" type="text"
                                                            class="form-control placeholde-size flatpickr-input"
                                                            placeholder="Select date" data-input="" readonly="readonly">
                                                    </div>
                                                </td>
                                                <td class="pe-0">
                                                <button type="button" class="text-danger remove  border-0  btn  mt-4 pe-0"><i
                                                        class="mdi mdi-minus-circle fs-4 "></i></button>
                                            </td>
                                            </tr>
                                            `);
                    flatpickr("#consignment_delivery" + rowIdx, {
                        wrap: true,
                        dateFormat: "d-M-Y h:i",
                        // defaultDate: "today",
                        minDate: "today",
                        enableTime: true,
                        position: "above"

                    });
                    var sel_product = $(`#consignment${select2}`).empty();
                    $.each(parent.find('option'), function(key, val) {
                        var option = $(this);
                        if (val.value == "") {
                            sel_product.append(
                                $('<option></option>').text(val.text)
                                .attr("value", val.value)
                                .attr("disabled", true).attr("selected", true));
                        } else {
                            // if (val.value != parent.find(":selected").val()) {
                            sel_product.append(
                                $('<option></option>').text(val.text)
                                .attr("value", val.value)
                                .data("delivery_location", option.data('delivery_location'))
                                .data("delivery_date", option.data('delivery_date'))
                            );
                            // }
                        }
                    })
                    $("#consignment" + select2).select2({
                        dropdownParent: $("#add_cons")
                    });
                    select2++;
                    rowIdx++;
                }
            });
            $('#tbody').on('click', '.remove', function() {
                // Removing the current row.
                $(this).closest('tr').remove();

                // Decreasing the total number of rows by 1.
                rowIdx--;
            });
        });
        // flatepicker and select2 call
        $(function() {
            'use strict'
            if ($(".js-example-basic-single").length) {
                $(".js-example-basic-single").select2({
                    dropdownParent: $("#add_cons")
                });
            }
            if ($('#consignment_delivery').length) {
                const today = new Date();
                flatpickr("#consignment_delivery", {
                    wrap: true,
                    dateFormat: "d-M-Y H:i",
                    // defaultDate: "today",
                    minDate: "today",
                    enableTime: true,
                    // dateFormat: "H:i",
                });
            }
            if ($('#flate_input').length) {
                const today = new Date();
                // Calculate the minimum date as 1 month before today
                const minDate = new Date();
                minDate.setMonth(today.getMonth() - 1);
                flatpickr("#flate_input", {
                    wrap: true,
                    dateFormat: "d-M-Y",
                    defaultDate: "today",
                    minDate: "today",
                });
            }
        });
        // onchnage on consignments
        $(function(ready) {
            $(document).on('change', '.consignments', function() {
                $this = $(this);
                var deliveryLocation = $this.find(':selected').data('delivery_location')
                var deliveryDate = $this.find(':selected').data('delivery_date')
                // get inputs where we append
                var input = $this.parent().parent().find('.delivery-location');
                var inputDate = $this.parent().parent().find('.deliveryDate');
                // appending value
                input.val(deliveryLocation);
                inputDate.val(deliveryDate);
            });
        });
        // get consignments when select origin location
        function getConsignments(element) {
            var id = element.value;
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'getConsignement/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    // console.log(data);
                    var select = $('.consignments');
                    if (data.status == 200) {
                        select.empty();
                        select.parent().parent().find('.delivery-location').val("");


                        var option = $('<option>');
                        option.text("Select Consignment");
                        option.val("");
                        option.attr("disabled", true);
                        option.attr("selected", true);
                        select.append(option);

                        data.consignments.forEach(function(consignment) {

                            // Create a new <option> element
                            var option = $('<option>');
                            // Set the text and class for the option
                            option.text(consignment.id);
                            option.val(consignment.id); // Set the value attribute
                            option.data('delivery_location', consignment.warehouse ? consignment
                                .warehouse.name : consignment.depo.name
                            ); // Set the data-delivery_location attribute
                            option.data('delivery_date', consignment
                                .date); // Set the data-delivery_location attribute
                            // Append the option to the select element
                            select.append(option);
                        });
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }
        // delete trip
        function deleteTrip(button) {
            var $button = $(button);
            var $row = $button.closest('tr'); // Find the closest tr element to the button
            var id = $button.attr('data-id'); // Assuming you have set the data-id attribute for each row
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger me-2'
                },
                buttonsStyling: false,
            })
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this trip!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'me-2',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: 'DELETE', // Assuming the method is DELETE, change it accordingly
                        dataType: 'JSON',
                        url: 'trip/' + id,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            $('#spin').removeClass('d-none');
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                $row.remove();
                                // Handle success response
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
                        error: function(xhr, status, error) {

                            // Handle error response
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: false,
                            });
                            Toast.fire({
                                icon: 'error',
                                title: "Oops Something went wrong!"
                            })
                        },
                        complete: function() {
                            $('#spin').addClass('d-none');
                        }
                    });
                }
            });
        }

        // onchange on vehicle
        function getDriver(object) {
            var $object = $(object);
            var id = $object.val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'getDriver/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    if (data.status == 200) {
                        var $driverNameInput = $object.closest('.row').find('.driver_name');
                        $driverNameInput.val(data.driver.user.name);
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }
    </script>
    {{-- edit trip --}}
    <script>
        // get trip data
        function getTrip(id) {
            $.ajax({
                type: 'GET',
                // dataType: 'JSON',
                url: 'trip/' + id + '/edit',
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#edit_cons').modal('show');
                    $('#trip_id').val(data.data.id);

                    $('#edit_originLocation').val(data.data.warehouse.name ?? 'Unknown');
                    appendToMultiPointTable(data.data.trip_items, data.available_consignements);

                    // Set date for min date with minimum date check
                    let startDate = new Date(data.data.start_date);
                    const today = new Date();
                    if (startDate < today) {
                        minDate = startDate;
                    } else {
                        minDate = today;
                    }

                    // set date for start date
                    flatpickr("#edit_flate_input", {
                        wrap: true,
                        dateFormat: "d M Y",
                        defaultDate: data.data.start_date,
                        minDate: minDate
                    });

                    $('#edit_vehicleno').select2({
                        dropdownParent: $("#edit_cons")
                    });
                    appendOptionsToVehicleEdit(data.vehicles);
                    $('#edit_vehicleno').val(data.data.vehicle_id).trigger('change');
                    $('#driver_name').val(data.data.user.name);

                },

                error: function(xhr, status, error) {
                    console.error(error);
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }

        function appendToMultiPointTable(tripItems, available_consignements) {
            // Remove the d-none class from an element
            // $("#edit_single-point").addClass("d-none");
            $("#edit_multi-point").removeClass("d-none");
            const multiPointTable = $("#edit_tbody");

            // Clear the existing rows (if any)
            multiPointTable.empty();

            tripItems.forEach((item, index) => {
                const newRow = `
                                <tr>
                                    <td class="col-4 pt-0 ps-0">
                                        <label for="deliver_to" class="form-label mb-2 ms-1 d-block">Consignment No.</label>
                                        <select class="js-example-basic-single form-select consignments" id="cons${item.id}" name="old_consignments[${item.id}]" data-width="100%">
                                            <option disabled selected >Select Consignment</option>
                                            <option value="${item.consignement_id}" data-delivery_location="${item.consignements.warehouse ? item.consignements.warehouse.name :item.consignements.depo.name }" data-delivery_date="${item.consignements.date}" selected >${item.consignement_id}</option>
                                        </select>
                                    </td>
                                    <td class="col-4 pe-0 pt-0 ps-2">
                                        <label for="deliver_to" class="form-label mb-2 ms-1 d-block">Delivery Location</label>
                                        <input type="text" class="form-control delivery-location" readonly value="${item.consignements.warehouse ? item.consignements.warehouse.name :item.consignements.depo.name }" placeholder="Delivery Location">
                                    </td>
                                    <td class="col-4 pe-0 pt-0 ps-2"><label for="deliver_to"
                                                        class="form-label mb-2 ms-1">Delivery Date
                                                    </label>
                                                    <div class="input-group flatpickr" id="old_consignment_delivery${item.id}">
                                                        <input name="oldDeliveryDate[${item.id}]" type="text"
                                                            class="form-control placeholde-size flatpickr-input"
                                                            placeholder="Select date" data-input="" readonly="readonly">
                                                    </div>
                                    </td>
                                        ${index === 0 ? '<td class="pe-0"><button id="edit_addBtn" type="button" class="border-0 text-primary btn mt-4 pe-0"><i class="mdi mdi-plus-circle fs-4"></i></button></td>' : '<td class="pe-0"><button id="removeBtn" type="button" class="border-0 text-danger remove btn mt-4 pe-0"><i class="mdi mdi-minus-circle fs-4"></i></button></td>'}
                                </tr>`;
                multiPointTable.append(newRow);

                flatpickr("#old_consignment_delivery" + item.id, {
                    wrap: true,
                    dateFormat: "d-M-Y H:i",
                    defaultDate: item.last_delivery_date,
                    // minDate: "today",
                    enableTime: true,
                    position: "above"

                });
                // populate the select element in this row with options.
                const select = $('#cons' + item.id);
                available_consignements.forEach(option => {
                    const deliveryLocation = option.warehouse ? option.warehouse.name : option.depo.name;
                    const deliveryDate = option.date;
                    const optionValue = option.id;
                    // Append the option with data attributes
                    select.append(
                        `<option data-delivery_location="${deliveryLocation}" data-delivery_date="${deliveryDate}" value="${optionValue}">${optionValue}</option>`
                    );
                });
                // Call Select2 on the select element
                $(select).select2({
                    dropdownParent: $("#edit_cons")
                });
            });
        }

        var edit_rowIdx = 0;
        var edit_select2 = 0;
        // add and remove tr 
        $(function() {
            $('#edit_tbody').on('click', '#edit_addBtn', function() {
                if (edit_rowIdx < 10) {
                    var parent = $('#edit_tbody tr:last td:first');

                    $('#edit_tbody').append(`<tr>
                                    <td class="col-4 pt-0 ps-0"><label for="deliver_to"
                                            class="form-label mb-2 ms-1 d-block">Consignment No.</label>
                                        <select class="js-example-basic-single1 form-select consignments"
                                            id="consignment${edit_select2}" name="consignments[${edit_rowIdx}]" data-width="100%">
                                            <option selected disabled>Select Consignment</option>
                                            
                                            <option data-delivery_location="" value=""></option>
                                            
                                        </select>
                                    </td>
                                    <td class="col-4 pe-0 pt-0 ps-2"><label for="deliver_to"
                                            class="form-label mb-2 ms-1 d-block">Delivery Location
                                        </label>
                                        <input type="text" class="form-control delivery-location " readonly value=""
                                            placeholder="Delivery Location">
                                    </td>
                                
                                    <td class="col-4 pe-0 pt-0 ps-2"><label for="deliver_to"
                                                        class="form-label mb-2 ms-1">Delivery Date
                                                    </label>
                                                    <div class="input-group flatpickr" id="edit_consignment_delivery${edit_rowIdx}">
                                                        <input name="deliveryDate[${edit_rowIdx}]" type="text"
                                                            class="form-control placeholde-size flatpickr-input"
                                                            placeholder="Select date" data-input="" readonly="readonly">
                                                    </div>
                                    </td>
                                    <td class="pe-0">
                                    <button id="remove_btn" type="button" class="text-danger removeNew  border-0  btn  mt-4 pe-0"><i
                                            class="mdi mdi-minus-circle fs-4 "></i></button>
                                </td>
                                </tr>
                                `);
                    flatpickr("#edit_consignment_delivery" + edit_rowIdx, {
                        wrap: true,
                        dateFormat: "d-M-Y h:i",
                        // defaultDate: "today",
                        minDate: "today",
                        enableTime: true,
                        position: "above"

                    });
                    var sel_product = $(`#consignment${edit_select2}`).empty();
                    $.each(parent.find('option'), function(key, val) {
                        var option = $(this);
                        if (val.text == "Select Consignment") {
                            sel_product.append(
                                $('<option></option>').text(val.text)
                                .attr("value", val.value)
                                .attr("disabled", true).attr("selected", true));
                        } else {
                            // if (val.value != parent.find(":selected").val()) {
                            sel_product.append(
                                $('<option></option>').text(val.text)
                                .attr("value", val.value)
                                .data("delivery_location", option.data('delivery_location'))
                                .data("delivery_date", option.data('delivery_date'))
                            );
                            // }
                        }
                    })
                    $("#consignment" + edit_select2).select2({
                        dropdownParent: $("#edit_cons")
                    });
                    edit_select2++;
                    edit_rowIdx++;
                }
            });
            $('#edit_tbody').on('click', '.remove', function() {
                // Removing the current row.
                $(this).closest('tr').remove();
            });
            // remove for new appenddata
            $('#edit_tbody').on('click', '.removeNew', function() {
                // Removing the current row.
                $(this).closest('tr').remove();
                // Decreasing the total number of rows by 1.
                edit_rowIdx--;
            });

        });

        // Validations in form
        $(function() {
            $.validator.setDefaults({
                submitHandler: function(form) {
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger me-2'
                        },
                        buttonsStyling: false,
                    })
                    swalWithBootstrapButtons.fire({
                        title: 'Are you sure?',
                        text: "you want to submit form",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'me-2',
                        confirmButtonText: 'Save',
                        cancelButtonText: 'check again',
                        reverseButtons: true
                    }).then((result) => {

                        if (result.value) {
                            $('#spin').removeClass('d-none');
                            $('#warehouse_from').prop('disabled', false);
                            $('#deliver_to').prop('disabled', false);
                            form.submit();
                        }
                    })

                }
            });


            $(document).ready(function() {
                const rules = {
                    delivery_Type: {
                        required: true,
                    },
                    originLocation: {
                        required: true,
                    },
                };

                for (let index = 0; index < 9; index++) {
                    rules[`consignments[${index}]`] = {
                        required: true,
                        uniqueSelection: true,
                    }
                };
                for (let index = 0; index < 9; index++) {
                    rules[`deliveryDate[${index}]`] = {
                        required: true,
                    }
                };
                rules.vehicleno = {
                    required: true,
                };
                rules.start_date = {
                    required: true,
                };
                $("#edit_trip").validate({
                    rules: rules,
                    messages: {
                        delivery_Type: {
                            required: "Select Delivery Type"
                        },
                        originLocation: {
                            required: "Select Origin Location"
                        },
                        vehicleno: {
                            required: "Select Vehicle NO.",
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
                        if ($(element).hasClass("select2-hidden-accessible")) {
                            $(element).parent().find('.select2-container').addClass(
                                "is-invalid").removeClass("is-valid");
                        }
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-invalid").removeClass("is-valid");
                        }
                    },
                    unhighlight: function(element, errorClass) {
                        if ($(element).hasClass("select2-hidden-accessible")) {
                            $(element).parent().find('.select2-container').addClass("is-valid")
                                .removeClass("is-invalid");

                        }
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-valid").removeClass("is-invalid");
                        }
                    },
                    ignore: [], // This line allows validation for hidden elements
                });
                $.validator.addMethod('uniqueSelection', function(value, element) {
                    var selectedValues = [];
                    $('.consignments').not(element).each(function() {
                        selectedValues.push($(this).val());
                    });

                    var currentValue = $(element).val();

                    return $.inArray(currentValue, selectedValues) === -1;
                }, "Consignments cannot be the same");
            });
        });
    </script>

    {{-- onchnage on start and end date --}}
    <script>
        $(document).ready(function() {
            $('#add_cons').on('change', 'input[name="start_date"], input[name^="deliveryDate"]', function() {
                const startDateValue = $('#add_cons input[name="start_date"]').val();

                let endDateValue = null;
                $('#add_cons input[name^="deliveryDate"]').each(function(index, element) {
                    const deliveryDateValue = $(element).val();
                    if (!endDateValue || deliveryDateValue > endDateValue) {
                        endDateValue = deliveryDateValue;
                    }
                });

                if (startDateValue && endDateValue) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: '{{ route('trips.getVehicles') }}',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "startDate": startDateValue,
                            'endDate': endDateValue

                        },
                        beforeSend: function() {
                            $('#spin').removeClass('d-none');
                        },
                        success: function(data) {
                            appendOptionsToVehicle(data.vehicles);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        },
                        complete: function() {
                            $('#spin').addClass('d-none');
                        }
                    });
                }
            });
        });

        // Function to append options to the select element
        function appendOptionsToVehicle(vehicles) {
            let selectElement = $('#vehicleno'); // Assuming you have a select element with the id 'vehicleno'
            selectElement.empty(); // Clear existing options
            // append a disabled option
            selectElement.append($('<option>', {
                value: "",
                text: 'Select Vehicle',
                disabled: true,
                selected: true
            }));
            // Append each option to the select element
            vehicles.forEach(function(vehicle) {
                selectElement.append($('<option>', {
                    value: vehicle.vehicle_id,
                    text: vehicle.vehicle.vehicle_number
                }));
            });
        }

        // for edit
        $(document).ready(function() {
            $('#edit_cons').on('change', 'input[name="start_date"], input[name^="oldDeliveryDate"]', function() {
                const startDateValue = $('#edit_cons input[name="start_date"]').val();
                const tripId = $('#edit_cons input[name="trip_id"]').val();

                let endDateValue = null;
                $('#edit_cons input[name^="deliveryDate"], #edit_cons input[name^="oldDeliveryDate"]').each(
                    function(index, element) {
                        const deliveryDateValue = $(element).val();
                        if (!endDateValue || deliveryDateValue > endDateValue) {
                            endDateValue = deliveryDateValue;
                        }
                    });

                if (startDateValue && endDateValue) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: '{{ route('trips.getVehicles') }}',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "startDate": startDateValue,
                            'endDate': endDateValue,
                            'trip_id': tripId
                        },
                        beforeSend: function() {
                            $('#spin').removeClass('d-none');
                        },
                        success: function(data) {
                            appendOptionsToVehicleEdit(data.vehicles);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        },
                        complete: function() {
                            $('#spin').addClass('d-none');
                        }
                    });
                }
            });
        });

        // Function to append options to the select element
        function appendOptionsToVehicleEdit(vehicles) {
            let selectElement = $('#edit_vehicleno'); // Assuming you have a select element with the id 'vehicleno'
            selectElement.empty(); // Clear existing options
            // append a disabled option
            selectElement.append($('<option>', {
                value: "",
                text: 'Select Vehicle',
                disabled: true,
                selected: true
            }));
            // Append each option to the select element
            vehicles.forEach(function(vehicle) {
                selectElement.append($('<option>', {
                    value: vehicle.vehicle_id,
                    text: vehicle.vehicle.vehicle_number
                }));
            });
        }
    </script>
@endsection
