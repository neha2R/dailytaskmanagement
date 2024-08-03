@extends('depot_head.layouts.app')
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
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
        <div>
            <h4 class="mb-3 mb-md-0">Stock Transfer</h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body ">
            <div class="table-responsive">
                <table id="dataTableExample1" class="table" data-order=''>
                    <thead>
                        <tr>
                            <th>Con.No</th>
                            <th>Origin Location</th>
                            <th>Destination Location</th>
                            <th>Total Item</th>
                            <th>Delvery By date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ env('PrefixCon') . $item->id }}</td>
                                <td>{{ $item->origin_source()->name ?? '' }}</td>
                                <td>{{ $item->destination_source()->name ?? '-' }}</td>

                                <td>{{ $item->products_count }}</td>
                                <td>{{ dateformat($item->delivery_by_date, 'd M Y') }}</td>
                                <td>
                                    @switch($item->status)
                                        @case('pending')
                                            <span class="badge bg-warning xs">Pending</span>
                                        @break

                                        @case('trip_assigned')
                                            <span class="badge bg-primary xs">Trip Assigned</span>
                                        @break

                                        @case('delivered')
                                            <span class="badge bg-success xs">Delivered</span>
                                        @break

                                        @default
                                    @endswitch
                                </td>
                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                            <a onclick="viewConsignementDetails('{{ route('dpHead.consignements.show', $item->id) }}')"
                                                class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="eye" class="icon-sm me-2"></i> <span class="">View
                                                    Consignment</span></a>
                                            @if ($item->status == 'delivered')
                                                <a href="{{ route('dpHead.conCheckout', $item->id) }}"
                                                    class="dropdown-item d-flex align-items-center"><i data-feather="edit"
                                                        class="icon-sm me-2"></i>
                                                    <span class="">Checkout Products</span></a>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    {{-- <button type="sumbit" class="btn btn-primary">Update</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function viewConsignementDetails(url) {
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
    </script>
@endsection
