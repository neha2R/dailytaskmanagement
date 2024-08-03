@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('project-management.jobs') }}" class="nav-link  tab-heading" aria-selected="true">Jobs</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('project-management.sub-tasks') }}" class="nav-link active tab-heading"
                    aria-selected="false">Sub-Tasks</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('project-management.inputs') }}" class="nav-link tab-heading"
                    aria-selected="false">Inputs</a>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_subtask"
                data-bs-whatever="@getbootstrap">
                <i class="mdi mdi-plus fs-6"></i> Add Sub-Task
            </button>
        </div>
    </div>

    <!-- Manage Sub-Tasks -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center px-3 py-2">
            <h5 class="info-heading mb-0">Manage Sub-Tasks</h5>
        </div>

        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Sub-Task Name</th>
                            <th>Jobs</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subTasks as $key => $subtask)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $subtask->name }}</td>
                                <td> {{ $subtask->jobs->pluck('name')->slice(0, 2)->implode(', ') }}
                                    @if ($subtask->jobs->isNotEmpty())
                                        @if ($subtask->jobs->count() > 2)
                                            <span class="text-primary" style="cursor:pointer;" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom"
                                                title="{{ implode(', ',$subtask->jobs->slice(2)->pluck('name')->toArray()) }}">
                                                ({{ $subtask->jobs->count() - 2 }} more)
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-secondary">No jobs associated. </span>
                                    @endif

                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $subtask->id }}" id="flexSwitchCheckChecked"
                                            {{ $subtask->is_active ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button"
                                            id="dropdownMenuButton{{ $key }}" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $key }}">
                                            <a type="button" class="dropdown-item d-flex align-items-center"
                                                onclick="editSubTask('{{ route('project-management.editSubTask', $subtask->id) }}')">
                                                <i class="icon-sm me-2 " data-feather="edit"></i>
                                                <span class="">Edit</span>
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

    <!-- Add Sub-Task Modal -->
    <div class="modal fade" id="add_subtask" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-briefcase-plus-outline me-2"></i> Create New Sub-Task
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add_subtask_form" method="post" action="{{ route('project-management.sub-tasks.store') }}">
                    @csrf
                    <div class="modal-body">
                        {{-- <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="job_id" class="form-label">Job</label>
                            </div>
                            <div class="col-lg-8 col-sm-9 position-relative">
                                <select class="js-example-basic-single" style="width: 100%;" name="job_id" id="job_id">
                                    <option selected disabled>Select Job</option>
                                    @foreach ($active_jobs as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="subtask_name" class="form-label">Sub Task Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input value="{{ old('name') }}" id="subtask_name" placeholder="Enter Sub-Task Name"
                                    class="form-control" name="subtasks[0]" type="text">
                            </div>
                            <div class="col-lg-1 col-sm-1 text-center ps-0">
                                <button id="addBtn" type="button" class="btn border-0 text-primary ps-0"><i
                                        class="mdi mdi-plus-circle fs-4"></i></button>
                            </div>
                        </div>
                        <!-- Container to dynamically add and remove sub-tasks -->
                        <div id="subtaskContainer"></div>
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
        // Define the rules and messages for add sub-task form
        $(document).ready(function() {
            const addSubTaskRules = {
                // job_id: {
                //     required: true,
                // }
            };
            const addSubTaskMessages = {
                // job_id: {
                //     required: "Job is required.",
                // }
            };

            for (let index = 0; index < 9; index++) {
                addSubTaskRules[`subtasks[${index}]`] = {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                    noDoubleSpaces: true,
                    uniqueSubTaskName: true,
                    uniqueSubtaskNames: true,
                };
                addSubTaskMessages[`subtasks[${index}]`] = {
                    required: "Sub-Task name is required.",
                    maxlength: "Sub-Task name should not exceed 50 characters.",
                };
            };
            $.validator.addMethod("uniqueSubTaskName", function(value, element, callback) {
                var isUnique = false;
                $.ajax({
                    url: "{{ route('project-management.checkUniqueSubTaskName') }}",
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
            $.validator.addMethod("uniqueSubtaskNames", function(value, element, callback) {
                var subtaskNames = {}; // Object to store unique names
                var isValid = true;

                // Iterate over all subtask inputs
                $('[name^="subtasks"]').each(function() {
                    var subtaskName = $(this).val();

                    // Check if the name is not already in the subtaskNames object
                    if (subtaskNames[subtaskName]) {
                        isValid = false;
                        return false; // Break the loop if a duplicate is found
                    }

                    // Add the name to the subtaskNames object
                    subtaskNames[subtaskName] = true;
                });

                return isValid;
            }, "All subtask names should be unique.");

            initializeValidation("#add_subtask_form", addSubTaskRules, addSubTaskMessages);
            // initializeSelect2("#job_id", "#add_subtask");

            // Counter for dynamically added sub-tasks
            let subtaskCount = 0;

            // Maximum allowed sub-tasks
            const maxSubtasks = 9;

            // Function to add sub-task dynamically
            function addSubTaskRow(subtaskName) {
                subtaskCount++;
                const subtaskRow = `
                    <div class="row mb-2" id="subtaskRow${subtaskCount}">
                        <div class="col-lg-3 col-sm-3 p-0"></div>
                        <div class="col-lg-8 col-sm-8">
                            <input value="${subtaskName}" class="form-control" name="subtasks[${subtaskCount}]" type="text" >
                        </div>
                        <div class="col-lg-1 col-sm-1 text-center ps-0">
                            <button type="button" class="btn border-0 text-danger ps-0" onclick="removeSubTask(${subtaskCount})">
                                <i class="mdi mdi-minus-circle fs-4"></i>
                            </button>
                        </div>
                    </div>
                 `;
                $('#subtaskContainer').append(subtaskRow);
                // Disable "Add Sub-Task" button when the maximum is reached
                if (subtaskCount >= maxSubtasks) {
                    $('#addBtn').prop('disabled', true);
                }
            }

            // Function to remove sub-task dynamically
            window.removeSubTask = function(subtaskIndex) {
                $(`#subtaskRow${subtaskIndex}`).remove();
                subtaskCount--;

                // Enable "Add Sub-Task" button after removing a sub-task
                $('#addBtn').prop('disabled', false);
                // Update the name and ID series
                updateSubtaskSeries();
            };
            // Function to update the name and ID series
            function updateSubtaskSeries() {
                $('#subtaskContainer > .row').each(function(index) {
                    const currentSubtaskIndex = index + 1;
                    $(this).attr('id', `subtaskRow${currentSubtaskIndex}`);
                    $(this).find('input').attr('name', `subtasks[${currentSubtaskIndex}]`);
                    $(this).find('button').attr('onclick', `removeSubTask(${currentSubtaskIndex})`);
                });
            }


            // Event handler for clicking the Add Sub-Task button
            $('#addBtn').click(function() {
                const subtaskName = $('#subtask_name').val();
                if (subtaskName.trim() !== '') {
                    addSubTaskRow(subtaskName);
                    $('#subtask_name').val('');
                }
            });

            $('.item_status').change(function() {
                handleStatusChange('{{ route('project-management.subTaskStatus') }}', $(this).prop(
                    'checked'), $(this).data(
                    "id"));
            });
        });

        function editSubTask(url) {
            getData(url, function(data) {
                console.log(data);
                if (data.status == 200) {
                    // Select the edit-container
                    var editContainer = $('#edit-container');
                    var headingElement = document.getElementById("edit_heading");

                    // Create the form HTML with modified IDs
                    var formHTML = `
                        <form id="edit_subtask_form" method="post" action="{{ route('project-management.sub-tasks.update') }}">
                            @csrf

                            <div class="modal-body">
                                <div class="row mb-3">
                                    <input type="hidden" name="id" value="${data.data.id}">
                                    <div class="col-lg-3 col-sm-3">
                                        <label for="subtask_name" class="form-label">Sub Task Name</label>
                                    </div>
                                    <div class="col-lg-8 col-sm-8">
                                        <input value="${data.data.name}" id="subtask_name" placeholder="Enter Sub-Task Name"
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
                    const editSubTaskRules = {
                        // job_id: {
                        //     required: true,
                        // },
                        name: {
                            required: true,
                            noDoubleSpaces: true,
                            minlength: 3,
                            maxlength: 50,
                        }
                    };
                    const editSubTaskMessages = {
                        // job_id: {
                        //     required: "Job is required.",
                        // },
                        name: {
                            required: "Sub-Task name is required.",
                            maxlength: "Sub-Task name should not exceed 50 characters.",
                        }
                    };
                    // Initialize validation for the edit division form
                    initializeValidation("#edit_subtask_form", editSubTaskRules, editSubTaskMessages);
                    // initializeSelect2("#edit_job_id", "#edit_model");
                    // $('#edit_job_id').val(data.data.parent_id).trigger('change');
                    headingElement.innerHTML = "Edit Sub Task Details";
                    // Show the modified modal
                    $('#edit_model').modal('show');
                }
            });
        }
    </script>
@endsection
