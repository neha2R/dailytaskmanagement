@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Vehicle Management
            </h4>
        </div>
    </div>
    <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-line-tab" data-bs-toggle="tab" data-bs-target="#vehicle_mf" role="tab"
                aria-controls="home" aria-selected="true">Vehicles</a>
        </li>
    </ul>
    <div class="tab-content mt-3" id="lineTabContent">
        <div class="tab-pane fade show active" id="vehicle_mf" role="tabpanel" aria-labelledby="home-line-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#create_vehicle" data-bs-whatever="@getbootstrap">Add Vehicle</button>
                </div>
                <div class="card-body">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Vehicle No</th>
                                <th>Model Name</th>
                                {{-- <th>Vehicle Type</th> --}}
                                <th>Registration_date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicles as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ $item->vehicle_number }}</td>
                                    <td>{{ ucwords($item->model->name ?? '-') }}</td>
                                    {{-- <td>{{ ucwords($item->vehicle_body_type) }}</td> --}}
                                    <td>{{ dateformat($item->registration_date,'d M Y') }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item_status" type="checkbox" role="switch"
                                                data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                                {{ $item->is_active ? 'checked' : '' }} />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown mb-2">
                                            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                                <a href="{{ route('vehicle.vehicleDetails', $item->id) }}"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="eye"
                                                        class="icon-sm me-2"></i> <span class="">View</span></a>
                                                <a type="button" onclick="getVehicle({{ $item->id }})"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="edit"
                                                        class="icon-sm me-2"></i> <span class="">Edit</span></a>
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
    {{-- <div class="modal fade" id="create_vehicle" tabindex="-1" aria-labelledby="create_vehicle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Vehicle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="create_vehicle_form" method="post" action="{{ route('vehicle.vehicles.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">Vehicle Body Type</label>
                                <select class="js-example-basic-single form-select" id="vb_type" name="vehicle_body_type"
                                    data-width="100%">
                                    <option selected disabled>Select Vehicle Body Type</option>
                                    <option value="Full body Truck">Full body Truck (FBT)</option>
                                    <option value="Half body Truck">Half body Truck (HBT)</option>
                                    <option value="Platform Truck">Platform Truck</option>
                                    <option value="Container Body">Container Body</option>
                                    <option value="Gas Cascade Body">Gas Cascade body</option>
                                    <option value="Cryogenic Capsule">Cryogenic Capsule</option>
                                    <option value="Fuel & Chemical Tanker">Fuel & Chemical Tanker</option>
                                    <option value="Milk/Water Tanker">Milk/Water Tanker</option>
                                    <option value="Tipper Body">Tipper body</option>
                                    <option value="Special Application Body">Special Application Body</option>

                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">Vehicle Condition</label>
                                <select class="js-example-basic-single form-select" id="v_condition"
                                    name="vehicle_condition" data-width="100%">
                                    <option selected disabled>Select Vehicle Condition</option>
                                    <option value="new">New</option>
                                    <option value="old">Old</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">Manufacturer Name </label>
                                <select class="js-example-basic-single form-select" id="manufacturer"
                                    name="manufacturer_id" data-width="100%">
                                    <option selected disabled>Select Manufacturer</option>
                                    @foreach (getVehicleManufacturer() as $item)
                                        <option value="{{ $item->id }}">{{ $item->sort_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Model Name</label>
                                <select disabled class="js-example-basic-single form-select" id="model"
                                    name="model_id" data-width="100%">
                                    <option selected disabled>Select Vehicle Model</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Vehicle Color</label>
                                <select class="js-example-basic-single form-select" id="vehicle_color"
                                    name="vehicle_color" data-width="100%">
                                    <option selected disabled>Select Vehicle Color</option>
                                    <option value="red">Red</option>
                                    <option value="black">Black</option>
                                    <option value="white">White</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Vehcile Number</label>
                                <input value="{{ old('name') }}" id="v_number" placeholder="Vehcile Number"
                                    class="form-control" name="vehicle_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Chassis Number</label>
                                <input value="{{ old('name') }}" id="c_number" placeholder="Chassis Number"
                                    class="form-control" name="chassis_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Engine Number</label>
                                <input value="{{ old('name') }}" id="e_number" placeholder="Engine Number"
                                    class="form-control" name="engine_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Wheelbase (In Mm)</label>
                                <input value="{{ old('wheelbase') }}" id="wheelbase" placeholder=""
                                    class="form-control" name="wheelbase" type="number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Fuel Type</label>
                                <input value="{{ old('wheelbase') }}" readonly id="fule_type" placeholder=""
                                    class="form-control" name="fule_type" type="text">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Service Duration Time (in months)</label>
                                <input value="{{ old('service_time_duration') }}" id="service_time_duration" placeholder=""
                                    class="form-control" name="service_time_duration" type="number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label mb-2 ms-1">Service Duration Km</label>
                                <input value="{{ old('service_time_duration') }}" readonly id="service_time_duration" placeholder=""
                                    class="form-control" name="service_km_duration" type="number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="insurance_valid_from" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Vehicle Registration Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date" data-input=""
                                        readonly="readonly" id="registration_date" name="registration_date">

                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="insurance_valid_to" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Vehicle Reg Validity Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date" data-input=""
                                        readonly="readonly" id="validity_date" name="validity_date">
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="createVehicleBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="create_vehicle" tabindex="-1" aria-labelledby="create_vehicle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="mdi mdi-car"></span> Add Vehicle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="create_vehicle_form" method="post" action="{{ route('vehicle.vehicles.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="vehicle_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle Number
                                </label>
                                <input value="{{ old('name') }}" id="vehicle_number" placeholder="Vehicle Number"
                                    class="form-control" name="vehicle_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle Body Type
                                </label>
                                <select class="js-example-basic-single form-select" id="vb_type" name="vehicle_body_type"
                                    data-width="100%">
                                    <option selected disabled>Select Vehicle Body Type</option>
                                    @foreach (getVehicleBodyTypes() as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="manufacturer" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Manufacturer Name
                                </label>
                                <select class="js-example-basic-single form-select" id="manufacturer"
                                    name="manufacturer_id" data-width="100%">
                                    <option selected disabled>Select Manufacturer</option>
                                    @foreach (getVehicleManufacturer() as $item)
                                        <option value="{{ $item->id }}">{{ $item->sort_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="model" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Model Name
                                </label>
                                <select disabled class="js-example-basic-single form-select" id="model"
                                    name="model_id" data-width="100%">
                                    <option selected disabled>Select Vehicle Model</option>
                                    <!-- Add other options -->
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="vehicle_color" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle Color
                                </label>
                                <select class="js-example-basic-single form-select" id="vehicle_color"
                                    name="vehicle_color" data-width="100%">
                                    <option selected disabled>Select Vehicle Color</option>
                                    @foreach (getVehicleColors() as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="v_condition" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle Condition
                                </label>
                                <select class="js-example-basic-single form-select" id="v_condition"
                                    name="vehicle_condition" data-width="100%">
                                    <option selected disabled>Select Vehicle Condition</option>
                                    <option value="new">New</option>
                                    <option value="old">Old</option>
                                </select>
                            </div>


                            <div class="col-md-6 mb-2">
                                <label for="chassis_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Chassis Number
                                </label>
                                <input value="{{ old('name') }}" id="chassis_number" placeholder="Chassis Number"
                                    class="form-control" name="chassis_number" type="text">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="engine_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Engine Number
                                </label>
                                <input value="{{ old('name') }}" id="engine_number" placeholder="Engine Number"
                                    class="form-control" name="engine_number" type="text">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="wheelbase" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Wheelbase (In Mm)
                                </label>
                                <input value="{{ old('wheelbase') }}" id="wheelbase" placeholder="Wheelbase (In Mm)"
                                    class="form-control" name="wheelbase" type="number">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="fule_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi mdi-gas-station"></span> Fuel Type
                                </label>
                                <input value="{{ old('wheelbase') }}" readonly id="fule_type" placeholder="Fuel Type"
                                    class="form-control" name="fule_type" type="text">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="service_time_duration" class="form-label mb-2 ms-1" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="The duration, in months, between consecutive services.">
                                    <span class="mdi mdi-car"></span> Service Duration Time (in months)
                                </label>
                                <input value="{{ old('service_time_duration') }}" id="service_time_duration"
                                    placeholder="Service Duration Time (in months)" class="form-control"
                                    name="service_time_duration" type="number" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="The duration, in months, between consecutive services.">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="service_km_duration" class="form-label mb-2 ms-1" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="The distance, in kilometers, between consecutive services.">
                                    <span class="mdi mdi-car"></span> Service Duration Km
                                </label>
                                <input value="{{ old('service_time_duration') }}" id="service_km_duration"
                                    placeholder="Service Duration Km" class="form-control" name="service_km_duration"
                                    type="number" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="The distance, in kilometers, between consecutive services.">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="registration_date" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-calendar"></span> Vehicle Registration Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Check your vehicle's Registration Certificate (RC) for the date it was first registered with the authorities."
                                        readonly="readonly" id="registration_date" name="registration_date">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validity_date" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-calendar"></span> Vehicle Reg. Validity Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Check your vehicle's Registration Certificate (RC) for the expiration date. Make sure to renew it before this date to avoid any issues."
                                        readonly="readonly" id="validity_date" name="validity_date">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="createVehicleBtn" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_vehicle" tabindex="-1" aria-labelledby="edit_vehicle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Vehicle Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_vehicle_form" method="post" action="{{ route('vehicle.vehicles.update', '1') }}">
                        @csrf
                        <div class="row">
                            <input type="hidden" value="" id="id" name="id">
                            <div class="col-md-6 mb-2">
                                <label for="edit_v_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle Number
                                </label>
                                <input value="" id="edit_v_number" placeholder="Vehicle Number"
                                    class="form-control" name="vehicle_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle Body Type
                                </label>
                                <select class="js-example-basic-single form-select" id="edit_vb_type"
                                    name="vehicle_body_type" data-width="100%">
                                    <option selected disabled>Select Vehicle Body Type</option>
                                    @foreach (getVehicleBodyTypes() as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="col-md-6 mb-2">
                                <label for="manufacturer" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Manufacturer Name
                                </label>
                                <select class="js-example-basic-single form-select" id="edit_manufacturer"
                                    name="manufacturer_id" data-width="100%">
                                    <option selected disabled>Select Manufacturer</option>
                                    @foreach (getVehicleManufacturer() as $item)
                                        <option value="{{ $item->id }}">{{ $item->sort_name }}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="model" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Model Name
                                </label>
                                <select class="js-example-basic-single form-select" id="edit_model" name="model_id"
                                    data-width="100%">
                                    <option selected disabled>Select Vehicle Model</option>

                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="edit_vehicle_color" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle Color
                                </label>
                                <select class="js-example-basic-single form-select" id="edit_vehicle_color"
                                    name="vehicle_color" data-width="100%">
                                    <option selected disabled>Select Vehicle Color</option>
                                    @foreach (getVehicleColors() as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="v_condition" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle Condition
                                </label>
                                <select class="js-example-basic-single form-select" id="edit_v_condition"
                                    name="vehicle_condition" data-width="100%">
                                    <option selected disabled>Select Vehicle Condition</option>
                                    <option value="new">New</option>
                                    <option value="old">Old</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="chassis_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Chassis Number
                                </label>
                                <input value="" id="edit_c_number" placeholder="Chassis Number"
                                    class="form-control" name="chassis_number" type="text">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="edit_e_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Engine Number
                                </label>
                                <input value="" id="edit_e_number" placeholder="Engine Number"
                                    class="form-control" name="engine_number" type="text">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="wheelbase" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Wheelbase (In Mm)
                                </label>
                                <input value="" id="edit_wheelbase" placeholder="Wheelbase (In Mm)"
                                    class="form-control" name="wheelbase" type="number">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="fule_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi mdi-gas-station"></span> Fuel Type
                                </label>
                                <input value="" readonly id="edit_fuel_type" placeholder="Fuel Type"
                                    class="form-control" name="fule_type" type="text">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="service_time_duration" class="form-label mb-2 ms-1" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="The duration, in months, between consecutive services.">
                                    <span class="mdi mdi-car"></span> Service Duration Time (in months)
                                </label>
                                <input value="{{ old('service_time_duration') }}" id="edit_service_time_duration"
                                    placeholder="Service Duration Time (in months)" class="form-control"
                                    name="service_time_duration" type="number" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="The duration, in months, between consecutive services.">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="service_km_duration" class="form-label mb-2 ms-1" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="The distance, in kilometers, between consecutive services.">
                                    <span class="mdi mdi-car"></span> Service Duration Km
                                </label>
                                <input value="{{ old('service_time_duration') }}" id="edit_service_km_duration"
                                    placeholder="Service Duration Km" class="form-control" name="service_km_duration"
                                    type="number" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="The distance, in kilometers, between consecutive services.">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_registration_date" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-calendar"></span> Vehicle Registration Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Check your vehicle's Registration Certificate (RC) for the date it was first registered with the authorities."
                                        readonly="readonly" id="edit_registration_date" name="registration_date">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_validity_date" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-calendar"></span> Vehicle Reg. Validity Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Check your vehicle's Registration Certificate (RC) for the expiration date. Make sure to renew it before this date to avoid any issues."
                                        readonly="readonly" id="edit_validity_date" name="validity_date">
                                </div>
                            </div>
                        </div>
                        @method('PATCH')
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="editVehicleBtn" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            if ($(".js-example-basic-single").length) {
                $(".js-example-basic-single").select2({
                    dropdownParent: $("#create_vehicle")
                });
            }
            initFlatpickrWithMinDate('#registration_date', '#validity_date');
            initFlatpickrWithMaxDate('#validity_date', '#registration_date');
            // Define validation rules and messages
            const vehicleFormRules = {
                vehicle_number: {
                    required: true,
                    uniqueVehicleNumber: true,
                    validVehicleNumber: true,
                },

                vehicle_body_type: {
                    // required: true,
                },
                vehicle_condition: {
                    required: true,
                },
                manufacturer_id: {
                    required: true,
                },
                model_id: {
                    required: true,
                },

                chassis_number: {
                    required: true,
                },
                engine_number: {
                    required: true,
                },
                wheelbase: {
                    required: true,
                },
                fule_type: {
                    required: true,
                },
                service_time_duration: {
                    required: true,
                },
                service_km_duration: {
                    required: true,
                },
                registration_date: {
                    required: true,
                },
                validity_date: {
                    required: true,
                },
            };

            const vehicleFormMessages = {
                vehicle_body_type: {
                    required: "Vehicle body type is required",
                },
                vehicle_condition: {
                    required: "Vehicle condition is required",
                },
                manufacturer_id: {
                    required: "Manufacturer is required",
                },
                model_id: {
                    required: "Model is required",
                },
                vehicle_number: {
                    required: "Please enter vehicle number",
                },
                chassis_number: {
                    required: "Please enter chassis number",
                },
                engine_number: {
                    required: "Please enter engine number",
                },
                service_time_duration: {
                    required: "Please enter the service duration in months.",
                },
                service_km_duration: {
                    required: "Please enter the service duration in kilometers.",
                },
                registration_date: {
                    required: "Please select the vehicle registration date.",
                },
                validity_date: {
                    required: "Please select the vehicle registration validity date.",
                },
            };
            const editVehicleFormRules = {
                vehicle_number: {
                    required: true,
                    validVehicleNumber: true,
                },

                vehicle_body_type: {
                    // required: true,
                },
                vehicle_condition: {
                    required: true,
                },
                manufacturer_id: {
                    required: true,
                },
                model_id: {
                    required: true,
                },

                chassis_number: {
                    required: true,
                },
                engine_number: {
                    required: true,
                },
                wheelbase: {
                    required: true,
                },
                fule_type: {
                    required: true,
                },
                service_time_duration: {
                    required: true,
                },
                service_km_duration: {
                    required: true,
                },
                registration_date: {
                    required: true,
                },
                validity_date: {
                    required: true,
                },
            };

            const editVehicleFormMessages = {
                vehicle_body_type: {
                    required: "Vehicle body type is required",
                },
                vehicle_condition: {
                    required: "Vehicle condition is required",
                },
                manufacturer_id: {
                    required: "Manufacturer is required",
                },
                model_id: {
                    required: "Model is required",
                },
                vehicle_number: {
                    required: "Please enter vehicle number",
                },
                chassis_number: {
                    required: "Please enter chassis number",
                },
                engine_number: {
                    required: "Please enter engine number",
                },
                service_time_duration: {
                    required: "Please enter the service duration in months.",
                },
                service_km_duration: {
                    required: "Please enter the service duration in kilometers.",
                },
                registration_date: {
                    required: "Please select the vehicle registration date.",
                },
                validity_date: {
                    required: "Please select the vehicle registration validity date.",
                },
            };
            $.validator.addMethod("uniqueVehicleNumber", function(value, element, callback) {
                var isUnique = false;
                $.ajax({
                    url: "{{ route('vehicle.checkUniqueVehicleNumber') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    async: false,
                    data: {
                        vehicle_number: value
                    },
                    success: function(response) {
                        isUnique = response.unique;
                    }
                });
                return isUnique;

            }, "This Vehicle Number is already registered");
            $.validator.addMethod("validVehicleNumber", function(value, element, params) {
                // Your custom format validation logic goes here
                var regex = /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/;
                return this.optional(element) || regex.test(value);
            }, "Please enter a valid vehicle number");

            initializeValidation("#create_vehicle_form", vehicleFormRules, vehicleFormMessages);
            initializeValidation("#edit_vehicle_form", editVehicleFormRules, editVehicleFormMessages);

            $(document).on("input", "#vehicle_number", function() {
                $(this).val(function(_, val) {
                    return val.toUpperCase();
                });
            });
        });

        $('#manufacturer').change(function() {
            var id = $(this).find(":selected").val();
            var url = '../getModels/' + id;

            // Use getData function for AJAX request
            getData(url, function(data) {
                $('#model').prop("disabled", false).empty();
                $('#model').append($("<option></option>")
                    .attr("value", "").attr("disabled", 'true').attr("selected", 'true')
                    .text('Select model name'));
                $.each(data, function(key, value) {
                    $('#model').append($("<option></option>")
                        .attr("value", value.id).attr('data-fule_type', value.fule_type)
                        .text(value.name))
                })
            });
        });
        $('#model').change(function() {
            var value = $(this).find(":selected").attr('data-fule_type');
            $('#fule_type').val(value);
        });

        function getVehicle(id) {
            var url = 'vehicles/' + id;
            // Use getData function for AJAX request
            getData(url, function(data) {
                console.log(data);
                $('#edit_vehicle').modal('show');
                $('#id').val(data.data.id);
                $('#edit_vb_type').val(data.data.vehicle_body_type).select2({
                    dropdownParent: $("#edit_vehicle")
                });
                $('#edit_v_condition').val(data.data.vehicle_condition).select2({
                    dropdownParent: $("#edit_vehicle")
                });
                $('#edit_manufacturer').val(data.data.manufacturer_id).select2({
                    dropdownParent: $("#edit_vehicle")
                });
                $('#edit_model').empty();
                $.each(data.models, function(key, value) {
                    $('#edit_model').append($("<option></option>")
                        .attr("value", value.id).attr('data-fule_type', value.fule_type)
                        .text(value.name))
                });
                $('#edit_model').val(data.data.model_id).select2({
                    dropdownParent: $("#edit_vehicle")
                });
                $('#edit_vehicle_color').val(data.data.vehicle_color).select2({
                    dropdownParent: $("#edit_vehicle")
                });
                $('#edit_v_number').val(data.data.vehicle_number);
                $('#edit_c_number').val(data.data.chassis_number);
                $('#edit_e_number').val(data.data.engine_number);
                $('#edit_wheelbase').val(data.data.wheelbase);
                $('#edit_fuel_type').val(data.data.model.fule_type);
                $('#edit_service_time_duration').val(data.data.service_time_duration);
                $('#edit_service_km_duration').val(data.data.service_km_duration);

                var registration_date = data.data.registration_date;
                var validity_date = data.data.validity_date;
                initFlatpickrWithMinDate('#edit_registration_date', '#edit_validity_date', registration_date);
                initFlatpickrWithMaxDate('#edit_validity_date', '#edit_registration_date', validity_date);
            });
        }
        $('#edit_manufacturer').change(function() {
            var id = $(this).find(":selected").val();
            var url = '../getModels/' + id;

            // Use getData function for AJAX request
            getData(url, function(data) {
                $('#edit_model').prop("disabled", false).empty();
                $('#edit_model').append($("<option></option>")
                    .attr("value", "").attr("disabled", 'true').attr("selected", 'true')
                    .text('Select model name'));
                $('#edit_fuel_type').val("");
                $.each(data, function(key, value) {
                    $('#edit_model').append($("<option></option>")
                        .attr("value", value.id).attr('data-fule_type', value.fule_type)
                        .text(value.name))
                });
            });
        });
        $('#edit_model').change(function() {
            var value = $(this).find(":selected").attr('data-fule_type');
            $('#edit_fuel_type').val(value);
        });
        $('.item_status').change(function() {
            var mode = $(this).prop('checked');
            var id = $(this).data("id");
            handleStatusChange('{{ route('vehicle.vehicles.status') }}', mode, id);
        });

        $("#createVehicleBtn").on("click", function() {
            $("#create_vehicle_form").submit();
        });

        $("#editVehicleBtn").on("click", function() {
            $("#edit_vehicle_form").submit();
        });
    </script>
@endsection
