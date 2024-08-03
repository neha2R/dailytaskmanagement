@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('project-management.jobs') }}" class="nav-link active tab-heading"
                    aria-selected="true">Jobs</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('project-management.sub-tasks') }}" class="nav-link tab-heading"
                    aria-selected="false">Sub-Tasks</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('project-management.inputs') }}" class="nav-link tab-heading"
                    aria-selected="false">Inputs</a>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_job"
                data-bs-whatever="@getbootstrap">
                <i class="mdi mdi-plus fs-6"></i> Add Job
            </button>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
            <h5 class="info-heading ms-3">Manage Jobs</h5>
        </div>
    
        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th class="text-center">No of Sub-Tasks</th>
                            <th class="text-center">No of Inputs</th>
                            <th >Status</th>
                            <th >Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobs as $key => $job)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $job->name }}</td>
                                <td class="text-center">{{ $job->subtask_count }}</td>
                                <td class="text-center">{{ $job->inputs_count }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $job->id }}" id="flexSwitchCheck{{ $key }}"
                                            {{ $job->is_active ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td >
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $key }}">
                                            <a href="#" class="dropdown-item d-flex align-items-center"
                                                onclick="editJob('{{ route('project-management.editJobAndTasks', $job->id) }}')">
                                                <i class="icon-sm me-2 " data-feather="edit"></i>
                                                <span class="">Edit</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{route('project-management.manageSubTasks', $job->id)}}" >
                                                <i class="icon-sm me-2 " data-feather="edit"></i>
                                                <span class="">Manage Sub Tasks</span>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{route('project-management.manageInputs', $job->id)}}" >
                                                <i class="icon-sm me-2 " data-feather="edit"></i>
                                                <span class="">Manage Inputs</span>
                                            </a>
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
    
    <div class="modal fade" id="add_job" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">
                        <i class="mdi mdi-briefcase-plus-outline me-2"></i> Create New Job
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_job_form" method="post" action="{{ route('project-management.jobs.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="job_name" class="form-label">Job Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input value="{{ old('name') }}" id="job_name" placeholder="Enter Job Name"
                                    class="form-control" name="name" type="text">
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
    @component('components.editmoal')
    @endcomponent
@endsection
@section('js')
    <script>
        // Define the rules and messages for form
        $(document).ready(function() {
            const addJobRules = {
                name: {
                    required: true,
                    uniqueJobName: true,
                    minlength: 3,
                    maxlength: 50,
                    noDoubleSpaces: true
                }
            };
            const addJobMessages = {
                name: {
                    required: "Job name is required.",
                    maxlength: "Job name should not exceed 50 characters.",
                }
            };
            $.validator.addMethod("uniqueJobName", function(value, element, callback) {
                var isUnique = false;
                $.ajax({
                    url: "{{ route('project-management.checkUniqueJobName') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    async: false,
                    data: {
                        name: value
                    },
                    success: function(response) {
                        isUnique = response.unique;
                    }
                });
                return isUnique;

            }, "This Name is already in use. Please choose a different name.");

            initializeValidation("#add_job_form", addJobRules, addJobMessages);

            $('.item_status').change(function() {
                handleStatusChange('{{ route('project-management.jobAndSubTaskStatus') }}', $(this).prop(
                    'checked'), $(this).data(
                    "id"));
            });
        });

        function editJob(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    // Select the edit-container
                    var editContainer = $('#edit-container');
                    var headingElement = document.getElementById("edit_heading");

                    // Create the form HTML with modified IDs
                    var formHTML = `
                        <form id="edit_job_form" method="post" action="{{ route('project-management.jobs.update') }}">
                            @csrf
                            <div class="modal-body">
                                    <div class="row mb-3">
                                        <input type="hidden" name="id" value="${data.data.id}">
                                        <div class="col-lg-3 col-sm-3">
                                            <label for="job_name" class="form-label">Job Name</label>
                                        </div>
                                        <div class="col-lg-8 col-sm-8 position-relative">
                                            <input value="${data.data.name}" id="edit_job_name" placeholder="Enter Job Name"
                                                class="form-control" name="name" type="text">
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
                    const editJobRules = {
                        name: {
                            required: true,
                            minlength: 3,
                            maxlength: 50,
                            noDoubleSpaces: true
                        }
                    };
                    const editJobMessages = {
                        name: {
                            required: "Job name is required.",
                            maxlength: "Job name should not exceed 50 characters.",
                        }
                    };
                    // Initialize validation for the edit division form
                    initializeValidation("#edit_job_form", editJobRules, editJobMessages);
                    headingElement.innerHTML="Edit Job Details";
                    // Show the modified modal
                    $('#edit_model').modal('show');
                }
            });
        }
    </script>
@endsection
