@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Leave Management</h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="dataTableExample" class="table">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Name</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Days</th>
                        <th>Action</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaves as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>{{ dateformat($item->start_date, 'd M Y') }}</td>
                            <td>{{ dateformat($item->end_date, 'd M Y') }}</td>
                            <td>{{ $item->days }}</td>
                            <td id="status{{$item->id}}">
                                @if ($item->is_approved == '1')
                                    <span class="badge bg-success">Approved</span>
                                @elseif ($item->is_approved == '2')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif ($item->is_approved == '3')
                                    <span class="badge bg-warning">Cancelled</span>
                                @elseif ($item->is_approved == '0')
                                    <select onchange="change_status({{ $item->id }})" class="form-select"
                                        id="leave_status">
                                        <option disabled selected value="0">Pending</option>
                                        <option id="approve" {{ $item->is_approved == '1' ? 'selected' : '' }}
                                            value="1">
                                            {{ $item->is_approved == '1' ? 'Approved' : 'Approve' }}</option>
                                        <option id="reject" {{ $item->is_approved == '2' ? 'selected' : '' }}
                                            value="2">
                                            {{ $item->is_approved == '2' ? 'Rejected' : 'Reject' }}</option>
                                    </select>
                                @endif
                            </td>
                            <td>
                                <button onclick="getLeaveData({{ $item->id }})" type="button"
                                    class="btn btn-primary btn-icon btn-xs"><i data-feather="eye"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="show_leave_modal" tabindex="-1" aria-labelledby="show_leave" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Leave Request</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3 ">
                            <label for="defaultconfig" class="col-form-label fw-bold">Name-:</label>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label id="name" for="defaultconfig" class="col-form-label">mahaveer</label>
                        </div>
                        <div class="col-lg-6 mb-3 ">
                            <label for="defaultconfig" class="col-form-label fw-bold">Department-:</label>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label id="department" for="defaultconfig" class="col-form-label">Web devlopment</label>
                        </div>
                        <div class="col-lg-6 mb-3 ">
                            <label for="defaultconfig" class="col-form-label fw-bold">Days-:</label>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label id="days" for="defaultconfig" class="col-form-label"></label>
                        </div>
                        <div class="col-lg-3 mb-3 ">
                            <label for="defaultconfig" class="col-form-label fw-bold">From-:</label>
                        </div>
                        <div class="col-lg-3 mb-3 text-start">
                            <label id="from" for="defaultconfig" class="col-form-label"></label>
                        </div>
                        <div class="col-lg-3 mb-3 ">
                            <la bel for="defaultconfig" class="col-form-label fw-bold">To-:</label>
                        </div>
                        <div class="col-lg-3 mb-3 text-start">
                            <label id="to" for="defaultconfig" class="col-form-label"></label>
                        </div>
                        <div class="col-lg-12 text-start">
                            <label id="name" for="defaultconfig" class="col-form-label fw-bold">Leave Reason -:</label>
                        </div>
                        <div class="col-lg-12 mb-3 text-start">
                            <label id="leave_reason" for="defaultconfig" class="col-form-label">Lorem, ipsum dolor sit amet
                                consectetur adipisicing elit. Tenetur enim cumque asperiores quae, consectetur iusto minima
                                debitis esse quasi architecto, dolorem reprehenderit. Excepturi vel cupiditate perferendis
                                accusantium doloribus voluptatem laudantium.</label>
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
        function getLeaveData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'leaves/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    if (data.data == null) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Not Found',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        $("#show_leave_modal").modal('show');
                        $('#name').empty().text(data.user.name);
                        $('#department').empty().text(data.department);
                        $('#days').empty().text(data.data.days + ' day');
                        $('#from').empty().text(data.data.start_date);
                        $('#to').empty().text(data.data.end_date);
                        $('#leave_reason').empty().text(data.data.description);
                    }
                },

                complete: function() {
                    $('#spin').addClass('d-none');
                },
            });

        }

        function change_status(id) {
            Swal.fire({
                position: 'top',
                title: 'Are you sure?',
                text: "After change you won't be able to revert this!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'primary',
                cancelButtonColor: 'danger',
                confirmButtonText: 'Yes, update'
            }).then((result) => {
                    if (result.isConfirmed) {
                        var status = $('#leave_status').val();
                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            url: '{{ route('admin.leaves.store') }}',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                'id': id,
                                "status": status
                            },
                            beforeSend: function() {
                                $('#spin').removeClass('d-none')
                            },
                            success: function(data) {
                                console.log(data);
                                console.log(status);
                                if (data.status == 200) {
                                    if (status == '1') {
                                        $('#status'+id).empty().append(
                                            '<span class="badge bg-success">Approved</span>');
                                    }
                                    if (status == '2') {
                                        $('#status'+id).empty().append(
                                            '<span class="badge bg-danger">Rejected</span>');
                                    }
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: false,
                                    });
                                    Toast.fire({
                                        icon: 'success',
                                        title: data.message
                                    });
                                }
                                if (data.status == 201) {
                                    Toast.fire({
                                        icon: 'error',
                                        title: data.message
                                    });
                                }
                            },
                            complete: function() {
                                document.body.firstChild.className += "d-none";
                            },
                        });
                    }
                }

            )
        };
    </script>
@endsection
