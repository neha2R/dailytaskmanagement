@extends('layouts.app')
@section('content')
    <style>
        .v_heading li {
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
    </style>
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center px-2 info-heading">
                    <h5 class="">Trip Details</h5>
                </div>
                <div class="card-body p-0 pt-2">
                    <div class="row">
                        <div class="">
                            <ul class="d-flex list-unstyled text-center v_heading">
                                <li class="text-capitalize  col">Trip No</li>
                                <li class="text-capitalize  col">Vehicle Type</li>
                                <li class="text-capitalize  col">Vehicle No</li>
                                <li class="text-capitalize  col">Driver Name</li>
                            </ul>
                            <ul class="d-flex list-unstyled text-center v_details">
                                <li class="text-capitalize col">{{ env('PrefixTrip') . $trip->id }}</li>
                                <li class="text-capitalize col">
                                    {{ $trip->vehicle ? $trip->vehicle->vehicle_body_type : -'' }}</li>
                                <li class="text-capitalize col">
                                    {{ $trip->vehicle ? $trip->vehicle->vehicle_number : -'' }}</li>
                                <li class="text-capitalize col">
                                    {{ $trip->user ? $trip->user->name : -'' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion mt-3" id="accordionExample">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center px-2 info-heading">
                        <h5 class="">Trip Order Detail</h5>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <div class="d-flex justify-content-between">
                                <button class="accordion-button fw-bold " type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Origin Location
                                    @if ($trip->status == 'pending')
                                        <p class="badge bg-warning xs position-absolute end-0 me-5">Pending</p>
                                    @elseif ($trip->status == 'ongoing')
                                        <p class="badge bg-info xs position-absolute end-0 me-5">Ongoing</p>
                                    @elseif ($trip->status == 'completed')
                                        <p class="badge bg-success xs position-absolute end-0 me-5">Completed</p>
                                    @endif
                                </button>
                            </div>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="d-flex">
                                    <h6 class="p-1">{{ $trip->origin_source()->name }} </h6>
                                    <p class="ms-2">{{ dateFormat($trip->start_date, 'd-m-Y') }}</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="">

                                        <p><i class="mdi mdi-google-maps"></i>{{ $trip->origin_source()->address }}</p>
                                        <p>{{ $trip->origin_source()->city }}</p>
                                    </div>
                                    <div class="">
                                        <a onclick="viewAllConsignements('{{ route('trips.getAllCons', $trip->id) }}')"
                                            type="button" class="fw-bold">View All Consignments</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @forelse ($deliveryLocation as $index => $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <div class="d-flex justify-content-between">
                                    <button class="accordion-button fw-bold collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                        aria-expanded="true" aria-controls="collapse{{ $index }}">
                                        Location {{ $index + 1 }}
                                        @if ($item->status == 'pending')
                                            <p class="badge bg-warning xs position-absolute end-0 me-5">Pending</p>
                                        @elseif ($item->status == 'trip_assigned')
                                            <p class="badge bg-info xs position-absolute end-0 me-5">Trip Assigned</p>
                                        @elseif ($item->status == 'delivered')
                                            <p class="badge bg-success xs position-absolute end-0 me-5">Deliverd</p>
                                        @endif
                                    </button>
                                </div>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse"
                                aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="d-flex">
                                        <h6 class="p-1">{{ $item->location_name }}</h6>
                                        <p class="ms-2"></p>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <p><i class="mdi mdi-google-maps"></i>{{ $item->location_address }}</p>
                                            <p>{{ $item->location_city }}</p>
                                        </div>
                                        <div>
                                            <a type="button" class="fw-bold"
                                                onclick="viewAllConsignementsByLoc('{{ route('trips.getCons', [$trip->id, $item->location_id]) }}')">
                                                View Consignements</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Handle empty case if needed -->
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0 ">
                    <h5>Trip Expenses</h5>
                    <button class="btn btn-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#add_expense"
                        data-bs-whatever="@getbootstrap">
                        <i class="mdi mdi-plus fs-6"></i> Add</button>
                </div>
                <div class="card-body scrollable-tbody pt-2 pb-2">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Expense</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trip->expenses->sortByDesc('id') as $item)
                                    <tr>
                                        <td><a onclick="viewExpense(('{{ route('trips.getExpense', $item->id) }}'))"
                                                type="button"
                                                class="fw-bold text-secondary">{{ $item->expense->name }}</a></td>
                                        <td class="text-end"><a type="button"
                                                class="fw-bold text-dark">{{ $item->amount }}</a></td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th class="text-end">{{ number_format($trip->expenses->sum('amount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
                    <h5>Delivery Challan</h5>
                    <button class="btn btn-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#add_challan"
                        data-bs-whatever="@getbootstrap"><i class="mdi mdi-plus fs-6 pe-1"></i>Add</button>
                </div>
                <div class="card-body scrollable-tbody pt-2 pb-2">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Consignement</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($challans->sortByDesc('id') as $item)
                                    <tr>
                                        <td>{{ env('PrefixCon') . $item->consignement_id }}</td>
                                        <td class="text-end"><button
                                                onclick="viewChallan('{{ route('trips.getChallan', $item->id) }}')"
                                                class="btn btn-xs btn-success">view</button></td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0">
                    <h5>PODs</h5>
                    {{-- <button class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#add_pods"
                    data-bs-whatever="@getbootstrap"><i class="mdi mdi-plus fs-6 pe-1"></i>Add</button> --}}
                </div>
                <div class="card-body scrollable-tbody pt-2 pb-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Consignement</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pods->sortByDesc('id') as $item)
                                <tr>
                                    <td>{{ env('PrefixCon') . $item->consignement_id }}</td>
                                    <td class="text-end"><button class="btn btn-xs btn-success">view</button></td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center px-2 py-2 m-0 ">
                    <h5>E-way bill</h5>
                    <button class="btn btn-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#add_bill"
                        data-bs-whatever="@getbootstrap"><i class="mdi mdi-plus fs-6"></i>Add</button>
                </div>
                <div class="card-body scrollable-tbody pt-2 pb-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bill No</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bills->sortByDesc('id') as $item)
                                <tr>
                                    <td>{{ $item->bill_number }}</td>
                                    <td class="text-end"><button
                                            onclick="viewBill('{{ route('trips.getBill', $item->id) }}')"
                                            class="btn btn-xs btn-success">view</button></td>
                                </tr>
                            @empty
                            @endforelse
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
    <div class="modal fade" id="add_expense" tabindex="-1" aria-labelledby="add_expense" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Expense</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="add-expense-form" action="{{ route('trips.storeExpenseDetails') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row gx-5 mb-3">
                                <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                                <div class="col-12">
                                    <label for="expenseDate" class="form-label">Date</label>
                                    <div class="input-group flatpickr" id="expenseDate">
                                        <input type="text" name="expenseDate"
                                            class="form-control placeholde-size flatpickr-input" placeholder="Select date"
                                            data-input="" readonly="readonly" id="expenseDateInput">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="expenseType" class="form-label">Expense Type</label>
                                    <select style="width: 100%;" name="expenseType" id="expenseType"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select Expense Type</option>
                                        @forelse (getactiveExpenses() as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="expenseAmount" class="form-label">Amount</label>
                                    <input class="form-control" id="expenseAmount" name="expenseAmount"
                                        placeholder="Amount" type="number" min="0">
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="expensePayMode" class="form-label">Payment Mode</label>
                                    <select class="form-select " id="expensePayMode" name="expensePayMode"
                                        data-width="100%">
                                        <option value="" selected>Select Mode</option>
                                        <option value="online">Online</option>
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    {{-- <label for="sparePartsChange" class="form-label">Spare Parts Change</label> --}}
                                    <input class="dropify" type="file" id="expenseDocument" name="expenseDocument"
                                        data-height="100" data-show-remove="false">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveExpenseDetails" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_challan" tabindex="-1" aria-labelledby="add_challan" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Challan</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="add-challan-form" action="{{ route('trips.storeChallanDetails') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row gx-5 mb-3">
                                <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                                <div class="col-12">
                                    <label for="challanDate" class="form-label">Date</label>
                                    <div class="input-group flatpickr" id="challanDate">
                                        <input type="text" name="challanDate"
                                            class="form-control placeholde-size flatpickr-input" placeholder="Select date"
                                            data-input="" readonly="readonly" id="challanDateInput">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="consignment" class="form-label">Consignment No.</label>
                                    <select style="width: 100%;" name="consignments" id="consignments"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select Consignment</option>
                                        @forelse ($challanConsignements as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['con_number'] }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="materialValue" class="form-label">Material Value</label>
                                    <input class="form-control" id="materialValue" name="materialValue"
                                        placeholder="Material Value" type="number" min="0">
                                </div>
                                <div class="col-12">
                                    {{-- <label for="sparePartsChange" class="form-label">Spare Parts Change</label> --}}
                                    <input class="dropify" type="file" id="challanDocument" name="challanDocument"
                                        data-height="100" data-show-remove="false">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveChallanDetails" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_bill" tabindex="-1" aria-labelledby="add_bill" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add E-way Bill</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="add-bill-form" action="{{ route('trips.storeBillDetails') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row gx-5 mb-3">
                                <input type="hidden" name="trip_id" value="{{ $trip->id }}">
                                <div class="col-12">
                                    <label for="challanDate" class="form-label">Date</label>
                                    <div class="input-group flatpickr" id="billDate">
                                        <input type="text" name="billDate"
                                            class="form-control placeholde-size flatpickr-input" placeholder="Select date"
                                            data-input="" readonly="readonly" id="billDateInput">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="bill-consignments" class="form-label">Consignments</label>
                                    <select style="width: 100%;" name="consignments[]" id="bill-consignments"
                                        class="form-select js-example-basic-multiple" multiple="multiple">
                                        {{-- <option selected disabled>Select Consignment</option> --}}
                                        @forelse ($billsConsignements as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['con_number'] }}</option>

                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="billNo" class="form-label"> E-way Bill No.</label>
                                    <input class="form-control" id="billNo" name="bill_number"
                                        placeholder=" E-way Bill Number" type="text">
                                </div>
                                <div class="col-12">
                                    {{-- <input class="dropify" data-show-errors="true"data-errors-position="outside"
                                        type="file" class="dropify" id="billDocument" name="billDocument"
                                        data-height="100" data-show-remove="false"> --}}

                                        <input class="dropify" type="file" id="billDocument" name="billDocument"
                                        data-height="100" data-show-remove="false">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveBillDetails" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
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
            // Define validation rules and messages for the add_expense form
            const expenseRules = {
                expenseDate: {
                    required: true,
                },
                expenseType: {
                    required: true,
                },
                expenseAmount: {
                    required: true,
                    min: 1,
                    max: 100000,
                    number: true,
                },
                expensePayMode: {
                    required: true,
                },
                // expenseDocument: {
                //     required: true,
                // },
            };

            const expenseMessages = {
                expenseDate: {
                    required: "Please select a date.",
                },
                expenseType: {
                    required: "Please select an expense type.",
                },
                expenseAmount: {
                    required: "Please enter the amount.",
                    min: "Expense amount must be at least $1.",
                    max: "Expense amount must not exceed $10000.",
                    number: "Please enter a valid number.",
                },
                expensePayMode: {
                    required: "Please select a payment mode.",
                },
                expenseDocument: {
                    required: "Please upload a document.",
                },
            };

            // Initialize validation for the add_expense form
            initializeValidation("#add-expense-form", expenseRules, expenseMessages);

            // Define validation rules and messages for the add_challan form
            const challanRules = {
                challanDate: {
                    required: true,
                },
                consignments: {
                    required: true,
                },
                materialValue: {
                    // required: true,
                    number: true,
                    min: 1,
                    max: 1000000,
                },
                challanDocument: {
                    required: true,
                },
            };

            const challanMessages = {
                challanDate: {
                    required: "Please select a date.",
                },
                consignments: {
                    required: "Please select a consignment.",
                },
                materialValue: {
                    required: "Please enter the material value.",
                    number: "Please enter a valid number.",
                    min: "Expense amount must be at least $1.",
                    max: "Expense amount must not exceed $100000.",
                },
                challanDocument: {
                    required: "Please upload a document.",
                },
            };

            // Initialize validation for the add_challan form
            initializeValidation("#add-challan-form", challanRules, challanMessages);

            // Define validation rules and messages for the add_challan form
            const ewayBillRules = {
                date: {
                    required: true,
                },
                bill_number: {
                    required: true,
                    minlength: 1,
                    maxlength: 20,
                },
                "consignments[]": {
                    required: true,
                },

                billDocument: {
                    required: true,
                }
            };

            const ewayBillMessages = {
                date: {
                    required: "Please select a date.",
                },
                bill_number: {
                    required: "Please enter bill number.",
                    minlength: "Bill number should be at least 1 character.",
                    maxlength: "Bill number should not exceed 20 characters."
                },
                "consignments[]": {
                    required: "Please select consignments",
                },
                billDocument: {
                    required: "Please upload a document.",

                },
            };

            // Initialize validation for the add_challan form
            initializeValidation("#add-bill-form", ewayBillRules, ewayBillMessages);

            // dropify
            $('#challanDocument').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Change Document',
                }
            });
            $('#expenseDocument').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Change Document',
                }
            });
            $('#billDocument').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Change Document',
                }
            });

            // select 2 and flatepicker
            if ($("#expenseType").length) {
                $("#expenseType").select2({
                    dropdownParent: $("#add_expense"),
                });
            }
            if ($('#expenseDate').length) {
                const today = new Date();
                const serviceDateInput = flatpickr("#expenseDate", {
                    wrap: true,
                    dateFormat: "d M Y",
                    // minDate: today,
                });
            }

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
            if ($('#billDate').length) {
                const today = new Date();
                const serviceDateInput = flatpickr("#billDate", {
                    wrap: true,
                    dateFormat: "d M Y",
                    // minDate: today,
                });
            }
            if ($("#bill-consignments").length) {
                $("#bill-consignments").select2({
                    dropdownParent: $("#add_bill"),
                });
            }
        });
        $("#saveChallanDetails").on("click", function() {
            $("#add-challan-form").submit();
        });
        $("#saveExpenseDetails").on("click", function() {
            $("#add-expense-form").submit();
        });
        $("#saveBillDetails").on("click", function() {
            $("#add-bill-form").submit();
        });

        function viewAllConsignements(url) {
            getData(url, function(data) {
                console.log(data);
                let modelHeading = document.getElementById('headingDetailsModel');
                let modalContent = document.getElementById('DetailsContainer');

                modelHeading.innerHTML = 'Consignements';
                modalContent.innerHTML = `
                    <table class="table table-hover">
                        <thead class="text-center">
                            <tr>
                                <th>Consignment</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Delivery Date</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            ${printConsignments(data.consignments)}
                        </tbody>
                    </table>
                `;
                // Show the modal
                $('#viewDetailsModel').modal('show');
            });
        }

        function viewAllConsignementsByLoc(url) {
            getData(url, function(data) {
                console.log(data);
                let modelHeading = document.getElementById('headingDetailsModel');
                let modalContent = document.getElementById('DetailsContainer');

                modelHeading.innerHTML = 'Consignements';
                modalContent.innerHTML = `
                    <table class="table table-hover">
                        <thead class="text-center">
                            <tr>
                                <th>Consignment</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Delivery Date</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            ${printConsignments(data.consignments)}
                        </tbody>
                    </table>
                `;
                // Show the modal
                $('#viewDetailsModel').modal('show');
            });
        }

        function printConsignments(consignments) {
            return consignments.map((consignment) => {
                return `
            <tr>
                <td>${consignment.con_number}</td>
                <td>${consignment.origin_location}</td>
                <td>${consignment.destination_location}</td>
                <td>${consignment.delivery_date}</td>
                <td>${consignment.products_count}</td>
            </tr>
        `;
            }).join('');
        }

        function viewChallan(url) {
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
    </script>
@endsection
