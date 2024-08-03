@extends('layouts.app')
@section('content')
    <style>
        .card-header {
            /* border-bottom: 1px solid #6571ff; */
            background-color: #f8f9fa;
        }

        .info-heading {
            padding-top: 13px;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('admin.divisions-sites.index') }}" class="nav-link tab-heading"
                    aria-selected="true">Divisions</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.subDivisions') }}" class="nav-link active tab-heading" aria-selected="false">Sub
                    Divisions</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.sites') }}" class="nav-link tab-heading" aria-selected="false">Sites</a>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#add_sub_div"
                data-bs-whatever="@getbootstrap">
                <i class="mdi mdi-plus fs-6"></i> Add Sub-Division
            </button>
            <button type="button" class="btn btn-sm bg-custom-info ms-1 text-white" data-bs-toggle="modal"
                data-bs-target="#multi_maping" data-bs-whatever="@getbootstrap">
                <i class="mdi mdi mdi-account-multiple-outline"></i> Map Users
            </button>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
            <h5 class="info-heading ms-3">Manage Sub Divisions</h5>

        </div>

        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Division</th>
                            <th>Head</th>
                            <th>Mapping Status</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subDivisions as $key => $item)
                            <tr>
                                <td>{{ $key+1}}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->city ?? '-' }}</td>
                                <td>{{ $item->division->name ?? '-' }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>
                                    @if ($item->user_id == null)
                                        <span class="badge bg-danger xs">Not Mapped</span>
                                    @else
                                        <span class="badge bg-success xs">Mapped</span>
                                    @endif
                                </td>
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
                                            {{-- <a type="button" class="dropdown-item d-flex align-items-center"
                                                href="#">
                                                <i data-feather="eye" class="icon-sm me-2"></i>
                                                <span class="">View Sub-Divisions</span>
                                            </a> --}}
                                            <a type="button"
                                                onclick="editSubDivision('{{ route('admin.subDivEdit', $item->id) }}')"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class="icon-sm me-2 " data-feather="edit"></i>
                                                <span class="">Edit</span>
                                            </a>
                                            <a type="button" onclick="showHistory('{{ $item->id }}','subdivision')"
                                                class="dropdown-item d-flex align-items-center"><i
                                                    class="icon-sm me-2 mdi mdi-history"></i> <span
                                                    class="">ViewHistory</span></a>
                                            @if ($item->user_id == null)
                                                <a type="button"
                                                    onclick="openSingleMap('{{ $item->name }}','{{ $item->id }}')"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        data-feather="user-plus" class="icon-sm me-2"></i> <span
                                                        class="">Map</span></a>
                                            @else
                                                <a type="button" onclick="unmap('{{ $item->id }}','subdivision')"
                                                    class="dropdown-item d-flex align-items-center"><i
                                                        data-feather="user-minus" class="icon-sm me-2"></i> <span
                                                        class="">Unmap</span></a>
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
    </div>
    <div class="modal fade" id="add_sub_div" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Sub Division</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_sub_div_form" method="post" action="{{ route('admin.subDivStore') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input value="" id="sub_div_name" placeholder="Sub Division Name"
                                    class="form-control" name="sub_div_name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="division_id" class="form-label">Division</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <select style="width: 100%;" name="division_id" id="division_id"
                                    class="form-select js-example-basic-single">
                                    <option selected disabled>Select Division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="city" class="form-label">City</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input value="{{ old('city') }}" id="sub_div_city" placeholder="City Name"
                                    class="form-control" name="sub_div_city" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="address" class="form-label">Address</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <textarea name="sub_div_address" class="form-control" id="sub_div_address" rows="4" spellcheck="false"></textarea>
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="sub_division_head_id" class="form-label">Sub Division Head</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <select style="width: 100%;" name="sub_division_head_id" id="subDivHead"
                                    class="form-select js-example-basic-single">
                                    <option selected disabled>Select Sub Division Head</option>
                                    @foreach (getUsersByRoleName(['SITE DIVISIONAL HEAD']) as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="single_mapping" tabindex="-1" aria-labelledby="wh_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map User with Division
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div id="maping-container">
                    <form id="single_mapping_form" method="post" action="{{ route('admin.divisionSiteMaping') }}">
                        <div class="modal-body">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="" class="form-label">Division</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <input type="hidden" name="map_ids[]" id="map_ids" value="">
                                    <input type="hidden" name="mapping_type" value="subdivision">
                                    <input type="text" id="name" readonly class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="users" class="form-label">Select User</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single" style="width: 100%;" name="user_id"
                                        id="single_users">
                                        <option selected disabled>Select User</option>
                                        @foreach (getUsersByRoleName([env('DivisionHeadRole')]) as $user)
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
    <div class="modal fade" id="multi_maping" tabindex="-1" aria-labelledby="multi_wh_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map User With Divisions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="maping-container">
                    <form id="multi_mapping_form" method="post" action="{{ route('admin.divisionSiteMaping') }}">
                        <div class="modal-body">
                            @csrf
                            <!-- Add a hidden input field to store the type -->
                            <input type="hidden" name="mapping_type" value="subdivision">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="warehouses_users" class="form-label">Select User</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single" style="width: 100%;" name="user_id"
                                        id="users">
                                        <option selected disabled>Select User</option>
                                        @foreach (getUsersByRoleName([env('DivisionHeadRole')]) as $user)

                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="warehouses_ids" class="form-label">Select Sub Divisions</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-multiple" style="width: 100%;" name="map_ids[]"
                                        id="subdivision_ids" multiple="multiple">
                                        @foreach ($unMappedSubDivisions as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
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
    @component('components.editmoal')
    @endcomponent
@endsection
@section('js')
    <script>
        // Define the rules and messages for form
        const addSubDivRules = {
            sub_div_name: {
                required: true,
                maxlength: 50,
            },
            division_id: {
                required: true,
            },
            sub_div_city: {
                required: true,
                maxlength: 30,
            },
            sub_div_address: {
                required: true,
                maxlength: 100,
            },
            // sub_division_head_id: {
            //     required: true,
            // },
        };

        const addSubDivMessages = {
            sub_div_name: {
                required: "The sub division name is required.",
                maxlength: "The sub division name must not exceed 50 characters.",
            },
            division_id: {
                required: "The division is required.",
            },
            sub_div_city: {
                required: "The city is required.",
                maxlength: "The city must not exceed 30 characters.",
            },
            sub_div_address: {
                required: "The address is required.",
                maxlength: "The address must not exceed 100 characters.",
            },
            sub_division_head_id: {
                required: "The sub division head is required.",
            },
        };
        // Initialize form validations
        $(function() {
            // Initialize validation for the add sub division form
            initializeValidation("#add_sub_div_form", addSubDivRules, addSubDivMessages);

            initializeSelect2("#users", "#multi_maping");
            initializeSelect2("#subdivision_ids", "#multi_maping");
            initializeSelect2("#single_users", "#single_mapping");
            initializeSelect2("#division_id", "#add_sub_div");

            const singleMappingRules = {
                "map_ids[]": {
                    required: true
                },
                user_id: {
                    required: true
                }
            };
            const singleMappingMessages = {
                "map_ids[]": {
                    required: "Please select at least one item."
                },
                user_id: {
                    required: "Please select a user."
                }
        
            };
            initializeValidation("#single_mapping_form", singleMappingRules, singleMappingMessages);

            const multiMappingRules = {
                user_id: {
                    required: true
                },
                "map_ids[]": {
                    required: true
                }
            };

            const multiMappingMessages = {
                user_id: {
                    required: "Please select a user."
                },
                "map_ids[]": {
                    required: "Please select at least one item."
                }
            };
            initializeValidation("#multi_mapping_form", multiMappingRules, multiMappingMessages);
            // Handle status change
            $('.item_status').change(function() {
                handleStatusChange('{{ route('admin.subDivStatus') }}', $(this).prop('checked'), $(this).data(
                    "id"));
            });
        });
        // edit
        function editSubDivision(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    // Select the edit-container
                    var editContainer = $('#edit-container');

                    // Create the form HTML with modified IDs
                    var formHTML = `
                    <form id="edit_sub_div_form" method="post" action="{{ route('admin.subDivUpdate') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="name" class="form-label">Name</label>
                                </div>
                                <div class="col-lg-8 col-sm-8 position-relative">
                                    <input type="hidden" name="id" value="${data.sub_division.id}">
                                    <input value="${data.sub_division.name}" id="edit_sub_div_name" placeholder="Sub Division Name"
                                        class="form-control" name="sub_div_name" type="text">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="division_id" class="form-label">Division</label>
                                </div>
                                <div class="col-lg-8 col-sm-8 position-relative">
                                    <select style="width: 100%;" name="division_id" id="edit_division_id"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select Division</option>
                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="city" class="form-label">City</label>
                                </div>
                                <div class="col-lg-8 col-sm-8 position-relative">
                                    <input value="${data.sub_division.city}" id="edit_sub_div_city" placeholder="City Name"
                                        class="form-control" name="sub_div_city" type="text">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="address" class="form-label">Address</label>
                                </div>
                                <div class="col-lg-8 col-sm-8 position-relative">
                                    <textarea name="sub_div_address" class="form-control" id="sub_div_address" rows="4" spellcheck="false">${data.sub_division.address}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>`;

                    // Modify the ID of the edit modal
                    editContainer.html(formHTML);

                    // Initialize Select2 for the division dropdown
                    $('#edit_division_id').select2({
                        dropdownParent: $("#edit_model"),
                    });

                    // Set the value for the division dropdown
                    $('#edit_division_id').val(data.sub_division.division_id).trigger('change');

                    // Initialize Select2 for the Sub Division Head dropdown
                    $('#edit_subDivHead').select2({
                        dropdownParent: $("#edit_model"),
                    });

                    // Set the value for the Sub Division Head dropdown
                    $('#edit_subDivHead').val(data.sub_division.user_id).trigger('change');

                    // Initialize validation for the edit sub division form
                    initializeValidation("#edit_sub_div_form", addSubDivRules, addSubDivMessages);

                    $('#edit_heading').text("Edit Sub Division Details")

                    // Show the modified modal
                    $('#edit_model').modal('show');
                }
            });
        }
        function openSingleMap(name, id) {
            var model = $('#single_mapping');
            if (name && id) {
                $('#name').val(name);
                $('#map_ids').val(id);
                model.modal('show');
            }
        }

        function showHistory(id, type) {
            console.log(type);
            var url = "{{ route('admin.divisionSiteHistory', ['id' => ':id', 'type' => ':type']) }}";
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
                    $('#historyModal').modal("show");
                    $('#sourcename').text('Sub Division');
                    if (data.data.length) {
                        $('#history tbody').empty();
                        $.each(data.data, function(key, value) {
                            $('#history tbody').append(`
                            <tr>
                                <td>${value.subdivision ? value.subdivision.name : "-" }</td>
                                <td>
                                    ${
                                    value.action === 'map'
                                        ? `<span class="badge bg-primary xs">Map</span>`
                                        : value.action === 'unmap'
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
                        var url = "{{ route('admin.divisionSiteUnmap', ['id' => ':id', 'type' => ':type']) }}";
                        url = url.replace(/:id/g, id);
                        url = url.replace(/:type/g, type);
                        window.location.href = url;
                    }
                }
            });
        }
    </script>
@endsection
