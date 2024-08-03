@extends('layouts.app')
<style>
    .list-unstyled li {
        padding-bottom: 10px;
    }
</style>
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0 font-weight-bold">Task Management
            </h4>
        </div>
        <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-primary btn-xs mb-3" data-bs-toggle="modal" data-bs-target="#add_fuel"
                    data-bs-whatever="@getbootstrap"> <i class="mdi mdi-plus"></i>Add
                    Task</button>
            </div>
    </div>
    <div class="container">
        <div class="row justify-content-center row-cols-7">
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Task</h5>
                        <hr>
                        <p class="card-text">{{ $taskdata->count() ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Today's</h5>
                        <hr>
                        <p class="card-text">
                            {{ \App\Models\ManageTask::whereDate('startdate',\Carbon\Carbon::today()->toDateString())->where('status', 'to-do')->count(); }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Completed</h5>
                        <hr>
                        <p class="card-text">
                            {{ \App\Models\ManageTask::where('status', 'completed')->count(); }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Inprogress</h5>
                        <hr>
                        <p class="card-text">
                        {{ \App\Models\ManageTask::where('status', 'in-process')->count(); }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Cancel</h5>
                        <hr>
                        <p class="card-text">
                        {{ \App\Models\ManageTask::where('status', 'cancelled')->count(); }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Overdue</h5>
                        <hr>
                        <p class="card-text">
                        {{ \App\Models\ManageTask::where('status', 'overdue')->count(); }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Upcoming</h5>
                        <hr>
                        <p class="card-text">
                        {{ \App\Models\ManageTask::where('status', 'upcoming')->count(); }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
           
            <div class="table-responsive">
            <div class="fillter-cotent d-flex align-items-center justify-content-end mb-3">
            <div>
            <h6 class="mb-3 mb-md-0 font-weight-bold">Filter By
            </h6>
        </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle fw-bold fillter-border-right py-0 border-0" type="button"
                            id="FillterMenuButton" role="button" aria-expanded="false">All Statuses</button>
                            <!-- <i class="mdi mdi-filter me-2" style="font-size:14px;"></i>Apply Filter</button> -->
                    </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle fw-bold fillter-border-right py-0 border-0" type="button"
                            id="FillterMenuButton" role="button" aria-expanded="false">More Filters</button>
                            <!-- <i class="mdi mdi-filter me-2" style="font-size:14px;"></i>Apply Filter</button> -->
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn dropdown-toggle fw-bold fillter-border-right py-0 border-0" type="button"
                            id="dropdownMenuButton" data-bs-toggle="dropdown">All Priority</button>
                        <div class="dropdown-menu">
                        <a type="button" id="customExportCsvBtn" class="dropdown-item">Low</a>
                        <a type="button" id="customExportPdfBtn" class="dropdown-item">Medium</a>
                        <a type="button" id="customExportPdfBtn" class="dropdown-item">High</a>
                         </div>
                            <!-- <i class="mdi mdi-filter me-2" style="font-size:14px;"></i>Apply Filter</button> -->
                    </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle fw-bold fillter-border-right py-0 border-0" type="button"
                            id="dropdownMenuButton" data-bs-toggle="dropdown">
                            <i class="mdi mdi-microsoft-excel me-2" style="font-size:14px;"></i>Export</button>
                        <div class="dropdown-menu">
                            <a type="button" id="customExportCsvBtn" class="dropdown-item">Export as CSV</a>
                            <a type="button" id="customExportPdfBtn" class="dropdown-item">Export as Excel</a>
                        </div>
                    </div>
                </div>
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr no</th>
                            <th>Task Name </th>
                            <!--<th>Description</th>-->
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Assign To</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <!--<th>Cancel Task</th>-->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskdata as $key => $item)
                            <tr>
                                <td class="center">{{ $key + 1 }}</td>
                                <td>{{ $item->name ?? '-' }}</td>
                                <!--<td>{{ $item->description ?? '-' }}</td>-->
                                <td>{{ $item->startdate ?? '-' }}</td>
                                <td>{{ $item->enddate ?? '-' }}</td>
                                <td>{{ $item->user ? $item->user->name : '-' }}</td>


                                <td>{{ ($item->priority == 1 ) ? "Low" : (( $item->priority == 2 ) ? "Medium" : "High") }}</td>
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
                                                <span class="badge bg-info xs">Cancel</span>
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
                                    <!--<td>-->
                                    <!--<div class="form-check form-switch">-->
                                    <!--    <input class="form-check-input item_status" type="checkbox" role="switch"-->
                                    <!--        data-id="{{ $item->id }}" id="flexSwitchCheckChecked"-->
                                    <!--        {{ $item->is_active ? 'checked' : '' }} />-->
                                    <!--</div>-->
                                    <!--</td>-->

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
                                                    @if($item->status=="to-do" || $item->status=="upcoming")
                                                    <a type="button" onclick="getTaskData({{ $item->id }})"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="edit"
                                                        class="icon-sm me-2"></i>
                                                    <span class="">Edit</span></a>
                                                    @endif
                                                     <a type="button" id="{{$item->id}}"
                                                    class="dropdown-item d-flex align-items-center item_status"><i data-feather="edit"
                                                        class="icon-sm me-2"></i>
                                                    <span class="">Cancel</span></a>
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

    <div class="modal fade" id="add_fuel" tabindex="-1" aria-labelledby="add_fuel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Task</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="add-task-form" action="{{ route('admin.manage-tasks.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row gx-5 mb-3">
                               <div class="col-12">
                                    <label for="fule_quantity" class="form-label">Task Name</label>
                                    <input type="text" class="form-control" id="task_name" name="task_name"
                                        placeholder="Task Name">
                                </div>
                                <div class="col-12">
                                 <div class="col-lg-4 col-sm-4">
                                <label for="name" class="form-label">Description(optional)</label>
                                </div>
                                <div class="col-lg-12 col-sm-12 position-realtive">
                                <textarea name="description" class="form-control" id="descriptionTextarea" rows="4" spellcheck="false"></textarea>
                               </div>
                               </div>
                                <div class="col-12">
                                    <label for="serviceDate" class="form-label">Start Date</label>
                                    <div class="input-group flatpickr" id="startdate">
                                        <input type="text" name="startdate"
                                            class="form-control placeholde-size flatpickr-input" placeholder="Start date"
                                            data-input="" readonly="readonly" id="dateInput">
                                    </div>
                                </div>
                                 <div class="col-12">
                                    <label for="serviceDate" class="form-label">End Date</label>
                                    <div class="input-group flatpickr" id="enddate">
                                        <input type="text" name="enddate"
                                            class="form-control placeholde-size flatpickr-input" placeholder="End date"
                                            data-input="" readonly="readonly" id="dateInput">
                                    </div>
                                </div>
                              <div class="col-12">
                            <div class="col-12">
                                <label for="department" class="form-label">Department</label>
                                <select required class="form-select" name="emp_type" id="emp_type">
                                    <option selected disabled>Select department</option>
                                    @foreach ($department as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                            <div class="col-12">
                            <div class="col-12">
                                <label for="role" class="form-label">Role</label>


                                     <select required style="width: 100%;"
                                        class="form-select js-example-basic-single" disabled name="role_id" id="role_id">
                                    <option selected disabled>Select Role</option>
                                     </select>
                              </div>
                              </div>  

                                <div class="col-12">
                                   <div class="col-12">
                                    <label for="vehicle_id" class="form-label">Assign To</label>
                                    <select required style="width: 100%;"
                                        class="form-select js-example-basic-single" disabled name="user_id" id="user_id">
                                    <option selected disabled>Select User</option>
                                     </select>
                                </div>
                                </div>
                                 <div class="col-12">
                                   <div class="col-12">
                                    <label for="vehicle_id" class="form-label">Priority</label>
                                    <select style="width: 100%;" name="priority" id="priority"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select Priority </option>
                                       
                                            <option value="1">Low</option>
                                            <option value="2">Medium</option>
                                            <option value="3">High</option>
                                      
                                    </select>
                                </div>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveTaskDetails" class="btn btn-primary">Save</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
        <div class="modal fade" id="edit_depo" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Task Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_depo_form" method="post" action="{{ route('admin.manage-tasks.update', '1') }}">
                    <div class="modal-body">
                        @csrf
                        @method('PATCH')
                        <div class="col-12">
                            <div class="col-lg-3 col-sm-3">
                                <label for="name" class="form-label">Task Name</label>
                            </div>
                            <div class="col-lg-8 col-sm-8 position-realtive">
                                <input value="{{ old('name') }}" id="edit_task_name" placeholder="Task Name"
                                    class="form-control" name="task_name" type="text">
                                <input name="id" type="hidden" value="" id="edit_task_id">

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-lg-4 col-sm-4">
                                <label for="name" class="form-label">Description</label>
                            </div>
                            <div class="col-lg-12 col-sm-12 position-realtive">
                                <textarea name="description" class="form-control" id="edit_taks_description" rows="4" spellcheck="false"></textarea>
                            </div>
                        </div>
                        
                         <div class="col-12">
                                    <label for="serviceDate" class="form-label">Start Date</label>
                                    <div class="input-group flatpickr" id="startdate">
                                        <input type="text" name="startdate"
                                            class="form-control placeholde-size flatpickr-input" placeholder="Start date"
                                            data-input="" readonly="readonly" id="edit_startdate">
                                    </div>
                                </div>
                                 <div class="col-12">
                                    <label for="serviceDate" class="form-label">End Date</label>
                                    <div class="input-group flatpickr" id="enddate">
                                        <input type="text" name="enddate"
                                            class="form-control placeholde-size flatpickr-input" placeholder="End date"
                                            data-input="" readonly="readonly" id="edit_enddate">
                                    </div>
                                </div>
                                  <div class="col-12">
                                <div class="col-12">
                                <label for="department" class="form-label">Department</label>
                            
                                <select required class="form-select" name="emp_type" id="edit_department">
                                    <option selected disabled>Select department</option>
                                    @foreach ($department as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                </div>
                               </div>
                                <div class="col-12">
                                   <div class="col-12">
                                    <label for="vehicle_id" class="form-label">Role</label>
                                    <select required style="width: 100%;" name="role_id" id="edit_role_id"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select role </option>
                                        
                                    </select>
                                </div>
                                </div>
                                 <div class="col-12">
                                   <div class="col-12">
                                    <label for="vehicle_id" class="form-label">Assign To</label>
                                    <select required style="width: 100%;" name="user_id" id="edit_user_id"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select User </option>
                              
                                    </select>
                                </div>
                                </div>
                                 <div class="col-12">
                                   <div class="col-12">
                                    <label for="vehicle_id" class="form-label">Priority</label>
                                    <select style="width: 100%;" name="priority" id="edit_priority"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select Priority </option>
                                       
                                            <option value="1">Low</option>
                                            <option value="2">Medium</option>
                                            <option value="3">High</option>
                                      
                                    </select>
                                </div>
                                </div>
                       
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Update</button>
                    </div>
                </form>
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
        $('#emp_type').change(function() {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'get_roles/' + $(this).val(),
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#role_id').prop("disabled", false).empty().append($("<option></option>")
                            .attr("value", "")
                            .attr("selected", "")
                            .attr("disabled", "")
                            .text('Select roles'))
                        $.each(data.roles, function(key, value) {
                            $('#role_id').append($("<option></option>")
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
        $('#edit_department').change(function() {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'get_roles/' + $(this).val(),
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#edit_role_id').prop("disabled", false).empty().append($("<option></option>")
                            .attr("value", "")
                            .attr("selected", "")
                            .attr("disabled", "")
                            .text('Select roles'))
                        $.each(data.roles, function(key, value) {
                            $('#edit_role_id').append($("<option></option>")
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
                            .text('Select User'))
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
        $('#edit_role_id').change(function() {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'get_users/' + $(this).val(),
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#edit_user_id').prop("disabled", false).empty().append($("<option></option>")
                            .attr("value", "")
                            .attr("selected", "")
                            .attr("disabled", "")
                            .text('Select User'))
                        $.each(data.users, function(key, value) {
                            $('#edit_user_id').append($("<option></option>")
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

            getData('manage-tasks/show1/' + id, function(data) {
                $('#edit_depo').modal('show');
                $('#edit_task_id').val(data.data.id);
                $('#edit_task_name').val(data.data.name);
                $('#edit_startdate').val(data.data.startdate);
                $('#edit_enddate').val(data.data.enddate);
                $('#edit_department').val(data.data.dept_id);

                $('#edit_role_id').empty();
                $('#edit_user_id').empty();


                $.each(data.roles1, function(key, value) {
                    $('#edit_role_id').append($("<option></option>")
                        .attr("value", value.id)
                        .text(value.name))
                });
                $.each(data.users, function(key, value) {
                    $('#edit_user_id').append($("<option></option>")
                        .attr("value", value.id)
                        .text(value.name))
                });
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
