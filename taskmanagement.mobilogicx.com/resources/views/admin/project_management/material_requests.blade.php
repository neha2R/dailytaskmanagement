@extends('layouts.app')
@section('content')
    <style>
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
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
            <h5 class="info-heading ms-3">Material Requests</h5>
        </div>

        <div class="card-body pt-2 pb-2">
            <div class="table-responsive">
                <table id="dataTableExample" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Project</th>
                            <th>Site</th>
                            <th>Division</th>
                            <th>Request By</th>
                            <th>Request Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $key => $request)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $request->project->project_name ?? '-' }}</td>
                                <td>{{ $request->site->name ?? '-' }}</td>
                                <td>{{ $request->site->sub_division->division->name ?? '-' }}</td>
                                <td>{{ $request->user->name ?? '-' }}</td>
                                <td>
                                    {{ dateformat($request->created_at, 'd M Y') }}
                                </td>
                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $key }}">
                                            <a href="#" class="dropdown-item d-flex align-items-center"
                                                onclick="viewProducts('{{ route('project-management.material-requests.show', $request->id) }}')">
                                                <i class="icon-sm me-2 " data-feather="eye"></i>
                                                <span class="">View</span>
                                            </a>
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
    <div class="modal fade" id="view_details" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Request Details
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
    {{-- @component('components.editmoal')
    @endcomponent --}}
@endsection
@section('js')
    <script>
        function viewProducts(url) {
            getData(url, function(data) {
                console.log(data);
                if (data.status == 200) {
                    $('#view_details').modal('show');
                    $('#viewData').empty().append(`
                            <div class="mb-3">
                                <p id="project_name" class="mb-2">Project Name.:-${data.request.project_name}</p>
                                <p>Request By:- ${data.request.user_name}</p>
                                <p>Requested Date:- ${data.request.created_at_formatted}</p>
                            </div>
                                <div class="card view-consignment-card px-2 py-3">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div class="ms-3">
                                            <p class="mb-0">Site Name</p>
                                            <Small>${data.request.site_name}</Small>
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
