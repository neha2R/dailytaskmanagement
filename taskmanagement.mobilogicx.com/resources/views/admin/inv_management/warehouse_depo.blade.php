@extends('layouts.app')
@section('content')
    {{-- <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Warehouse And Depot
            </h4>
        </div>
    </div> --}}
    <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active tab-heading" id="home-line-tab" data-bs-toggle="tab" data-bs-target="#warehouse"
                role="tab" aria-controls="home" aria-selected="true">Warehouse</a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-heading" id="home-line-tab" data-bs-toggle="tab" data-bs-target="#depo" role="tab"
                aria-controls="home" aria-selected="true">Depot</a>
        </li>
    </ul>



    <div class="tab-content mt-3" id="lineTabContent">
        <div class="tab-pane fade show active" id="warehouse" role="tabpanel" aria-labelledby="home-line-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    <button type="button" class="btn btn-primary me-1 btn-sm" data-bs-toggle="modal"
                        data-bs-target="#create_warehouse" data-bs-whatever="@getbootstrap">
                        <i class="mdi mdi-plus"></i> New Warehouse
                    </button>
                    <button type="button" class="btn bg-custom-info text-white btn-sm" data-bs-toggle="modal"
                        data-bs-target="#multi_wh_maping" data-bs-whatever="@getbootstrap">
                        <i class="mdi mdi mdi-account-multiple-outline"></i> Map Warehouses
                    </button>
                </div>
               
                <div class="card-body">
                    <table id="dataTableExample" class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Name</th>
                                <th>city</th>
                                <th>Head</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($warehouses as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->city }}</td>
                                    @if ($item->user->last())
                                        <td>{{ $item->user->last()->user->name ?? '-' }}</td>
                                    @else
                                        <td> <span class="badge bg-danger">Not Mapped</span></td>
                                    @endif
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item_status" type="checkbox" role="switch"
                                                data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                                {{ $item->is_active ? 'checked' : '' }} />
                                        </div>
                                    </td>
                                    {{-- <td>
                                        <button onclick="getWarehouseData({{ $item->id }})" type="button"
                                            class="btn btn-primary btn-icon btn-xs"><i data-feather="edit"></i></button>
                                    </td> --}}
                                    <td>
                                        <div class="dropdown mb-2">
                                            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                                @if (!$item->user->last())
                                                    <a type="button"
                                                        onclick="openWhModel('{{ $item->name }}',{{ $item->id }})"
                                                        class="dropdown-item d-flex align-items-center"><i
                                                            data-feather="user-plus" class="icon-sm me-2"></i> <span
                                                            class="">Map</span></a>
                                                @else
                                                    <a type="button" onclick="unmap({{ $item->id }},'warehouse')"
                                                        class="dropdown-item d-flex align-items-center"><i
                                                            data-feather="user-minus" class="icon-sm me-2"></i> <span
                                                            class="">Unmap</span></a>
                                                @endif
                                                <a type="button" onclick="showHistory({{ $item->id }},'warehouse')"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="eye"
                                                        class="icon-sm me-2"></i> <span class="">View</span></a>
                                                <a type="button" onclick="getWarehouseData({{ $item->id }})"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="edit"
                                                        class="icon-sm me-2"></i>
                                                    <span class="">Edit</span></a>
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
        <div class="tab-pane fade" id="depo" role="tabpanel" aria-labelledby="home-line-tab">
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    <button type="button" class="btn btn-primary btn-xs me-1" data-bs-toggle="modal"
                        data-bs-target="#create_depo" data-bs-whatever="@getbootstrap">
                        <i class="mdi mdi-plus"></i> New Depot
                    </button>
                    <button type="button" class="btn btn-info btn-xs" data-bs-toggle="modal"
                        data-bs-target="#multi_dp_maping" data-bs-whatever="@getbootstrap">
                        <i class="mdi mdi mdi-account-multiple-outline"></i> Map Depot
                    </button>
                </div>
                <div class="card-body">
                    <table id="dataTableExample1" class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Name</th>
                                <th>Warehouse</th>
                                <th>City</th>
                                <th>Head</th>
                                <th>status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($depots as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->warehouse->name ?? '-' }}</td>
                                    <td>{{ $item->city }}</td>
                                    @if ($item->user->last())
                                        <td>{{ $item->user->last()->user->name ?? '-' }}</td>
                                    @else
                                        <td> <span class="badge bg-danger">Not Mapped</span></td>
                                    @endif
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input item_status1" type="checkbox" role="switch"
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
                                                @if (!$item->user->last())
                                                    <a type="button"
                                                        onclick="openDpModel('{{ $item->name }}',{{ $item->id }})"
                                                        class="dropdown-item d-flex align-items-center"><i
                                                            data-feather="user-plus" class="icon-sm me-2"></i> <span
                                                            class="">Map</span></a>
                                                @else
                                                    <a type="button" onclick="unmap({{ $item->id }},'depo')"
                                                        class="dropdown-item d-flex align-items-center"><i
                                                            data-feather="user-minus" class="icon-sm me-2"></i> <span
                                                            class="">Unmap</span></a>
                                                @endif
                                                <a type="button" onclick="showHistory({{ $item->id }},'depo')"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="eye"
                                                        class="icon-sm me-2"></i> <span class="">View</span></a>
                                                <a type="button" onclick="getDepoData({{ $item->id }})"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="edit"
                                                        class="icon-sm me-2"></i>
                                                    <span class="">Edit</span></a>
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

    <div class="modal fade" id="create_warehouse" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Warehouse
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_warehouse_form" method="post" action="{{ route('admin.warehouse.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="warehouse_name" placeholder="Warehouse Name"
                                    class="form-control" name="name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">City</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('city') }}" id="city" placeholder="City Name"
                                    class="form-control" name="city" type="text">
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
    <div class="modal fade" id="create_depo" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Depot
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_depo_form" method="post" action="{{ route('admin.depo.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="depo_name" placeholder="Depot Name"
                                    class="form-control" name="name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Warehouse</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <select class="form-select" name="warehouse_id" id="warehouse_id">
                                    <option selected disabled>Select Warehouse</option>
                                    @foreach (getWarehouses() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">City</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('city') }}" id="city" placeholder="City Name"
                                    class="form-control" name="city" type="text">
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
    <div class="modal fade" id="edit_warehouse" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Warehouse Detail's
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_warehouse_form" method="post" action="{{ route('admin.warehouse.update', '1') }}">
                    @method('PATCH')
                    <div class="modal-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="edit_name" placeholder="Warehouse Name"
                                    class="form-control" name="name" type="text">
                                <input name="id" type="hidden" value="" id="edit_id">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">City</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('city') }}" id="edit_city" placeholder="City Name"
                                    class="form-control" name="city" type="text">
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
    <div class="modal fade" id="edit_depo" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Depot Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_depo_form" method="post" action="{{ route('admin.depo.update', '1') }}">
                    <div class="modal-body">
                        @csrf
                        @method('PATCH')
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="edit_depo_name" placeholder="Depo Name"
                                    class="form-control" name="name" type="text">
                                <input name="id" type="hidden" value="" id="edit_depo_id">

                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Warehouse</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <select class="form-select" name="warehouse_id" id="edit_warehouse_id">
                                    <option selected disabled>Select Warehouse</option>
                                    @foreach (getWarehouses() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">City</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('city') }}" id="edit_depo_city" placeholder="City Name"
                                    class="form-control" name="city" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Address</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <textarea name="address" class="form-control" id="edit_depo_address" rows="4" spellcheck="false"></textarea>
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

    <div class="modal fade" id="wh_maping" tabindex="-1" aria-labelledby="wh_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map User with warehouse
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div id="maping-container">
                    <form id="wh_maping_form" method="post" action="{{ route('admin.warehouseDepoMap') }}">
                        <div class="modal-body">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Warehouse</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <input type="hidden" name="warehouse_id" id="wh_id" value="">
                                    <input type="text" id="wh_name" readonly class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Select User</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single " style="width: 100%;"
                                        name="warehouse_user_id" id="wh_user_id">
                                        <option selected disabled>Select User</option>
                                        @if ($warehouseUsers)
                                        @foreach ($warehouseUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                        @endif
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
    <div class="modal fade" id="multi_wh_maping" tabindex="-1" aria-labelledby="multi_wh_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map User With Multiple Warehouses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="maping-container">
                    <form id="wh_multiple_maping_form" method="post"
                        action="{{ route('admin.warehouseDepoMultiMap') }}">
                        <div class="modal-body">
                            @csrf
                            <!-- Add a hidden input field to store the type -->
                            <input type="hidden" name="mapping_type" value="warehouse">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="warehouses_users" class="form-label">Select User</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single" style="width: 100%;" name="user_id"
                                        id="warehouses_users">
                                        <option selected disabled>Select User</option>
                                        @foreach ($warehouseUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="warehouses_ids" class="form-label">Select Warehouses</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-multiple" style="width: 100%;" name="map_ids[]"
                                        id="warehouses_ids" multiple="multiple">
                                        @foreach ($availableWarehouses as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
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
    </div>
    <div class="modal fade" id="multi_dp_maping" tabindex="-1" aria-labelledby="multi_dp_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map User With Multiple Depot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="maping-container">
                    <form id="depot_multiple_mapping_form" method="post"
                        action="{{ route('admin.warehouseDepoMultiMap') }}">
                        <div class="modal-body">
                            @csrf
                            <!-- Add a hidden input field to store the type -->
                            <input type="hidden" name="mapping_type" value="depot">

                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="depot_users" class="form-label">Select User</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single" style="width: 100%;" name="user_id"
                                        id="depot_users">
                                        <option selected disabled>Select User</option>
                                        @if ($depotUsers)
                                        @foreach ($depotUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="depot_ids" class="form-label">Select Depots</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-multiple" style="width: 100%;" name="map_ids[]"
                                        id="depot_ids" multiple="multiple">
                                        @foreach ($availableDepots as $depot)
                                            <option value="{{ $depot->id }}">{{ $depot->name }}</option>
                                        @endforeach
                                    </select>
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
    </div>

    <div class="modal fade" id="dp_maping" tabindex="-1" aria-labelledby="dp_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map User with Depot
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div id="maping-container">
                    <form id="dp_maping_form" method="post" action="{{ route('admin.warehouseDepoMap') }}">
                        <div class="modal-body">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Depot</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <input type="hidden" id="dp_id" value="" name="depo_id">
                                    <input type="text" id="dp_name" readonly class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="gender" class="form-label">Select User</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single " style="width: 100%;" name="depo_user_id"
                                        id="dp_user_id">
                                        <option selected disabled>Select User</option>
                                        @foreach ($depotUsers as $user)
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

    <div class="modal fade" id="show_assignment" tabindex="-1" aria-labelledby="show_assignment" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">History of Map/Unmap</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive pt-0">
                        <table class="table table-hover" id="history">
                            <thead>
                                <th>Warehouse/Depot</th>
                                <th>Action</th>
                                <th>User</th>
                                <th>date</th>
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
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // Validation rules for add warehouse form
            var addWarehouseValidationRules = {
                name: {
                    required: true,
                    minlength: 3
                },
                city: {
                    required: true,
                },
            };

            // Validation messages for add warehouse form
            var addWarehouseValidationMessages = {
                name: {
                    required: "Please enter warehouse name",
                    minlength: "Category name must consist of at least 3 characters "
                },
                city: {
                    required: "Please enter city name",
                }
            };

            // Validation rules for add depot form
            var addDepotValidationRules = {
                name: {
                    required: true,
                    minlength: 3
                },
                city: {
                    required: true,
                },
            };

            // Validation messages for add depot form
            var addDepotValidationMessages = {
                name: {
                    required: "Please enter sub-category name",
                    minlength: "Sub-category must consist of at least 3 characters "
                },
                city: {
                    required: "Please enter city name",
                }
            };

            // Initialize validation for the Warehouse createing Form
            initializeValidation("#add_warehouse_form", addWarehouseValidationRules,
                addWarehouseValidationMessages);
            initializeValidation("#add_depo_form", addDepotValidationRules, addDepotValidationMessages);

            // create validation for wh maping
            const whMapingRules = {
                wh_user_id: {
                    required: true,
                },
            };
            const whMapingMessages = {
                wh_user_id: {
                    required: "The User is required.",
                }
            };
            // Initialize validation for the add division form
            initializeValidation("#wh_maping_form", whMapingRules, whMapingMessages);

            // create validation for dp maping
            const dpMapingRules = {
                dp_user_id: {
                    required: true,
                },
            };
            const dpMapingMessages = {
                dp_user_id: {
                    required: "The User is required.",
                }
            };
            // Initialize validation for the add division form
            initializeValidation("#dp_maping_form", dpMapingRules, dpMapingMessages);


            // Initialize validation for the Warehouse multiple Mapping Form
            initializeValidation("#wh_multiple_maping_form", {
                user_id: "required",
                "map_ids[]": "required",
            }, {
                user_id: "Please select a user",
                "map_ids[]": "Please select at least one warehouse",
            });

            // Initialize validation for the Depot Mapping Form
            initializeValidation("#depot_multiple_mapping_form", {
                user_id: "required",
                "map_ids[]": "required",
            }, {
                user_id: "Please select a user",
                "map_ids[]": "Please select at least one depot",
            });


            initializeSelect2("#wh_user_id", "#wh_maping");
            initializeSelect2("#dp_user_id", "#dp_maping");
            initializeSelect2("#warehouses_users", "#multi_wh_maping");
            initializeSelect2("#warehouses_ids", "#multi_wh_maping");
            initializeSelect2("#depot_users", "#multi_dp_maping");
            initializeSelect2("#depot_ids", "#multi_dp_maping");

            // Event handler for item status change
            $('.item_status').change(function() {
                var mode = $(this).prop('checked');
                var id = $(this).data("id");
                var url = '{{ route('admin.warehouse.status') }}';

                handleStatusChange(url, mode, id);
            });

            // Event handler for item status change (assuming .item_status1 is used for depot)
            $('.item_status1').change(function() {
                var mode = $(this).prop('checked');
                var id = $(this).data("id");
                var url = '{{ route('admin.depo.status') }}';

                handleStatusChange(url, mode, id);
            });

        });

        // Function to get warehouse data
        function getWarehouseData(id) {
            getData('warehouse/' + id, function(data) {
                $('#edit_warehouse').modal('show');
                $('#edit_id').val(data.data.id);
                $('#edit_name').val(data.data.name);
                $('#edit_city').val(data.data.city);
                $('#edit_address').val(data.data.city);
            });
        }

        // Function to get depo data
        function getDepoData(id) {
            getData('depo/' + id, function(data) {
                $('#edit_depo').modal('show');
                $('#edit_depo_id').val(data.data.id);
                $('#edit_depo_name').val(data.data.name);
                $('#edit_depo_city').val(data.data.city);
                $('#edit_warehouse_id').val(data.data.warehouse_id);
                $('#edit_depo_address').val(data.data.address);
            });
        }

        function openWhModel(wh_name, wh_id) {
            var model = $('#wh_maping');
            if (wh_name && wh_id) {
                $('#wh_name').val(wh_name);
                $('#wh_id').val(wh_id);
                model.modal('show');
            }
        }

        function openDpModel(dp_name, dp_id) {
            var model = $('#dp_maping');
            if (dp_name && dp_id) {
                $('#dp_name').val(dp_name);
                $('#dp_id').val(dp_id);
                model.modal('show');
            }
        }


        function unmap(id, type) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger me-2'
                },
                buttonsStyling: false,
            })
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "you want to unmap this",
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
                        var url = "{{ route('admin.warehouseDepoUnmap', ['id' => ':id', 'type' => ':type']) }}";
                        url = url.replace(/:id/g, id);
                        url = url.replace(/:type/g, type);
                        window.location.href = url;
                    }
                }
            });
        }

        function showHistory(id, type) {

            var url = "{{ route('admin.whDpHistoryMapUnmap', ['id' => ':id', 'type' => ':type']) }}";
            url = url.replace(/:id/g, id);
            url = url.replace(/:type/g, type);
            console.log(url);
            getData(url, function(data) {
                console.log(data);
                if (data.data.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No history found for this ' + type,
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
                                            <td>${value.warehouse ? value.warehouse.name : value.depo.name}</td>
                                            <td>
                                                ${
                                                value.type === 'map'
                                                    ? `<span class="badge bg-primary xs">Map</span>`
                                                    : value.type === 'unmap'
                                                    ? `<span class="badge bg-secondary xs">Unmap</span>`
                                                    : ''
                                                }
                                            </td>
                                            <td>${value.user.name}</td>
                                            <td>${value.date}</td>
                                        </tr>
                            `);
                        })
                    } else {

                    }

                }
            })
        }
    </script>
@endsection
