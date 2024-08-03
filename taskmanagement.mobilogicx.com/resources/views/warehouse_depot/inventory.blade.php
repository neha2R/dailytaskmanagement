@extends(Auth::check() ? (Auth::user()->role ? (Auth::user()->role->name === 'Warehouse Head' ? 'warehouse_head.layouts.app' : (Auth::user()->role->name === 'Depot Head' ? 'depot_head.layouts.app' : 'guest.layouts.app')) : 'layouts.app') : 'guest.layouts.app')
@section('content')
    <style>
        /* .v_heading li {
                        font-size: 18px;
                        font-weight: 500;
                    } */

        .v_details li {
            font-size: 18px;
            font-weight: 500;
        }

        .scrollable-tbody {
            max-height: 320px;
            overflow-y: auto;
            display: block;
        }

        .card-header {
            border-bottom: 1px solid #6571ff;
        }

        .info-heading {
            padding-top: 13px;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
        <div>
            <h4 class="mb-3 mb-md-0 font-weight-bold">Inventory Management</h4>
        </div>
        {{-- <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Add Product
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#create_product"
                    data-bs-whatever="@getbootstrap" data-target-section="selectWarehouse">Add to Warehouse </button>
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#create_product"
                    data-bs-whatever="@getbootstrap" data-target-section="selectDepo">Add to Depot </button>
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
                            <a type="button" id="customExportCsvBtn" class="dropdown-item">Export as CSV</a>
                            <a type="button" id="customExportPdfBtn" class="dropdown-item">Export as Excel</a>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="collapseExample">
                    <div class="card card-body">
                        <form id="filter-form" method="GET" action="">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filterName" class="form-label">Product Name</label>
                                    <select class="js-example-basic-multiple" name="productsNames[]" id="filterName"
                                        multiple="multiple" data-width="100%">
                                        @foreach ($productData as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, request('productsNames', [])) ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterBrand" class="form-label">Companies</label>
                                    <select class="js-example-basic-multiple" name="companies[]" id="filterBrand"
                                        multiple="multiple" data-width="100%">
                                        @foreach ($companies as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, request('companies', [])) ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterWarehouse" class="form-label">Warehouse/Depot</label>
                                    <select class="js-example-basic-multiple" name="source_ids[]" id="filterWarehouse"
                                        multiple="multiple" data-width="100%">

                                        @foreach ($warehousesAndDepos as $item)
                                            @php
                                                $optionValue = json_encode(['id' => $item->id, 'inventory_type_id' => $item->inventory_type_id]);
                                                $isSelected = in_array($optionValue, request('source_ids', []));
                                            @endphp

                                            <option value="{{ $optionValue }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterCategory" class="form-label">Category</label>
                                    <select class="js-example-basic-multiple" name="category_id[]" id="filterCategory"
                                        multiple="multiple" data-width="100%">

                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, request('category_id', [])) ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                @if (count(request()->all()) > 0)
                                    <a href="{{ url(url()->current()) }}" class="btn btn-danger">Remove
                                        Filters</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                {{-- <h4 class="mb-3 mb-md-0 font-weight-bold text-primary">Product Inventory</h4> --}}
                <table id="dataTableExample1" class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Locations</th>
                            <th>Total Stocks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $series = 1 @endphp
                        @foreach ($products as $key => $item)
                            <tr>
                                <td>{{ $series }}</td>
                                <td>{{ $item->first()->product->name ?? '-' }}</td>
                                <td>{{ $item->first()->product->company->name ?? '-' }}</td>
                                <td>
                                    {{ $item->first()->source() ? $item->first()->source()->name : '' }}
                                    @if (count($item) > 1)
                                        <span class="text-primary" style="cursor:pointer;" data-bs-toggle="tooltip"
                                            data-bs-placement="bottom"
                                            title="@foreach ($item->slice(1) as $tooltipLocations) {{ $tooltipLocations->source() ? $tooltipLocations->source()->name : '' }}, @endforeach">
                                            ({{ count($item) - 1 }} more)
                                        </span>
                                    @endif
                                </td>
                                </td>

                                <td>{{ $item->sum('quantity') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link p-0" type="button"
                                            id="dropdownMenuButton{{ $key }}" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $key }}">
                                            @if (Auth::user() && Auth::user()->role)
                                                @if (Auth::user()->role->name == 'Warehouse Head')
                                                    <a
                                                        href="{{ route('whHead.inventory.viewDetails', $item->first()->product_id) }}">
                                                        <i data-feather="eye" class=" icon-sm me-2 mdi mdi-history"></i>
                                                        <span class="">View Details</span>
                                                    </a>
                                                @elseif (Auth::user()->role->name == 'Depot Head')
                                                    <a
                                                        href="{{ route('dpHead.inventory.viewDetails', $item->first()->product_id) }}">
                                                        <i data-feather="eye" class=" icon-sm me-2 mdi mdi-history"></i>
                                                        <span class="">View Details</span>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @php $series++ @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
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

        function formatQuantity(quantity) {
            // Format quantity as needed (e.g., add commas, decimal places)
            return Number(quantity).toLocaleString();
        }
    </script>
@endsection
