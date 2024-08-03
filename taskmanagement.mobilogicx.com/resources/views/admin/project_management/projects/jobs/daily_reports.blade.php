@extends('layouts.app')
@section('content')
    <style>
        .scrollable-tbody {
            max-height: 320px;
            overflow-y: auto;
            display: block;
        }

        .card-header {
            border-bottom: 1px solid #6571ff;
        }

        .info-heading {
            padding-top: 13px;
        }
    </style>
    <div class="row">
        <div class="col-8">
            <div class="accordion" id="accordionExample">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-2 px-2 info-heading">
                        <h5 class="mb-0">Progress Report</h5>
                    </div>

                    @forelse ($task->progress as $key => $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $key }}">
                                <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $key }}" aria-expanded="false"
                                    aria-controls="collapse{{ $key }}">
                                    Date: {{ dateformat($item->work_date, 'd M Y') }}
                                    <div class="position-absolute end-0 me-5">
                                        <p class="m-0">Progress {{ $item->progress_quantity }} {{ $item->uom->name }}</p>
                                    </div>
                                </button>
                            </h2>

                            <div id="collapse{{ $key }}" class="accordion-collapse collapse"
                                aria-labelledby="heading{{ $key }}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <h6 class="">Labour & Mason</h6>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Labour</th>
                                                    <td>{{ $item->labour_quantity }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Mason</th>
                                                    <td>{{ $item->mason_quantity }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="table-responsive">
                                                <h6 class="mb-3">Products</h6>
                                                <table class="table ">
                                                    <thead>
                                                        <tr>
                                                            <th>Product Name</th>
                                                            <th>Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($item->products as $product)
                                                            <tr>
                                                                <td>{{ $product->product->name ?? '-' }}</td>
                                                                <td>{{ $product->quantity }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="2">No products</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="table-responsive">
                                                <h6 class="mb-3">Machinery</h6>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Machine</th>
                                                            <th>Hours</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($item->machinery as $vehicle)
                                                            <tr>
                                                                <td>{{ $vehicle->vehicle->vehicle_number }}</td>
                                                                <td>{{ $vehicle->total_duration_minutes }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="2">No machinery</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-secondary text-center" role="alert">
                            No progress reports available.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
        <div class="col-4">
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
                                        <td>{{ $task->projectJob->project->project_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Job Name</td>
                                        <td>{{ $task->projectJob->job->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status</td>
                                        <td> @switch($task->status)
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
                                        <td>{{ $task->start_date ? dateformat($task->start_date, 'd M Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">End Date</td>
                                        <td>{{ $task->end_date ? dateformat($task->end_date, 'd M Y') : '-' }}</td>

                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Assigned To</td>
                                        <td>{{ $task->projectJob->site_head->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Division</td>
                                        <td>{{ $task->projectJob->division->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Sub Division</td>
                                        <td>{{ $task->projectJob->subdivision->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Site Name</td>
                                        <td>{{ $task->projectJob->site->name ?? '-' }}</td>
                                    </tr>
                                    @foreach ($task->projectJob->inputs as $item)
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
@endsection
@section('js')
@endsection
