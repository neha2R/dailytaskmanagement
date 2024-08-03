@extends('layouts.app')
@section('content')
    <style>
    </style>
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Monthly Attendance</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            <form method="GET" action="{{ route('admin.monthly_attendance.index') }}">
                <div class="input-group flatpickr me-2 mb-2 mb-md-0">
                    <!---<select name="emp_type" class="form-select me-2 border border-primary">
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
                    <select name="date" class="form-select me-1 border border-primary">
                        @forelse (getFilterMonths() as $item)
                            {{-- <option value="">{{$active_month }}</option>
                        <option value=""> {{$item}}</option> --}}

                            <option {{ dateformat($active_month, 'M') == dateformat($item, 'M') ? 'selected' : '' }}
                                value="{{ $item }}">{{ $item->format('M Y') }}</option>
                        @empty
                            <option value="">No data found</option>
                        @endforelse
                    </select>
                    <button class="btn btn-primary ms-1">Filter</button>
                </div>
            </form>
        </div>
    </div>
    {{-- {{ request()->get('date') == $key ? 'selected' : '' }} --}}
    <div class="d-flex mb-3">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary btn-sm">Daily</a>
        <a href="{{ route('admin.monthly_attendance.index') }}" class="btn btn-primary btn-sm ms-2">Monthly</a>
    </div>
    <div class="d-flex justify-content-between align-items-center flex-wrap mt-2">
        <div>
            <p>Lists of {{ ucfirst(request()->get('emp_type') ?? 'Regular') }} Staff of
                <span class="fw-bold">{{ dateformat($active_month, 'F') }}</span> month
            </p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="attendance_form" method="POST" action="{{ route('admin.attendance.store') }}">
                <input type="hidden"name="date" value="{{ $active_month }}">
                @csrf
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>Pending</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Half Day</th>
                            <th>Leaves</th>
                            <th>Total Days</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $key => $item)
                            <tr>
                                <td class="center">{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ countMonhtlyAttendance($item->id, dateformat($active_month, 'Y-m'), '0') }}</td>
                                <td>{{ countMonhtlyAttendance($item->id, dateformat($active_month, 'Y-m'), '1') }}</td>
                                <td>{{ countMonhtlyAttendance($item->id, dateformat($active_month, 'Y-m'), '2') }}</td>
                                <td>{{ countMonhtlyAttendance($item->id, dateformat($active_month, 'Y-m'), '3') }}</td>
                                <td>{{ countMonhtlyAttendance($item->id, dateformat($active_month, 'Y-m'), '4') }}</td>
                                <td>{{ countTotalP($item->id, dateformat($active_month, 'Y-m')) }}</td>
                                {{-- <td>{{Carbon\Carbon::today()->day}}</td> --}}
                                <td><button
                                        onclick="getEmpData({{ $item->id }}, '{{ dateformat($active_month, 'Y-m') }}')"
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Attendance Information </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table  id="examplelatest" class="table">
                            <thead>
                                <tr>
                                <th>Date</th>
                                <th>Days Name</th>
                                <th>Status</th>
                                <th>Login Time</th>
                                <th>Logout Time</th>
                                <th>Working Hours</th>
                                </tr>
                                 <tbody>
                                @foreach (getDateAndDays($active_month, 'd M') as $key =>$item)
                                <tr>
                                    <td>{{$item}}</td>
                                    <td>{{getDateAndDays($active_month, 'D')[$key]}}</td>
                                    <td id="status{{ $key }}"></td>
                                    <td id="loginattendance{{ $key }}"></td>
                                    <td id="logoutattendance{{ $key }}"></td>
                                    <td id="workinghours{{ $key }}"></td>
                               </tr>
                               @endforeach
                               <tbody>
                        </table>
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
      <script src="https://cdn.datatables.net/buttons/1.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.1/js/buttons.html5.min.js"></script>
    <script>
        $(function() {
            'use strict'
            if ($('#attendance-flatpickr-date').length) {
                const today = new Date();
                const firstDayOfMonth = today.getMonth() === 0 ? new Date(today.getFullYear(), 11, 31) : new Date(
                    today.getFullYear(), today.getMonth(), 1);

                flatpickr("#attendance-flatpickr-date", {
                    wrap: true,
                    dateFormat: "M-Y",
                    defaultDate: "@if (request()->has('date')) {{ request()->get('date') }} @else today @endif",
                    maxDate: "today",
                    minDate: firstDayOfMonth
                });
            }
        });

        function getEmpData(id, active_month) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'monthly_attendance/' + id + '/' + active_month,
                beforeSend: function() {
                    $('#spin').removeClass('d-none')
                },
                success: function(data) {
                    console.log(data);
                    if (data.attendance == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Attendance Not Found',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        $("#show_attendance").modal('show');
                        // php code to get date for active month
                        @php
                            $array = getDateAndDays(dateformat($active_month, 'Y-m-d'), 'Y-m-d');
                        @endphp
                        var dates = @json($array);
                        // $('#status').child().empty();
                        $(dates).each(function(index, element) {
                            $('#status' + index).empty().append(
                                '<span class="badge bg-light text-dark">-</span>')
                            $(data.attendance).each(function(ind, ele) {
                                if (element == ele.date) {
                                       var dtStart = new Date(ele.date+ " "  + ele.login_time);
                                     var dtEnd = new Date(ele.date+ " "  + ele.logout_time);
                                    var diff = ((dtEnd - dtStart)) / 1000;
                                    var totalTime = 0;
        
                                    if (diff > 60*60*12) {
                                    totalTime = formatDate(60*60*12);
                                     } else {
                                     totalTime = formatDate(diff);
                                     }
                                    $('#loginattendance' + index).empty().append(
                                    '<td><span class="badge bg-primary">'+ele.login_time+'</span></td>'
                                    )
                                    $('#logoutattendance' + index).empty().append(
                                    '<td><span class="badge bg-primary">'+ele.logout_time+'</span></td>'
                                    )
                                    $('#workinghours' + index).empty().append(
                                    '<td><span class="badge bg-primary">'+totalTime+'</span></td>'
                                    )
                                    switch (ele.is_approved) {
                                        case '0':
                                            $('#status' + index).empty().append(
                                                '<td><span class="badge bg-primary">Pending</span></td>'
                                            )
                                            break;
                                        case '1':
                                            $('#status' + index).empty().append(
                                                '<td><span class="badge bg-success">Present</span></td>'
                                            )
                                            break;
                                        case '2':
                                            $('#status' + index).empty().append(
                                                '<td><span class="badge bg-danger">Absent</span></td>'
                                            )
                                            break;
                                        case '3':
                                            $('#status' + index).empty().append(
                                                '<td><span class="badge bg-info">Half Day</span></td>'
                                            )
                                            break;
                                        case '4':
                                            $('#status' + index).empty().append(
                                                '<td><span class="badge bg-warning">Leave</span></td>'
                                            )
                                            break;
                                        default:
                                            break;
                                    }
                                }
                            });

                        });
                         var dataTable = $('#examplelatest').DataTable({
            "sDom": "<'exportOptions text-right'B><'table-responsive't><'row'<p>>",
            "scrollCollapse": false,
            "paging": false,
            // "bSort": true,
            "info": false,

            buttons: [
               {
        extend: 'csvHtml5',
        text: 'Export Records',
        className: 'btn btn-default',
       
             }

],

        });
                    }
                },
                complete: function() {
                    document.body.firstChild.className += "d-none";
                },
            });

        }
        function formatDate(diff){
            ////console.log(diff);

        var hours = parseInt( diff / 3600 ) % 24;
        var minutes = parseInt( diff / 60 ) % 60;
        var seconds = diff % 60;
        return (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes);
    }
    </script>
@endsection
