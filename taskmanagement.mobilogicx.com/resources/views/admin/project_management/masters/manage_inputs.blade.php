@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addInputsModal"
            data-bs-whatever="@getbootstrap">
            <i class="mdi mdi-plus fs-6"></i> Add Inputs
        </button>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
            <h5 class="ms-">Manage Inputs for Job: <span class="info-heading text-primary">{{ $job->name }}</span></h5>
        </div>

        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Input Name</th>
                            <th>Job Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($job->inputs as $key => $input)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $input->name }}</td>
                                <td>{{ $job->name }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $input->pivot->id }}" id="flexSwitchCheck{{ $key }}"
                                            {{ $input->pivot->is_active ? 'checked' : '' }} />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Inputs Modal -->
    <div class="modal fade" id="addInputsModal" tabindex="-1" aria-labelledby="addInputsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInputsModalLabel">
                        <i class="mdi mdi-briefcase-plus-outline me-2"></i> Add Inputs
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addInputsForm" method="post" action="{{ route('project-management.manageInputsStore') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="jobName" class="form-label">Job Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input type="hidden" value="{{ $job->id }}" name="job_id">
                                <input value="{{ old('name') }}" id="jobName" placeholder="{{ $job->name }}"
                                    class="form-control" name="job_name" readonly type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="selectedInputs" class="form-label">Select Inputs</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <select style="width: 100%;" name="inputs[]" id="inputsSelect"
                                    class="form-select js-example-basic-multiple" multiple="multiple">
                                    @forelse ($remainingInputs as $input)
                                        <option value="{{ $input->id }}">{{ $input->name }}</option>
                                    @empty
                                    @endforelse
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
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Validation rules and messages
            const validationRules = {
                "inputs[]": {
                    required: true,
                }
            };
            const validationMessages = {
                "inputs[]": {
                    required: "Please select at least one input.",
                }
            };

            // Initialize validation
            initializeValidation("#addInputsForm", validationRules, validationMessages);

            // Initialize select2
            initializeSelect2('#inputsSelect', '#addInputsModal');

            // Handle input status change
            $('.item_status').change(function() {
                handleStatusChange('{{ route('project-management.manageInputsStatus') }}', $(this).prop(
                    'checked'), $(this).data('id'));
            });
        });
    </script>
@endsection
