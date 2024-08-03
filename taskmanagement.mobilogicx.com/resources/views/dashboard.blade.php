@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
        </div>
        {{-- <div class="d-flex align-items-center flex-wrap text-nowrap">
            <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
                <span class="input-group-text input-group-addon bg-transparent border-primary" data-toggle><i
                        data-feather="calendar" class="text-primary"></i></span>
                <input type="text" class="form-control bg-transparent border-primary" placeholder="Select date" data-input>
            </div>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card shadow-sm"> <!-- Bootstrap shadow utility for a subtle shadow effect -->
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="d-flex align-items-center mb-0">
                                    <span class="mdi mdi-cube-outline me-2 fs-3"></span>Total Products
                                </h6>
                                <h3 class="mb-0 text-primary">{{ $countTotal }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="d-flex align-items-center mb-0">
                                    <span class="mdi mdi-alert-outline me-2 fs-3"></span>Below Min Level
                                </h6>
                                <h3 class="mb-0 text-warning">{{ $belowMinLevel->total() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="d-flex align-items-center mb-0">
                                    <span class="mdi mdi-close-box-outline me-2 fs-3"></span>Out Of Stock
                                </h6>
                                <h3 class="mb-0 text-danger">{{ $outOfStockProducts->total() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- row -->
    <div class="row">

        <div class="col-lg-6 col-xl-6 stretch-card">
            <div class="card">
                <div class="card-body product-container">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title border-primary">Overdue Tasks</h6>
                    </div>
                    <div class="table-responsive">
                        @if ($taskdata->isEmpty())
                            <h6
                                class="text-muted text-center product-container-h6">
                                No Tasks Available</h6>
                        @else
                            <table class="table {{ $belowMinLevel->total() <= 4 ? 'mb-4' : '' }}">
                                <thead class="thead-light" >
                                    <tr>
                            <th class="pt-0">Sr no</th>
                            <th class="pt-0">Task Name </th>
                            <th class="pt-0">Start Date</th>
                            <th class="pt-0">End Date</th>
                            <th class="pt-0">Assign To</th>
                            <th class="pt-0">Status</th>
                            <th class="pt-0">Action</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($taskdata as $key => $item)
                            <tr>
                                <td class="center">{{ $key + 1 }}</td>
                                <td>{{ $item->name ?? '-' }}</td>
                                <td>{{ $item->startdate ?? '-' }}</td>
                                <td>{{ $item->enddate ?? '-' }}</td>
                                <td>{{ $item->user ? $item->user->name : '-' }}</td>


                                <!---<td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                            {{ $item->is_active ? 'checked' : '' }} />
                                    </div>
                                </td>--->
                                <td>
                                        @switch($item->status)
                                            @case('to-do')
                                                <span class="badge bg-primary xs"> To-Do</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-info xs">Cancelled</span>
                                            @break

                                            @case('overdue')
                                                <span class="badge bg-danger xs">Overdue</span>
                                            @break

                                            @case('in-process')
                                                <span class="badge bg-info xs">In process</span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-success xs">Completed</span>
                                            @break

                                            @case('upcoming')
                                                <span class="badge bg-success xs">Upcoming</span>
                                            @break

                                            <span class="badge bg-danger xs">-</span>

                                            @default
                                        @endswitch
                                    </td>
                                    <!-- <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                            {{ $item->is_active ? 'checked' : '' }} />
                                    </div>
                                    </td> -->

                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                        <a href="{{ route('admin.manage-tasks.show', $item->id) }}" 
                                                class="dropdown-item d-flex align-items-center"><i data-feather="eye"
                                                    class="icon-sm me-2"></i> <span class="">View</span></a>
                                         
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                                </tbody>
                            </table>
                            @include('pagination', ['data' => $taskdata])
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-6 stretch-card">
            <div class="card">
                <div class="card-body product-container">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h6 class="card-title border-primary">Today's Tasks</h6>
                    </div>
                    <div class="table-responsive">
                        @if ($tasktodaydata->isEmpty())
                            <h6
                                class="text-muted text-center product-container-h6">
                                No Tasks Available</h6>
                        @else
                            <table class="table {{ $belowMinLevel->total() <= 4 ? 'mb-4' : '' }}">
                                <thead class="thead-light" >
                                    <tr>
                            <th class="pt-0">Sr no</th>
                            <th class="pt-0">Task Name </th>
                            <th class="pt-0">Start Date</th>
                            <th class="pt-0">End Date</th>
                            <th class="pt-0">Assign To</th>
                            <th class="pt-0">Status</th>
                            <th class="pt-0">Action</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($tasktodaydata as $key => $item)
                            <tr>
                                <td class="center">{{ $key + 1 }}</td>
                                <td>{{ $item->name ?? '-' }}</td>
                                <td>{{ $item->startdate ?? '-' }}</td>
                                <td>{{ $item->enddate ?? '-' }}</td>
                                <td>{{ $item->user ? $item->user->name : '-' }}</td>


                                <!---<td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                            {{ $item->is_active ? 'checked' : '' }} />
                                    </div>
                                </td>--->
                                <td>
                                        @switch($item->status)
                                            @case('to-do')
                                                <span class="badge bg-primary xs"> To-Do</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-info xs">Cancelled</span>
                                            @break

                                            @case('overdue')
                                                <span class="badge bg-danger xs">Overdue</span>
                                            @break

                                            @case('in-process')
                                                <span class="badge bg-info xs">In process</span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-success xs">Completed</span>
                                            @break

                                            @case('upcoming')
                                                <span class="badge bg-success xs">Upcoming</span>
                                            @break

                                            <span class="badge bg-danger xs">-</span>

                                            @default
                                        @endswitch
                                    </td>
                                    <!-- <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input item_status" type="checkbox" role="switch"
                                            data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                            {{ $item->is_active ? 'checked' : '' }} />
                                    </div>
                                    </td> -->

                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                             <a href="{{ route('admin.manage-tasks.show', $item->id) }}" 
                                                class="dropdown-item d-flex align-items-center"><i data-feather="eye"
                                                    class="icon-sm me-2"></i> <span class="">View</span></a>
                                                                       
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                                </tbody>
                            </table>
                            @include('pagination', ['data' => $tasktodaydata])
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="modal fade" id="fuelDetailsModel" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Task Details</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body" id="appendDetails">

                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
@section('js')

<script>
        $(document).ready(function() {
        
         
            $('#document').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Change Document',
                }
            });

            // select 2 and flatepicker
            if ($("#user_id").length) {
                $("#user_id").select2({
                    dropdownParent: $("#add_fuel"),
                });
            }
           if ($('#startdate').length) {
                const serviceDateInput = flatpickr("#startdate", {
                    wrap: true,
                    dateFormat: "d M Y",
                });
            }
            if ($('#edit_startdate').length) {
                const serviceDateInput = flatpickr("#edit_startdate", {
                    wrap: true,
                    dateFormat: "d M Y",
                });
            }
            if ($('#edit_enddate').length) {
                const serviceDateInput = flatpickr("#edit_enddate", {
                    wrap: true,
                    dateFormat: "d M Y",
                });
            }
           if ($('#enddate').length) {
                const serviceDateInput = flatpickr("#enddate", {
                    wrap: true,
                    dateFormat: "d M Y",
                });
            }  
           
            $('.item_status').on("click", function() {
                var id = $(this).prop("id");
                //alert(id);
                var url = '{{ route('admin.task.status') }}';

                handleCancelled(url, id);
            });
      
            // Define validation rules and messages
            const rules = {
                task_name: {
                    required: true,
                },
                startdate: {
                    required: true,
                },
                enddate: {
                    required: true,
                },
                user_id: {
                    required: true,
                },
                priority: {
                    required: true,
                },
              
                // fule_station: {
                //     required: true,
                // },
                // document: {
                //     required: true,
                //     extension: "jpg|jpeg|png|pdf", // Adjust the file extensions as needed
                // },
            };

            const messages = {
                task_name: {
                    required: "Please select task name.",
                },
                user_id: {
                    required: "Please select a user.",
                },
                startdate: {
                    required: "Please select a startdate.",
                },
                enddate: {
                    required: "Please select a startdate.",
                },
                priority: {
                    required: "Please select priority level.",

                },
                amount: {
                    required: "Please enter the amount.",

                },
              
                // fule_station: {
                //     required: "Please select a fuel station.",
                // },
                // document: {
                //     required: "Please upload a document.",
                //     extension: "Please upload a valid file (jpg, jpeg, png, pdf).",
                // },
            };
            // Initialize validation for the form
            initializeValidation("#add-task-form", rules, messages);

        });
        $('#role_id').change(function() {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'get_users/' + $(this).val(),
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#user_id').prop("disabled", false).empty().append($("<option></option>")
                            .attr("value", "")
                            .attr("selected", "")
                            .attr("disabled", "")
                            .text('Select seniors'))
                        $.each(data.users, function(key, value) {
                            $('#user_id').append($("<option></option>")
                                .attr("value", value.id)
                                .text(value.name))
                        })
                    }
                },

                complete: function() {
                    $('#spin').addClass('d-none');
                },
            });

        });
