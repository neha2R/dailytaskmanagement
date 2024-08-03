@extends('layouts.app')

@section('content')
    <style>
        .deliveryByDate {
            display: block;
            margin-top: 4px;
            margin-left: 4px;
            font-size: 12px;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('admin.consignments.index') }}" class="nav-link tab-heading"
                    aria-selected="true">Consignments</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('trips.trip.index') }}" class="nav-link tab-heading active" aria-selected="false">Trips</a>
            </li>
        </ul>
    </div>
    <div class="container">
        <h5 class="header mt-1 mb-2">
            <i class="mdi mdi-folder-plus-outline me-2"></i> Create New Trip
        </h5>
        <form id="create_trip" class="ajax-form" method="post" action="{{ route('trips.trip.store') }}">
            @csrf
            <div class="container">
                <div class="card border-0 pt-0 mb-3">
                    <div class="card-body py-2 px-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="origin_location_type" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-map-marker"></span> Origin Location Type
                                </label>
                                <select class="js-example-basic-single form-select" id="origin_location_type"
                                    name="origin_location_type" data-width="100%">
                                    <option selected disabled>Select Location Type</option>
                                    @foreach ($locationTypes as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="originLocation" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-map"></span> Origin Location
                                </label>
                                <select class="js-example-basic-single form-select" id="originLocation"
                                    name="originLocation" data-width="100%">
                                    <option selected disabled>Select Origin Location</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="deliver_to" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-calendar"></span> Trip Start Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input name="start_date" type="text" id="start_date"
                                        class="form-control placeholde-size flatpickr-input" placeholder="Select date"
                                        data-input="" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="end_date" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-calendar"></span> Trip End Date
                                </label>
                                <div class="input-group flatpickr">
                                    <input name="end_date" type="text" id="end_date"
                                        class="form-control placeholde-size flatpickr-input" placeholder="Select date"
                                        data-input="" readonly="readonly">
                                </div>
                            </div>
                            <!-- Add other fields with MDI icons as needed -->
                        </div>
                    </div>
                </div>

                <!-- Consignment Section (Assuming you have a similar structure for consignments) -->
                <div id="multi-point" class="card trip-card mb-3">
                    <div class="card-body py-2 px-3">
                        <div id="error-container">

                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody id="tbody">
                                        <tr>
                                            <td class="col-4 pt-0 ps-0">
                                                <label for="consignment" class="form-label mb-2 ms-1 d-block">
                                                    <span class="mdi mdi-package"></span> Consignment No.
                                                </label>
                                                <select class="js-example-basic-single form-select consignments"
                                                    id="consignment" name="consignments[0]" data-width="100%">
                                                    <option selected disabled>Select Consignment</option>
                                                </select>
                                            </td>
                                            <td class="col-4 pe-0 pt-0 ps-2">
                                                <label for="delivery_location" class="form-label mb-2 ms-1 d-block">
                                                    <span class="mdi mdi-map-marker"></span> Delivery Location
                                                </label>
                                                <input type="text" class="form-control delivery-location" readonly
                                                    value="" placeholder="Delivery Location">
                                            </td>
                                            <td class="col-4 pe-0 pt-0 ps-2">
                                                <label for="delivery_date" class="form-label mb-2 ms-1">
                                                    <span class="mdi mdi-calendar"></span> Delivery Date
                                                </label>
                                                <div class="input-group flatpickr">
                                                    <input type="text" name="deliveryDate[0]" id="consignment_delivery"
                                                        class="form-control placeholde-size flatpickr-input consignment_delivery"
                                                        placeholder="Select date" data-input="" readonly="readonly">
                                                </div>
                                                <span class="text-muted me-2 deliveryByDate"></span>

                                            </td>
                                            <td class="pe-0">
                                                <button id="addBtn" type="button"
                                                    class="border-0 text-primary btn mt-4 pe-0">
                                                    <i class="mdi mdi-plus-circle fs-4"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Vehicle and Driver Section -->
                <div class="card">
                    <div class="card-body py-2 px-3">
                        <div class="row">
                            <div class="col-md-6 mb-2 ">
                                <label for="vehicleno" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-car"></span> Vehicle NO.
                                </label>
                                <select onchange="getDriver(this)" class="js-example-basic-single form-select"
                                    id="vehicle_id" name="vehicle_id" data-width="100%">
                                    <option selected disabled>Select Vehicle</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2 ">
                                <label for="driver_name" class="form-label mb-2 ms-1">
                                    <span class="mdi mdi-account"></span> Driver Name
                                </label>
                                <input type="text" readonly class="form-control driver_name" value=""
                                    placeholder="Driver Name">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn-primary btn-sm btn"> Create Trip
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            var rowIdx = 1;
            var maxRow = 20;
            $('#create_consignment button[type="submit"]').prop('disabled', true);


            $('#addBtn').on('click', function() {
                if (rowIdx <= maxRow) {
                    // Create a new row HTML string
                    var newRowHtml = `<tr id="new_tr_${rowIdx}">
                                <td class="col-4 pt-0 ps-0">
                                    <label for="consignment" class="form-label mb-2 ms-1 d-block">
                                        <span class="mdi mdi-package"></span> Consignment No.
                                    </label>
                                    <select class="js-example-basic-single form-select consignments"
                                        id="consignment_${rowIdx}" name="consignments[${rowIdx}]" data-width="100%">
                                    </select>
                                </td>
                                <td class="col-4 pe-0 pt-0 ps-2">
                                    <label for="delivery_location" class="form-label mb-2 ms-1 d-block">
                                        <span class="mdi mdi-map-marker"></span> Delivery Location
                                    </label>
                                    <input type="text" class="form-control delivery-location" readonly
                                        value="" placeholder="Delivery Location">
                                </td>
                                <td class="col-4 pe-0 pt-0 ps-2">
                                    <label for="delivery_date" class="form-label mb-2 ms-1">
                                        <span class="mdi mdi-calendar"></span> Delivery Date
                                    </label>
                                    <div class="input-group flatpickr">
                                        <input type="text" name="deliveryDate[${rowIdx}]" id="consignment_delivery_${rowIdx}"
                                            class="form-control placeholde-size flatpickr-input consignment_delivery"
                                            placeholder="Select date" data-input="" readonly="readonly">
                                    </div>
                                    <span class="text-muted me-2 deliveryByDate"></span>
                                </td>
                                <td class="pe-0">
                                    <button type="button" class="border-0 text-danger btn mt-4 pe-0 remove">
                                        <i class="mdi mdi-minus-circle fs-4"></i>
                                    </button>
                                </td>
                            </tr>`;

                    // Append the new row HTML to the tbody
                    $('#tbody').append(newRowHtml);

                    cloneOptions('#consignment', `#consignment_${rowIdx}`, 'Select Consignment');

                    // Initialize Select2 for the newly added select element
                    initializeSelect2(`#consignment_${rowIdx}`, 'body');
                    initializeFlatpickrWithTime(`#consignment_delivery_${rowIdx}`);


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

                    // Gets the row number from <tr> id.
                    var dig = parseInt(id.split('_')[2]);

                    // Modifying row id.
                    $(this).attr('id', `new_tr_${dig - 1}`);

                    // Modifying select element id.
                    $(this).find('.consignments').attr('id', `consignment_${dig - 1}`);

                    // Modifying flatpickr input id.
                    $(this).find('.consignment_delivery').attr('id',
                        `consignment_delivery_${dig - 1}`);

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

            validateForm();
            initializeSelect2('#origin_location_type', 'body');
            initializeSelect2('#originLocation', 'body');
            initializeSelect2('#consignment', 'body');
            initializeSelect2('#vehicle_id', 'body');

            initFlatpickrWithMinDate('#start_date', '#end_date');
            initFlatpickrWithMaxDate('#end_date', '#start_date');
            // initializeFlatpickr('#end_date_input');
            initializeFlatpickrWithTime('#consignment_delivery');

            $('#origin_location_type').on('change', function() {
                var selectedValue = $(this).val();

                // Replace 'type' in the URL with the selected value
                var url = "{{ route('trips.getLocations', ['type' => 'type']) }}";
                url = url.replace('type', selectedValue);

                // Call the getData function
                getData(url, function(data) {
                    console.log(data);
                    if (data.status == 200) {
                        updateDropdown('originLocation', data.data, 'Select Origin Location');
                    }
                });
            });

            $('#originLocation').on('change', function() {
                enableSubmitButton()
                var selectedValue = $(this).val();
                var originType = $('#origin_location_type').val();


                // Replace 'type' in the URL with the selected value
                var url = "{{ route('trips.getConsignments', ['id' => 'id', 'type' => 'type']) }}";
                url = url.replace('id', selectedValue);
                url = url.replace('type', originType);
                // Call the getData function
                getData(url, function(data) {
                    if (data.status == 200) {
                        console.log(data);
                        updateDropdownsInMultipleSelect('consignments', data.consignments,
                            'Select Consignment');
                    }
                });
            });

            $(document).on('change', '.consignments', function() {
                var selectedValue = $(this).val();
                var originType = $('#origin_location_type').val();
                var currentRow = $(this).closest('tr');

                // Replace 'type' in the URL with the selected value
                var url = "{{ route('trips.getConDetails', ['id' => 'id']) }}";
                url = url.replace('id', selectedValue);

                // Call the getData function
                getData(url, function(data) {
                    if (data.status == 200) {
                        console.log(data);
                        currentRow.find('.delivery-location').val(data.consignment
                            .destination_location);
                        // Update delivery date text
                        var deliveryDateText = currentRow.find('.deliveryByDate');
                        deliveryDateText.text('Delivery By date: ' + data
                            .consignment.delivery_by_date);
                    }
                });
            });

            $('#create_trip').on('change', 'input[name="start_date"], input[name^="end_date"]', function() {
                var startDate = $('input[name="start_date"]').val();
                var endDate = $('input[name="end_date"]').val();

                // Validate if both start_date and deliveryDate are set
                if (startDate && endDate) {
                    // Make an AJAX request
                    $.ajax({
                        url: '{{ route('trips.getVehicles') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "startDate": startDate,
                            'endDate': endDate
                        },
                        beforeSend: function() {
                            $('#spin').removeClass('d-none');
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                updateDropdown('vehicle_id', response.vehicles,
                                    'Select Vehicle')
                            }
                            $('#spin').addClass('d-none');

                        },
                        error: function(error) {
                            $('#spin').addClass('d-none');
                        }
                    });
                }
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
                $('.consignments').not(element).each(function() {
                    selectedValues.push($(this).val());
                });

                var currentValue = $(element).val();
                return $.inArray(currentValue, selectedValues) === -1;
            }, "Consignment cannot be the same");

            // Define validation rules and messages
            const rules = {
                origin_location_type: "required",
                originLocation: "required",
                start_date: "required",
                end_date: "required",
                vehicle_id: "required",
                driver_name: "required",
            };

            const messages = {
                origin_location_type: "Please select the origin location type.",
                originLocation: "Please select the origin location.",
                start_date: "Please select the trip start date.",
                end_date: "Please select the trip end date.",

                vehicle_id: "Please select a vehicle.",
                driver_name: "Please enter the driver name.",
            };

            // Initialize validation using jQuery Validation Plugin
            for (let index = 0; index < 9; index++) {
                // Validation rules for consignments
                rules[`consignments[${index}]`] = {
                    required: true,
                    uniqueSelection: true,
                };
                // Validation messages for consignments
                messages[`consignments[${index}]`] = {
                    required: "Please select a consignment.",
                    uniqueSelection: "Consignment cannot be the same.",
                };
                // Validation rules for delivery dates
                rules[`deliveryDate[${index}]`] = {
                    required: true,
                };

                // Validation messages for delivery dates
                messages[`deliveryDate[${index}]`] = {
                    required: "Please enter the delivery date. with time",
                };
            }


            // Initialize validation using jQuery Validation Plugin
            const validator = initializeValidation('#create_trip', rules, messages);


            $('#create_trip').on('submit', function(e) {
                e.preventDefault();

                if ($(this).valid()) { // Check if the form is valid
                    // Serialize the form data
                    const formData = $(this).serialize();

                    // Perform AJAX submission
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('trips.trip.store') }}",
                        data: formData,
                        beforeSend: function() {
                            $('#spin').removeClass('d-none');
                        },
                        success: function(response) {
                            // Handle the success response
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
                                        "{{ route('trips.trip.index') }}";
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
                const errorList = $('<ul class="error-list ps-2"></ul>');

                // Iterate through errors and add them to the list
                $.each(errors, function(_, errorMessage) {
                    const listItem = $('<li class="text-danger"></li>').html(errorMessage);
                    errorList.append(listItem);
                });

                // Append the error list to the desired container
                $('#error-container').html(errorList);
            }
        }

        function getDriver(object) {
            var $object = $(object);
            var id = $object.val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'getDriver/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    if (data.status == 200) {
                        var $driverNameInput = $object.closest('.row').find('.driver_name');
                        $driverNameInput.val(data.driver.user.name);
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }
    </script>
@endsection
