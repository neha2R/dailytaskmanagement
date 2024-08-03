@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
        <div>
            <h4 class="mb-3 mb-md-0">Products</h4>
        </div>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#create_product"
            data-bs-whatever="@getbootstrap"> Add product</button>
    </div>
    <div class="card">
        <div class="card-body">
            <table id="dataTableExample1" class="table">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>UOM</th>
                        <th>Min Stock (W)</th>
                        <th>Min Stock (D)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $key => $item)
                        <tr>
                            <td class="center">{{ $key + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->category->name ?? '-' }}</td>
                            <td>{{ $item->uom->name ?? "-"}}</td>
                            <td>{{ $item->min_stock_warehouse }}</td>
                            <td>{{ $item->min_stock_depo }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input item_status" type="checkbox" role="switch"
                                        data-id="{{ $item->id }}" id="flexSwitchCheckChecked"
                                        {{ $item->is_active ? 'checked' : '' }} />
                                </div>
                            </td>
                            <td>
                                <div class="dropdown mb-2">
                                    <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                        <a type="button"
                                            onclick="editProductDetails('{{ route('admin.productMaster.show', $item->id) }}')"
                                            class="dropdown-item d-flex align-items-center"><i data-feather="edit"
                                                class="icon-sm me-2"></i> <span class="">Edit</span></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="create_product" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Add a New Product </h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add_product_form" method="post" action="{{ route('admin.productMaster.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name </label>
                                    <input value="{{ old('name') }}" id="name" placeholder="E.g., SuperWidget 5000"
                                        class="form-control" name="name" type="text" required>
                                    <span id="name-error" class="text-danger"></span>
                                    <div id="loader" style="display: none;">Loading...</div>
                                </div>
                                <div class="mb-3">
                                    <label for="brand" class="form-label">Company</label>
                                    <select class="js-example-basic-single form-select" id="brand" name="company_id"
                                        data-width="100%" required>
                                        <option selected disabled>Select Company</option>
                                        @foreach (getCompanies() as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="min_stock_depo" class="form-label">Min. Stock Reserve for Depo </label>
                                    <input value="{{ old('min_stock_depo') }}" id="min_stock_depo" placeholder="E.g., 5"
                                        class="form-control" name="min_stock_depo" type="number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category </label>
                                    <select class="js-example-basic-single form-select" id="category"
                                        name="sub_category_id" data-width="100%" required>
                                        <option selected disabled>Select Product Category</option>
                                        @foreach (getCategories() as $item)
                                            <optgroup label="{{ $item->name }}"></optgroup>
                                            @foreach (getSubCategory($item->id) as $subCategory)
                                                <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="uom" class="form-label">Unit of Measure (UOM) </label>
                                    <select class="js-example-basic-single form-select" id="uom" name="uom"
                                        data-width="100%" required>
                                        <option selected disabled>Select Unit of Measure</option>
                                        @foreach (getUOM() as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="min_stock_warehouses" class="form-label">Min. Stock Reserve for Warehouses
                                    </label>
                                    <input value="{{ old('min_stock_warehouses') }}" id="min_stock_warehouses"
                                        placeholder="E.g., 10" class="form-control" name="min_stock_warehouses"
                                        type="number" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="submitBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_modal" tabindex="-1" aria-labelledby="edit_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Edit Product Details </h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="edit_modal_container">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="submitUpdateBtn" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // Define your validation rules and messages
            var validationRules = {
                name: {
                    required: true,
                    uniqueProductName: true
                },
                sub_category_id: {
                    required: true
                },
                company_id: {
                    required: true
                },
                uom: {
                    required: true
                },
                min_stock_warehouses: {
                    required: true,
                    digits: true // Example rule for numeric input
                },
                min_stock_depo: {
                    required: true,
                    digits: true // Example rule for numeric input
                }
            };

            var validationMessages = {
                name: {
                    required: "Please enter a product name",
                    uniqueProductName: "This product name is already taken"
                },
                sub_category_id: {
                    required: "Please select a product category"
                },
                company_id: {
                    required: "Please select a company"
                },
                uom: {
                    required: "Please select a unit of measure"
                },
                min_stock_warehouses: {
                    required: "Please enter the minimum stock for warehouses",
                    digits: "Please enter a valid number"
                },
                min_stock_depo: {
                    required: "Please enter the minimum stock for depo",
                    digits: "Please enter a valid number"
                }
            };
            $.validator.addMethod("uniqueProductName", function(value, element, callback) {
                var isUnique = false;
                $.ajax({
                    url: "{{ route('admin.checkUniqueProductName') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    async: false,
                    data: {
                        name: value
                    },
                    success: function(response) {
                        isUnique = response.unique;
                    }
                });
                return isUnique;

            }, "This product name is already taken");

            // Call initializeValidation function
            initializeValidation("#add_product_form", validationRules, validationMessages);

            // Optionally, you can handle the form submission with validation
            $("#submitBtn").click(function() {
                $("#add_product_form").submit();
            });
            // Initialize Select2 for Company dropdown
            initializeSelect2('#brand', '#create_product');

            // Initialize Select2 for Category dropdown
            initializeSelect2('#category', '#create_product');

            // Initialize Select2 for UOM dropdown
            initializeSelect2('#uom', '#create_product');
        });
        // for edit
        function editProductDetails(url) {
            getData(url, function(data) {
                let modalContent = document.getElementById('edit_modal_container');
                console.log(data);
                data = data.data;
                // Set the values based on the provided data
                modalContent.innerHTML = `
                            <form id="edit_product_form" method="post" action="{{ route('admin.productMaster.update', 1) }}">
                                @csrf
                                @method('PATCH')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="hidden" name="id" value="${data.id}">
                                            <label for="name" class="form-label">Product Name</label>
                                            <input value="${data.name}" id="name" placeholder="E.g., SuperWidget 5000" class="form-control" name="name" type="text" required>
                                            <span id="name-error" class="text-danger"></span>
                                            <div id="loader" style="display: none;">Loading...</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="brand" class="form-label">Company</label>
                                            <select class="js-example-basic-single form-select" id="edit_brand" name="company_id" data-width="100%" required>
                                                <option selected disabled>Select Company</option>
                                                @foreach (getCompanies() as $item)
                                                    <option value="{{ $item->id }}" ${data.company_id == '{{ $item->id }}' ? 'selected' : ''}>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="min_stock_depo" class="form-label">Min. Stock Reserve for Depo</label>
                                            <input value="${data.min_stock_depo}" id="min_stock_depo" placeholder="E.g., 5" class="form-control" name="min_stock_depo" type="number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="js-example-basic-single form-select" id="edit_category" name="sub_category_id" data-width="100%" required>
                                                <option selected disabled>Select Product Category</option>
                                                @foreach (getCategories() as $item)
                                                    <optgroup label="{{ $item->name }}"></optgroup>
                                                    @foreach (getSubCategory($item->id) as $subCategory)
                                                        <option value="{{ $subCategory->id }}" ${data.sub_category_id == '{{ $subCategory->id }}' ? 'selected' : ''}>{{ $subCategory->name }}</option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="uom" class="form-label">Unit of Measure (UOM)</label>
                                            <select class="js-example-basic-single form-select" id="edit_uom" name="uom" data-width="100%" required>
                                                <option selected disabled>Select Unit of Measure</option>
                                                @foreach (getUOM() as $item)
                                                    <option value="{{ $item->id }}" ${data.uom_id == '{{ $item->id }}' ? 'selected' : ''}>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="min_stock_warehouses" class="form-label">Min. Stock Reserve for Warehouses</label>
                                            <input value="${data.min_stock_warehouse}" id="min_stock_warehouses" placeholder="E.g., 10" class="form-control" name="min_stock_warehouses" type="number" required>
                                        </div>
                                    </div>
                                </div>
                            </form>
                `;

                initializeSelect2('#edit_brand', '#edit_modal');
                initializeSelect2('#edit_category', '#edit_modal');
                initializeSelect2('#edit_uom', '#edit_modal');

                var editValidationRules = {
                    name: {
                        required: true,
                    },
                    sub_category_id: {
                        required: true
                    },
                    company_id: {
                        required: true
                    },
                    uom: {
                        required: true
                    },
                    min_stock_warehouses: {
                        required: true,
                        digits: true // Example rule for numeric input
                    },
                    min_stock_depo: {
                        required: true,
                        digits: true // Example rule for numeric input
                    }
                };

                var editValidationMessages = {
                    name: {
                        required: "Please enter a product name",
                        uniqueProductName: "This product name is already taken"
                    },
                    sub_category_id: {
                        required: "Please select a product category"
                    },
                    company_id: {
                        required: "Please select a company"
                    },
                    uom: {
                        required: "Please select a unit of measure"
                    },
                    min_stock_warehouses: {
                        required: "Please enter the minimum stock for warehouses",
                        digits: "Please enter a valid number"
                    },
                    min_stock_depo: {
                        required: "Please enter the minimum stock for depo",
                        digits: "Please enter a valid number"
                    }
                };
                initializeValidation("#edit_product_form", editValidationRules, editValidationMessages);

                // Show the modal
                $('#edit_modal').modal('show');
            });
        }
        $("#submitUpdateBtn").click(function() {
            $("#edit_product_form").submit();
        });
        $('.item_status').change(function() {
            var mode = $(this).prop('checked');
            var id = $(this).data("id");
            var url = '{{ route('admin.productMaster.status') }}';

            // Call the handleStatusChange function with the parameters
            handleStatusChange(url, mode, id);
        });
    </script>
@endsection