function getTaskData(id) {
           // console.log("call");

            getData('manage-tasks/' + id, function(data) {
                $('#edit_depo').modal('show');
                $('#edit_task_id').val(data.data.id);
                $('#edit_task_name').val(data.data.name);
                $('#edit_startdate').val(data.data.startdate);
                $('#edit_enddate').val(data.data.enddate);
                $('#edit_user_id').val(data.data.user_id);
                $('#edit_taks_description').val(data.data.description);
                $('#edit_priority').val(data.data.priority);
                $('#edit_role_id').val(data.data.role_id);
            });
        }
function cancelled(id) {
           // console.log("call");

            getData('manage-tasks/' + id, function(data) {
                $('#edit_depo').modal('show');
                $('#edit_task_id').val(data.data.id);
                $('#edit_task_name').val(data.data.name);
                $('#edit_startdate').val(data.data.startdate);
                $('#edit_enddate').val(data.data.enddate);
                $('#edit_user_id').val(data.data.user_id);
                $('#edit_taks_description').val(data.data.description);
                $('#edit_priority').val(data.data.priority);
                $('#edit_role_id').val(data.data.role_id);
            });
        }
        $("#saveTaskDetails").on("click", function() {
            $("#add-task-form").submit();
        });

        function viewFuelDetails(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    data = data.data;

                    // Assuming data is the JSON object you provided
                    var modalBody = document.getElementById('appendDetails');

                    // Build the HTML content using all fields in the data
                    var content = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>Task Name:</strong></li>
                                            <li><strong>Description:</strong></li>
                                            <li><strong>Start Date:</strong></li>
                                            <li><strong>End Date:</strong></li>
                                            <li><strong>Assign To:</strong></li>
                                            <li><strong>Priority:</strong></li>


                                          
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li>${data.name ?? "-"}</li>
                                            <li>${data.description ?? "-"}</li>
                                            <li>${data.startdate ?? "-"}</li>
                                            <li>${data.enddate ?? "-"}</li>
                                            <li>${data.user ? data.user.name : "-"}</li>
                                         
                                            
                                            <li>${(data.priority == 1 ) ? "Low" : (( data.priority == 2 ) ? "Medium" : "High")}</li>
                                     
                                        </ul>
                                    </div>
                                </div>
                                ${data.document ? `<div class="text-center"><button class="btn btn-primary mt-3" onclick="showDocument('${data.document}')">View Document</button></div>` : ''}
                                `;

                    // Append the content to the modal body
                    modalBody.innerHTML = content;

                    // Open the modal
                    $('#fuelDetailsModel').modal('show');
                }
            });
        }
    </script>
    @endsection
