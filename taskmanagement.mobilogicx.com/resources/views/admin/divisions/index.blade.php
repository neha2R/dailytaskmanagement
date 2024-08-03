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
                <a href="{{ route('admin.divisions-sites.index') }}" class="nav-link active tab-heading"
                    aria-selected="true">Divisions</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.subDivisions') }}" class="nav-link tab-heading" aria-selected="false">Sub
                    Divisions</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.sites') }}" class="nav-link tab-heading" aria-selected="false">Sites</a>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#add_div"
                data-bs-whatever="@getbootstrap">
                <i class="mdi mdi-plus fs-6"></i> Add Division
            </button>
            <button type="button" class="btn btn-sm bg-custom-info ms-1 text-white" data-bs-toggle="modal"
                data-bs-target="#multi_maping" data-bs-whatever="@getbootstrap">
                <i class="mdi mdi mdi-account-multiple-outline"></i> Map Users
            </button>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
            <h5 class="info-heading ms-3">Manage Divisions</h5>

        </div>

        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Head</th>
                            <th>Mapping Status</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($divisions as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->city ?? '-' }}</td>
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
                                                onclick="editDivision('{{ route('admin.divisions-sites.edit', $item->id) }}')"
                                                class="dropdown-item d-flex align-items-center">
                                                <i class="icon-sm me-2 " data-feather="edit"></i>
                                                <span class="">Edit</span>
                                            </a>
                                            <a type="button" onclick="showHistory('{{ $item->id }}','division')"
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
                                                <a type="button" onclick="unmap('{{ $item->id }}','division')"
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
    <div class="modal fade" id="add_div" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Division</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_div_form" method="post" action="{{ route('admin.divisions-sites.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input value="{{ old('name') }}" id="division_name" placeholder="Division Name"
                                    class="form-control" name="division_name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="city" class="form-label">City</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input value="{{ old('city') }}" id="city" placeholder="City Name"
                                    class="form-control" name="division_city" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="address" class="form-label">Address</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <textarea name="division_address" class="form-control" id="division_address" rows="4" spellcheck="false"></textarea>
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="division_head_id" class="form-label">Division Head</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <select style="width: 100%;" name="division_head_id" id="divHead"
                                    class="form-select js-example-basic-single">
                                    <option selected disabled>Select Division Head</option>
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
                                    <input type="hidden" name="mapping_type" value="division">
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
                            <input type="hidden" name="mapping_type" value="division">
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
                                    <label for="warehouses_ids" class="form-label">Select Divisions</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-multiple" style="width: 100%;" name="map_ids[]"
                                        id="division_ids" multiple="multiple">
                                        @foreach ($unMappedDivisions as $item)
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
        const addDivRules = {
            division_name: {
                required: true,
                maxlength: 50,
            },
            division_city: {
                required: true,
                maxlength: 30,
            },
            division_address: {
                required: true,
                maxlength: 100,
            },
        };
        const addDivMessages = {
            division_name: {
                required: "The division name is required.",
                maxlength: "The division name must not exceed 50 characters.",
            },
            division_city: {
                required: "The division city is required.",
                maxlength: "The division city must not exceed 30 characters.",
            },
            division_address: {
                required: "The division address is required.",
                maxlength: "The division address must not exceed 100 characters.",
            },
            division_head_id: {
                required: "The division head is required.",
            },
        };
        // Initialize form validations
        $(function() {
            initializeValidation("#add_div_form", addDivRules, addDivMessages);
            initializeSelect2("#users", "#multi_maping");
            initializeSelect2("#division_ids", "#multi_maping");
            initializeSelect2("#single_users", "#single_mapping");

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
                handleStatusChange('{{ route('admin.divStatus') }}', $(this).prop('checked'), $(this).data(
                    "id"));
            });
        });
        // edit
        function editDivision(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    // Select the edit-container
                    var editContainer = $('#edit-container');
                    var headingElement = document.getElementById("edit_heading");

                    // Create the form HTML with modified IDs
                    var formHTML = `
                                <form id="edit_div_form" method="post" action="{{ route('admin.divisions-sites.update', 1) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-lg-3 col-sm-3">
                                                <label for="modified_name" class="form-label">Name</label>
                                            </div>
                                            <div class="col-lg-8 col-sm-8 position-relative">
                                                <input type="hidden" name="id" value="${data.division.id}">
                                                <input value="${data.division.name}" id="modified_division_name" placeholder="Division Name" class="form-control" name="division_name" type="text">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-3 col-sm-3">
                                                <label for="modified_city" class="form-label">City</label>
                                            </div>
                                            <div class="col-lg-8 col-sm-8 position-relative">
                                                <input value="${data.division.city}" id="modified_city" placeholder="City Name" class="form-control" name="division_city" type="text">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-3 col-sm-3">
                                                <label for="modified_address" class="form-label">Address</label>
                                            </div>
                                            <div class="col-lg-8 col-sm-8 position-relative">
                                                <textarea name="division_address" class="form-control" id="modified_division_address" rows="4" spellcheck="false">${data.division.address}</textarea>
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

                    // Initialize Select2 for the modified_divHead select element
                    $('#modified_divHead').select2({
                        dropdownParent: $("#edit_model"),
                    });

                    // Set a value for the modified_divHead select element
                    $('#modified_divHead').val(data.division.user_id).trigger('change');
                    // Initialize validation for the edit division form
                    initializeValidation("#edit_div_form", addDivRules, addDivMessages);
                    $('#edit_heading').text("Edit Division Details")
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
                    $('#sourcename').text('Division');
                    if (data.data.length) {
                        $('#history tbody').empty();
                        $.each(data.data, function(key, value) {
                            $('#history tbody').append(`
                            <tr>
                                <td>${value.division ? value.division.name : "-" }</td>
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
