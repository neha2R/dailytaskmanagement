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
                    <h2 class="card-title">Manage Tasks</h2>
                  
                </div>
                <div class="card-body pt-0 px-2">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">Sr no</th>
                                    <th class="pt-0">Progress Report</th>
                                    <th class="pt-0">Start Date</th>
                                    <th class="pt-0">End Date</th>
                                    <th class="pt-0 d-xl-block d-lg-none d-none">Assigned To</th>
                                    <th class="pt-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($taskprogress as $key => $tasks)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $tasks->progress_report ? $tasks->progress_report : ''}}</td>
                                        <td>{{ $tasks->start_date ? dateformat($tasks->start_date, 'd M Y') : '-'}}</td>
                                        <td>{{ $tasks->end_date ? dateformat($tasks->end_date, 'd M Y') : '-' }}</td>
                                        <td class="d-xl-block d-lg-none d-none">{{ $tasks->user->name }}</td>
                                        <td>
                                            @switch($tasks->status)
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
                                @endforeach

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
           
            <div class="card">
                <div class="card-header px-2 py-2">
                    <h2 class="card-title mb-0">Task Details</h2>
                </div>
                <div class="card-body pt-0 px-2">
                    <div class="container px-0">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Task Name</td>
                                        <td>{{ $data->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Description</td>
                                        <td>{{ $data->description }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Start Date</td>
                                        <td>{{ $data->startdate }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">End Date</td>
                                        <td>{{ $data->enddate }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Assign To</td>
                                        <td>{{ $data->user ? $data->user->name : "-" }}</td>
                                    </tr>
                                    <tr>
                                    
                                        <td class="fw-bold">Priority</td>
                                        <td>{{ ($data->priority == 1 ) ? "Low" : (( $data->priority == 2 ) ? "Medium" : "High") }}</td>
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
