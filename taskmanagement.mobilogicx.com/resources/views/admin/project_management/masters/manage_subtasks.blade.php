@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-end mb-2">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_sub_task"
            data-bs-whatever="@getbootstrap">
            <i class="mdi mdi-plus fs-6"></i> Add Sub Task
        </button>
    </div>


    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
            <h5 class="ms-">Manage Sub tasks for job :- <span class="info-heading text-primary">{{$job->name}}</span> </h5>
        </div>

        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Sub Task Name</th>
                            <th>Job Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($job->subtasks as $key => $task)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $task->name }}</td>
                                <td>{{ $job->name }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $task->pivot->id }}" id="flexSwitchCheck{{ $key }}"
                                            {{ $task->pivot->is_active ? 'checked' : '' }} />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_sub_task" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">
                        <i class="mdi mdi-briefcase-plus-outline me-2"></i> Add Sub Task
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="add_subtask_form" method="post" action="{{ route('project-management.manageSubTasksStore') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="job_name" class="form-label">Job Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">
                                <input type="hidden" value="{{ $job->id }}" name="job_id">
                                <input value="{{ old('name') }}" id="job_name" placeholder="{{ $job->name }}"
                                    class="form-control" name="job_name" readonly value="{{ $job->name }}"
                                    type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-3">
                                <label for="subtasks" class="form-label">Sub Tasks</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-relative">

                                <select style="width: 100%;" name="subtasks[]" id="subtasks"
                                    class="form-select js-example-basic-multiple" multiple="multiple">
                                    @forelse ($remainingSubTasks as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
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
        // Define the rules and messages for form
        $(document).ready(function() {
            const addJobRules = {
                "subtasks[]": {
                    required: true,
                }
            };
            const addJobMessages = {
                "subtasks[]": {
                    required: "Please select atleast one subtask.",
                }
            };

            initializeValidation("#add_subtask_form", addJobRules, addJobMessages);

            initializeSelect2('#subtasks', '#add_sub_task')

            $('.item_status').change(function() {
                handleStatusChange('{{ route('project-management.manageSubTaskStatus') }}', $(this).prop(
                    'checked'), $(this).data(
                    "id"));
            });
        });
    </script>
@endsection
