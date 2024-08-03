@extends('warehouse_head.layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
        <div>
            <h4 class="mb-3 mb-md-0">Inventory</h4>
        </div>
        {{-- <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Add product
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#create_product"
                    data-bs-whatever="@getbootstrap" data-target-section="selectWarehouse">Warehouse </button>
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#create_product"
                    data-bs-whatever="@getbootstrap" data-target-section="selectDepo">Depo </button>
            </div>
        </div> --}}
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <div class="fillter-cotent d-flex align-items-center justify-content-end mb-3">
                    <div class="dropdown">
                        <button class="btn dropdown-toggle fw-bold fillter-border-right py-0 border-0" type="button"
                            id="FillterMenuButton" role="button" aria-expanded="false">
                            <i class="mdi mdi-filter me-2" style="font-size:14px;"></i>Apply Filter</button>
                    </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle fw-bold fillter-border-right py-0 border-0" type="button"
                            id="dropdownMenuButton" data-bs-toggle="dropdown">
                            <i class="mdi mdi-microsoft-excel me-2" style="font-size:14px;"></i>Export</button>
                        <div class="dropdown-menu">
                            <a type="button" id="customExportCsvBtn" class="dropdown-item">Csv</a>
                            <a type="button" id="customExportPdfBtn" class="dropdown-item">Excel</a>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                        <form id="filter-form" method="GET" action="{{ route('whHead.inventory-management.index') }}">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filterName" class="form-label">Product Name</label>
                                    <select class="js-example-basic-multiple " name="productsNames[]" id="filterName"
                                        multiple="multiple" data-width="100%">
                                        @foreach ($productData as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, request('productsNames', [])) ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filterBrand" class="form-label">Brand</label>
                                    <select class="js-example-basic-multiple " name="brands[]" id="filterBrand"
                                        multiple="multiple" data-width="100%">
                                        @foreach ($brands as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, request('brands', [])) ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="col-md-3">
                                    <label for="filterWarehouse" class="form-label">Warehouse/Depo</label>
                                    <select class="js-example-basic-multiple " name="warehouse_id[]" id="filterWarehouse"
                                        multiple="multiple" data-width="100%">

                                        @foreach ($warehousesAndDepos as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, request('warehouse_id', [])) ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                <div class="col-md-4">
                                    <label for="filterCategory" class="form-label">Category</label>

                                    <select class="js-example-basic-multiple " name="category_id[]" id="filterCategory"
                                        multiple="multiple" data-width="100%">

                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, request('category_id', [])) ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                @if (count(request()->all()) > 0)
                                    <a href="{{ route('whHead.inventory-management.index') }}" class="btn btn-danger">Remove
                                        Filters</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                <table id="dataTableExample1" class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Warehouse/Depo</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $key => $item)
                            <tr>
                                <td class="center">{{ $key + 1 }}</td>
                                <td>{{ $item->product->name ?? '-' }}</td>
                                <td>{{ $item->company->name ?? '-' }}</td>
                                <td>{{ $item->warehouse ? $item->warehouse->name : $item->depo->name ?? '-' }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                            <a type="button"
                                                onclick="viewProductDetails('{{ route('whHead.inventory-management.show', $item->id) }}')"
                                                class="dropdown-item d-flex align-items-center"><i data-feather="eye"
                                                    class="icon-sm me-2"></i> <span class="">View</span></a>
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
    <div class="modal fade" id="productDetailsModel" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Product Details</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body" id="appendProductDetails">

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
        // add for depo
        $(document).ready(function() {
            // Handle button click event
            $('.dropdown-item').on('click', function() {
                // Get the target section
                var targetSection = $(this).data('target-section');

                // Hide all sections
                $('#selectWarehouse, #selectDepo').addClass('d-none');

                // Show the selected section
                $('#' + targetSection).removeClass('d-none');
            });

            $("#FillterMenuButton").on("click", function() {
                $("#collapseExample").collapse("toggle");

            });
            @if (count(request()->all()) > 0)
                $("#collapseExample").collapse("toggle");
            @endif

            $('#collapseExample').on('shown.bs.collapse hidden.bs.collapse', function() {
                // Change button text based on collapse state
                var buttonText = $("#collapseExample").hasClass("show") ? "Hide Filter" :
                    "Apply Filter";
                var iconClass = $("#collapseExample").hasClass("show") ? "mdi-filter-remove" :
                    "mdi-filter";
                $("#FillterMenuButton").html(
                    `<i class="mdi ${iconClass} me-2" style="font-size:14px;"></i>` + buttonText);
            });

            if ($("#filterWarehouse").length) {
                $("#filterWarehouse").select2({
                    // dropdownParent: $("#bulk_maping_depo")
                });
            }
            if ($("#filterCategory").length) {
                $("#filterCategory").select2({
                    // dropdownParent: $("#bulk_maping_warehouse")
                });
            }
            if ($("#filterBrand").length) {
                $("#filterBrand").select2({
                    // dropdownParent: $("#bulk_maping_warehouse")
                });
            }
            if ($("#filterName").length) {
                $("#filterName").select2({
                    // dropdownParent: $("#bulk_maping_warehouse")
                });
            }
        });

        function viewProductDetails(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    data = data.data;
                    console.log(data);

                    var modalBody = document.getElementById('appendProductDetails');

                    var content = `
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Product Name</th>
                                                    <td>${data.product_with_category.name ?? '-'}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Category</th>
                                                    <td>${data.product_with_category.category.name ?? '-'}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Sub Category</th>
                                                    <td>${data.product_with_category.sub_category.name ?? '-'}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Brand</th>
                                                    <td>${data.company.name ?? '-'}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Vendor</th>
                                                    <td>${data.vendor.name ?? '-'}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Price</th>
                                                    <td>${data.price ?? '-'}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Quantity</th>
                                                    <td>${data.quantity ?? '-'}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Model</th>
                                                    <td>${data.model_name ?? '-'}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>`;




                    modalBody.innerHTML = content;

                    $('#productDetailsModel').modal('show');
                }
            });
        }
    </script>
@endsection
