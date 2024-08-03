@extends('layouts.app')
@section('content')
    <style>
        .scrollable-tbody {
            max-height: 320px;
            overflow-y: auto;
            display: block;
        }
    </style>
    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div class="card-header px-2 py-2 mb-2 d-flex justify-content-between">
                    <h2 class="card-title">Sub Tasks</h2>
                    {{-- <a href="{{ route('project-management.addJobs', $job->project->id) }}" class="btn btn-primary btn-xs">
                        <i class="mdi mdi-plus fs-6"></i> Add Job
                    </a> --}}
                </div>
                <div class="card-body pt-0 px-2">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">Task</th>
                                    <th class="pt-0">Start Date</th>
                                    <th class="pt-0">End Date</th>
                                    <th class="pt-0">Status</th>
                                    <th class="pt-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($job->subtasks as $task)
                                    <tr>
                                        <td>{{ $task->subtask->name ?? '-' }}</td>
                                        {{-- <td>{{ optional($job->start_date)->format('d M Y') ?? '-' }}</td>
                                        <td>{{ optional($job->end_date)->format('d M Y') ?? '-' }}</td> --}}
                                        <td>
                                            @if ($task->start_date)
                                                {{ dateformat($task->start_date, 'd M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($task->end_date)
                                                {{ dateformat($task->end_date, 'd M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            @switch($task->status)
                                                @case('to-do')
                                                    <span class="badge bg-primary xs"> To-Do</span>
                                                @break

                                                @case('in-process')
                                                    <span class="badge bg-info xs">In process</span>
                                                @break

                                                @case('completed')
                                                    <span class="badge bg-success xs">Completed</span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger xs">-</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu drodpdown-custom p-0"
                                                    aria-labelledby="dropdownMenuButton7">
                                                    <a href="{{ route('project-management.dailyReport', $task->id) }}"
                                                        class="dropdown-item d-flex align-items-center">
                                                        <i data-feather="eye" class="icon-sm me-2"></i>
                                                        <span class="">View Daily Report</span>
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
            <div class="card mb-2">
                <div class="card-header px-2 py-2 mb-2 d-flex justify-content-between">
                    <h2 class="card-title">Stock Consumption</h2>
                    {{-- <a href="{{ route('project-management.addJobs', $job->project->id) }}" class="btn btn-primary btn-xs">
                        <i class="mdi mdi-plus fs-6"></i> Add Job
                    </a> --}}
                </div>
                <div class="card-body pt-0 px-2">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">Product Name</th>
                                    <th class="pt-0">Quantity</th>
                                    <th class="pt-0">Date</th>
                                    <th class="pt-0">Task Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consumptionDetails as $task)
                                    <tr>
                                        <td>{{ $task['product_name'] }}</td>
                                        <td>{{ $task['quantity'] }}</td>
                                        <td>{{ $task['date'] }}</td>
                                        <td>{{ $task['task_name'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card mb-2">
                <div class="card-header px-2 py-2">
                    <h2 class="card-title mb-2">Progress</h2>
                </div>
                <div class="card-body pt-0 px-2 pb-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: {{ $job->calculateProgress() }}%;" aria-valuenow="{{ $job->calculateProgress() }}"
                            aria-valuemin="0" aria-valuemax="100">{{ $job->calculateProgress() }}%</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header px-2 py-2">
                    <h2 class="card-title mb-0">Basic Details</h2>
                </div>
                <div class="card-body pt-0 px-2">
                    <div class="container px-0">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Project Name</td>
                                        <td>{{ $job->project->project_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Job Name</td>
                                        <td>{{ $job->job->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status</td>
                                        <td> @switch($job->status)
                                                @case('to-do')
                                                    <span class="badge bg-primary xs"> To-Do</span>
                                                @break

                                                @case('in-process')
                                                    <span class="badge bg-info xs">In process</span>
                                                @break

                                                @case('completed')
                                                    <span class="badge bg-info xs">Completed</span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger xs">-</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Start Date</td>
                                        <td>{{ dateformat($job->start_date, 'd M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">End Date</td>
                                        <td>{{ dateformat($job->end_date, 'd M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Assigned To</td>
                                        <td>{{ $job->site_head->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Division</td>
                                        <td>{{ $job->division->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Sub Division</td>
                                        <td>{{ $job->subdivision->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Site Name</td>
                                        <td>{{ $job->site->name ?? '-' }}</td>
                                    </tr>
                                    @foreach ($job->inputs as $item)
                                        <tr>
                                            <td class="fw-bold">{{ $item->input->name ?? '-' }}</td>
                                            <td>{{ $item->value ?? '-' }} {{ $item->uom->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Horizontal wizard</h4>
                    <div id="wizard">
                        <form id="myForm">
                            <h3>Step 1</h3>
                            <section>
                                <h4>First Step</h4>
                                <input type="text" name="firststepinput" >
                                <input type="text" name="firststepinput2" >
                            </section>

                            <h3>Step 2</h3>
                            <section>
                                <h4>Second Step</h4>
                                <input type="text" name="secondstepinput" required>
                                <input type="text" name="secondstepinput2" required>
                            </section>

                            <h3>Step 3</h3>
                            <section>
                                <h4>Third Step</h4>
                                <!-- Add your content for the third step here -->
                            </section>

                            <h3>Step 4</h3>
                            <section>
                                <h4>Fourth Step</h4>
                                <!-- Add your content for the fourth step here -->
                            </section>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
@section('js')
    <script>
        // Usage
        // $(document).ready(function() {
        //     var val = {
        //         // Specify validation rules
        //         rules: {
        //             // (step 1)
        //             firststepinput: {
        //                 required: true,
        //             },
        //             firststepinput2: {
        //                 required: true,
        //             },
        //             secondstepinput: {
        //                 required: true,
        //             },
        //             secondstepinput2: {
        //                 required: true,
        //             },
        //         },
        //         // Specify validation error messages
        //         messages: {

        //         },
        //     }
        //     $("#myForm").multiStepForm({
        //         validations: val,
        //     });
        // });
    </script>
@endsection
