@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('project-management.jobs') }}" class="nav-link  tab-heading" aria-selected="true">Jobs</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('project-management.sub-tasks') }}" class="nav-link tab-heading"
                    aria-selected="false">Sub-Tasks</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('project-management.inputs') }}" class="nav-link active tab-heading"
                    aria-selected="false">Inputs</a>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_input"
                data-bs-whatever="@getbootstrap">
                <i class="mdi mdi-plus fs-6"></i> Add Inputs
            </button>
        </div>
    </div>

    <div class="card mb-4 mt-2">
        <div class="card-header d-flex justify-content-between align-items-center px-3 py-2">
            <h5 class="info-heading mb-0">Manage Inputs</h5>
        </div>

        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Input Name</th>
                            <th>Jobs</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inputs as $key => $input)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $input->name }}</td>
                                <td>
                                    @if ($input->jobs->isNotEmpty())
                                        {{ $input->jobs->pluck('name')->slice(0, 2)->implode(', ') }}
                                        @if ($input->jobs->count() > 2)
                                            <span class="text-primary" style="cursor:pointer;" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom"
                                                title="{{ implode(', ',$input->jobs->slice(2)->pluck('name')->toArray()) }}">
                                                ({{ $input->jobs->count() - 2 }} more)
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-secondary">No jobs associated. </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $input->id }}" id="flexSwitchCheck{{ $key }}"
                                            {{ $input->is_active ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $key }}">
                                            <a href="#" class="dropdown-item d-flex align-items-center"
                                                onclick="editSubInput('{{ route('project-management.editInput', $input->id) }}')">
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


    <div class="modal fade" id="add_input" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-briefcase-plus-outline me-2"></i> Create New Inputs
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add_input_form" method="post" action="{{ route('project-management.inputs.store') }}">
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
                                <label for="subtask_name" class="form-label">Input Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8">
                                <input value="{{ old('name') }}" id="input_name" placeholder="Enter Input Name"
                                    class="form-control" name="name[0]" type="text">
                            </div>
                            <div class="col-lg-1 col-sm-1 text-center ps-0">
                                <button id="addBtn" type="button" class="btn border-0 text-primary ps-0"><i
                                        class="mdi mdi-plus-circle fs-4"></i></button>
                            </div>
                        </div>
                        <!-- Container to dynamically add and remove inputs -->
                        <div id="inputContainer"></div>
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
            const addInputsRules = {
                // job_id: {
                //     required: true,
                // }
            };
            const addInputsMessages = {
                // job_id: {
                //     required: "Job is required.",
                // }
            };

            for (let index = 0; index < 9; index++) {
                addInputsRules[`name[${index}]`] = {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                    noDoubleSpaces: true,
                    uniqueInputName: true,
                    uniqueInputsNames: true,
                };
                addInputsMessages[`name[${index}]`] = {
                    required: "Sub-Task name is required.",
                    maxlength: "Sub-Task name should not exceed 50 characters.",
                };
            };
            $.validator.addMethod("uniqueInputName", function(value, element, callback) {
                var isUnique = false;
                $.ajax({
                    url: "{{ route('project-management.checkUniqueInputName') }}",
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
            $.validator.addMethod("uniqueInputsNames", function(value, element, callback) {
                var subtaskNames = {}; // Object to store unique names
                var isValid = true;

                // Iterate over all subtask inputs
                $('[name^="name"]').each(function() {
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
            }, "All inputs names should be unique.");

            initializeValidation("#add_input_form", addInputsRules, addInputsMessages);
            // initializeSelect2("#job_id", "#add_input");

            // Counter for dynamically added sub-tasks
            let subtaskCount = 0;

            // Maximum allowed sub-tasks
            const maxSubtasks = 9;


            // Function to add input dynamically
            function addInputRow(inputName) {
                subtaskCount++;
                const inputRow = `
                    <div class="row mb-2" id="inputRow${subtaskCount}">
                        <div class="col-lg-3 col-sm-3 p-0"></div>
                        <div class="col-lg-8 col-sm-8">
                            <input value="${inputName}" class="form-control" name="name[${subtaskCount}]" type="text">
                        </div>
                        <div class="col-lg-1 col-sm-1 text-center ps-0">
                            <button type="button" class="btn border-0 text-danger ps-0" onclick="removeInput(${subtaskCount})">
                                <i class="mdi mdi-minus-circle fs-4"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#inputContainer').append(inputRow);
                // Disable "Add Input" button when the maximum is reached
                if (subtaskCount >= maxSubtasks) {
                    $('#addBtn').prop('disabled', true);
                }
            }

            // Function to remove input dynamically
            window.removeInput = function(inputIndex) {
                $(`#inputRow${inputIndex}`).remove();
                subtaskCount--;

                // Enable "Add Input" button after removing an input
                $('#addBtn').prop('disabled', false);
                // Update the name and ID series
                updateInputSeries();
            };

            // Function to update the name and ID series
            function updateInputSeries() {
                $('#inputContainer > .row').each(function(index) {
                    const currentInputIndex = index + 1;
                    $(this).attr('id', `inputRow${currentInputIndex}`);
                    $(this).find('input').attr('name', `inputs[${currentInputIndex}]`);
                    $(this).find('button').attr('onclick', `removeInput(${currentInputIndex})`);
                });
            }

            // Event handler for clicking the Add Input button
            $('#addBtn').click(function() {
                const inputName = $('#input_name').val();
                if (inputName.trim() !== '') {
                    addInputRow(inputName);
                    $('#input_name').val('');
                }
            });

            $('.item_status').change(function() {
                handleStatusChange('{{ route('project-management.inputStatus') }}', $(this).prop(
                    'checked'), $(this).data(
                    "id"));
            });
        });

        function editSubInput(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    // Select the edit-container
                    var editContainer = $('#edit-container');
                    var headingElement = document.getElementById("edit_heading");

                    // Create the form HTML with modified IDs
                    var formHTML = `
                        <form id="edit_input_form" method="post" action="{{ route('project-management.input.update') }}">
                            @csrf

                            <div class="modal-body">
                                <div class="row mb-3">
                                    <input type="hidden" name="id" value="${data.data.id}">

                                    <div class="col-lg-3 col-sm-3">
                                        <label for="subtask_name" class="form-label">Input Name</label>
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
                    const editInputRules = {
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
                    const editInputMessages = {
                        // job_id: {
                        //     required: "Job is required.",
                        // },
                        name: {
                            required: "Sub-Task name is required.",
                            maxlength: "Sub-Task name should not exceed 50 characters.",
                        }
                    };
                    // Initialize validation for the edit division form
                    initializeValidation("#edit_input_form", editInputRules, editInputMessages);
                    // initializeSelect2("#edit_job_id", "#edit_model");
                    // $('#edit_job_id').val(data.data.job_id).trigger('change');
                    headingElement.innerHTML = "Edit Input Details";
                    // Show the modified modal
                    $('#edit_model').modal('show');
                }
            });
        }
    </script>
@endsection
