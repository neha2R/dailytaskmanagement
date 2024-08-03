@extends('depot_head.layouts.app')
@section('content')
    <style>
        <style>.v_heading li {
            font-size: 18px;
            font-weight: 500;
        }

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

        .matrial-inputs {
            width: 110px;
        }
    </style>
    <div class="row">
        <div class="col-8">
            <div class="card mb-3 pb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 ">
                    <h5 class="">Consignement Details</h5>
                </div>
                <div class="card-body p-0 pt-2">
                    <div class="row">
                        <div class="">
                            <ul class="d-flex list-unstyled text-center v_heading">
                                <li class="text-capitalize  col">Consignement No</li>
                                <li class="text-capitalize  col">Trip No</li>
                                <li class="text-capitalize  col">Vechile No</li>
                                <li class="text-capitalize  col">Driver Name</li>
                            </ul>
                            <ul class="d-flex list-unstyled text-center v_details">
                                <li class="text-capitalize col">{{ env('PrefixTrip') . $consignment->id ?? '-' }}</li>
                                <li class="text-capitalize col">
                                    {{ env('PrefixCon') . $consignment->trip_assigned_cons->trip_id ?? '' }}
                                </li>
                                <li class="text-capitalize col">
                                    {{ $consignment->trip_assigned_cons->trip->vehicle->vehicle_number ?? '' }}
                                </li>
                                <li class="text-capitalize col">
                                    {{ $consignment->trip_assigned_cons->trip->user->name ?? '' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center py-2 px-2 ">
                    <h5 class="">Material Details</h5>
                    @if ($consignment->checkout_data)
                        <p><span class="badge bg-success">Checked Out Successfully</span></p>
                    @else
                        <p><span class="badge bg-primary">Ready for Checkout</span></p>
                    @endif
                </div>
                <div class="card-body pb-1">
                    <form method="POST" id="checkout-form" action="{{ route('dpHead.inventory-management.store') }}">
                        @csrf
                        <input type="hidden" name="consignement_id" value="{{ $consignment->id }}">
                        <div class="table-responsive scrollable-tbody">
                            <table class="table table-hover ">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th class="text-center">Delivered Qty</th>
                                        <th>UOM</th>
                                        <th class="text-center">Damaged/Missing</th>
                                        <th class="text-end">Actual Qty</th>
                                        <th class="text-end">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($consignment->checkout_data)
                                        @foreach ($consignment->checkout_data->checkout_products as $item)
                                            <tr>
                                                <td>
                                                    <p class="mt-2">{{ $item->product->name }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="mt-2">
                                                        {{ intval($item->actual_quantity) + intval($item->missing_damage_quantity) }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="mt-2">{{ $item->product->uom->name ?? '-' }}</p>
                                                </td>
                                                <td class="text-center">
                                                    <p class="mt-2">{{ floor($item->missing_damage_quantity) ?? '-' }}</p>
                                                </td>
                                                <td class="text-end">
                                                    <p class="mt-2">{{ floor($item->actual_quantity) ?? '-' }}</p>
                                                </td>
                                                <td class="text-end">
                                                    <p class="mt-2">{{ $item->description ?? '-' }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @foreach ($consignment->products as $item)
                                            <tr>
                                                <td>
                                                    <p class="mt-2">{{ $item->product->name }}</p>
                                                </td>
                                                <td>
                                                    <p class="mt-2">{{ $item->quantity }}</p>
                                                </td>
                                                <td>
                                                    <p class="mt-2">{{ $item->product->uom->name ?? '-' }}</p>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="">
                                                            <input min="0" type="number"
                                                                name="missingDamageQty[{{ $item->product_id }}][]"
                                                                class="form-control missing-input" style="width: 95px"
                                                                oninput="updateActualQty(this)">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end">
                                                        <div class="">
                                                            <input min="0" readonly
                                                                data-quantity="{{ $item->quantity }}"
                                                                value="{{ $item->quantity }}" type="text"
                                                                name="actualQty[{{ $item->product_id }}][]"
                                                                class="form-control actual-input" style="width: 70px">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end">
                                                        <div class="">
                                                            <input type="text"
                                                                name="description[{{ $item->product_id }}][]"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end me-2 mt-4">
                            @if (!$consignment->checkout_data)
                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
                    <h5>Delivery Challan</h5>
                    {{-- <button class="btn btn-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#add_challan"
                        data-bs-whatever="@getbootstrap"><i class="mdi mdi-plus fs-6 pe-1"></i>Add</button> --}}
                </div>
                <div class="card-body scrollable-tbody pt-2 pb-2">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-start">Consignement</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($consignment->delivery_challan)
                                    <tr>
                                        <td class="text-start">{{ env('PrefixCon') . $consignment->id }}</td>
                                        <td class="text-end"><button
                                                onclick="viewChallan('{{ route('dpHead.getChallan', $consignment->delivery_challan->id) }}')"
                                                class="btn btn-xs btn-success">view</button></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center text-secondary">No delivery challan available.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 ">
                    <h5>PODs</h5>
                    {{-- <button class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#add_pods"
                    data-bs-whatever="@getbootstrap"><i class="mdi mdi-plus fs-6 pe-1"></i>Add</button> --}}
                </div>
                <div class="card-body scrollable-tbody pt-2 pb-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-start">Consignement</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($consignment->pods)
                                <tr>
                                    <td class="text-start">{{ env('PrefixCon') . $consignment->id }}</td>
                                    <td class="text-end"><button
                                            onclick="viewChallan('{{ route('dpHead.getChallan', $consignment->pods->id) }}')"
                                            class="btn btn-xs btn-success">view</button></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2" class="text-center text-secondary">No delivery PODs available.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0 ">
                    <h5>E-way bill</h5>
                    {{-- <button class="btn btn-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#add_bill"
                        data-bs-whatever="@getbootstrap"><i class="mdi mdi-plus fs-6"></i>Add</button> --}}
                </div>
                <div class="card-body scrollable-tbody pt-2 pb-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-start">Bill No</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($consignment->eway_bill)
                                <tr>
                                    <td class="text-start">{{ $consignment->eway_bill->bill_number }}</td>
                                    <td class="text-end"><button
                                            onclick="viewBill('{{ route('dpHead.getBill', $consignment->eway_bill->id) }}')"
                                            class="btn btn-xs btn-success">view</button></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2" class="text-center text-secondary">No e-way bill available.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
                    <h5>Activity</h5>
                </div>
                <div class="card-body ">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewDetailsModel" tabindex="-1" aria-labelledby="viewDetailsModel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="headingDetailsModel"></h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container" id="DetailsContainer">

                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // Define validation rules and messages for the checkout-form
            const checkoutRules = {};
            const checkoutMessages = {};
            @foreach ($consignment->products as $item)
                // Validation for 'missingDamageQty'
                checkoutRules[`missingDamageQty[{{ $item->product_id }}][]`] = {
                    required: true,
                    min: 0,
                    max: {{ $item->quantity }},
                };

                checkoutMessages[`missingDamageQty[{{ $item->product_id }}][]`] = {
                    required: "This Required",
                    min: "Minimum 0",
                    max: `Maximum {{ $item->quantity }}`,
                };

                // Validation for 'actualQty'
                checkoutRules[`actualQty[{{ $item->product_id }}][]`] = {
                    required: true,
                    min: 0,
                };

                checkoutMessages[`actualQty[{{ $item->product_id }}][]`] = {
                    required: "This Required",
                    min: "Minimum 0",
                };

                // description if missing or damage
                checkoutRules[`description[{{ $item->product_id }}][]`] = {
                    required: function(element) {
                        return $("input[name='missingDamageQty[{{ $item->product_id }}][]']").val() > 0;
                    },
                };

                checkoutMessages[`description[{{ $item->product_id }}][]`] = {
                    required: "This Required",
                };
            @endforeach

            // Initialize validation for the checkout-form
            initializeValidation("#checkout-form", checkoutRules, checkoutMessages);


            // Initialize validation for the checkout-form
            initializeValidation("#checkout-form", checkoutRules, checkoutMessages);


            // dropify
            $('#challanDocument').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Change Document',
                }
            });

            if ($("#consignments").length) {
                $("#consignments").select2({
                    dropdownParent: $("#add_challan"),
                });
            }
            if ($('#challanDate').length) {
                const today = new Date();
                const serviceDateInput = flatpickr("#challanDate", {
                    wrap: true,
                    dateFormat: "d M Y",
                    // minDate: today,
                });
            }
        });

        function viewChallan(url) {
            console.log(url);
            getData(url, function(data) {
                console.log(data);
                let modelHeading = document.getElementById('headingDetailsModel');
                let modalContent = document.getElementById('DetailsContainer');

                modelHeading.innerHTML = 'Challan Details';
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><strong>Consignement:</strong></li>
                                <li><strong>Upload By:</strong></li>
                                <li><strong>Date:</strong></li>
                                <li><strong>Matrial Value:</strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li>{{ env('PrefixCon') }}${data.challan.consignement_id}</li>
                                <li>${data.challan.user.name}</li>
                                <li>${data.challan.date}</li>
                                <li>${data.challan.matrial_value ?? '-'}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                ${data.challan.document_path ? `<div class="text-center"><button class="btn btn-primary mt-3" onclick="showDocument('${data.challan.document_path}')">View Document</button></div>` : ''}
                `;
                // Show the modal
                $('#viewDetailsModel').modal('show');
            });
        }

        function viewBill(url) {
            getData(url, function(data) {
                console.log(data);
                let modelHeading = document.getElementById('headingDetailsModel');
                let modalContent = document.getElementById('DetailsContainer');

                modelHeading.innerHTML = 'Bill Details';
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><strong>Consignement:</strong></li>
                                <li><strong>Upload By:</strong></li>
                                <li><strong>Bill Number:</strong></li>
                                <li><strong>Date:</strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li>{{ env('PrefixCon') }}${data.bill.consignement_id}</li>
                                <li>${data.bill.user.name}</li>
                                <li>${data.bill.bill_number}</li>
                                <li>${data.bill.date}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                ${data.bill.document_path ? `<div class="text-center"><button class="btn btn-primary mt-3" onclick="showDocument('${data.bill.document_path}')">View Document</button></div>` : ''}
                `;
                // Show the modal
                $('#viewDetailsModel').modal('show');
            });
        }

        function viewExpense(url) {
            getData(url, function(data) {
                console.log(data);
                let modelHeading = document.getElementById('headingDetailsModel');
                let modalContent = document.getElementById('DetailsContainer');

                modelHeading.innerHTML = 'Expense Details';
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><strong>Expense Type:</strong></li>
                                <li><strong>Expense Added By:</strong></li>
                                <li><strong>Date:</strong></li>
                                <li><strong>Amount:</strong></li>
                                <li><strong>Payment Mode:</strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li>${data.expense.expense.name}</li>
                                <li>${data.expense.user ? data.expense.user.name :" {{ auth()->user()->name }}"}</li>
                                <li>${data.expense.date}</li>
                                <li>${data.expense.amount}</li>
                                <li>${data.expense.payment_mode}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                ${data.expense.document_path ? `<div class="text-center"><button class="btn btn-primary mt-3" onclick="showDocument('${data.expense.document_path}')">View Document</button></div>` : ''}
                `;
                // Show the modal
                $('#viewDetailsModel').modal('show');
            });
        }

        function updateActualQty(input) {
            // Find the corresponding actualQty input in the same row
            var actualInput = input.closest('tr').querySelector('.actual-input');

            // Get the original quantity from the data-quantity attribute
            var originalQuantity = parseInt(actualInput.getAttribute('data-quantity'));

            // Subtract the missingDamageQty value from the original quantity
            var missingQuantity = parseInt(input.value) || 0;

            // Set missingQuantity to 0 if it's negative
            missingQuantity = Math.max(0, missingQuantity);
            var newActualQuantity = originalQuantity - missingQuantity;

            // Update the actualQty input with the new value
            actualInput.value = newActualQuantity < 0 ? 0 : newActualQuantity;
        }
    </script>
@endsection
