@extends('layouts.app')
@section('content')
    <style>
        .v_heading li {
            font-size: 18px;
            font-weight: 500;
        }

        .v_details li {
            font-size: 22px;
            font-weight: 700;
        }

        .partsname {
            width: 340px;
            margin-right: -11px;
        }

        .filter {
            width: 18px;
            height: 18px;
        }

        .border-left {
            border-left: 1px solid #000 !important;
        }

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

        .list-unstyled li {
            padding-bottom: 5px;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Vehicle Details
            </h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="row">
                <div class="mt-3">
                    <ul class="d-flex list-unstyled text-center v_heading">
                        <li class="text-capitalize text-secondary col">Vechile No</li>
                        <li class="text-capitalize text-secondary col">Vehicle Type</li>
                        {{-- <li class="text-capitalize text-secondary col">Supervisor</li> --}}
                        <li class="text-capitalize text-secondary col">Driver</li>
                    </ul>
                    <ul class="d-flex list-unstyled text-center v_details">
                        <li class="text-capitalize  text-black-50 col">{{ $vehicle->vehicle_number }}</li>
                        <li class="text-capitalize  text-black-50 col">{{ $vehicle->vehicle_body_type }}</li>
                        <li class="text-capitalize  text-black-50 col">
                            {{ $vehicle->user_vehicle ? $vehicle->user_vehicle->user->name : '-' }}</li>
                        {{-- <li class="text-capitalize  text-black-50 col">Test driver</li> --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-1">
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-line nav-justified" id="lineTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link " id="trip-line-tab" data-bs-toggle="tab" data-bs-target="#trip" role="tab"
                        aria-controls="home" aria-selected="true">Trips</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="driver-line-tab" data-bs-toggle="tab" data-bs-target="#driver" role="tab"
                        aria-controls="profile" aria-selected="false">Driver</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="service-line-tab" data-bs-toggle="tab" data-bs-target="#service" role="tab"
                        aria-controls="service" aria-selected="false">Service</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('vehicle.vehicleDocuments',$vehicle->id)}}" id="expenses-line-tab">Documents</a>
                </li>
            </ul>

        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // Define validation rules and messages
            const rules = {
                serviceDate: {
                    required: true,
                },
                odometerReading: {
                    required: true,
                    number: true,
                    min: parseInt(
                        '{{ $vehicle->services->last() ? $vehicle->services->last()->odometerReading : 0 }}'
                    ) + 1,
                },
                serviceType: {
                    required: true,
                },
                serviceAmount: {
                    required: function(element) {
                        console.log($('#serviceType').val());
                        return $('#serviceType').val() !== null;
                    },
                    number: true,
                },
                oilChange: {
                    required: true,
                },
                oilChangeAmount: {
                    required: {
                        depends: function(element) {
                            return $('#oilChange').val() === '1';
                        }
                    },
                    number: true,
                },

                sparePartsChange: {
                    required: true,
                },
                // totalAmount: {
                //     required: true,
                //     number: true,
                // },
                // document: {
                //     required: true,
                // },
            };
            for (let index = 0; index < 9; index++) {
                rules[`sparePartsName[${index}]`] = {
                    required: {
                        depends: function(element) {
                            return $('#sparePartsChange').val() === '1';
                        }
                    }
                };
                rules[`sparePartsAmount[${index}]`] = {
                    required: {
                        depends: function(element) {
                            return $('#sparePartsChange').val() === '1';
                        }
                    },
                    number: true
                };
            }

            const messages = {
                serviceDate: {
                    required: "Please select a service date.",
                },
                odometerReading: {
                    required: "Please enter the odometer reading.",
                    number: "Please enter a valid number.",
                },
                kmRun: {
                    required: "Please enter the kilometers run.",
                    number: "Please enter a valid number.",
                },
                timeGap: {
                    required: "Please enter the time gap in months.",
                    number: "Please enter a valid number.",
                },
                serviceType: {
                    required: "Please select a service type.",
                },
                serviceAmount: {
                    required: "Please enter the service amount.",
                    number: "Please enter a valid number.",
                },
                oilChangeAmount: {
                    required: "Please enter the oil change amount.",
                    number: "Please enter a valid number.",
                },
                sparePartsName: {
                    required: "Please enter the spare parts name.",
                },
                sparePartsAmount: {
                    required: "Please enter the spare parts amount.",
                    number: "Please enter a valid number.",
                },
                totalAmount: {
                    required: "Please enter the total amount.",
                    number: "Please enter a valid number.",
                },
                document: {
                    required: "Please upload a document.",
                },
            };
            // Initialize validation for the form
            initializeValidation("#service-sechdule-form", rules, messages);
        });
    </script>
@endsection
