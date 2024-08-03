@extends('warehouse_head.layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Assigned Vehicles
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
                <div class="card-body">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Vehicle No</th>
                                <th>Driver</th>
                                <th>Model Name</th>
                                <th>Vehicle Type</th>
                                <th>Assigned at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicles as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ $item->vehicle->vehicle_number }}</td>
                                    <td>{{ $item->vehicle->user_vehicle ? $item->vehicle->user_vehicle->user->name : "" ?? '-'}}</td>
                                    <td>{{ ucwords($item->vehicle->model->name ?? '-') }}</td>
                                    <td>{{ ucwords($item->vehicle->vehicle_body_type) }}</td>
                                    <td>{{ dateformat($item->assigned_at,'d M Y h:i A') }}</td>
                                    {{-- <td>
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
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
