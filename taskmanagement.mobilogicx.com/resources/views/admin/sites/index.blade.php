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
                <a href="{{ route('admin.subDivisions') }}" class="nav-link  tab-heading" aria-selected="false">Sub
                    Divisions</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.sites') }}" class="nav-link active tab-heading" aria-selected="false">Sites</a>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#add_site"
                data-bs-whatever="@getbootstrap">
                <i class="mdi mdi-plus fs-6"></i> Add Site
            </button>
            <button type="button" class="btn btn-sm bg-custom-info ms-1 text-white" data-bs-toggle="modal"
                data-bs-target="#multi_maping" data-bs-whatever="@getbootstrap">
                <i class="mdi mdi mdi-account-multiple-outline"></i> Map Users
            </button>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
            <h5 class="info-heading ms-3">Manage Sites</h5>

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
                            <th>Sub Division</th>
                            <th>Mapping Status</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sites as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->city ?? '-' }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->sub_division->name ?? '-' }}({{$item->sub_division->division->name}})</td>
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
                                        
                                            <a type="button" onclick="editSite('{{ route('admin.siteEdit', $item->id) }}')"
                                                class="dropdown-item d-flex align-items-center"><i data-feather="edit"
                                                    class="icon-sm me-2"></i>
                                                <span class="">Edit Site</span></a>
                                            <a type="button" onclick="showHistory('{{ $item->id }}','site')"
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
                                                <a type="button" onclick="unmap('{{ $item->id }}','site')"
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

    <div class="modal fade" id="add_site" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Create Site</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_site_form" method="post" action="{{ route('admin.siteStore') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input value="{{ old('name') }}" id="site_name" placeholder="Site Name"
                                    class="form-control" name="site_name" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="division_id" class="form-label">Sub Division</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <select style="width: 100%;" name="sub_division_id" id="sub_division_id"
                                    class="form-select js-example-basic-single">
                                    <option selected disabled>Select Sub Division</option>
                                    @foreach (getActiveDivisions() as $items)
                                        <optgroup label="{{ $items->name }}">
                                            @foreach ($items->sub_divisions->where('is_active',true) as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="city" class="form-label">City</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input value="{{ old('city') }}" id="site_city" placeholder="City Name"
                                    class="form-control" name="site_city" type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="address" class="form-label">Address</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <textarea name="site_address" class="form-control" id="site_address" rows="4" spellcheck="false"></textarea>
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="site_head_id" class="form-label">Site Head</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <select style="width: 100%;" name="site_head_id" id="siteHead"
                                    class="form-select js-example-basic-single">
                                    <option selected disabled>Select Site Head</option>
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
    <div class="modal fade" id="single_mapping" tabindex="-1" aria-labelledby="single_mapping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map User with Site
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
                                    <input type="hidden" name="mapping_type" value="site">
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
                                        @foreach (getUsersByRoleName([env('SiteHeadRole')]) as $user)

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
    <div class="modal fade" id="multi_maping" tabindex="-1" aria-labelledby="multi_maping" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Map User With Sites</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="maping-container">
                    <form id="multi_mapping_form" method="post" action="{{ route('admin.divisionSiteMaping') }}">
                        <div class="modal-body">
                            @csrf
                            <!-- Add a hidden input field to store the type -->
                            <input type="hidden" name="mapping_type" value="site">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="warehouses_users" class="form-label">Select User</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-single" style="width: 100%;" name="user_id"
                                        id="users">
                                        <option selected disabled>Select User</option>
                                        @foreach (getUsersByRoleName([env('SiteHeadRole')]) as $user)

                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="warehouses_ids" class="form-label">Select Sites</label>
                                </div>
                                <div class="col-lg-8 col-sm-8">
                                    <select class="js-example-basic-multiple" style="width: 100%;" name="map_ids[]"
                                        id="subdivision_ids" multiple="multiple">
                                        @foreach ($unMappedSites as $item)
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
        const addSiteRules = {
            site_name: {
                required: true,
                maxlength: 50,
            },
            sub_division_id: {
                required: true,
            },
            site_city: {
                required: true,
                maxlength: 30,
            },
            site_address: {
                required: true,
                maxlength: 100,
            },
            // site_head_id: {
            //     required: true,
            // },
        };

        const addSiteMessages = {
            site_name: {
                required: "The site name is required.",
                maxlength: "The site name must not exceed 50 characters.",
            },
            sub_division_id: {
                required: "The sub division is required.",
            },
            site_city: {
                required: "The city is required.",
                maxlength: "The city must not exceed 30 characters.",
            },
            site_address: {
                required: "The address is required.",
                maxlength: "The address must not exceed 100 characters.",
            },
            site_head_id: {
                required: "The site head is required.",
            },
        };
        // Initialize form validations
        $(function() {
            // Initialize validation for the add sub division form
            initializeValidation("#add_site_form", addSiteRules, addSiteMessages);

            initializeSelect2("#users", "#multi_maping");
            initializeSelect2("#subdivision_ids", "#multi_maping");
            initializeSelect2("#single_users", "#single_mapping");
            initializeSelect2("#sub_division_id", "#add_site");
            // initializeSelect2("#siteHead", "#add_site");
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
                handleStatusChange('{{ route('admin.siteStatus') }}', $(this).prop('checked'), $(this)
                    .data(
                        "id"));
            });
        });

        function editSite(url) {
            getData(url, function(data) {
                console.log(data);
                if (data.status == 200) {
                    // Select the edit-container
                    var editContainer = $('#edit-container');

                    // Create the form HTML with modified IDs
                    var formHTML = `
                    <form id="add_site_form" method="post" action="{{ route('admin.siteUpdate') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="name" class="form-label">Name</label>
                                </div>
                                <div class="col-lg-8 col-sm-8 position-relative">
                                    <input type="hidden" name="id" value="${data.site.id}">
                                    <input value="${data.site.name}" id="edit_site_name" placeholder="Site Name"
                                        class="form-control" name="site_name" type="text">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="division_id" class="form-label">Sub Division</label>
                                </div>
                                <div class="col-lg-8 col-sm-8 position-relative">
                                    <select style="width: 100%;" name="sub_division_id" id="edit_sub_division_id"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select Sub Division</option>
                                        @foreach (getActiveDivisions() as $items)
                                            <optgroup label="{{ $items->name }}">
                                                @foreach ($items->sub_divisions as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div cl <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="city" class="form-label">City</label>
                                </div>
                                <div class="col-lg-8 col-sm-8 position-relative">
                                    <input value="${data.site.city}" id="edit_site_city" placeholder="City Name"
                                        class="form-control" name="site_city" type="text">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-3">
                                    <label for="address" class="form-label">Address</label>
                                </div>
                                <div class="col-lg-8 col-sm-8 position-relative">
                                    <textarea name="site_address" class="form-control" id="site_address" rows="4" spellcheck="false">${data.site.address}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>`;

                    // append data
                    editContainer.html(formHTML);

                    // Initialize Select2 for the select elements
                    $('#edit_sub_division_id').select2({
                        dropdownParent: $("#edit_model"),
                    });
                    $('#edit_siteHead').select2({
                        dropdownParent: $("#edit_model"),
                    });

                    // Set values for the select elements
                    $('#edit_sub_division_id').val(data.site.sub_division_id).trigger('change');
                    $('#edit_siteHead').val(data.site.user_id).trigger('change');


                    // Initialize validation for the add site form
                    initializeValidation("#edit_site_form", addSiteRules, addSiteMessages);

                    $('#edit_heading').text("Edit Site Details")

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
                    $('#sourcename').text('Site');
                    if (data.data.length) {
                        $('#history tbody').empty();
                        $.each(data.data, function(key, value) {
                            $('#history tbody').append(`
                            <tr>
                                <td>${value.site ? value.site.name : "-" }</td>
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
