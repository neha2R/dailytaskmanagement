@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Daily Attendance</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <form method="GET" action="{{ route('admin.attendance.index') }}">
                <div class="input-group flatpickr me-2 mb-2 mb-md-0" id="attendance-flatpickr-date">
                   <!--- <select name="emp_type" class="form-select me-2 border border-primary">
                        <option {{ request()->get('emp_type') == 'regular' ? 'selected' : '' }} value="regular">Regular
                        </option>
                        <option {{ request()->get('emp_type') == 'daily' ? 'selected' : '' }} value="daily">Daily</option>
                        <option {{ request()->get('emp_type') == 'monthly' ? 'selected' : '' }} value="monthly">Monthly
                        </option>
                    </select>--->
                    <span class="input-group-text input-group-addon bg-transparent border-primary" data-toggle=""><svg
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-calendar text-primary">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2">
                            </rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg></span>
                    <input name="date" type="text" class="form-control bg-transparent border-primary flatpickr-input"
                        placeholder="Select date" data-input="" readonly="readonly">

                    <button class="btn btn-primary ms-1">Filter</button>
                </div>
            </form>
        </div>
    </div>
    <div class="d-flex">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary btn-sm">Daily</a>
        <a href="{{ route('admin.monthly_attendance.index') }}" class="btn btn-outline-secondary btn-sm ms-2">Monthly</a>
    </div>
    <div class="container">
        <div class="row justify-content-center row-cols-5">
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Staff</h5>
                        <hr>
                        <p class="card-text">{{ $employees->count() ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Present</h5>
                        <hr>
                        <p class="card-text">
                            {{ countAttendance($active_date, 'P', request()->get('emp_type') ?? 'regular') }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Absent</h5>
                        <hr>
                        <p class="card-text">
                            {{ countAttendance($active_date, 'A', request()->get('emp_type') ?? 'regular') }}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Half Day</h5>
                        <hr>
                        <p class="card-text">
                            {{ countAttendance($active_date, 'HD', request()->get('emp_type') ?? 'regular') }}</p>
                    </div>
                </div>
            </div>
           <!--- <div class="col">
                <div class="card box-shadow mx-auto my-5">
                    <div class="card-body text-center">
                        <h5 class="card-title">Leaves</h5>
                        <hr>
                        <p class="card-text">
                            {{ countAttendance($active_date, 'L', request()->get('emp_type') ?? 'regular') }}</p>
                    </div>
                </div>
            </div>--->
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <p>Lists of {{ ucfirst(request()->get('emp_type') ?? 'Regular') }} Staff :
                {{ dateformat($active_date, 'M d, Y') }} </p>
        </div>
        <div class="d-flex">
            <button type="button" id="mark_present" class="btn btn-primary btn-sm">Mark as Present</button>
            <button type="button" id="mark_half_day" class="btn btn-secondary btn-sm ms-2">Mark as Half Day</button>
            <button type="button" id="mark_absent" class="btn btn-danger btn-sm ms-2">Mark as Absent</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="attendance_form" method="POST" action="{{ route('admin.attendance.store') }}">
                <input type="hidden"name="date" value="{{ $active_date }}">
                @csrf
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="form-check-input" id="selectall"></th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $key => $item)
                            <tr>
                                <td class="center"><input class="form-check-input" type="checkbox"
                                        name="check[{{ $item->id }}]"></td>
                                <td>{{ $item->name }}</td>
                                <td>{{ getRole($item->role_id)->name ?? '-' }}</td>
                                <td>{{ getDepartment($item->department_id)->name ?? '-' }}</td>
                                <td>
                                    @if ($item->attendance == null)
                                        <span class="badge bg-secondary">Not Applied</span>
                                    @elseif ($item->attendance->is_approved == '0')
                                        <span class="badge bg-primary">Pending</span>
                                    @elseif ($item->attendance->is_approved == '1')
                                        <span class="badge bg-success">Present</span>
                                    @elseif ($item->attendance->is_approved == '2')
                                        <span class="badge bg-danger">Absent</span>
                                    @elseif ($item->attendance->is_approved == '3')
                                        <span class="badge bg-info">Half Day</span>
                                    @elseif ($item->attendance->is_approved == '4')
                                        <span class="badge bg-warning">Leave</span>
                                    @endif
                                </td>
                                <td><button
                                        onclick="getEmpData({{ $item->id }}, '{{ dateformat($active_date, 'Y-m-d') }}')"
                                        type="button" class="btn btn-primary btn-icon btn-xs"><i
                                            data-feather="eye"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <div class="modal fade" id="show_attendance" tabindex="-1" aria-labelledby="show_attendance" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Attendance Information </h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button> --}}
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 d-flex">
                            <div class="row col-lg-6 mb-3 ">
                                <div class="col ">
                                    <label for="defaultconfig" class="col-form-label fw-bold">Name-:</label>
                                </div>
                                <div class="col">
                                    <label id="name" for="defaultconfig" class="col-form-label"></label>
                                </div>
                            </div>
                            <div class="row col-lg-6 mb-3 ">
                                <div class="col ">
                                    <label for="defaultconfig" class="col-form-label fw-bold">Status-:</label>
                                </div>
                                <div class="col">
                                    <label id="status" for="defaultconfig" class="col-form-label"><span
                                            class="badge bg-success">Present</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex">
                            <div class="row col-lg-6 mb-3 ">
                                <div class="col ">
                                    <label for="defaultconfig" class="col-form-label fw-bold">Login Time-:</label>
                                </div>
                                <div class="col">
                                    <label id="login_time" for="defaultconfig" class="col-form-label"></label>
                                </div>
                            </div>
                            <div class="row col-lg-6 mb-3 ">
                                <div class="col ">
                                    <label for="defaultconfig" class="col-form-label fw-bold">Logout Time-:</label>
                                </div>
                                <div class="col">
                                    <label id="logout_time" for="defaultconfig" class="col-form-label"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex">
                            <div class="row col-lg-6">
                                <div class="col ">
                                    <label for="defaultconfig" class="col-form-label fw-bold">Login Location</label>
                                </div>
                            </div>
                            <div class="row col-lg-6">
                                <div class="col ">
                                    <label for="defaultconfig" class="col-form-label fw-bold">Logout Location</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex">
                            <div class="row col-lg-6">
                                <div class="col ">
                                    <label id="login_btn" for="defaultconfig" class="col-form-label"></label>
                                </div>
                            </div>
                            <div class="row col-lg-6">
                                <div class="col  ">
                                    <label id="logout_btn" for="defaultconfig" class="col-form-label"></label>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 d-flex">
                            <div class="row">
                                <div class="col">
                                    <label for="defaultconfig" class="col-form-label fw-bold">Comment</label>
                                    <p id="comments"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            $('#mark_present').click(function() {
                if ($('#attendance_form').find('input[type="checkbox"]:checked').length > 0) {
                    Swal.fire({
                        position: 'top',
                        title: 'Are you sure?',
                        text: "You want to Mark as 'Present'",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: 'primary',
                        cancelButtonColor: 'danger',
                        confirmButtonText: 'Yes, Mark as Present'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("<input />").attr("type", "hidden")
                                .attr("name", "type")
                                .attr("value", "P")
                                .appendTo("#attendance_form");
                            $("#attendance_form").submit();
                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please select atleast one employee',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
            $('#mark_half_day').click(function() {
                if ($('#attendance_form').find('input[type="checkbox"]:checked').length > 0) {
                    Swal.fire({
                        position: 'top',
                        title: 'Are you sure?',
                        text: "You want to Mark as 'Half Day'",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: 'primary',
                        cancelButtonColor: 'danger',
                        confirmButtonText: 'Yes, Mark as Half-Day'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("<input />").attr("type", "hidden")
                                .attr("name", "type")
                                .attr("value", "HD")
                                .appendTo("#attendance_form");
                            $("#attendance_form").submit();
                        }
                    })

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please select atleast one employee',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
            $('#mark_absent').click(function() {
                if ($('#attendance_form').find('input[type="checkbox"]:checked').length > 0) {
                    Swal.fire({
                        position: 'top',
                        title: 'Are you sure?',
                        text: "You want to Mark as 'Absent'",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: 'primary',
                        cancelButtonColor: 'danger',
                        confirmButtonText: 'Yes, Mark as Absent'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("<input />").attr("type", "hidden")
                                .attr("name", "type")
                                .attr("value", "A")
                                .appendTo("#attendance_form");
                            $("#attendance_form").submit();
                        }
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please select atleast one employee',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        });
        $(function() {
            'use strict'
            if ($('#attendance-flatpickr-date').length) {
                const today = new Date();
                const firstDayOfMonth = today.getMonth() === 0 ? new Date(today.getFullYear(), 11, 31) : new Date(
                    today.getFullYear(), today.getMonth(), 1);

                flatpickr("#attendance-flatpickr-date", {
                    wrap: true,
                    dateFormat: "d-M-Y",
                    defaultDate: "@if (request()->has('date')) {{ request()->get('date') }} @else today @endif",
                    maxDate: "today",
//                    minDate: firstDayOfMonth
                });
            }
        });

        function getEmpData(id, active_date) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'attendance/' + id + '/' + active_date,
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    if (data.employee.attendance == null) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Not Found',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        $("#show_attendance").modal('show');
                        $('#att_date').empty().text(data.employee.attendance.date);
                        $('#name').empty().text(data.employee.name);
                        switch (data.employee.attendance.is_approved) {
                            case '0':
                                $('#status').empty().append('<span class="badge bg-primary">Pending</span>')
                                break;
                            case '1':
                                $('#status').empty().append('<span class="badge bg-success">Present</span>')
                                break;
                            case '2':
                                $('#status').empty().append('<span class="badge bg-danger">Absent</span>')
                                break;
                            case '3':
                                $('#status').empty().append('<span class="badge bg-info">Half Day</span>')
                                break;
                            case '4':
                                $('#status').empty().append('<span class="badge bg-warning">Leave</span>')
                                break;
                            default:
                                break;
                        }
                        $('#login_time').empty().text(data.employee.attendance.login_time);
                        $('#logout_time').empty().text(data.employee.attendance.logout_time);
                        if (data.employee.attendance.login_latitude !== null) {
                            $('#login_btn').empty().append(
                                `<a target="blank" href="https://maps.google.com/?q=${data.employee.attendance.login_latitude},${data.employee.attendance.login_longitude}" class="btn btn-primary btn-sm">View</a>`
                            );
                        } else {
                            $('#login_btn').empty();
                        }
                        if (data.employee.attendance.logout_latitude !== null) {
                            $('#logout_btn').empty().append(
                                `<a target="blank" href="https://maps.google.com/?q=${data.employee.attendance.logout_latitude},${data.employee.attendance.logout_longitude}" class="btn btn-primary btn-sm">View</a>`
                            );
                        } else {
                            $('#logout_btn').empty();
                        }
                        $('#comments').empty().text(data.employee.attendance.comments);
                    }
                },
                complete: function() {
                    document.body.firstChild.className += "d-none";
                },
            });

        }
    </script>
@endsection
