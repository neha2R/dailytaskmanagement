@extends('layouts.app')
@section('content')
    <div class="container" id="add_jobs">
        <h5 class="header mt-1 mb-2">
            <i class="mdi mdi-folder-plus-outline me-2"></i> Add New Job
        </h5>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('project-management.storeJob') }}" method="POST" id="add_job_form">
                    @csrf
                    <h6><i class="fas fa-briefcase"></i> Job Details</h6>
                    <section class="p-0">
                        <div class="card mb-2 p-0">
                            <div class="card-body row p-2">
                                <!-- Job Name -->
                                <div class="col-md-6 mb-2">
                                    <input type="hidden" name="project_id" value="{{$project->id}}">
                                    <label for="job_id" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-file-document-outline me-2"></i> Job Name
                                    </label>
                                    <select class="js-example-basic-single form-select" id="job_id" name="job_id"
                                        data-width="100%" onchange="getInputs(this)">
                                        <option selected disabled>Select Job</option>
                                        @foreach ($activeJobs as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach

                                    </select>
                                </div>

                                <!-- Division -->
                                <div class="col-md-6 mb-2">
                                    <label for="division" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-file-document-outline me-2"></i> Division
                                    </label>
                                    <select class="js-example-basic-single form-select" id="division_id" name="division_id"
                                        data-width="100%" onchange="getSubDivisions(this)">
                                        <option selected disabled>Select Division</option>
                                        @foreach ($activeDivisions as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sub Division -->
                                <div class="col-md-6 mb-2">
                                    <label for="sub_division" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-file-document-outline me-2"></i> Sub Division
                                    </label>
                                    <select class="js-example-basic-single form-select" id="sub_division_id"
                                        name="sub_division_id" data-width="100%" onchange="getSites(this)">
                                        <option selected disabled>Select Sub Division</option>
                                    </select>
                                </div>

                                <!-- Site -->
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="site" class="form-label mb-2 ms-1">
                                            <i class="mdi mdi-domain me-2"></i> Site
                                        </label>
                                        <a href="{{route('admin.sites')}}" class="fw-bold">Add Site</a>
                                    </div>
                                    <select class="js-example-basic-single form-select" id="site_id" name="site_id"
                                        data-width="100%" onchange="getSiteUser(this)">
                                        <option selected disabled>Select Site</option>
                                    </select>
                                </div>

                                <!-- Start Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-calendar me-2"></i> Start Date
                                    </label>
                                    <div class="input-group flatpickr">
                                        <input type="text" class="form-control" placeholder="Select date"
                                            readonly="readonly" id="start_date" name="start_date">
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-calendar me-2"></i> End Date
                                    </label>
                                    <div class="input-group flatpickr">
                                        <input type="text" class="form-control" placeholder="Select date"
                                            readonly="readonly" id="end_date" name="end_date">
                                    </div>
                                </div>

                                <!-- Division Head -->
                                <div class="col-md-6 mb-2">
                                    <label for="division_head" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-file-document-outline me-2"></i> Division Head
                                    </label>
                                    <select class="js-example-basic-single form-select" id="division_head_id"
                                        name="division_head_id" data-width="100%">
                                        <option selected disabled>Select Division Head</option>
                                        {{-- @foreach ($activeVendors as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>

                                <!-- Assign To -->
                                <div class="col-md-6 mb-2">
                                    <label for="assign_to" class="form-label mb-2 ms-1">
                                        <i class="mdi mdi-file-document-outline me-2"></i> Site Head
                                    </label>
                                    <select class="js-example-basic-single form-select" id="site_head_id"
                                        name="site_head_id" data-width="100%">
                                        <option selected disabled>Select Site Head</option>
                                        {{-- @foreach ($activeVendors as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>

                                <h6 class="mt-2 mb-2 ms-0">Inputs Details</h6>

                                <div class="row" id="inputsContainer">

                                </div>
                            </div>
                        </div>
                    </section>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn-primary btn-sm btn">Add Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(() => {
            $('#add_job_form button[type="submit"]').prop('disabled', true);
            initFlatpickrWithMinDate('#add_jobs #start_date', '#add_jobs #end_date');
            initFlatpickrWithMaxDate('#add_jobs #end_date', '#add_jobs #start_date');
            initializeSelect2('#add_jobs #job_id', '#add_jobs');
            initializeSelect2('#add_jobs #division_id', '#add_jobs');
            initializeSelect2('#add_jobs #sub_division_id', '#add_jobs');
            initializeSelect2('#add_jobs #site_id', '#add_jobs');
            initializeSelect2('#add_jobs #division_head_id', '#add_jobs');
            initializeSelect2('#add_jobs #site_head_id', '#add_jobs');
        });

        function getInputs(element) {
            const id = element.value;
            const url = `{{ route('project-management.getInputs', ['id' => ':id']) }}`.replace(':id', id);

            getData(url, (data) => {
                validateForm(data.inputs);
                enableSubmitButton();
            });
        }

        function getSubDivisions(element) {
            const id = element.value;
            const url = `{{ route('project-management.getSubDivisions', ['id' => ':id']) }}`.replace(':id', id);

            getData(url, (data) => {
                updateDropdown('sub_division_id', data.subDivisions, 'Select Sub Division');
                updateSingleDropdown('division_head_id', data.user.user, 'Select Division Head');
                updateDropdown('site_id', [], 'Select Site');
                updateSingleDropdown('site_head_id', null, 'Select Site head');
            });
        }

        function getSites(element) {
            const id = element.value;
            const url = `{{ route('project-management.getSites', ['id' => ':id']) }}`.replace(':id', id);

            getData(url, (data) => {
                updateDropdown('site_id', data.sites, 'Select Site');
                updateSingleDropdown('site_head_id', null, 'Select Site head');
            });
        }

        function getSiteUser(element) {
            const id = element.value;
            const url = `{{ route('project-management.getSiteUser', ['id' => ':id']) }}`.replace(':id', id);
            getData(url, (data) => {
                console.log(data);
                updateSingleDropdown('site_head_id', data.user.user, 'Select Site head');
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

        function updateSingleDropdown(elementId, options, defaultOption = 'Select options') {
            const selectElement = $(`#${elementId}`);
            clearAndAddDefaultOption(selectElement, defaultOption);

            if (options) {
                selectElement.append($('<option>', {
                    value: options.id,
                    text: options.name
                }));
            }
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

        function appendInputs(inputs) {
            const inputsContainer = $('#inputsContainer');
            inputsContainer.empty();

            inputs.forEach((item) => {
                const inputId = `input_${item.id}`;
                const inputUomId = `inputUom_${item.id}`;

                const inputHtml = `
                    <div class="col-md-6 mb-2">
                        <label for="${inputId}" class="form-label mb-2 ms-1">${item.name}</label>
                        <div class="d-flex">
                            <div class="me-2 w-50">
                                <input type="number" min="0" class="form-control" id="${inputId}" name="inputsValue[${item.id}]" value="" placeholder="Enter ${item.name}">
                            </div>
                            <div class="w-50">
                                <select class="js-example-basic-single form-select" id="${inputUomId}" name="inputsUom[${item.id}]" data-width="100%">
                                    <option selected disabled>Select UOM</option>
                                    @foreach (getUOM() as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                `;

                inputsContainer.append(inputHtml);
                initializeSelect2(`#${inputUomId}`, '#add_jobs');
            });
        }

        function validateForm(inputs) {
            const rules = {
                job_id: 'required',
                division_id: 'required',
                sub_division_id: 'required',
                site_id: 'required',
                start_date: 'required',
                end_date: 'required',
                division_head_id: 'required',
                site_head_id: 'required',
            };

            const messages = {
                job_id: {
                    required: 'Please select a job '
                },
                division_id: {
                    required: 'Please select a division.'
                },
                sub_division_id: {
                    required: 'Please select a sub-division.'
                },
                site_id: {
                    required: 'Please select a site.'
                },
                start_date: {
                    required: 'Please enter the start date.'
                },
                end_date: {
                    required: 'Please enter the end date.'
                },
                division_head_id: {
                    required: 'Please select a division head.'
                },
                site_head_id: {
                    required: 'Please select a site head.'
                },
            };

            inputs.forEach((item) => {
                const inputId = `input_${item.id}`;
                const inputUomId = `inputUom_${item.id}`;

                rules[`inputsValue[${item.id}]`] = 'required';
                messages[`inputsValue[${item.id}]`] = {
                    required: `Please enter ${item.name}.`
                };

                rules[`inputsUom[${item.id}]`] = 'required';
                messages[`inputsUom[${item.id}]`] = {
                    required: `Please select a UOM for ${item.name}.`
                };
            });

            appendInputs(inputs);
            initializeValidation('#add_job_form', rules, messages);
        }

        function enableSubmitButton() {
            $('#add_job_form button[type="submit"]').prop('disabled', false);
        }
    </script>
@endsection
