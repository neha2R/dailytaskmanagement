@extends('layouts.app')
@section('content')
    <div class="header-section">
        <div class="d-flex justify-content-between mb-2">
            <h4 class="page-title">Projects</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_project"
                data-bs-whatever="@getbootstrap">
                <i class="mdi mdi-plus fs-6"></i> Add New Project
            </button>
        </div>
    </div>
    <div class="card-container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Manage Projects</h2>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Project</th>
                                <th>Contract No</th>
                                <th>Customer Name</th>
                                <th>Assigned To</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $key => $project)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <p>{{ $project->project_name ?? "" }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $project->contract_number }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $project->poc_name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $project->user->name ?? '-' }}</p>
                                    </td>
                                    <td>
                                        <p>{{ dateformat($project->start_date, 'd M Y') }}</p>
                                    </td>
                                    <td>
                                        <p>{{ dateformat($project->end_date, 'd M Y') }}</p>
                                    </td>
                                    <td>
                                        @switch($project->status)
                                            @case('to-do')
                                                <span class="badge bg-primary xs"> To-Do</span>
                                            @break

                                            @case('in-process')
                                                <span class="badge bg-info xs">In process</span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-success xs">Completed</span>
                                            @break

                                            <span class="badge bg-danger xs">-</span>

                                            @default
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="dropdown mb-2">
                                            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                                <a href="{{route('project-management.projects.show',$project->id)}}" class="dropdown-item d-flex align-items-center"><i
                                                        data-feather="eye" class="icon-sm me-2"></i>
                                                    <span class="">View</span></a>
                                                <a type="button"
                                                    onclick="editProject('{{ route('project-management.projects.edit', $project->id) }}')"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="edit"
                                                        class="icon-sm me-2"></i>
                                                    <span class="">Edit</span></a>
                                                <a type="button" data-id="{{ $project->id }}" onclick="deleteProject(this)"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="trash"
                                                        class="icon-sm me-2"></i>
                                                    <span class="">Delete</span></a>
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
    {{-- <div class="modal fade" id="add_project" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">
                        <i class="mdi mdi-folder-plus-outline me-2"></i> Create New Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                @csrf
                <div class="modal-body">
                    <form id="add_project_form" method="post" action="{{ route('project-management.projects.store') }}">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="vehicle_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Project Name
                                </label>
                                <input value="{{ old('name') }}" id="vehicle_number" placeholder="Vehicle Number"
                                    class="form-control" name="vehicle_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vehicle_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Contract Number
                                </label>
                                <input value="{{ old('name') }}" id="vehicle_number" placeholder="Vehicle Number"
                                    class="form-control" name="vehicle_number" type="text">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validity_date" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-calendar"></span> Start Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Check your vehicle's Registration Certificate (RC) for the expiration date. Make sure to renew it before this date to avoid any issues."
                                        readonly="readonly" id="validity_date" name="validity_date">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="validity_date" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-calendar"></span> End Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input type="text" class="form-control" placeholder="Select date"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Check your vehicle's Registration Certificate (RC) for the expiration date. Make sure to renew it before this date to avoid any issues."
                                        readonly="readonly" id="validity_date" name="validity_date">
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Assigned To
                                </label>
                                <select class="js-example-basic-single form-select" id="vb_type" name="vehicle_body_type"
                                    data-width="100%">
                                    <option selected disabled>Select Vehicle Body Type</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Company
                                </label>
                                <select class="js-example-basic-single form-select" id="vb_type" name="vehicle_body_type"
                                    data-width="100%">
                                    <option selected disabled>Select Vehicle Body Type</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vehicle_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> POC Name
                                </label>
                                <input value="{{ old('name') }}" id="vehicle_number" placeholder="Vehicle Number"
                                    class="form-control" name="vehicle_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vehicle_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span>Contact Number
                                </label>
                                <input value="{{ old('name') }}" id="vehicle_number" placeholder="Vehicle Number"
                                    class="form-control" name="vehicle_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vehicle_number" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span>Email
                                </label>
                                <input value="{{ old('name') }}" id="vehicle_number" placeholder="Vehicle Number"
                                    class="form-control" name="vehicle_number" type="text">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> City
                                </label>
                                <select class="js-example-basic-single form-select" id="vb_type" name="vehicle_body_type"
                                    data-width="100%">
                                    <option selected disabled>Select Vehicle Body Type</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> State
                                </label>
                                <select class="js-example-basic-single form-select" id="vb_type" name="vehicle_body_type"
                                    data-width="100%">
                                    <option selected disabled>Select Vehicle Body Type</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="modal fade" id="add_project" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">
                        <i class="mdi mdi-folder-plus-outline me-2"></i> Create New Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body p-2">
                    <form id="add_project_form" action="{{ route('project-management.projects.store') }}" method="post">
                        @csrf
                        <div class="card mb-2 p-2">
                            <div class="card-header p-0 m-0">
                                <h5 class="card-title">
                                    <i class="mdi mdi-file-document-outline me-2"></i> Project Details
                                </h5>
                            </div>
                            <div class="card-body row p-2">
                                <div class="col-md-6 mb-2">
                                    <label for="project_name" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-file-document-outline me-2"></i> Project Name
                                    </label>
                                    <input value="{{ old('name') }}" id="project_name" placeholder="Enter Project Name"
                                        class="form-control" name="project_name" type="text">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="contract_number" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-numeric me-2"></i> Contract Number
                                    </label>
                                    <input value="{{ old('name') }}" id="contract_number"
                                        placeholder="Enter Contract Number" class="form-control" name="contract_number"
                                        type="text">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-calendar me-2"></i> Start Date
                                    </label>
                                    <div class="input-group flatpickr">
                                        <input type="text" class="form-control" placeholder="Select date"
                                            readonly="readonly" id="start_date" name="start_date">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-calendar me-2"></i> End Date
                                    </label>
                                    <div class="input-group flatpickr">
                                        <input type="text" class="form-control" placeholder="Select date"
                                            readonly="readonly" id="end_date" name="end_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card p-2">
                            <div class="card-header p-0 m-0">
                                <h5 class="card-title">
                                    <i class="mdi mdi-account-multiple me-2"></i> Client Details
                                </h5>
                            </div>
                            <div class="card-body row p-2">
                                <div class="col-md-6 mb-2">
                                    <label for="assigned_to" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-account-multiple me-2"></i> Assigned To
                                    </label>
                                    <select class="js-example-basic-single form-select" id="assigned_to"
                                        name="assigned_to" data-width="100%">
                                        <option selected disabled>Select Assigned To</option>
                                        @foreach ($assignedToUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="company" class="form-label mb-2 ms-1">
                                            <i class="mdi mdi-domain me-2"></i> Vendor
                                        </label>
                                        <a href="{{ route('admin.vendor.index') }}" class="fw-bold">Add Vendor</a>
                                    </div>

                                    {{-- <input autocomplete="off" class="form-control" type="text" placeholder="Company"> --}}
                                    <select class="js-example-basic-single form-select" id="vendor" name="vendor_id"
                                        data-width="100%">
                                        <option selected disabled>Select Vendor</option>
                                        @foreach ($activeVendors as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="poc_name" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-account me-2"></i> POC Name
                                    </label>
                                    <input value="{{ old('poc_name') }}" id="poc_name" placeholder="Enter POC Name"
                                        class="form-control" name="poc_name" type="text">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="contact_number" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-phone me-2"></i> Contact Number
                                    </label>
                                    <input value="{{ old('contact_number') }}" id="contact_number"
                                        placeholder="Enter Contact Number" class="form-control" name="contact_number"
                                        type="number" min="0">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="email" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-email me-2"></i> Email
                                    </label>
                                    <input value="{{ old('email') }}" id="email" placeholder="Enter Email"
                                        class="form-control" name="email" type="text">
                                </div>
                                {{-- <div class="col-md-6 mb-2">
                                    <label for="state" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-map me-2"></i> State
                                    </label>
                                    <select class="js-example-basic-single form-select" id="state" name="state"
                                        data-width="100%">
                                        <option selected disabled>Select State</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="city" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-map-marker me-2"></i> City
                                    </label>
                                    <select class="js-example-basic-single form-select" id="city" name="city"
                                        data-width="100%">
                                        <option selected disabled>Select City</option>
                                    </select>
                                </div> --}}

                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button"class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="saveProject" type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_model" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">
                        <i class="mdi mdi-folder-plus-outline me-2"></i> Edit Project Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div id="edit-container" class="modal-body p-2">

                </div>
                <div class="modal-footer">
                    <button type="button"class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="saveProject" type="submit" class="btn btn-primary">Update Project</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        // Define the rules and messages for form
        $(document).ready(function() {
            initFlatpickrWithMinDate('#add_project_form #start_date', '#add_project_form #end_date');
            initFlatpickrWithMaxDate('#add_project_form #end_date', '#add_project_form #start_date');
            initializeSelect2('#add_project_form #assigned_to', '#add_project');
            // initializeSelect2('#add_project_form #state', '#add_project_form');
            // initializeSelect2('#add_project_form #city', '#add_project_form');
            initializeSelect2('#add_project_form #vendor', '#add_project');
            initializeProjectFormValidation();
        });
        $("#add_project #saveProject").on("click", function() {
            $("#add_project_form").submit();
        });

        function initializeProjectFormValidation() {
            const addProjectRules = {
                project_name: {
                    required: true,
                    maxlength: 50,
                    noDoubleSpaces: true,
                },
                contract_number: {
                    required: true,
                    maxlength: 50,
                    uniqueContractNumber: true,
                    noDoubleSpaces: true,

                },
                start_date: {
                    required: true,
                    // Add date-specific validation rules if needed
                },
                end_date: {
                    required: true,
                    // Add date-specific validation rules if needed
                },
                assigned_to: {
                    required: true,
                },
                vendor_id: {
                    required: true,
                },
                poc_name: {
                    required: true,
                    maxlength: 50,
                },
                contact_number: {
                    required: true,
                    digits: true, // Ensures only numeric values are allowed
                    minlength: 10, // Adjust the length as needed
                    maxlength: 10, // Adjust the length as needed
                },
                email: {
                    required: true,
                    email: true,
                },
                // Add rules for other fields as needed
            };

            const addProjectMessages = {
                project_name: {
                    required: "Project name is required.",
                    maxlength: "Project name should not exceed 50 characters.",
                },
                contract_number: {
                    required: "Contract number is required.",
                    maxlength: "Contract number should not exceed 50 characters.",
                },
                start_date: {
                    required: "Start date is required.",
                    // Add date-specific error messages if needed
                },
                end_date: {
                    required: "End date is required.",
                    // Add date-specific error messages if needed
                },
                assigned_to: {
                    required: "Assigned To is required.",
                },
                vendor_id: {
                    required: "Vendor is required.",
                },
                poc_name: {
                    required: "POC Name is required.",
                    maxlength: "POC Name should not exceed 50 characters.",
                    noDoubleSpaces: true,
                },
                contact_number: {
                    required: "Contact number is required.",

                    digits: "Please enter a valid numeric contact number.",
                    minlength: "Contact number should be at least 10 digits.",
                    maxlength: "Contact number should not exceed 10 digits.",
                },
                email: {
                    required: "Email is required.",
                    email: "Enter a valid email address.",
                },
            };
            $.validator.addMethod("uniqueContractNumber", function(value, element, callback) {
                var isUnique = false;
                $.ajax({
                    url: "{{ route('project-management.checkContractNumber') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    async: false,
                    data: {
                        name: value
                    },
                    success: function(response) {
                        console.log(response);
                        isUnique = response.unique;
                    }
                });
                return isUnique;

            }, "This Contract Number already exists. Please use a different number for your project.");
            initializeValidation("#add_project_form", addProjectRules, addProjectMessages);
        }
        function initializeEditProjectFormValidation() {
            const addProjectRules = {
                project_name: {
                    required: true,
                    maxlength: 50,
                    noDoubleSpaces: true,
                },
                contract_number: {
                    required: true,
                    maxlength: 50,
                    noDoubleSpaces: true,

                },
                start_date: {
                    required: true,
                    // Add date-specific validation rules if needed
                },
                end_date: {
                    required: true,
                    // Add date-specific validation rules if needed
                },
                assigned_to: {
                    required: true,
                },
                vendor_id: {
                    required: true,
                },
                poc_name: {
                    required: true,
                    maxlength: 50,
                },
                contact_number: {
                    required: true,
                    digits: true, // Ensures only numeric values are allowed
                    minlength: 10, // Adjust the length as needed
                    maxlength: 10, // Adjust the length as needed
                },
                email: {
                    required: true,
                    email: true,
                },
                // Add rules for other fields as needed
            };

            const addProjectMessages = {
                project_name: {
                    required: "Project name is required.",
                    maxlength: "Project name should not exceed 50 characters.",
                },
                contract_number: {
                    required: "Contract number is required.",
                    maxlength: "Contract number should not exceed 50 characters.",
                },
                start_date: {
                    required: "Start date is required.",
                    // Add date-specific error messages if needed
                },
                end_date: {
                    required: "End date is required.",
                    // Add date-specific error messages if needed
                },
                assigned_to: {
                    required: "Assigned To is required.",
                },
                vendor_id: {
                    required: "Vendor is required.",
                },
                poc_name: {
                    required: "POC Name is required.",
                    maxlength: "POC Name should not exceed 50 characters.",
                    noDoubleSpaces: true,
                },
                contact_number: {
                    required: "Contact number is required.",

                    digits: "Please enter a valid numeric contact number.",
                    minlength: "Contact number should be at least 10 digits.",
                    maxlength: "Contact number should not exceed 10 digits.",
                },
                email: {
                    required: "Email is required.",
                    email: "Enter a valid email address.",
                },
            };
            initializeValidation("#edit_project_form", addProjectRules, addProjectMessages);
        }

        function editProject(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    // Select the edit-container
                    var editContainer = $('#edit-container');
                    var headingElement = document.getElementById("edit_heading");

                    // Create the form HTML with modified IDs
                    var formHTML = `
                        <form id="edit_project_form" method="post" action="{{ route('project-management.projects.update',0) }}">
                            @csrf
                            @method('PATCH')
                            <div class="card mb-2 p-2">
                            <div class="card-header p-0 m-0">
                                <h5 class="card-title">
                                    <i class="mdi mdi-file-document-outline me-2"></i> Project Details
                                </h5>
                            </div>
                            <div class="card-body row p-2">
                                <div class="col-md-6 mb-2">
                                    <label for="project_name" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-file-document-outline me-2"></i> Project Name
                                    </label>
                                    <input type="hidden" value="${data.project.id}" name="id" >
                                    <input value="${data.project.project_name}" id="project_name" placeholder="Enter Project Name"
                                        class="form-control" name="project_name" type="text">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="contract_number" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-numeric me-2"></i> Contract Number
                                    </label>
                                    <input value="${data.project.contract_number}" id="contract_number"
                                        placeholder="Enter Contract Number" class="form-control" name="contract_number"
                                        type="text">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-calendar me-2"></i> Start Date
                                    </label>
                                    <div class="input-group flatpickr">
                                        <input type="text" class="form-control" placeholder="Select date"
                                            readonly="readonly" id="start_date" name="start_date">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-calendar me-2"></i> End Date
                                    </label>
                                    <div class="input-group flatpickr">
                                        <input type="text" class="form-control" placeholder="Select date"
                                            readonly="readonly" id="end_date" name="end_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card p-2">
                            <div class="card-header p-0 m-0">
                                <h5 class="card-title">
                                    <i class="mdi mdi-account-multiple me-2"></i> Client Details
                                </h5>
                            </div>
                            <div class="card-body row p-2">
                                <div class="col-md-6 mb-2">
                                    <label for="assigned_to" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-account-multiple me-2"></i> Assigned To
                                    </label>
                                    <select class="js-example-basic-single form-select" id="assigned_to"
                                        name="assigned_to" data-width="100%">
                                        <option selected disabled>Select Assigned To</option>
                                        @foreach ($assignedToUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="company" class="form-label mb-2 ms-1">
                                            <i class="mdi mdi-domain me-2"></i> Vendor
                                        </label>
                                        <a href="{{ route('admin.vendor.index') }}" class="fw-bold">Add Vendor</a>
                                    </div>
                                    <select class="js-example-basic-single form-select" id="vendor" name="vendor_id"
                                        data-width="100%">
                                        <option selected disabled>Select Vendor</option>
                                        @foreach ($activeVendors as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="poc_name" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-account me-2"></i> POC Name
                                    </label>
                                    <input value="${data.project.poc_name}" id="poc_name" placeholder="Enter POC Name"
                                        class="form-control" name="poc_name" type="text">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="contact_number" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-phone me-2"></i> Contact Number
                                    </label>
                                    <input value="${data.project.contact_number}" id="contact_number"
                                        placeholder="Enter Contact Number" class="form-control" name="contact_number"
                                        type="number" min="0">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="email" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-email me-2"></i> Email
                                    </label>
                                    <input value="${data.project.email}" id="email" placeholder="Enter Email"
                                        class="form-control" name="email" type="text">
                                </div>
                            </div>
                        </div>
                        </form>`;
                    // Modify the ID of the edit modal
                    editContainer.html(formHTML);
                    initFlatpickrWithMinDate('#edit_project_form #start_date', '#edit_project_form #end_date', data
                        .project.start_date);
                    initFlatpickrWithMaxDate('#edit_project_form #end_date', '#edit_project_form #start_date', data
                        .project.end_date);

                    initializeAndSetValue('#edit_project_form #assigned_to','#edit_model',data.project.assigned_to);
                    initializeAndSetValue('#edit_project_form #vendor','#edit_model',data.project.vendor_id);
                    
                    initializeEditProjectFormValidation();
                    // Show the modified modal
                    $('#edit_model').modal('show');
                }
            });
        }

        $("#edit_model #saveProject").on("click", function() {
            $("#edit_project_form").submit();
        });
        function deleteProject(button) {
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
                text: "Once deleted, you will not be able to recover this project!",
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
                        url: 'projects/' + id,
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
    </script>
@endsection
