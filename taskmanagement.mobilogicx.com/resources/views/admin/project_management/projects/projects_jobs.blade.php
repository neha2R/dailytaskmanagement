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
            <div class="card">
                <div class="card-header px-2 py-2 mb-2 d-flex justify-content-between">
                    <h2 class="card-title">Manage Jobs</h2>
                    <a href="{{ route('project-management.addJobs', $project->id) }}" class="btn btn-primary btn-xs">
                        <i class="mdi mdi-plus fs-6"></i> Add Job
                    </a>
                </div>
                <div class="card-body pt-0 px-2">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">Job No</th>
                                    <th class="pt-0">Job Name</th>
                                    <th class="pt-0">Start Date</th>
                                    <th class="pt-0">End Date</th>
                                    <th class="pt-0 d-xl-block d-lg-none d-none">Assigned To</th>
                                    <th class="pt-0">Status</th>
                                    <th class="pt-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($project->jobs as $job)
                                    <tr>
                                        <td>{{ env('PrefixJob') . $job->id }}</td>
                                        <td>{{ $job->job->name }}</td>
                                        <td>{{ dateformat($job->start_date, 'd M Y') }}</td>
                                        <td>{{ dateformat($job->end_date, 'd M Y') }}</td>
                                        <td class="d-xl-block d-lg-none d-none">{{ $job->site_head->name }}</td>
                                        <td>
                                            @switch($job->status)
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
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu drodpdown-custom p-0"
                                                    aria-labelledby="dropdownMenuButton7">
                                                    <a href="{{ route('project-management.viewJob', $job->id) }}"
                                                        class="dropdown-item d-flex align-items-center">
                                                        <i data-feather="eye" class="icon-sm me-2"></i>
                                                        <span class="">View</span>
                                                    </a>
                                                    {{-- Additional actions can be added here (e.g., Edit and Delete) --}}
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
        <div class="col-4">
            <div class="card mb-2">
                <div class="card-header px-2 py-2">
                    <h2 class="card-title mb-2">Progress</h2>
                </div>
                <div class="card-body pt-0 px-2 pb-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: {{ $project->calculateProgress() }}%;"
                            aria-valuenow="{{ $project->calculateProgress() }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $project->calculateProgress() }}%</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header px-2 py-2">
                    <h2 class="card-title mb-0">Project Details</h2>
                </div>
                <div class="card-body pt-0 px-2">
                    <div class="container px-0">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Project Name</td>
                                        <td>{{ $project->project_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Contract Number</td>
                                        <td>{{ $project->contract_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Start Date</td>
                                        <td>{{ dateformat($project->start_date, 'd M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">End Date</td>
                                        <td>{{ dateformat($project->end_date, 'd M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Assigned To</td>
                                        <td>{{ $project->user->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Vendor</td>
                                        <td>{{ $project->vendor->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">POC Name</td>
                                        <td>{{ $project->poc_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Contact Number</td>
                                        <td>{{ $project->contact_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Email</td>
                                        <td>{{ $project->email }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
