@extends('layouts.app')
@section('content')
    <style>
        .filter {
            width: 18px;
            height: 18px;
        }

        .border-left {
            border-left: 1px solid #000 !important;
        }

        .view-constigment th {
            background: #6571ff;
            color: white !important;
            border: 1px solid #000 !important;
            border-bottom: 0 !important;
        }

        .view-constigment td {
            border: 1px solid #000 !important;
            border-top: 0 !important;
        }

        .view-consignment-card {
            border: 1px solid gray !important;
            border-radius: 0;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('admin.consignments.index') }}" class="nav-link active tab-heading"
                    aria-selected="true">Consignments</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('trips.trip.index') }}" class="nav-link tab-heading" aria-selected="false">Trips</a>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <a type="button" href="{{ route('admin.consignments.create') }}"
                class="btn btn-primary btn-sm d-flex align-items-center">
                <i class="mdi mdi-plus fs-6"></i> Create Consignment
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center px-3 py-2 m-0">
            <h5 class="info-heading">Manage Consignments</h5>
        </div>

        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample1" class="table">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Con.No</th>
                            <th>Origin Location</th>
                            <th>Destination Location</th>
                            {{-- <th>Delivery Type</th> --}}
                            <th>Total Items</th>
                            <th>Delivery By Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $item)
                            <tr id="dt_tr{{ $item->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td>{{ env('PrefixCon') . $item->id }}</td>
                                <td>{{ $item->origin_source()->name ?? '-' }}</td>
                                <td>{{ $item->destination_source()->name ?? '-' }}</td>
                                {{-- <td>{{ ucwords($item->type) }}</td> --}}
                                <td>{{ $item->products_count }}</td>
                                <td>{{ dateformat($item->delivery_by_date, 'd M Y') }}</td>
                                <td>
                                    @switch($item->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @break

                                        @case('trip_assigned')
                                            <span class="badge bg-primary">Trip Assigned</span>
                                        @break

                                        @case('delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @break

                                        @default
                                    @endswitch
                                </td>
                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $key }}">
                                            <a onclick="viewConsignment('{{ route('admin.consignments.show', $item->id) }}')"
                                                class="dropdown-item" href="javascript:;"><i class="mdi mdi-eye me-2"></i>
                                                View Consignment</a>
                                            @if ($item->status == 'pending')
                                                <a onclick="delete_con({{ $item->id }})" class="dropdown-item"
                                                    href="javascript:;"><i class="mdi mdi-delete me-2"></i> Delete
                                                    Consignment</a>
                                            @endif
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


    <div class="modal fade" id="edit_consignment" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Consignment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_cons" method="post" action="{{ route('admin.consignments.update', '1') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body" id="edit_modal_body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="view_cons" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">View Consignment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>

                <div id="viewData" class="modal-body">

                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                    {{-- <button type="sumbit" class="btn btn-primary">Update</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function viewConsignment(url) {
            getData(url, function(data) {
                console.log(data);
                if (data.status == 200) {
                    $('#view_cons').modal('show');
                    $('#viewData').empty().append(`
                            <div class="mb-3">
                                <p id="con_no" class="mb-2">Consignment No.:-${data.con.con_num}</p>
                                <p>Delivery By Date:- ${data.con.delivery_by_date}</p>
                            </div>
                                <div class="card view-consignment-card px-2 py-3">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div class="ms-3">
                                            <p class="mb-0">Origin Location</p>
                                            <Small>${data.con.origin_location}</Small>
                                        </div>
                                        <div class="ps-3">
                                            <p class="mb-0">Delivery Location</p>
                                            <Small>${data.con.destination_location}</Small>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 px-0">
                                        <div class="table-responsive">
                                            <table id="" class="table view-constigment text-center" data-order=''>
                                                <thead>
                                                    <tr>
                                                        <th class="pe-0">SR.No</th>
                                                        <th class="pe-0">Item Name</th>
                                                        <th class="pe-0">Item Category</th>
                                                        <th class="pe-0">Transfer Quantity</th>
                                                        <th class="pe-0">UOM</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="viewProductTbl">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                        `);
                    var tbldata = $('#viewProductTbl');
                    $.each(data.products, function(key, val) {
                        tbldata.append(`
                                            <tr>
                                                <td>${key+1}</td>
                                                <td>${val.product_name}</td>
                                                <td>${val.category_name}</td>
                                                <td>${val.quantity}</td>
                                                <td>${val.uom_name}</td>
                                            </tr>`);
                    });
                }
            })
        }

        function delete_con(id) {
            // SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Once deleted, you will not be able to recover this consignment!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, proceed with the AJAX request
                    $.ajax({
                        type: 'DELETE',
                        dataType: 'JSON',
                        url: 'consignments/' + id,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            $('#spin').removeClass('d-none');
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.status == 200) {
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
                                $('#dt_tr' + id).remove();
                            }
                        },
                        complete: function() {
                            $('#spin').addClass('d-none');
                        }
                    });
                }
            });
        }
    </script>
@endsection
