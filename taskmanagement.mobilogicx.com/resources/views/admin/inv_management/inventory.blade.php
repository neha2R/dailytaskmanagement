@extends('layouts.app')
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
        <div class="btn-group" role="group">
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
        </div>
    </div>
    {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create_product"
            data-bs-whatever="@getbootstrap"> Add product</button> --}}

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
                        <form id="filter-form" method="GET" action="{{ route('admin.inventory.index') }}">
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
                                {{-- @dd(request('source_ids',[])) --}}
                                <div class="col-md-3">
                                    <label for="filterWarehouse" class="form-label">Warehouse/Depot</label>
                                    <select class="js-example-basic-multiple" name="source_ids[]" id="filterWarehouse"
                                        multiple="multiple" data-width="100%">
                                        @foreach ($warehousesAndDepos as $item)
                                            @php
                                                $optionValue = json_encode(['id' => $item->id, 'inventory_type_id' => $item->inventory_type_id]);
                                                $isSelected = in_array($optionValue,request('source_ids',[]));
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
                                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-danger">Remove
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
                                        <div class="dropdown-menu"
                                            aria-labelledby="dropdownMenuButton{{ $key }}">
                                            {{-- <a type="button"
                                                onclick="viewProductDetails('{{ route('admin.inventory.show', $item->first()->product_id) }}')"
                                                class="dropdown-item d-flex align-items-center">
                                                <i data-feather="eye" class="icon-sm me-2"></i>
                                                <span class="">View</span>
                                            </a> --}}
                                            <a href="{{ route('admin.inventory.viewDetails', $item->first()->product_id) }}"
                                                {{-- onclick="viewProductHistory('{{ route('admin.inventory.viewDetails', $item->first()->product_id) }}')" --}} class="dropdown-item d-flex align-items-center">
                                                <i data-feather="eye" class=" icon-sm me-2 mdi mdi-history"></i>
                                                <span class="">View Details</span>
                                            </a>
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
    <div class="modal fade" id="create_product" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add New Product to Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>

                <form id="add_product_form" method="post" action="{{ route('admin.inventory.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="product" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-package-variant"></i> Select Product
                                </label>
                                <select class="js-example-basic-single form-select" id="product" name="product_id"
                                    data-width="100%" required>
                                    <option selected disabled>Select a product</option>
                                    @foreach (getProducts() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2" id="selectWarehouse">
                                <label for="warehouse" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-factory"></i> Warehouse
                                </label>
                                <select class="js-example-basic-single form-select" id="warehouse" name="warehouse_id"
                                    data-width="100%" required>
                                    <option selected disabled>Select Warehouse</option>
                                    @foreach (getWarehouses() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2 " id="selectDepo">
                                <label for="depo" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-factory"></i> Depot
                                </label>
                                <select class="js-example-basic-single form-select" id="depo" name="depo_id"
                                    data-width="100%" required>
                                    <option selected disabled>Select Depot</option>
                                    @foreach (getActiveDepos() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="category" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-label"></i> Category
                                </label>
                                <div class="input-group">
                                    <input readonly id="category" class="form-control" type="text"
                                        placeholder="Category">
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="sub_category" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-label-outline"></i> Sub Category
                                </label>
                                <div class="input-group">
                                    <input readonly id="sub_category" class="form-control" type="text"
                                        placeholder="Sub Category">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="vendor" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-truck-delivery"></i> Vendor
                                </label>
                                <select class="js-example-basic-single form-select" id="vendor" name="vendor_id"
                                    data-width="100%" required>
                                    <option selected disabled>Select Vendor</option>
                                    @foreach (getVendors() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="col-md-6 mb-2">
                                <label for="price" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-currency-inr"></i> Product Price
                                </label>
                                <input value="{{ old('wheelbase') }}" id="price" placeholder="Enter Price"
                                    class="form-control" name="price" type="number" required>
                            </div> --}}

                            <div class="col-md-6 mb-2">
                                <label for="product_q" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-package-variant"></i> Product Quantity
                                </label>
                                <input value="{{ old('product_quantity') }}" id="product_q" placeholder="Enter Quantity"
                                    class="form-control" name="product_quantity" type="number" required>
                            </div>

                            {{-- <div class="col-md-6 mb-2">
                                <label for="model" class="form-label mb-2 ms-1">
                                    <i class="mdi mdi-cube-outline"></i> Model (optional)
                                </label>
                                <input value="{{ old('model') }}" id="model" placeholder="Enter Model Name"
                                    class="form-control" name="model" type="text">
                            </div> --}}
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
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
        // form with submit handler
        $(function() {
            $.validator.setDefaults({
                submitHandler: function(form) {
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger me-2'
                        },
                        buttonsStyling: false,
                    })
                    swalWithBootstrapButtons.fire({
                        title: 'Are you sure?',
                        text: "Please make sure the entries of warehouse & product quantity is correct as you will not be able to change this later on.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'me-2',
                        confirmButtonText: 'Save',
                        cancelButtonText: 'check again',
                        reverseButtons: true
                    }).then((result) => {
                        console.log(result);
                        if (result.value) {
                            $('#spin').removeClass('d-none');
                            form.submit();
                        }
                    })

                }
            });
            $(function() {
                $("#add_product_form").validate({
                    ignore: ":hidden:not(.ignore-validation)", // Ignore hidden elements except those with class 'ignore-validation'
                    rules: {
                        product_id: {
                            required: true,

                        },

                        price: {
                            required: true,

                        },
                        // price: {
                        //     required: true,

                        // },
                        warehouse_id: {
                            required: true,

                        },
                        depo_id: {
                            required: true,

                        },
                        vendor_id: {
                            required: true,

                        },
                        product_quantity: {
                            required: true,
                        },
                    },
                    messages: {
                        product_id: {
                            required: 'Please select a product.',
                        },

                        // price: {
                        //     required: 'Please enter a price.',
                        // },
                        warehouse_id: {
                            required: 'Please select a warehouse.',
                        },
                        depo_id: {
                            required: 'Please select a Depo.',
                        },
                        vendor_id: {
                            required: 'Please select a vendor.',
                        },
                        product_quantity: {
                            required: 'Please enter the product quantity.',
                        },
                    },
                    errorPlacement: function(error, element) {
                        error.addClass("invalid-feedback");
                        if (element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                        } else if (element.prop('type') === 'radio' && element.parent(
                                '.radio-inline').length) {
                            error.insertAfter(element.parent().parent());
                        } else if (element.prop('type') === 'checkbox' || element.prop(
                                'type') === 'radio') {
                            error.appendTo(element.parent().parent());
                        } else if (element.hasClass('select2-hidden-accessible')) {
                            element.parent().find('.select2-container').addClass(
                                'form-control p-0 is-invalid');
                            error.appendTo(element.parent());
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function(element, errorClass) {
                        if ($(element).prop('type') !== 'checkbox' && $(element).prop(
                                'type') !== 'radio') {
                            $(element).addClass("is-invalid").removeClass("is-valid");
                        }
                        if ($(element).hasClass("select2-hidden-accessible")) {
                            $(element).on('select2:select', function() {
                                $(element).parent().find('.select2-container')
                                    .addClass("is-valid").removeClass("is-invalid");
                            });
                            $(element).on('select2:unselect', function() {
                                $(element).parent().find('.select2-container')
                                    .addClass("is-invalid").removeClass("is-valid");
                            });
                        }
                    },
                    unhighlight: function(element, errorClass) {
                        if ($(element).prop('type') !== 'checkbox' && $(element).prop(
                                'type') !== 'radio') {
                            $(element).addClass("is-valid").removeClass("is-invalid");
                        }
                        // Add the following code to handle select2 changes
                        if ($(element).hasClass("select2-hidden-accessible")) {
                            $(element).on('select2:select', function() {
                                $(element).parent().find('.select2-container')
                                    .addClass("is-valid").removeClass("is-invalid");
                            });
                            $(element).on('select2:unselect', function() {
                                $(element).parent().find('.select2-container')
                                    .addClass("is-invalid").removeClass("is-valid");
                            });
                        }
                    },
                });
            });
        });

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
            if ($(".js-example-basic-single").length) {
                $(".js-example-basic-single").select2({
                    dropdownParent: $("#create_product")
                });
            }
        });

        function viewProductDetails(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    data = data.data;

                    var modalBody = document.getElementById('appendProductDetails');
                    var invType = data.inventory_type;
                    var location = data[invType].name ?? "";

                    var content = `
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Product Name</th>
                                                <td>${data.product.name ?? '-'}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Category</th>
                                                <td>${data.product.category.name ?? '-'}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Subcategory</th>
                                                <td>${data.product.sub_category.name ?? '-'}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Company</th>
                                                <td>${data.product.company.name ?? '-'}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Location</th>
                                                <td>${location ?? '-'}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Location Type</th>
                                                <td>${data.inventory_type ?? '-'}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Quantity</th>
                                                <td>${formatQuantity(data.quantity) + " " + (data.product.uom ?? '-')}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>`;
                    modalBody.innerHTML = content;

                    // Initialize tooltips
                    $('[data-toggle="tooltip"]').tooltip();

                    $('#productDetailsModel').modal('show');
                }
            });
        }

        function viewProductHistory(url) {
            getData(url, function(data) {
                console.log(data);
                if (data.status == 200) {
                    if (data.data.history.length) {
                        // $('#history tbody').empty();
                        // $.each(data.data, function(key, value) {
                        //     $('#history tbody').append(`
                    //                 <tr>
                    //                     <td>${value.product.name ?? ""}</td>
                    //                     <td>${value.product.name ?? ""}</td>
                    //                     <td>${value.user.name}</td>
                    //                     <td>${value.date}</td>
                    //                 </tr>
                    //     `);
                        // });
                        $('#show_assignment').modal("show");
                    } else {

                    }
                }
            });
        }


        function formatQuantity(quantity) {
            // Format quantity as needed (e.g., add commas, decimal places)
            return Number(quantity).toLocaleString();
        }

        $('#product').change(function() {
            var id = $(this).find(":selected").val();
            var url = '../getProduct/' + id;
            getData(url, function(data) {
                $('#category').val(data.category ? data.category.name : "-");
                $('#sub_category').val(data.sub_category ? data.sub_category.name : "-");
            })
        });
    </script>
@endsection
