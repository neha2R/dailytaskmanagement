@extends(Auth::check() ? (Auth::user()->role->name === 'Warehouse Head' ? 'warehouse_head.layouts.app' : (Auth::user()->role->name === 'Depot Head' ? 'depot_head.layouts.app' : 'layouts.app')) : 'layouts.app')
<style>
    .list-unstyled li {
        padding-bottom: 10px;
    }
</style>
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Fuel Management
            </h4>
        </div>
    </div>
    <div class="card">

        <div class="card-body">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary btn-xs mb-3" data-bs-toggle="modal" data-bs-target="#add_fuel"
                    data-bs-whatever="@getbootstrap"> <i class="mdi mdi-plus"></i>Add
                    Fuel</button>
            </div>
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>Sr no</th>
                            <th>Vehicle </th>
                            <th>Driver</th>
                            <th>Fuel Type</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Odometer R.</th>
                            <th>Date</th>
                            <th>Fuel Station</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fuelsData as $key => $item)
                            <tr>
                                <td class="center">{{ $key + 1 }}</td>
                                <td>{{ $item->vehicle->vehicle_number ?? '-' }}</td>
                                <td>{{ $item->driver ? $item->driver->name : '-' }}</td>
                                <td>{{ ucwords($item->vehicle->model->fule_type ?? '-') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->amount }}</td>
                                <td>{{ $item->odometerReading }}</td>
                                <td>{{ dateFormat($item->date, 'd M Y') }}</td>
                                <td>{{ $item->fule_station ?? '-' }}</td>
                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                            <a type="button"
                                                onclick="viewFuelDetails('{{ route('whHead.fule-management.show', $item->id) }}')"
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
    <div class="modal fade" id="fuelDetailsModel" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Fuel Details</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body" id="appendDetails">

                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> --}}
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_fuel" tabindex="-1" aria-labelledby="add_fuel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Fuel</h5>
                    <h5 id="att_date" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <form id="add-fuel-form" action="{{ route('whHead.fule-management.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row gx-5 mb-3">
                                <div class="col-12">
                                    <label for="vehicle_id" class="form-label">Vehicle Number</label>
                                    <select style="width: 100%;" name="vehicle_id" id="vehicle_id"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select Vehicle </option>
                                        @foreach ($assigned_vehicles as $item)
                                            <option value="{{ $item->vehicle->id }}">{{ $item->vehicle->vehicle_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="serviceDate" class="form-label">Date</label>
                                    <div class="input-group flatpickr" id="date">
                                        <input type="text" name="date"
                                            class="form-control placeholde-size flatpickr-input" placeholder="Select date"
                                            data-input="" readonly="readonly" id="dateInput">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="fule_quantity" class="form-label">Fuel Quantity</label>
                                    <input type="number" class="form-control" id="fule_quantity" name="fule_quantity"
                                        placeholder="Fuel Quantity">
                                </div>
                                <div class="col-12">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amount" name="amount"
                                        placeholder="Amount">
                                </div>
                                <div class="col-12">
                                    <label for="odometerReading" class="form-label">Odometer Reading</label>
                                    <input type="number" class="form-control" id="odometerReading"
                                        name="odometerReading" placeholder="Odometer Reading">
                                </div>
                                <div class="col-12">
                                    <label for="fule_station" class="form-label">Fuel Station (Optional)</label>
                                    <select style="width: 100%;" name="fule_station" id="fule_station"
                                        class="form-select js-example-basic-single">
                                        <option selected disabled>Select station </option>
                                        @foreach (getFuelStations() as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    {{-- <label for="sparePartsChange" class="form-label"></label> --}}
                                    <input type="file" id="document" name="document" data-height="100"
                                        data-show-remove="false">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveFuelDetails" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // dropify
            $('#document').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Change Document',
                }
            });

            // select 2 and flatepicker
            if ($("#vehicle_id").length) {
                $("#vehicle_id").select2({
                    dropdownParent: $("#add_fuel"),
                });
            }
            if ($("#fule_station").length) {
                $("#fule_station").select2({
                    dropdownParent: $("#add_fuel"),
                });
            }
            if ($('#date').length) {
                const serviceDateInput = flatpickr("#date", {
                    wrap: true,
                    dateFormat: "d M Y",
                });
            }
            // Define validation rules and messages
            const rules = {
                vehicle_id: {
                    required: true,
                },
                date: {
                    required: true,
                },
                fule_quantity: {
                    required: true,
                    number: true,
                },
                amount: {
                    required: true,
                    number: true,
                },
                odometerReading: {
                    required: true,
                    number: true,
                },
                // fule_station: {
                //     required: true,
                // },
                // document: {
                //     required: true,
                //     extension: "jpg|jpeg|png|pdf", // Adjust the file extensions as needed
                // },
            };

            const messages = {
                vehicle_id: {
                    required: "Please select a vehicle.",
                },
                date: {
                    required: "Please select a date.",
                },
                fule_quantity: {
                    required: "Please enter the fuel quantity.",
                    number: "Please enter a valid number.",
                },
                amount: {
                    required: "Please enter the amount.",
                    number: "Please enter a valid number.",
                },
                odometerReading: {
                    required: "Please enter the odometer reading.",
                    number: "Please enter a valid number.",
                },
                // fule_station: {
                //     required: "Please select a fuel station.",
                // },
                // document: {
                //     required: "Please upload a document.",
                //     extension: "Please upload a valid file (jpg, jpeg, png, pdf).",
                // },
            };
            // Initialize validation for the form
            initializeValidation("#add-fuel-form", rules, messages);

        });
        $("#saveFuelDetails").on("click", function() {
            $("#add-fuel-form").submit();
        });

        function viewFuelDetails(url) {
            getData(url, function(data) {
                if (data.status == 200) {
                    data = data.data;

                    // Assuming data is the JSON object you provided
                    var modalBody = document.getElementById('appendDetails');

                    // Build the HTML content using all fields in the data
                    var content = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><strong>Vehicle Number:</strong></li>
                                            <li><strong>Driver Name:</strong></li>
                                            <li><strong>Date:</strong></li>
                                            <li><strong>Quantity:</strong></li>
                                            <li><strong>Amount:</strong></li>
                                            <li><strong>Odometer Reading:</strong></li>
                                            <li><strong>Fuel Station:</strong></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li>${data.vehicle.vehicle_number}</li>
                                            <li>${data.driver.name}</li>
                                            <li>${data.date}</li>
                                            <li>${data.quantity}</li>
                                            <li>${data.amount}</li>
                                            <li>${data.odometerReading}</li>
                                            <li>${data.fule_station}</li>
                                        </ul>
                                    </div>
                                </div>
                                ${data.document ? `<div class="text-center"><button class="btn btn-primary mt-3" onclick="showDocument('${data.document}')">View Document</button></div>` : ''}
                                `;

                    // Append the content to the modal body
                    modalBody.innerHTML = content;

                    // Open the modal
                    $('#fuelDetailsModel').modal('show');
                }
            });
        }

        function showDocument(documentUrl) {
            // You can open the document in a new tab or use any other method to display it
            window.open(`{{ asset('storage') }}` + '/' + documentUrl, '_blank');
        }
    </script>
@endsection
