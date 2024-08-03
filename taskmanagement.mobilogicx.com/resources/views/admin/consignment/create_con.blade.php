@extends('layouts.app')

@section('content')
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
    </div>
    <div class="container">
        <h5 class="header mt-1 mb-2">
            <i class="mdi mdi-folder-plus-outline me-2"></i> Add New Consignment
        </h5>
        <div class="card">
            <div class="card-body">
                <form id="create_consignment" class="ajax-form" method="post" action="{{ route('admin.consignments.store') }}">
                    @csrf
                    <!-- Consignment Form Fields -->
                    <h6 class="mb-2">Locations Details</h6>
                    <div class="row mb-2">
                        <div class="col-md-6 mb-3">
                            <label for="warehouse_from" class="form-label mb-2 ms-1">
                                <i class="mdi mdi-map-marker"></i> Origin Location Type
                            </label>
                            <select class="js-example-basic-single form-select" id="origin_location_type"
                                name="origin_location_type" data-width="100%">
                                <option selected disabled>Select Location Type</option>
                                @foreach ($locationTypes as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="delivery_location" class="form-label mb-2 ms-1">
                                <i class="mdi mdi-map-marker"></i> Delivery Location Type
                            </label>
                            <select class="js-example-basic-single form-select" id="delivery_location_type"
                                name="delivery_location_type" data-width="100%">
                                <option selected disabled>Select Location Type</option>
                                <!-- Add options dynamically from your data -->
                                @foreach ($locationTypes as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="origin_location" class="form-label mb-2 ms-1">
                                <i class="mdi mdi-map-marker"></i> Origin Location
                            </label>
                            <select class="js-example-basic-single form-select" id="origin_location" name="origin_location"
                                data-width="100%">
                                <option selected disabled>Select Location</option>
                                <!-- Add options dynamically from your data -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="delivery_location" class="form-label mb-2 ms-1">
                                <i class="mdi mdi-map-marker"></i> Delivery Location
                            </label>
                            <select class="js-example-basic-single form-select" id="delivery_location"
                                name="delivery_location" data-width="100%">
                                <option selected disabled>Select Location</option>
                                <!-- Add options dynamically from your data -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="delivery_by_date" class="form-label mb-2 ms-1">
                                <i class="mdi mdi-calendar"></i> Delivery By Date
                            </label>
                            <div class="input-group flatpickr">
                                <input type="text" class="form-control" placeholder="Select date" readonly="readonly"
                                    id="delivery_by_date" name="delivery_by_date">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3 d-none" id="project">
                            <label for="origin_location" class="form-label mb-2 ms-1">
                                <i class="mdi mdi-map-marker"></i> Project
                            </label>
                            <select class="js-example-basic-single form-select" id="project_id" name="project_id"
                                data-width="100%">
                                <option selected disabled>Select Project</option>
                                @foreach ($activeProjects as $item)
                                    <option value="{{$item->id}}">{{$item->project_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="mb-2">Product Details</h6>
                    <div id="error-container">

                    </div>
                    <div class="table-responsive">
                        <!-- Stocks Table and Fields... -->
                        <table class="table table-bordered text-center">
                            <thead class="bg-primary">
                                <tr>
                                    <th class="text-white w-25"><i class="mdi mdi-package"></i> Product</th>
                                    <th class="text-white"><i class="mdi mdi-cube"></i> Transfer QTY</th>
                                    <th class="text-white"><i class="mdi mdi-ruler"></i> UOM</th>
                                    <th class="text-white"><i class="mdi mdi-tag"></i> Category</th>
                                    <th class="text-white"><i class="mdi mdi-ruler"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                <tr id="first_tr">
                                    <td>
                                        <div class="">
                                            <label for="select_product" class="form-label visually-hidden">Product</label>
                                            <select class="js-example-basic-single form-select products" id="select_product"
                                                name="products[0]" data-width="100%">
                                                <option selected disabled>Select product</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <label for="transfer_qty" class="form-label visually-hidden">Transfer
                                            Quantity</label>
                                        <div class="input-group">
                                            <input value="{{ old('model') }}" id="transfer_qty" placeholder="Quantity"
                                                class="form-control qty" name="transfer_qty[0]" type="number">
                                            <div class="badge bg-success xs mt-1 max_qty d-none"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <label for="uom" class="form-label visually-hidden">UOM</label>
                                        <input value="{{ old('uom') }}" id="uom" class="form-control uom"
                                            type="text" placeholder="UOM" readonly>
                                    </td>
                                    <td>
                                        <label for="category" class="form-label visually-hidden">Category</label>
                                        <input value="{{ old('model') }}" id="category" class="form-control category"
                                            placeholder="Category" type="text" readonly>
                                    </td>
                                    <td>
                                        <label class="visually-hidden">Action</label>
                                        <button id="addBtn" type="button" class="btn border-0 text-primary">
                                            <i class="mdi mdi-plus-circle fs-4"></i>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Add other rows dynamically... -->
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn-primary btn-sm btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#create_consignment button[type="submit"]').prop('disabled', true);
            validateForm();
            var rowIdx = 1;
            var maxRow = 20;


            // jQuery button click event to add a row.
            $('#addBtn').on('click', function() {
                if (rowIdx <= maxRow) {
                    // Adding a row inside the tbody.
                    $('#tbody').append(`<tr id="new_tr_${rowIdx}">
                                        <td>
                                            <div class="">
                                                <label for="select_product_${rowIdx}" class="form-label visually-hidden">Product</label>
                                                <select class="js-example-basic-single form-select products"
                                                    id="select_product_${rowIdx}" name="products[${rowIdx}]" data-width="100%">
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <label for="transfer_qty" class="form-label visually-hidden">Transfer Quantity</label>
                                            <div class="input-group">
                                                <input value="{{ old('model') }}" id="transfer_qty_${rowIdx}" placeholder="Quantity"
                                                    class="form-control qty" name="transfer_qty[${rowIdx}]" type="number">
                                                <div class="badge bg-success xs mt-1 max_qty d-none"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <label for="uom" class="form-label visually-hidden">UOM</label>
                                            <input value="{{ old('uom') }}" id="uom_${rowIdx}" class="form-control uom" type="text"
                                                placeholder="UOM" readonly>
                                        </td>
                                        <td>
                                            <label for="category" class="form-label visually-hidden">Category</label>
                                            <input value="{{ old('model') }}" id="category_${rowIdx}" class="form-control category"
                                                placeholder="Category" type="text" readonly>
                                        </td>
                                        <td>
                                            <button type="button" class="btn text-danger remove"><i
                                                    class="mdi mdi-minus-circle fs-4"></i></button>
                                        </td>
                                    </tr>`

                    );
                    cloneOptions('#select_product', `#select_product_${rowIdx}`, 'Select Product');

                    // Initialize Select2 for the newly added select element
                    initializeSelect2(`#select_product_${rowIdx}`, 'body');

                    // Increment the row index for the next row
                    rowIdx++;
                } else {
                    alert('Maximum row limit reached!');
                }
            });

            $('#tbody').on('click', '.remove', function() {
                // Getting the current row
                var currentRow = $(this).closest('tr');

                // Getting all the rows next to the current row
                var childRows = currentRow.nextAll();

                // Iterate across all the rows obtained to change the index
                childRows.each(function() {
                    // Getting <tr> id.
                    var id = $(this).attr('id');

                    // Getting the <select> inside the .input-group class.
                    var selectId = $(this).find('.products').attr('id');

                    // Gets the row number from <tr> id.
                    var dig = parseInt(id.split('_')[2]);

                    // Modifying row id.
                    $(this).attr('id', `new_tr_${dig - 1}`);

                    // Modifying select element id.
                    $(this).find('.products').attr('id', `select_product_${dig - 1}`);

                    // Update names and IDs for all inputs within the row
                    $(this).find('[name]').each(function() {
                        var originalName = $(this).attr('name');
                        var newName = originalName.replace(/\[(\d+)\]/g, '[' + (dig - 1) +
                            ']');
                        $(this).attr('name', newName);

                        var originalId = $(this).attr('id');
                        if (originalId) {
                            var newId = originalId.replace(/\d+$/, (dig - 1));
                            $(this).attr('id', newId);
                        }
                    });
                });

                // Removing the current row.
                currentRow.remove();
                // Decrement the row index
                rowIdx--;
            });

            function cloneOptions(sourceSelector, targetSelector, defaultOption = 'Select options') {
                var sourceSelect = $(sourceSelector);
                var targetSelect = $(targetSelector);

                // Clear existing options in the target select
                targetSelect.empty();

                // Add the first default option
                targetSelect.append($('<option>', {
                    value: '',
                    text: defaultOption,
                    selected: true,
                    disabled: true
                }));

                // Clone options from source select to target select
                sourceSelect.find('option').each(function() {
                    var optionValue = $(this).val();
                    if (optionValue.trim() !== "") {
                        var optionText = $(this).text();
                        // Create a new option element with the same value and text
                        var optionClone = $('<option></option>').val(optionValue).text(optionText);
                        // Append the cloned option to the target select
                        targetSelect.append(optionClone);
                    }
                });
            }

            // Call initializeSelect2 for all select elements
            initializeSelect2('#origin_location_type', 'body');
            initializeSelect2('#delivery_location_type', 'body');
            initializeSelect2('#origin_location', 'body');
            initializeSelect2('#delivery_location', 'body');
            initializeSelect2('#select_product', 'body');
            initializeSelect2('#project_id', 'body');

            // Call initializeFlatpickr for a specific input
            initializeFlatpickr('#delivery_by_date');

            $('#origin_location_type').on('change', function() {
                var selectedValue = $(this).val();

                // Replace 'type' in the URL with the selected value
                var url = "{{ route('admin.getLocations', ['type' => 'type']) }}";
                url = url.replace('type', selectedValue);

                // Call the getData function
                getData(url, function(data) {
                    if (data.status == 200) {
                        updateDropdown('origin_location', data.data, 'Select Origin Location');
                    }
                });
            });

            $('#delivery_location_type').on('change', function() {
                var selectedValue = $(this).val();

                // Replace 'type' in the URL with the selected value
                var url = "{{ route('admin.getLocations', ['type' => 'type']) }}";
                url = url.replace('type', selectedValue);

                getData(url, function(data) {
                    if (data.status == 200) {
                        var projectInput = document.getElementById('project');
                        updateDropdown('delivery_location', data.data, 'Select Delivery Location');
                        if (selectedValue == "{{ getInventoryTypeBySlug('site') }}") {
                            projectInput.classList.remove('d-none');
                        } else {
                            projectInput.classList.add('d-none');

                        }
                    }
                });
            });

            $('#origin_location').on('change', function() {
                enableSubmitButton()
                var selectedValue = $(this).val();
                var originType = $('#origin_location_type').val();


                // Replace 'type' in the URL with the selected value
                var url = "{{ route('admin.getProducts', ['id' => 'id', 'type' => 'type']) }}";
                url = url.replace('id', selectedValue);
                url = url.replace('type', originType);

                // Call the getData function
                getData(url, function(data) {
                    if (data.status == 200) {
                        console.log(data);
                        updateDropdownsInMultipleSelect('products', data.products,
                            'Select Product');
                    }
                });
            });

            $(document).on('change', '.products', function() {
                var selectedValue = $(this).val();
                var originType = $('#origin_location_type').val();
                var currentRow = $(this).closest('tr');

                // Replace 'type' in the URL with the selected value
                var url = "{{ route('admin.getProductDetails', ['id' => 'id']) }}";
                url = url.replace('id', selectedValue);

                // Call the getData function
                getData(url, function(data) {
                    if (data.status == 200) {
                        console.log(data);
                        currentRow.find('.uom').val(data.product.uom_name);
                        currentRow.find('.category').val(data.product.category_name);
                        // currentRow.find('.qty').attr('maxval', data.product.id);
                    }
                });
            });

            function updateDropdownsInMultipleSelect(className, data, defaultOption) {
                $(`.${className}`).each(function() {
                    const selectElement = $(this);
                    clearAndAddDefaultOption(selectElement, defaultOption);
                    data.forEach((option) => {
                        selectElement.append($('<option>', {
                            value: option.id,
                            text: option.name
                        }));
                    });
                });
            }

            function updateDropdown(elementId, options, defaultOption = 'Select options') {
                const selectElement = $(`#${elementId}`);
                clearAndAddDefaultOption(selectElement, defaultOption);
                options.forEach((option) => {
                    selectElement.append($('<option>', {
                        value: option.id,
                        text: option.name
                    }));
                });
            }

            function clearAndAddDefaultOption(selectElement, defaultOption) {
                selectElement.empty();
                selectElement.append($('<option>', {
                    value: '',
                    text: defaultOption,
                    selected: true,
                    disabled: true
                }));
            }

            function enableSubmitButton() {
                $('#create_consignment button[type="submit"]').prop('disabled', false);
            }
        });

        function validateForm() {
            // Define custom methods
            $.validator.addMethod('uniqueSelection', function(value, element) {
                var selectedValues = [];
                $('.products').not(element).each(function() {
                    selectedValues.push($(this).val());
                });

                var currentValue = $(element).val();
                return $.inArray(currentValue, selectedValues) === -1;
            }, "Product cannot be same");

            // Add a custom method for validating if two fields have different values
            $.validator.addMethod("notEqualTo", function(value, element, param) {
                return this.optional(element) || value !== $(param).val();
            }, "Fields cannot be equal");

            // Add custom validation method for checking quantity
            $.validator.addMethod('checkQuantity', function(value, element) {
                const maxAllowedQuantity = $(element).data('maxval');
                return parseInt(value) <= parseInt(maxAllowedQuantity);
            }, function(params, element) {
                const maxAllowedQuantity = $(element).data('maxval');
                return $.validator.format(
                    `Quantity must be less than or equal to stock quantity ${maxAllowedQuantity}`);
            });
            // Define validation rules and messages
            const rules = {
                origin_location_type: "required",
                delivery_location_type: "required",
                origin_location: "required",
                delivery_location: {
                    required: true,
                    notEqualTo: "#origin_location",
                },
                delivery_by_date: "required",
                project_id: {
                    required: function(element) {
                        return $("#delivery_location_type").val() == "{{ getInventoryTypeBySlug('site') }}";
                    }
                },
            };

            const messages = {
                origin_location_type: "Please select the origin location type.",
                delivery_location_type: "Please select the delivery location type.",
                origin_location: "Please select the origin location.",
                delivery_location: {
                    required: "Please select the delivery location.",
                    notEqualTo: "Origin location and delivery location must be different.",
                },
                delivery_by_date:{
                    required:"Project is required when delivery location type is 'site'."
                },

            };

            // Dynamically add rules and messages for products and quantities
            for (let index = 0; index <= 20; index++) {
                rules[`products[${index}]`] = {
                    required: true,
                    uniqueSelection: true,
                };
                messages[`products[${index}]`] = {
                    required: "Please select a product.",
                    uniqueSelection: "Product cannot be the same.",
                };

                rules[`transfer_qty[${index}]`] = {
                    required: true,
                    // checkQuantity: true,
                };
                messages[`transfer_qty[${index}]`] = {
                    required: "Please enter the transfer quantity.",
                };
            }

            // Initialize validation using jQuery Validation Plugin
            const validator = initializeValidation('#create_consignment', rules, messages);

            $('#create_consignment').on('submit', function(e) {
                e.preventDefault();

                if ($(this).valid()) { // Check if the form is valid
                    // Serialize the form data
                    const formData = $(this).serialize();

                    // Perform AJAX submission
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('admin.consignments.store') }}",
                        data: formData,
                        beforeSend: function() {
                            $('#spin').removeClass('d-none');
                        },
                        success: function(response) {
                            // Handle the success response
                            console.log(response);
                            if (response.status == 200) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: false,
                                });
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message
                                });

                                // Use setTimeout for redirect after 2 seconds
                                setTimeout(function() {
                                    window.location.href =
                                        "{{ route('admin.consignments.index') }}";
                                }, 1000);
                                $('#spin').addClass('d-none');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                // Handle validation errors
                                displayValidationErrors(xhr.responseJSON.errors);
                            } else if (xhr.status === 500) {
                                // Handle 500 internal server error
                                console.error('Internal Server Error: ', xhr.responseText);
                                alert(
                                    'An unexpected error occurred. Please refresh and try again later.'
                                );
                            } else {
                                // Handle other errors
                                console.error(xhr.responseText);
                            }
                            $('#spin').addClass('d-none');

                        }
                    });
                }
            });

            function displayValidationErrors(errors) {
                // Clear existing error messages
                $('.error-list').remove();
                console.log(errors);
                const errorList = $('<ul class="error-list"></ul>');

                // Iterate through errors and add them to the list
                $.each(errors, function(_, errorMessage) {
                    const listItem = $('<li class="text-danger"></li>').html(errorMessage);
                    errorList.append(listItem);
                });

                // Append the error list to the desired container
                $('#error-container').html(errorList);
            }
        }
    </script>
@endsection
