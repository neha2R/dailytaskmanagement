@extends('layouts.app')
@section('content')
    <style>
        .v_details li {
            font-size: 18px;
            font-weight: 500;
        }

        .scrollable-tbody {
            max-height: 320px;
            overflow-y: auto;
            display: block;
        }

        .card-header {
            border-bottom: 1px solid #6571ff;
        }

        .info-heading {
            padding-top: 13px;
        }

        .partsname {
            width: 340px;
            margin-right: -11px;
        }

        .filter {
            width: 18px;
            height: 18px;
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

        .list-unstyled li {
            padding-bottom: 5px;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div class="header-section">
            <h4 class="mb-3 mb-md-0">Vehicle Details</h4>
        </div>
        <div class="action-buttons">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="documentDropdown" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="mdi mdi-file-plus-outline"></span> Add Document
                </button>
                <div class="dropdown-menu" aria-labelledby="documentDropdown">
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#insuranceModal">
                        <span class="mdi mdi-file-document-outline"></span> Vehicle Insurance</button>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#puccModal">
                        <span class="mdi mdi-file-document-outline"></span> PUC Certificate</button>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#fitnessModal">
                        <span class="mdi mdi-file-document-outline"></span> Fitness Certificate</button>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#taxModal">
                        <span class="mdi mdi-file-document-outline"></span> Tax Document</button>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                        data-bs-target="#nationalPermitModal">
                        <span class="mdi mdi-file-document-outline"></span> National Permit</button>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#statePermitModal">
                        <span class="mdi mdi-file-document-outline"></span> State Permit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="row">
                <div class="mt-3">
                    <ul class="d-flex list-unstyled text-center v_heading">
                        <li class="text-capitalize text-secondary col">Vechile No</li>
                        <li class="text-capitalize text-secondary col">Vehicle Type</li>
                        {{-- <li class="text-capitalize text-secondary col">Supervisor</li> --}}
                        <li class="text-capitalize text-secondary col">Driver</li>
                    </ul>
                    <ul class="d-flex list-unstyled text-center v_details">
                        <li class="text-capitalize  text-black-50 col">{{ $vehicle->vehicle_number }}</li>
                        <li class="text-capitalize  text-black-50 col">{{ $vehicle->vehicle_body_type }}</li>
                        <li class="text-capitalize  text-black-50 col">
                            {{ $vehicle->user_vehicle ? $vehicle->user_vehicle->user->name : '-' }}</li>
                        {{-- <li class="text-capitalize  text-black-50 col">Test driver</li> --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-1">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-line nav-justified" id="lineTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="trip-line-tab" data-bs-toggle="tab" data-bs-target="#trip" role="tab"
                        aria-controls="home" aria-selected="true">Trips</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="driver-line-tab" data-bs-toggle="tab" data-bs-target="#driver" role="tab"
                        aria-controls="profile" aria-selected="false">Driver</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" id="card-line-tab" data-bs-toggle="tab" data-bs-target="#card" role="tab"
                        aria-controls="contact" aria-selected="false">Cards</a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link" id="tyres-line-tab" data-bs-toggle="tab" data-bs-target="#tyres" role="tab"
                        aria-controls="contact" aria-selected="false">Tyres</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link" id="service-line-tab" data-bs-toggle="tab" data-bs-target="#service" role="tab"
                        aria-controls="service" aria-selected="false">Service</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" id="expenses-line-tab" data-bs-toggle="tab" data-bs-target="#expenses"
                        role="tab" aria-controls="service" aria-selected="false">Expenses</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link" id="expenses-line-tab" data-bs-toggle="tab" data-bs-target="#documents"
                        role="tab" aria-controls="documents" aria-selected="false">Documents</a>
                </li>
            </ul>
            <div class="tab-content mt-3" id="lineTabContent">
                <div class="tab-pane fade show active" id="trip" role="tabpanel" aria-labelledby="trip-line-tab">
                    <div class="table-responsive">
                        <table id="custom_datatable" class="table datatableForTabs">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Trip No</th>
                                    <th>Origin</th>
                                    <th>Destination</th>
                                    <th>Start date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trips as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ env('PrefixTrip') . $item->id }}</td>
                                        <td>{{ $item->origin_source()->name ?? '-' }}</td>
                                        <td>{{ $item->destination_source()->name ?? '-' }}</td>
                                        <td>
                                            @if ($item->start_date)
                                                {{ dateFormat($item->start_date, 'd M Y') }}
                                            @else
                                                <span class="badge bg-danger xs">Not available</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status == 'pending')
                                                <span class="badge bg-warning xs">Pending</span>
                                            @elseif($item->status == 'ongoing')
                                                <span class="badge bg-primary xs">Ongoing</span>
                                            @elseif($item->status == 'completed')
                                                <span class="badge bg-success xs">Completed</span>
                                            @endif
                                        </td>
                                        <td><button class="btn btn-primary btn-xs">View Trip</button></td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="tab-pane fade" id="driver" role="tabpanel" aria-labelledby="driver-line-tab">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Driver Name</th>
                                        {{-- <th>License no</th> --}}
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Duration(Month)</th>
                                        <th>Completed Trips</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($drivers as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item['driver_name'] ?? '-' }}</td>
                                            {{-- <td>{{ $item['license_no'] ?? '-' }}</td> --}}
                                            <td>
                                                @if (isset($item['from']))
                                                    {{ dateFormat($item['from'], 'd M Y H:i') }}
                                                @else
                                                    <span class="badge bg-danger xs">Not available</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($item['to']))
                                                    {{ dateFormat($item['to'], 'd M Y H:i') }}
                                                @else
                                                    <span class="badge bg-success xs">Currently Mapped</span>
                                                @endif
                                            </td>
                                            <td>{{ $item['duration'] ?? '-' }}</td>
                                            <td>{{ $item['completed_trips'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-line-tab">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-xs mb-3" data-bs-toggle="modal"
                            data-bs-target="#add_service" data-bs-whatever="@getbootstrap"> <i
                                class="mdi mdi-plus"></i>Add
                            Service</button>
                    </div>
                    <div class="table-responsive">
                        <table id="dataTableExample3" class="table datatableForTabs">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Service ID</th>
                                    <th>Service type</th>
                                    <th>Time Gap</th>
                                    <th>KM Run</th>
                                    <th>Date</th>
                                    <th>Expense</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($vehicle->services as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ env('PrefixService') . $item->id }}</td>
                                        <td>{{ $item->serviceType }}</td>
                                        <td>{{ $item->timeGap }}</td>
                                        <td>{{ $item->kmRun }}</td>
                                        <td>{{ dateformat($item->serviceDate, 'd M Y') }}</td>
                                        <td>{{ $item->totalAmount }}</td>
                                        <td><button class="btn btn-primary btn-xs"
                                                onclick="viewServiceDetails('{{ route('vehicle.serviceDetails', $item->id) }}')">View</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="expenses" role="tabpanel" aria-labelledby="expenses-line-tab">
                    <div class="table-responsive">
                        <table id="dataTableExample4" class="table datatableForTabs">
                            <thead>
                                <tr>
                                    <th>Expense Id</th>
                                    <th>Expense Type</th>
                                    <th>Expense Date</th>
                                    <th>Expense Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>121452</td>
                                    <td>Fuel Expense</td>
                                    <td>21 Jul 2023</td>
                                    <td>5000</td>
                                    <td><button class="btn btn-primary btn-xs">View</span></td>
                                </tr>
                                <tr>
                                    <td>121452</td>
                                    <td>Fuel Expense</td>
                                    <td>21 Jul 2023</td>
                                    <td>5000</td>
                                    <td><button class="btn btn-primary btn-xs">View</span></td>
                                </tr>
                                <tr>
                                    <td>121452</td>
                                    <td>Fuel Expense</td>
                                    <td>21 Jul 2023</td>
                                    <td>5000</td>
                                    <td><button class="btn btn-primary btn-xs">View</span></td>
                                </tr>
                                <tr>
                                    <td>121452</td>
                                    <td>Fuel Expense</td>
                                    <td>21 Jul 2023</td>
                                    <td>5000</td>
                                    <td><button class="btn btn-primary btn-xs">View</span></td>
                                </tr>
                                {{-- @foreach ($vehicles as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ $item->vehicle_number }}</td>
                                    <td>{{ ucwords($item->model->name ?? '-') }}</td>
                                    <td>{{ ucwords($item->vehicle_body_type) }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item_status" type="checkbox" role="switch"
                                                data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                                {{ $item->is_active ? 'checked' : '' }} />
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{route('vehicle.vehicles.edit',$item->id)}}" class="btn btn-primary btn-icon btn-xs"><i data-feather="eye"></i></a>
                                        <button onclick="getVehicle({{ $item->id }})" type="button"
                                            class="btn btn-primary btn-icon btn-xs"><i data-feather="edit"></i></button>
                                    </td>
                                </tr>
                            @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="expenses-line-tab">
                    <div class="table-responsive">
                        <table id="dataTableExample1" class="table table-striped datatableForTabs">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Document Name</th>
                                    <th>Document No</th>
                                    <th>Valid From</th>
                                    <th>Valid To</th>
                                    <th>Issued By</th>
                                    <th>Days Remaining</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $key => $item)
                                    <tr>
                                        @php
                                            $today = \Carbon\Carbon::now(); // Get the current date and time
                                            $validTo = \Carbon\Carbon::parse($item->valid_to);
                                            $remainingDays = $today->diffInDays($validTo, false);
                                            $remainingHours = $today->diffInHours($validTo, false);
                                        @endphp
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->document_name }}</td>
                                        <td>{{ $item->document_number }}</td>
                                        <td>{{ dateformat($item->valid_from, 'd M Y') }}</td>
                                        <td>{{ dateformat($item->valid_to, 'd M Y') }}</td>
                                        <td>{{ $item->issuer_name }}</td>
                                        <td>

                                            @if ($remainingDays > 0)
                                                @if ($remainingDays > 14)
                                                    <span class="badge bg-success">Valid</span>
                                                @elseif($remainingDays <= 14 && $remainingDays > 7)
                                                    <span class="badge bg-info">Expires in
                                                        {{ $remainingDays }} days</span>
                                                @elseif($remainingDays <= 7)
                                                    <span class="badge bg-warning">Expires in
                                                        {{ $remainingDays }} days</span>
                                                @endif
                                            @elseif ($remainingDays == 0)
                                                @if ($remainingHours > 0)
                                                    <span class="badge bg-danger">Expires Today</span>
                                                @else
                                                    <span class="badge bg-danger">Expired</span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                        </td>


                                        {{-- <td>
                                            <span class="{{ $badgeClass }}">
                                                {{ $remainingDaysFormatted }}
                                            </span>
                                        </td> --}}
                                        <td>
                                            <div class="dropdown mb-2">
                                                <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                                    <a type="button"
                                                        onclick="viewDocuments('{{ route('vehicle.showVehicleDocument', $item->id) }}')"
                                                        class="dropdown-item d-flex align-items-center"><i
                                                            data-feather="eye" class="icon-sm me-2"></i> <span
                                                            class="">View</span></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_service" tabindex="-1" aria-labelledby="add_service" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Service</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="service-sechdule-form" action="{{ route('vehicle.vehicles.storeServiceDetails') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row gx-5 mb-3">
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                                <div class="col-12">
                                    <label for="vehicleNumber" class="form-label">Vehicle Number</label>
                                    <input type="text" class="form-control" value="{{ $vehicle->vehicle_number }}"
                                        readonly id="vehicleNumber" name="vehicleNumber" placeholder="Vehicle Number">
                                </div>
                                <div class="col-6">
                                    <label for="serviceDate" class="form-label">Service Date</label>
                                    <div class="input-group flatpickr" id="serviceDate">
                                        <input type="text" name="serviceDate"
                                            class="form-control placeholde-size flatpickr-input" placeholder="Select date"
                                            data-input="" readonly="readonly" id="serviceDateInput" name="serviceDate">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="odometerReading" class="form-label">Odometer Reading</label>
                                    <input type="text" class="form-control" id="odometerReading"
                                        name="odometerReading" placeholder="Odometer Reading">
                                </div>
                                <div class="col-6">
                                    <label for="kmRun" class="form-label">Km Run</label>
                                    <input type="text" class="form-control" id="kmRun" name="kmRun"
                                        placeholder="Km Run">
                                </div>
                                <div class="col-6">
                                    <label for="timeGap" class="form-label">Time Gap (In months)</label>
                                    <input type="text" class="form-control" id="timeGap" name="timeGap"
                                        placeholder="Time Gap" readonly>
                                </div>
                            </div>
                            <h5>Service Description</h5>
                            <div class="row gx-5 mt-1">
                                <div class="col-6">
                                    <label for="serviceType" class="form-label">Service Type</label>
                                    <select style="width: 100%;" name="serviceType" id="serviceType"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select Service Type</option>
                                        <option value="General Service">General Service</option>
                                        <option value="Repair">Repair</option>

                                        {{-- @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach --}}
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="serviceAmount" class="form-label">Amount</label>
                                    <input class="form-control" id="serviceAmount" name="serviceAmount"
                                        placeholder="Amount" type="number" min="1" readonly>
                                </div>
                                <div class="col-6">
                                    <label for="oilChange" class="form-label">Oil Change</label>
                                    <select class="form-select " id="oilChange" name="oilChange" data-width="100%">
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="oilChangeAmount" class="form-label">Amount</label>
                                    <input class="form-control" id="oilChangeAmount" name="oilChangeAmount"
                                        type="number" min="1" placeholder="Amount" readonly>
                                </div>
                                <div class="col-6">
                                    <label for="sparePartsChange" class="form-label">Spare Parts Change</label>
                                    <select class="form-select " id="sparePartsChange" name="sparePartsChange"
                                        data-width="100%">
                                        <option value="0" selected>No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="parts-container d-none container row gx-5">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody id="tbody">
                                                <td class="ps-0 pe-0 ">
                                                    <label for="sparePartsName" class="form-label mb-2 ms-1 d-block">Spare
                                                        Parts Name</label>
                                                    <input type="text" class="form-control partsname"
                                                        placeholder="Part Name" id="sparePartsName"
                                                        name="sparePartsName[0]">
                                                </td>
                                                <td class="ps-0">
                                                    <label for="sparePartsAmount"
                                                        class="form-label mb-2 ms-1 d-block">Amount</label>
                                                    <input type="number" min="1" class="form-control"
                                                        placeholder="Amount" id="sparePartsAmount"
                                                        name="sparePartsAmount[0]">
                                                </td>
                                                <td class="ps-0 pe-0" style="width: 30px;">
                                                    <div class="text-end">
                                                        <button id="addBtn" type="button"
                                                            class="border-0 text-primary btn mt-4 ps-0 pe-0 pt-3"><i
                                                                class="mdi mdi-plus-circle fs-4"></i></button>
                                                    </div>
                                                </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-6 offset-6">
                                    <label for="sparePartsChange" class="form-label">Total Amount</label>
                                    <input type="text" class="form-control" id="totalAmount" name="totalAmount"
                                        placeholder="Total Amount" readonly>
                                </div>
                                <div class="col-12">
                                    {{-- <label for="sparePartsChange" class="form-label">Spare Parts Change</label> --}}
                                    <input type="file" id="document" name="document" data-height="100"
                                        data-show-remove="false">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveServiceDetails" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-labelledby="serviceDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceDetailsModalLabel">Service Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body" id="serviceDetailsContent">
                            <!-- Data will be dynamically populated here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="insuranceModal" tabindex="-1" aria-labelledby="insuranceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insuranceModalLabel">Add Insurance Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="insurance_add_document_form" method="post" enctype="multipart/form-data"
                    action="{{ route('vehicle.storeInsurance') }}">
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="insurance_policy_number" class="form-label">
                                    <i class="mdi mdi-numeric"></i> Policy Number
                                </label>
                                <input id="insurance_policy_number" class="form-control" type="text"
                                    name="policy_number" placeholder="Enter Policy Number" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="insurance_policy_type" class="form-label">
                                    <i class="mdi mdi-card-account-details"></i> Policy Type
                                </label>
                                <select class="js-example-basic-single form-select" id="insurance_policy_type"
                                    name="policy_type" data-width="100%" required>
                                    <option selected disabled>Select Policy Type</option>
                                    @foreach (getPolicyTypes() as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="insurance_valid_from" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Valid From
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date" data-input=""
                                        readonly="readonly" id="insurance_valid_fromInput" name="valid_from">

                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="insurance_valid_to" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Valid To
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date" data-input=""
                                        readonly="readonly" id="insurance_valid_toInput" name="valid_to">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="insurance_registration_date" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Registration Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date" data-input=""
                                        readonly="readonly" id="insurance_registration_dateInput"
                                        name="registration_date">

                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="insurance_issuer_name" class="form-label">
                                    <i class="mdi mdi-account"></i> Issuer Name
                                </label>
                                <input id="insurance_issuer_name" class="form-control" type="text" name="issuer_name"
                                    placeholder="Enter Issuer Name">
                            </div>

                            <div class="col-12 mb-3">
                                <input class="dropify" type="file" id="insurance_document" name="document"
                                    data-height="100" data-show-remove="false" placeholder="Choose Insurance Document">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="puccModal" tabindex="-1" aria-labelledby="puccModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Pollution Under Control Certificate (PUCC) Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="add_pucc_document_form" method="post" enctype="multipart/form-data"
                    action="{{ route('vehicle.storePUCC') }}">
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pucc_document_number" class="form-label">
                                    <i class="mdi mdi-numeric"></i> PUCC Number
                                </label>
                                <input id="pucc_document_number" class="form-control" type="text"
                                    name="document_number" required placeholder="Enter PUCC Number">

                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pucc_registration_date" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Registration Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select Registration Date"
                                        data-input="" readonly="readonly" id="pucc_registration_dateInput"
                                        name="registration_date">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pucc_valid_from" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Valid From
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select Valid From Date"
                                        data-input="" readonly="readonly" id="pucc_valid_fromInput" name="valid_from">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pucc_valid_to" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Valid To
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select Valid To Date"
                                        data-input="" readonly="readonly" id="pucc_valid_toInput" name="valid_to">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pucc_issuer_name" class="form-label">
                                    <i class="mdi mdi-account"></i> Issuer Name
                                </label>
                                <input id="pucc_issuer_name" class="form-control" type="text" name="issuer_name"
                                    placeholder="Enter Issuer Name">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="pucc_test_date" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Test Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select Test Date"
                                        data-input="" readonly="readonly" id="pucc_test_dateInput" name="test_date">
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <input class="dropify" type="file" id="pucc_insurance_doc" name="document"
                                    data-height="100" data-show-remove="false" placeholder="Choose PUCC Document">
                            </div>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fitnessModal" tabindex="-1" aria-labelledby="fitnessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Fitness Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>

                <form id="add_fitness_document_form" method="post" enctype="multipart/form-data"
                    action="{{ route('vehicle.storeFitness') }}">
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="fitness_application_number" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-numeric"></i> Application Number
                                </label>
                                <input id="fitness_application_number" class="form-control" type="text"
                                    name="application_number" required placeholder="Enter Application Number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="fitness_receipt_number" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-numeric"></i> Receipt Number
                                </label>
                                <input id="fitness_receipt_number" class="form-control" type="text"
                                    name="receipt_number" required placeholder="Enter Receipt Number">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="fitness_valid_from" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Valid From
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select Valid From Date"
                                        data-input="" readonly="readonly" id="fitness_valid_fromInput"
                                        name="valid_from">
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="fitness_valid_to" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Valid To
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select Valid To Date"
                                        data-input="" readonly="readonly" id="fitness_valid_toInput" name="valid_to">
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="fitness_issuer_name" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-account"></i> Issuer Name
                                </label>
                                <input id="fitness_issuer_name" class="form-control" type="text" name="issuer_name"
                                    placeholder="Enter Issuer Name">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="fitness_inspected_on" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Inspected On
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select Inspected On Date"
                                        data-input="" readonly="readonly" id="fitness_inspected_onInput"
                                        name="inspected_on">
                                </div>
                            </div>

                            <div class="col-12">
                                <input type="file" class="dropify" id="fitness_document" name="document"
                                    data-height="100" data-show-remove="false" placeholder="Choose Fitness Document">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="taxModal" tabindex="-1" aria-labelledby="taxModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Tax Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>

                <form id="add_tax_document_form" method="post" enctype="multipart/form-data"
                    action="{{ route('vehicle.storeTax') }}">
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="tax_document_number" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-numeric"></i> Document Number
                                </label>
                                <input id="tax_document_number" class="form-control" type="text"
                                    name="document_number" required placeholder="Enter Document Number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="tax_registration_date" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Registration Date
                                </label>
                                <input id="tax_registration_date" class="form-control" type="datetime-local"
                                    name="registration_date" placeholder="Select Registration Date">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="tax_valid_from" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Valid From
                                </label>
                                <input id="tax_valid_from" class="form-control" type="datetime-local" name="valid_from"
                                    placeholder="Select Valid From Date">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="tax_valid_to" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Valid To
                                </label>
                                <input id="tax_valid_to" class="form-control" type="datetime-local" name="valid_to"
                                    placeholder="Select Valid To Date">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="tax_issuer_name" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-account"></i> Issuer Name
                                </label>
                                <input id="tax_issuer_name" class="form-control" type="text" name="issuer_name"
                                    placeholder="Enter Issuer Name">
                            </div>
                            <div class="col-12">
                                <input type="file" id="tax_document" name="document" data-height="100"
                                    data-show-remove="false" placeholder="Choose Tax Document">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="nationalPermitModal" tabindex="-1" aria-labelledby="nationalPermitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add National Permit Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>

                <form id="add_national_permit_document_form" method="post" enctype="multipart/form-data"
                    action="{{ route('vehicle.storeNationalPermit') }}">
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="national_permit_number" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-numeric"></i> NP Number
                                </label>
                                <input id="national_permit_number" class="form-control" type="text"
                                    name="document_number" required placeholder="Enter NP Number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="national_permit_category" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-numeric"></i> Permit Category
                                </label>
                                <select class="js-example-basic-single form-select" id="national_permit_category"
                                    name="permit_category" data-width="100%" required placeholder="Select Category">
                                    <option selected disabled>Select Category</option>
                                    @foreach (getNPcategories() as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="national_permit_valid_from" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Valid From
                                </label>
                                <input id="national_permit_valid_from" class="form-control" type="datetime-local"
                                    name="valid_from" placeholder="Select Valid From Date">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="national_permit_valid_to" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Valid To
                                </label>
                                <input id="national_permit_valid_to" class="form-control" type="datetime-local"
                                    name="valid_to" placeholder="Select Valid To Date">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="national_permit_issuer_name" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-account"></i> Issuer Name
                                </label>
                                <input id="national_permit_issuer_name" class="form-control" type="text"
                                    name="issuer_name" placeholder="Enter Issuer Name">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="national_permit_registration_date" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Registration Date
                                </label>
                                <input id="national_permit_registration_date" class="form-control" type="datetime-local"
                                    name="registration_date" placeholder="Select Registration Date">
                            </div>

                            <div class="col-12">

                                <input type="file" id="national_permit_document" name="document" data-height="100"
                                    data-show-remove="false" placeholder="Choose National Permit Document">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statePermitModal" tabindex="-1" aria-labelledby="statePermitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add State Permit Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>

                <form id="add_state_permit_document_form" method="post" enctype="multipart/form-data"
                    action="{{ route('vehicle.storeStatePermit') }}">
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="state_permit_number" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-numeric"></i> Permit Number
                                </label>
                                <input id="state_permit_number" class="form-control" type="text"
                                    name="document_number" required placeholder="Enter Permit Number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="permit_holder_name" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-numeric"></i> Name Of Permit Holder
                                </label>
                                <input id="permit_holder_name" class="form-control" type="text"
                                    name="permit_holder_name" required placeholder="Enter Name Of Permit Holder">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="state_permit_valid_from" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Valid From
                                </label>
                                <input id="state_permit_valid_from" class="form-control" type="datetime-local"
                                    name="valid_from" placeholder="Select Valid From Date">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="state_permit_valid_to" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Valid To
                                </label>
                                <input id="state_permit_valid_to" class="form-control" type="datetime-local"
                                    name="valid_to" placeholder="Select Valid To Date">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="state_permit_issuer_name" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-account"></i> Issuer Name
                                </label>
                                <input id="state_permit_issuer_name" class="form-control" type="text"
                                    name="issuer_name" placeholder="Enter Issuer Name">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="state_permit_state" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-numeric"></i> State / UT where permit is valid
                                </label>
                                <select class="js-example-basic-single form-select" id="state_permit_state"
                                    name="permit_state" data-width="100%" required placeholder="Select State/UT">
                                    <option selected disabled>Select State/UT</option>
                                    @foreach (getStatesAndUT() as $state)
                                        <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="state_permit_registration_date" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-calendar"></i> Registration Date
                                </label>
                                <input id="state_permit_registration_date" class="form-control" type="datetime-local"
                                    name="registration_date" placeholder="Select Registration Date">
                            </div>

                            <div class="col-12">
                                <input type="file" class="" id="state_permit_document" name="document"
                                    data-height="100" data-show-remove="false"
                                    placeholder="Choose State Permit Document">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="viewDetailsModel" tabindex="-1" aria-labelledby="viewDetailsModel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="headingDetailsModel"></h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container" id="DetailsContainer">

                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // Define validation rules and messages
            const rules = {
                serviceDate: {
                    required: true,
                },
                odometerReading: {
                    required: true,
                    number: true,
                    min: parseInt(
                        '{{ $vehicle->services->last() ? $vehicle->services->last()->odometerReading : 0 }}'
                    ) + 1,
                },
                serviceType: {
                    required: true,
                },
                serviceAmount: {
                    required: function(element) {
                        console.log($('#serviceType').val());
                        return $('#serviceType').val() !== null;
                    },
                    number: true,
                },
                oilChange: {
                    required: true,
                },
                oilChangeAmount: {
                    required: {
                        depends: function(element) {
                            return $('#oilChange').val() === '1';
                        }
                    },
                    number: true,
                },

                sparePartsChange: {
                    required: true,
                },
                // totalAmount: {
                //     required: true,
                //     number: true,
                // },
                // document: {
                //     required: true,
                // },
            };
            for (let index = 0; index < 9; index++) {
                rules[`sparePartsName[${index}]`] = {
                    required: {
                        depends: function(element) {
                            return $('#sparePartsChange').val() === '1';
                        }
                    }
                };
                rules[`sparePartsAmount[${index}]`] = {
                    required: {
                        depends: function(element) {
                            return $('#sparePartsChange').val() === '1';
                        }
                    },
                    number: true
                };
            }

            const messages = {
                serviceDate: {
                    required: "Please select a service date.",
                },
                odometerReading: {
                    required: "Please enter the odometer reading.",
                    number: "Please enter a valid number.",
                },
                kmRun: {
                    required: "Please enter the kilometers run.",
                    number: "Please enter a valid number.",
                },
                timeGap: {
                    required: "Please enter the time gap in months.",
                    number: "Please enter a valid number.",
                },
                serviceType: {
                    required: "Please select a service type.",
                },
                serviceAmount: {
                    required: "Please enter the service amount.",
                    number: "Please enter a valid number.",
                },
                oilChangeAmount: {
                    required: "Please enter the oil change amount.",
                    number: "Please enter a valid number.",
                },
                sparePartsName: {
                    required: "Please enter the spare parts name.",
                },
                sparePartsAmount: {
                    required: "Please enter the spare parts amount.",
                    number: "Please enter a valid number.",
                },
                totalAmount: {
                    required: "Please enter the total amount.",
                    number: "Please enter a valid number.",
                },
                document: {
                    required: "Please upload a document.",
                },
            };
            // Initialize validation for the form
            initializeValidation("#service-sechdule-form", rules, messages);
            // add parts row and remove row
            var rowIdx = 1;

            $('#tbody').on('click', '#addBtn', function() {
                if (rowIdx < 10) {
                    // Append a new row with updated id and name attributes.
                    $('#tbody').append(`<tr>
                                                <td class="ps-0 pe-0">
                                                    <label for="sparePartsName${rowIdx}" class="form-label mb-2 ms-1 d-block">Spare
                                                        Parts Name</label>
                                                    <input type="text" class="form-control partsname" placeholder="Part Name"
                                                        id="sparePartsName${rowIdx}" name="sparePartsName[${rowIdx}]">
                                                </td>
                                                <td class="ps-0">
                                                    <label for="sparePartsAmount${rowIdx}" class="form-label mb-2 ms-1 d-block">Amount</label>
                                                    <input type="number" min="1" class="form-control" placeholder="Amount"
                                                        id="sparePartsAmount${rowIdx}" name="sparePartsAmount[${rowIdx}]">
                                                </td>
                                                <td class="pe-0">
                                                    <button type="button" class="text-danger remove border-0 btn mt-4 ps-0 pe-0 pt-3"><i
                                                            class="mdi mdi-minus-circle fs-4 "></i></button>
                                                </td>
                                            </tr>`);

                    // Increment the row index.
                    initializeValidation("#service-sechdule-form", rules, messages);

                    rowIdx++;
                }
            });

            $('#tbody').on('click', '.remove', function() {
                // Removing the current row.
                $(this).closest('tr').remove();

                // Update the id and name attributes for the remaining rows.
                updateRowAttributes();
                updateTotalAmount();

                // Decreasing the total number of rows by 1.
                rowIdx--;
            });

            function updateRowAttributes() {
                $('#tbody tr').each(function(index) {
                    // Update Spare Parts Name
                    $(this).find('[id^="sparePartsName"]').attr('id', 'sparePartsName' + index);
                    $(this).find('[name^="sparePartsName"]').attr('name', 'sparePartsName[' + index + ']');
                    $(this).find('label[for^="sparePartsName"]').attr('for', 'sparePartsName' + index);

                    // Update Spare Parts Amount
                    $(this).find('[id^="sparePartsAmount"]').attr('id', 'sparePartsAmount' + index);
                    $(this).find('[name^="sparePartsAmount"]').attr('name', 'sparePartsAmount[' + index +
                        ']');
                    $(this).find('label[for^="sparePartsAmount"]').attr('for', 'sparePartsAmount' + index);
                });
            }


            // Add change event listener to the select element
            $('#sparePartsChange').change(function() {
                // Remove all tr elements in the table except the first one
                $('#tbody tr:not(:first)').remove();

                // Set values of the remaining inputs to an empty string
                $('#tbody input').val('');
                $('.parts-container').toggleClass('d-none', $(this).val() == '0');
                var rowIdx = 1;
                updateTotalAmount();
            });

            // Add change event listener to the oilChange select element
            $('#oilChange').change(function() {
                // Get the selected value
                var selectedValue = $(this).val();

                // Set the oilChangeAmount field readonly and value based on the selected value
                if (selectedValue == '1') {
                    $('#oilChangeAmount').prop('readonly', false).val('0');
                } else {
                    $('#oilChangeAmount').prop('readonly', true).val('');
                }
            });

            // Add change event listener to the Service Type select element
            $('#serviceType').change(function() {
                // Get the selected value
                var selectedValue = $(this).val();

                // Set the oilChangeAmount field readonly and value based on the selected value
                if (selectedValue !== '') {
                    $('#serviceAmount').prop('readonly', false).val('0');
                } else {
                    $('#serviceAmount').prop('readonly', true).val('');
                }
            });

            // dropify
            $('#document').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Change Document',
                }
            });

            // select 2 and flatepicker


            if ($("#serviceType").length) {
                $("#serviceType").select2({
                    dropdownParent: $("#add_service"),
                });
            }
            if ($('#serviceDate').length) {
                const today = new Date();
                const timeGapInput = $('#timeGap');
                const staticDateStr =
                    '{{ $vehicle->services->last() ? $vehicle->services->last()->serviceDate : '' }}';

                const serviceDateInput = flatpickr("#serviceDate", {
                    wrap: true,
                    dateFormat: "d M Y",
                    minDate: staticDateStr ? new Date(staticDateStr) : undefined,
                    onChange: function(selectedDates, dateStr, instance) {
                        // Parse the selected and static dates
                        const selectedDate = new Date(dateStr);
                        const staticDate = staticDateStr ? new Date(staticDateStr) : today;

                        // Calculate the difference in months
                        const monthDiffValue = monthDiff(staticDate, selectedDate);

                        // Update the value of the timeGap input
                        timeGapInput.val(monthDiffValue);
                    },
                });
            }

            // Function to calculate the difference in months between two dates
            function monthDiff(date1, date2) {
                let months;
                months = (date2.getFullYear() - date1.getFullYear()) * 12;
                months -= date1.getMonth() + 1;
                months += date2.getMonth() + 1;
                return months <= 0 ? 0 : months;
            }

            // Function to calculate and update total amount
            function updateTotalAmount() {
                // Get values from the form
                var serviceAmount = parseFloat($("#serviceAmount").val()) || 0;
                var oilChangeAmount = parseFloat($("#oilChangeAmount").val()) || 0;

                // Calculate spare parts total amount
                var sparePartsTotalAmount = 0;
                $("input[name^='sparePartsAmount']").each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    sparePartsTotalAmount += value;
                });

                // Calculate total amount
                var totalAmount = serviceAmount + oilChangeAmount + sparePartsTotalAmount;

                // Update the total amount field
                $("#totalAmount").val(totalAmount.toFixed(2));
            }
            // Attach change event listeners to relevant input fields
            $("#serviceAmount, #oilChangeAmount").on("input", function() {
                updateTotalAmount();
            });

            // Add event listener for spare parts amount change
            $(".parts-container").on("input", "input[name^='sparePartsAmount']", function() {
                updateTotalAmount();
            });

            $("#odometerReading").on("input", function() {
                const kmRunInput = $('#kmRun');
                const odometerReadingValue = parseInt($(this).val()) || 0;

                // Get the last recorded odometerReading from the server-side variable
                const lastOdometerReading = parseInt(
                    '{{ $vehicle->services->last() ? $vehicle->services->last()->odometerReading : 0 }}'
                );

                // Calculate the kmRunValue
                const kmRunValue = odometerReadingValue - lastOdometerReading;

                // If kmRunValue is negative, set it to 0
                const finalKmRunValue = Math.max(kmRunValue, 0);

                // Update the value of the kmRun input
                kmRunInput.val(finalKmRunValue);
            });
        });
        $("#saveServiceDetails").on("click", function() {
            $("#service-sechdule-form").submit();
        });

        function viewServiceDetails(url) {
            getData(url, function(data) {
                console.log(data);
                let modalContent = document.getElementById('serviceDetailsContent');
                modalContent.innerHTML = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>Vehicle Number:</strong></li>
                                            <li><strong>Service Date:</strong></li>
                                            <li><strong>Odometer Reading:</strong></li>
                                            <li><strong>Km Run:</strong></li>
                                            <li><strong>Time Gap (In months):</strong></li>
                                            <li><strong>Service Type:</strong></li>
                                            <li><strong>Amount:</strong></li>
                                            <li><strong>Oil Change:</strong></li>
                                            <li><strong>Amount:</strong></li>
                                            <li><strong>Spare Parts Change:</strong></li>
                                            <li><strong>Total Amount:</strong></li>

                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li>${data.vehicle.vehicle_number}</li>
                                            <li>${data.serviceDate}</li>
                                            <li>${data.odometerReading}</li>
                                            <li>${data.kmRun}</li>
                                            <li>${data.timeGap}</li>
                                            <li>${data.serviceType}</li>
                                            <li>${data.serviceAmount}</li>
                                            <li>${data.oilChange == '1' ? 'Yes' : 'No'}</li>
                                            <li>${data.oilChangeAmount}</li>
                                            <li>${data.sparePartsChange == '1' ? 'Yes' : 'No'}</li>
                                            <li>${data.totalAmount}</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card border-0">
                                    <div class="card-body pt-0 px-0">
                                        <div class="table-responsive">
                                            <table id="dataTable" class="table view-constigment" data-order=''>
                                                <thead class="text-center">
                                                    <tr>
                                                        <th class="pe-0">SR.No</th>
                                                        <th class="pe-0">Part Name</th>
                                                        <th class="pe-0">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center" id="viewProductTbl">
                                                    <tr>
                                                        ${data.parts.map((part, index) => `
                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                <td>${index + 1}</td>
                                                                                                                                                                                                                                                                                <td>${part.sparePartsName}</td>
                                                                                                                                                                                                                                                                                <td>${part.sparePartsAmount}</td>
                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                        `).join('')}
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                ${data.document ? `<div class="text-center"><button class="btn btn-primary mt-3" onclick="showDocument('${data.document}')">View Document</button></div>` : ''}
                               
                    `;

                // Show the modal
                $('#serviceDetailsModal').modal('show');
            });
        }
    </script>

    {{-- for vehicle documents --}}
    <script>
        $(document).ready(function() {
            // Initialize Select2 for policy_type
            initializeSelect2('#insurance_policy_type', '#insuranceModal');
            initializeSelect2('#national_permit_category', '#nationalPermitModal');
            initializeSelect2('#state_permit_state', '#statePermitModal');


            // Initialize flatpickr for date inputs
            initFlatpickrWithMinDate('#insurance_valid_fromInput', '#insurance_valid_toInput');
            initFlatpickrWithMaxDate('#insurance_valid_toInput', '#insurance_valid_fromInput');
            initializeFlatpickr('#insurance_registration_dateInput');

            initializeFlatpickr('#pucc_registration_dateInput');
            initFlatpickrWithMinDate('#pucc_valid_fromInput', '#pucc_valid_toInput');
            initFlatpickrWithMaxDate('#pucc_valid_toInput', '#pucc_valid_fromInput');
            initializeFlatpickr('#pucc_test_dateInput');

            initFlatpickrWithMinDate('#fitness_valid_fromInput', '#fitness_valid_toInput');
            initFlatpickrWithMaxDate('#fitness_valid_toInput', '#fitness_valid_fromInput');
            initializeFlatpickr('#fitness_inspected_onInput');

            initializeFlatpickr('#tax_registration_date');
            initFlatpickrWithMinDate('#tax_valid_from', '#tax_valid_to');
            initFlatpickrWithMaxDate('#tax_valid_to', '#tax_valid_from');

            initializeFlatpickr('#national_permit_registration_date');
            initFlatpickrWithMinDate('#national_permit_valid_from', '#national_permit_valid_to');
            initFlatpickrWithMaxDate('#national_permit_valid_to', '#national_permit_valid_from');

            initializeFlatpickr('#state_permit_registration_date');
            initFlatpickrWithMinDate('#state_permit_valid_from', '#state_permit_valid_to');
            initFlatpickrWithMaxDate('#state_permit_valid_to', '#state_permit_valid_from');

            // Initialize Dropify for the insurance document input
            initializeDropify('#insurance_document');
            initializeDropify('#pucc_insurance_doc');
            initializeDropify('#fitness_document');
            initializeDropify('#tax_document');
            initializeDropify('#national_permit_document');
            initializeDropify('#state_permit_document');

            // validation
            initializeInsuranceValidation();
            initializePUCCDocumentValidation();
            initializeFitnessDocumentValidation();
            initializeTaxDocumentValidation();
            initializeNationalPermitDocumentValidation();
            initializeStatePermitDocumentValidation();


        });

        function viewDocuments(url) {
            getData(url, function(data) {
                console.log(data);
                let modelHeading = document.getElementById('headingDetailsModel');
                let modalContent = document.getElementById('DetailsContainer');

                modelHeading.innerHTML = data.document.document_name;
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><strong>Document Number:</strong></li>
                                <li><strong>Valid From:</strong></li>
                                <li><strong>Valid To</strong></li>
                                <li><strong>Registration Date</strong></li>
                                <li><strong>Issuer Name</strong></li>
                                ${data.document.attributes.map(attribute => 
                                    `<li><strong>${attribute.attribute_name}</strong></li>`
                                ).join('')}
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li>${data.document.document_number}</li>
                                <li>${data.document.valid_from}</li>
                                <li>${data.document.valid_to}</li>
                                <li>${data.document.registration_date}</li>
                                <li>${data.document.issuer_name}</li>
                                ${data.document.attributes.map(attribute => 
                                    `<li>${attribute.attribute_value}</li>`
                                ).join('')}
                            </ul>
                        </div>
                    </div>
                ${data.document.document_path ? `<div class="text-center"><button class="btn btn-primary mt-3" onclick="showDocument('${data.document.document_path }')">View Document</button></div>` : ''}
                `;
                // Show the modal
                $('#viewDetailsModel').modal('show');
            });
        }

        function initializeInsuranceValidation() {
            const insuranceFormId = '#insurance_add_document_form';

            initializeValidation(insuranceFormId, {
                policy_number: {
                    required: true,
                    // minlength: 3,
                    maxlength: 20,
                },
                policy_type: {
                    required: true,
                },
                valid_from: {
                    required: true,
                    date: true,
                },
                valid_to: {
                    required: true,
                    date: true,
                },
                registration_date: {
                    required: true,
                    date: true,
                },
                issuer_name: {
                    required: true,
                    maxlength: 20,
                },
                document: {
                    required: true,
                },
            }, {
                policy_number: {
                    required: "Please enter the policy number.",
                    minlength: "The policy number must be at least 3 characters long.",
                    maxlength: "The policy number must not exceed 20 words.",
                },
                policy_type: {
                    required: "Please select the policy type.",
                },
                valid_from: {
                    required: "Please select the valid from date.",
                    date: "Please enter a valid date.",
                },
                valid_to: {
                    required: "Please select the valid to date.",
                    date: "Please enter a valid date.",
                },
                registration_date: {
                    required: "Please select the registration date.",
                    date: "Please enter a valid date.",
                },
                issuer_name: {
                    required: "Please enter the issuer name.",
                    maxlength: "The issuer name must not exceed 20 words.",
                },
                document: {
                    required: "Please upload the insurance document.",
                },
            });
        }

        function initializePUCCDocumentValidation() {
            const puccFormId = '#add_pucc_document_form';

            initializeValidation(puccFormId, {
                document_number: {
                    required: true,
                    maxlength: 20,
                },
                registration_date: {
                    required: true,
                    date: true,
                },
                valid_from: {
                    required: true,
                    date: true,
                },
                valid_to: {
                    required: true,
                    date: true,
                },
                issuer_name: {
                    required: true,
                    maxlength: 20,
                },
                test_date: {
                    required: true,
                    date: true,
                },
                document: {
                    required: true,
                },
            }, {
                document_number: {
                    required: "Please enter the PUCC number.",
                    maxlength: "The PUCC number must not exceed 20 words.",
                },
                registration_date: {
                    required: "Please select the registration date.",
                    date: "Please enter a valid date.",
                },
                valid_from: {
                    required: "Please select the valid from date.",
                    date: "Please enter a valid date.",
                },
                valid_to: {
                    required: "Please select the valid to date.",
                    date: "Please enter a valid date.",
                },
                issuer_name: {
                    required: "Please enter the issuer name.",
                    maxlength: "The issuer name must not exceed 20 words.",
                },
                test_date: {
                    required: "Please select the test date.",
                    date: "Please enter a valid date.",
                },
                document: {
                    required: "Please upload the PUCC document.",
                },
            });
        }

        function initializeFitnessDocumentValidation() {
            const fitnessFormId = '#add_fitness_document_form';

            initializeValidation(fitnessFormId, {
                application_number: {
                    required: true,
                    maxlength: 20,
                },
                receipt_number: {
                    required: true,
                    maxlength: 20,
                },
                valid_from: {
                    required: true,
                    date: true,
                },
                valid_to: {
                    required: true,
                    date: true,
                },
                issuer_name: {
                    required: true,
                    maxlength: 20,
                },
                inspected_on: {
                    required: true,
                    date: true,
                },
                document: {
                    required: true,
                },
            }, {
                application_number: {
                    required: "Please enter the application number.",
                    maxlength: "The application number must not exceed 20 words.",
                },
                receipt_number: {
                    required: "Please enter the receipt number.",
                    maxlength: "The receipt number must not exceed 20 words.",
                },
                valid_from: {
                    required: "Please select the valid from date.",
                    date: "Please enter a valid date.",
                },
                valid_to: {
                    required: "Please select the valid to date.",
                    date: "Please enter a valid date.",
                },
                issuer_name: {
                    required: "Please enter the issuer name.",
                    maxlength: "The issuer name must not exceed 20 words.",
                },
                inspected_on: {
                    required: "Please select the inspected date.",
                    date: "Please enter a valid date.",
                },
                document: {
                    required: "Please upload the Fitness document.",
                },
            });
        }

        function initializeTaxDocumentValidation() {
            const taxFormId = '#add_tax_document_form';

            initializeValidation(taxFormId, {
                document_number: {
                    required: true,
                    maxlength: 20,
                },
                registration_date: {
                    required: true,
                },
                valid_from: {
                    required: true,
                },
                valid_to: {
                    required: true,
                },
                issuer_name: {
                    required: true,
                    maxlength: 20,
                },
                document: {
                    required: true,
                },
            }, {
                document_number: {
                    required: "Please enter the document number.",
                    maxlength: "The document number must not exceed 20 words.",
                },
                registration_date: {
                    required: "Please enter the registration date.",
                },
                valid_from: {
                    required: "Please enter the valid from date.",
                },
                valid_to: {
                    required: "Please enter the valid to date.",
                },
                issuer_name: {
                    required: "Please enter the issuer name.",
                    maxlength: "The issuer name must not exceed 20 words.",
                },
                document: {
                    required: "Please upload the Tax document.",
                },
            });
        }

        function initializeNationalPermitDocumentValidation() {
            const nationalPermitFormId = '#add_national_permit_document_form';

            initializeValidation(nationalPermitFormId, {
                document_number: {
                    required: true,
                    maxlength: 20,
                },
                permit_category: {
                    required: true,
                },
                valid_from: {
                    required: true,
                },
                valid_to: {
                    required: true,
                },
                issuer_name: {
                    required: true,
                    maxlength: 20,
                },
                registration_date: {
                    required: true,
                },
                document: {
                    required: true,
                },
            }, {
                document_number: {
                    required: "Please enter the document number.",
                    maxlength: "The document number must not exceed 20 words.",
                },
                permit_category: {
                    required: "Please select the permit category.",
                },
                valid_from: {
                    required: "Please enter the valid from date.",
                },
                valid_to: {
                    required: "Please enter the valid to date.",
                },
                issuer_name: {
                    required: "Please enter the issuer name.",
                    maxlength: "The issuer name must not exceed 20 words.",
                },
                registration_date: {
                    required: "Please enter the registration date.",
                },
                document: {
                    required: "Please upload the National Permit document.",
                },
            });
        }

        function initializeStatePermitDocumentValidation() {
            const statePermitFormId = '#add_state_permit_document_form';

            initializeValidation(statePermitFormId, {
                document_number: {
                    required: true,
                    maxlength: 20,
                },
                permit_holder_name: {
                    required: true,
                    maxlength: 20,
                },
                valid_from: {
                    required: true,
                },
                valid_to: {
                    required: true,
                },
                issuer_name: {
                    required: true,
                    maxlength: 20,
                },
                permit_state: {
                    required: true,
                },
                registration_date: {
                    required: true,
                },
                document: {
                    required: true,
                },
            }, {
                document_number: {
                    required: "Please enter the document number.",
                    maxlength: "The document number must not exceed 20 words.",
                },
                permit_holder_name: {
                    required: "Please enter the permit holder name.",
                    maxlength: "The permit holder name must not exceed 20 words.",
                },
                valid_from: {
                    required: "Please enter the valid from date.",
                },
                valid_to: {
                    required: "Please enter the valid to date.",
                },
                issuer_name: {
                    required: "Please enter the issuer name.",
                    maxlength: "The issuer name must not exceed 20 words.",
                },
                permit_state: {
                    required: "Please select the state/UT where the permit is valid.",
                },
                registration_date: {
                    required: "Please enter the registration date.",
                },
                document: {
                    required: "Please upload the State Permit document.",
                },
            });
        }
    </script>
@endsection
