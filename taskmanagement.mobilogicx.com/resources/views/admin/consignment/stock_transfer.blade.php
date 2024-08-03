@extends('layouts.app')
@section('content')
    <style>
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
    </style>
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mt-3">
        <div>
            <h4 class="mb-3 mb-md-0">Stock Transfer</h4>
        </div>
        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal"
            data-bs-target="#add_cons" data-bs-whatever="@getbootstrap"><i class="mdi mdi-plus-circle pe-1"></i>Add
            Consignement</button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTableExample1" class="table" data-order=''>
                    <thead>
                        <tr>
                            <th>Con.No</th>
                            <th>Origin Location</th>
                            <th>Destination Location</th>
                            <th>Delivery Type</th>
                            <th>Total Item</th>
                            <th>Delvery By date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr id="dt_tr{{ $item->id }}">
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->origin_warehouse->name ?? '' }}</td>
                                <td>{{ $item->warehouse->name ?? $item->depo->name }}</td>
                                <td>{{ ucwords($item->type) }}</td>
                                <td>{{ $item->products_count }}</td>
                                <td>{{ dateformat($item->date, 'd M y') }}</td>
                                <td>
                                    @switch($item->status)
                                        @case('pending')
                                            <span class="badge bg-warning xs">Pending</span>
                                        @break

                                        @case('trip_assigned')
                                            <span class="badge bg-primary xs">Trip Assigned</span>
                                        @break

                                        @case('deliverd')
                                            <span class="badge bg-success xs">Deliverd</span>
                                        @break

                                        @default
                                    @endswitch
                                </td>
                                <td>
                                    <div class="dropdown mb-2">
                                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-lg text-muted pb-3px" data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
                                            <a onclick="getData({{ $item->id }})"
                                                class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                    data-feather="eye" class="icon-sm me-2"></i> <span class="">View
                                                    Consignment</span></a>
                                            @if ($item->status == 'pending')
                                                <a onclick="editData({{ $item->id }})"
                                                    class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                        data-feather="edit" class="icon-sm me-2"></i>
                                                    <span class="">Edit
                                                        Consignment</span></a>
                                                <a onclick="delete_con({{ $item->id }})"
                                                    class="dropdown-item d-flex align-items-center" href="javascript:;"><i
                                                        data-feather="trash" class="icon-sm me-2"></i> <span
                                                        class="">Delete
                                                        Consignment</span></a>
                                            @endif
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
    <div class="modal fade" id="add_cons" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Add Consignment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <form id="create_consignment" method="post" action="{{ route('admin.consignements.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="warehouse_from" class="form-label mb-2 ms-1">Origin Location</label>
                                <select class="js-example-basic-single form-select" id="warehouse_from"
                                    name="warehouse_from" data-width="100%">
                                    <option selected disabled>Select warehouse</option>
                                    @foreach (getWarehouses() as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="deliver_to" class="form-label mb-2 ms-1">Deliver To</label>
                                <select class="js-example-basic-single form-select" id="deliver_to" name="deliver_to"
                                    data-width="100%">
                                    <option selected disabled>Select Deliver To</option>
                                    <option value="warehouse">Warehouse</option>
                                    <option value="depo">Depo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="delivery_location" class="form-label mb-2 ms-1">Delivery Location</label>
                                <select class="js-example-basic-single form-select" id="delivery_location"
                                    name="delivery_location_id" data-width="100%" disabled>
                                    <option selected disabled>Select Delivery Location</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">Delivery By Date</label>
                                <div class="input-group flatpickr me-2 mb-2 mb-md-0" id="delivery_by_date">
                                    <input name="date" type="text" class="form-control flatpickr-input"
                                        placeholder="Select date" data-input="" readonly="readonly">
                                </div>
                            </div>
                            <div class="modal-header">
                                <h5 class="modal-title" id="varyingModalLabel">Add Stocks
                                </h5>
                            </div>
                            <div class="table-responsive pt-3">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>
                                                Sr.no
                                            </th>
                                            <th class="w-25">
                                                Product
                                            </th class="w-25">
                                            <th>
                                                Category
                                            </th>
                                            <th>
                                                Transfer QTY
                                            </th>
                                            <th>
                                                UOM
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                        <tr id="first_tr">
                                            <td>
                                                <p class="mt-2">1</p>
                                            </td>
                                            <td>
                                                <select disabled class="js-example-basic-single form-select products"
                                                    id="select_product" name="products[]" data-width="100%">
                                                    <option selected disabled>Select product</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input value="{{ old('model') }}" id="category"
                                                    class="form-control category" placeholder="Category" type="text"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input value="{{ old('model') }}" id="transfer_qty"
                                                    placeholder="Quantity" class="form-control qty" name="transfer_qty[]"
                                                    type="number">
                                                <div class="d-flex justify-content-end ">
                                                    <div class="badge bg-success xs mt-1 max_qty d-none"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <input value="{{ old('uom') }}" id="uom"
                                                    class="form-control uom" type="text" placeholder="UOM" readonly>
                                            </td>
                                            <td>
                                                <button disabled id="addBtn" type="button"
                                                    class="btn border-0 text-primary"><i
                                                        class="mdi mdi-plus-circle fs-4"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="sumbit" class="btn btn-primary">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_consignment" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">Edit Consignment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <form id="edit_cons" method="post" action="{{ route('admin.consignements.update', '1') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body" id="edit_modal_body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="sumbit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="view_cons" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="varyingModalLabel">View Consignment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>

                <div id="viewData" class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    {{-- <button type="sumbit" class="btn btn-primary">Update</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
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
                        text: "you want to submit form",
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
                            $('#warehouse_from').prop('disabled', false);
                            $('#deliver_to').prop('disabled', false);
                            form.submit();
                        }
                    })

                }
            });


            $(function() {

                var form = $('form#create_consignment');

                form.on('submit', function(event) {
                    event.preventDefault();
                    form.validate().resetForm();
                    //Add validation rule for dynamically generated name fields;
                    $('.products').each(function() {
                        $(this).rules("add", {
                            required: true,
                            uniqueSelection: true
                        });
                    });
                    $('.qty').each(function() {
                        $(this).removeData('max_val')
                        const maxVal = $(this).data('max_val');
                        $(this).rules("add", {
                            required: true,
                            number: true,
                            max: maxVal,
                            min: 1,
                            messages: {
                                max: "Quantity cannot be greater than stock",
                                min: "Quantity cannot be less then 1"
                            }
                        });
                    });
                });

                $("#create_consignment").validate({
                    rules: {
                        warehouse_from: {
                            required: true,
                        },
                        deliver_to: {
                            required: true,
                        },
                        delivery_location_id: {
                            required: true,
                            notEqualTo: "#warehouse_from"
                        },
                        date: {
                            required: true,
                        }
                    },
                    messages: {},
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
                        if ($(element).hasClass("select2-hidden-accessible")) {
                            $(element).parent().find('.select2-container').addClass(
                                "is-invalid").removeClass("is-valid");
                        }
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-invalid").removeClass("is-valid");
                        }
                    },
                    unhighlight: function(element, errorClass) {
                        if ($(element).hasClass("select2-hidden-accessible")) {
                            $(element).parent().find('.select2-container').addClass("is-valid")
                                .removeClass("is-invalid");
                        }
                        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                            'radio') {
                            $(element).addClass("is-valid").removeClass("is-invalid");
                        }
                    },
                });
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
                    if ($('#deliver_to').val() == 'depo') {
                        return true;
                    }
                    return this.optional(element) || value !== $(param).val();
                }, "Origin location and Delivery Location can't be same");
            });
        });
        $(function() {
            'use strict'
            if ($(".js-example-basic-single").length) {
                $(".js-example-basic-single").select2({
                    dropdownParent: $("#add_cons")
                });
            }
            if ($('#delivery_by_date').length) {
                const today = new Date();
                // const firstDayOfMonth = today.getMonth() === 0 ? new Date(today.getFullYear(), 11, 31) : new Date(
                //     today.getFullYear(), today.getMonth(), 1);

                flatpickr("#delivery_by_date", {
                    wrap: true,
                    dateFormat: "d-M-Y",
                    defaultDate: "today",
                    minDate: "today"
                });
            }
            // Denotes total number of rows.
            var rowIdx = 1;
            var selectId = 1;
            // jQuery button click event to add a row.
            $('#addBtn').on('click', function() {
                $('#warehouse_from').prop('disabled', true);
                $('#deliver_to').prop('disabled', true);
                var parent = $('#tbody > tr').last().find('.products');
                // parent.prop('disabled', true);
                // Adding a row inside the tbody.
                $('#tbody').append(`<tr id="r${++rowIdx}">
                                            <td class="row-index">
                                                <p class="mt-2">${rowIdx}</p>
                                            </td>
                                            <td>
                                                <select class="js-example-basic-single form-select products" id="product${++selectId}"
                                                    name="products[${rowIdx}]" data-width="100%">
                                                    <option selected disabled>Select product</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input value="{{ old('model') }}" id="category" class="form-control category"
                                                 placeholder="Category" type="text" readonly>
                                            </td>
                                            <td>
                                                <input value="{{ old('model') }}" id="transfer_qty${rowIdx}"
                                                    placeholder="Quantity" class="form-control qty" name="transfer_qty[${rowIdx}]"
                                                    type="text">
                                                    <div class="d-flex justify-content-end ">
                                                        <div class="badge bg-success xs mt-1 max_qty d-none"></div>
                                                    </div>
                                            </td>
                                            <td>
                                                <input value="{{ old('uom') }}" id="uom" class="form-control uom"  type="text" placeholder="UOM" readonly>
                                            </td>
                                            <td>
                                                <button id="del" type="button" class="btn text-danger remove "><i
                                                        class="mdi mdi-minus-circle fs-4 "></i></button>
                                            </td>
                                        </tr>`);
                var sel_product = $(`#product${selectId}`).empty();
                $.each(parent.find('option'), function(key, val) {
                    if (val.value == "") {
                        sel_product.append(
                            $('<option></option>').text(val.text)
                            .attr("value", val.value)
                            .attr("disabled", true).attr("selected", true));
                    } else {
                        // if (val.value != parent.find(":selected").val()) {
                        sel_product.append(
                            $('<option></option>').text(val.text)
                            .attr("value", val.value).attr("data-category", $(this).attr(
                                "data-category")).attr("data-uom", $(this).attr("data-uom"))
                            .attr("data-max_val", $(this).attr("data-max_val"))
                        );
                        // }
                    }
                })
                $(`#product${selectId}`).select2({
                    dropdownParent: $("#add_cons")
                });
            });
            $('#tbody').on('click', '.remove', function() {

                // Getting all the rows next to the 
                // row containing the clicked button
                var child = $(this).closest('tr').nextAll();

                // Iterating across all the rows 
                // obtained to change the index
                child.each(function() {
                    // Getting <tr> id.
                    var id = $(this).attr('id');

                    // Getting the <p> inside the .row-index class.
                    var idx = $(this).children('.row-index').children('p');

                    // Gets the row number from <tr> id.
                    var dig = parseInt(id.substring(1));

                    // Modifying row index.
                    idx.html(`${dig - 1}`);

                    // Modifying row id.
                    $(this).attr('id', `r${dig - 1}`);
                });

                // Removing the current row.
                $(this).closest('tr').remove();

                // Decreasing the total number of rows by 1.
                rowIdx--;
            });
        });

        $('#warehouse_from').change(function() {
            var value = $(this).find(":selected").val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: '../getdProducts/' + value,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    $('#select_product').empty().append($('<option></option>').attr('selected', true)
                        .text('Select Product').attr("value", "").attr('disabled', true));
                    $.each(data, function(key, val) {
                        $('#select_product').append(
                            $('<option></option>')
                            .text(val.product_with_category.name)
                            .attr("value", val.product_with_category.id).attr(
                                "data-category", val.product_with_category.category.name)
                            .attr("data-uom", val.product_with_category.uom).attr(
                                "data-max_val", val.availeble_stock));
                    });
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        });

        $('#deliver_to').change(function() {
            var value = $(this).find(":selected").val();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'getWarehousesAndDepo/' + value,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    $('#select_product').prop('disabled', false);
                    console.log(data);
                    $('#delivery_location').prop('disabled', false);
                    $('#delivery_location').empty().append($('<option></option>').attr('selected', true)
                        .text(
                            'Select Delivery location').attr("value", "").attr('disabled', true));
                    $.each(data.data, function(key, val) {
                        if (value == 'warehouse') {
                            // if (val.id != $('#warehouse_from').val()) {
                            $('#delivery_location').append($('<option></option>').text(val
                                    .name)
                                .attr("value", val.id));
                            // }
                        } else {
                            $('#delivery_location').append($('<option></option>').text(val.name)
                                .attr("value", val.id));
                        }
                    });
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        });
        $(function(ready) {
            $(document).on('change', '.products', function() {
                $this = $(this);
                var tr = $this.parent().parent()
                tr.find('.category').val($this.find(":selected").data("category"));
                tr.find('.uom').val($this.find(":selected").data("uom"));
                tr.find('.max_qty').text(`Available stock ${$this.find(":selected").data("max_val")}`)
                    .removeClass("d-none");
                tr.find('.qty').attr("data-max_val", $this.find(":selected").data("max_val"));
                $('#addBtn').prop('disabled', false);
            });
        })

        function getData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'consignements/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#view_cons').modal('show');
                        $('#viewData').empty().append(`
                        <div class="mb-3">
                        <p id="con_no" class="mb-2">Consignment No.:-${data.data.id}</p>
                        <p>Order Date:- ${data.data.date}</p>
                    </div>
                    <div class="card view-consignment-card px-2 py-3">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="ms-3">
                                <p class="mb-0">Origin Location</p>
                                <Small>${data.data.origin_warehouse.name}</Small>
                            </div>
                            <div class="border-left ps-3">
                                <p class="mb-0">Delivery Location</p>
                                <Small>${data.data.warehouse ? data.data.warehouse.name : (data.data.depo ? data.data.depo.name : "")}</Small>
                            </div>
                            <div class="ps-3 me-5 border-left">
                                <p class="mb-0">Delivery To</p>
                                <Small>${data.data.type}</Small>
                            </div>
                        </div>
                        <div class="card-body pt-0 px-0">
                            <div class="table-responsive">
                                <table id="dataTable" class="table view-constigment" data-order=''>
                                    <thead>
                                        <tr>
                                            <th class="pe-0">SR.No</th>
                                            <th class="pe-0">Item Name</th>
                                            <th class="pe-0">Item Category</th>
                                            <th class="pe-0">Transfer Quantity</th>
                                            <th class="pe-0">UOM</th>
                                        </tr>
                                    </thead>
                                    <tbody id="viewProductTbl">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                        `);
                        var tbldata = $('#viewProductTbl');
                        $.each(data.products, function(key, val) {
                            tbldata.append(`
                                            <tr>
                                                <td>${key+1}</td>
                                                <td>${val.products_with_category.name}</td>
                                                <td>${val.products_with_category.category.name}</td>
                                                <td>${val.quantity}</td>
                                                <td>${val.products_with_category.uom}</td>
                                            </tr>`);
                        })
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }

        function delete_con(id) {
            $.ajax({
                type: 'DELETE',
                dataType: 'JSON',
                url: 'consignements/' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    console.log(data);
                    if (data.status == 200) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: false,
                        });
                        Toast.fire({
                            icon: 'success',
                            title: data.message
                        });
                        $('#dt_tr' + id).remove();
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }
    </script>


    {{-- for edit  --}}
    <script>
        var rowIdx = 0;
        var selectId = 0


        function editData(id) {
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: 'consignements/' + id,
                beforeSend: function() {
                    $('#spin').removeClass('d-none');
                },
                success: function(data) {
                    if (data.status == 200) {
                        $('#edit_consignment').modal('show');
                        var html = `<div class="row">
                            <input type="hidden" value="${data.data.id}" name="id">
                            <div class="col-md-6 mb-2">
                                
                                <label for="warehouse_from" class="form-label mb-2 ms-1">Origin Location</label>
                                <select disabled class="js-example-basic-single1 form-select" id="edit_warehouse_from"
                                    name="warehouse_from" data-width="100%">
                                    <option selected disabled>${data.data.origin_warehouse.name}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="deliver_to" class="form-label mb-2 ms-1">Deliver To</label>
                                <select disabled class="js-example-basic-single1 form-select" id="edit_deliver_to"
                                    name="deliver_to" data-width="100%">
                                    <option selected disabled>${data.data.type}</option>
                                    <option value="warehouse">Warehouse</option>
                                    <option value="depo">Depo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="delivery_location" class="form-label mb-2 ms-1">Delivery Location</label>
                                <select class="js-example-basic-single1 form-select" id="edit_delivery_location"
                                    name="delivery_location_id" data-width="100%" disabled>
                                    <option selected disabled>${data.data.warehouse ? data.data.warehouse.name : data.data.depo.name}</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="vb_type" class="form-label mb-2 ms-1">Delivery By Date</label>
                                <input value="${data.data.date}" id="edit_date" class="form-control" readonly>
                            </div>
                            <div class="modal-header">
                                <h5 class="modal-title" id="varyingModalLabel">Edit Stocks
                                </h5>
                            </div>
                            <div class="table-responsive pt-3">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>
                                                Sr.no
                                            </th>
                                            <th class="w-25">
                                                Product
                                            </th class="w-25">
                                            <th>
                                                Category
                                            </th>
                                            <th>
                                                Transfer QTY
                                            </th>
                                            <th>
                                                UOM
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="edit_products_table">

                                    </tbody>
                                </table>
                            </div>
                        </div> `
                        $('#edit_modal_body').empty().append(html);

                        $.each(data.products, function(key, val) {
                            var product_html = `<tr id="edit_first_tr">
                                            <td>
                                                <p class="mt-2">${key+1}</p>
                                            </td>
                                            <td>
                                                <select class="js-example-basic-single1 form-select products"
                                                    id="edit_select_product${key}" name="oldproducts[${val.id}]" data-width="100%">
                                                    <option value="" selected disabled >Select product</option>
                                                    ${getProductOptions(val.product_id)}

                                                </select>
                                            </td>
                                            <td>
                                                <input value="${val.products_with_category.category.name}" id="category"
                                                    class="form-control category" placeholder="Category" type="text"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input value="${val.quantity}" id="edit_transfer_qty${key}"
                                                    placeholder="Quantity" class="form-control qty" name="oldtransfer_qty[${val.id}]"
                                                    type="number">
                                                <div class="d-flex justify-content-end ">
                                                    <div class="badge bg-success xs mt-1 max_qty d-none"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <input value="${val.products_with_category.uom}" id="edit_uom"
                                                    class="form-control uom" type="text" placeholder="UOM" readonly>
                                            </td>
                                            <td>
                                                ${key==0 ? `<button id="edit_addBtn" type="button" class="btn edit_addBtn border-0 text-primary"><i class="mdi mdi-plus-circle fs-4"></i></button>`
                                                : `<button id="addBtn" type="button" class="btn text-danger edit_remove "><i class="mdi mdi-minus-circle fs-4 "></i></button>` }
                                            </td>
                                        </tr>`;
                            $('#edit_products_table').append(product_html);
                            rowIdx++
                        });

                        function getProductOptions(selected_id) {
                            var options = '';
                            data.active_products.forEach(function(option) {
                                options +=
                                    `<option data-category="${option.product_with_category.category.name}" data-uom="${option.product_with_category.uom}" data-max_val="${option.availeble_stock}" value="${option.product_id}" ${selected_id == option.product_id ? 'selected' : ''}>${option.product_with_category.name}</option>`;
                            });
                            return options;
                        }


                        if ($(".js-example-basic-single1").length) {
                            $(".js-example-basic-single1").select2({
                                dropdownParent: $("#edit_consignment")
                            });
                        }
                    }
                },
                complete: function() {
                    $('#spin').addClass('d-none');
                }
            });
        }

        $(function(ready) {
            // jQuery button click event to add a row.
            $(document).on('click', '.edit_addBtn', function() {
                var parent = $('#edit_products_table > tr').last().find('.products');
                // Adding a row inside the tbody.
                $('#edit_products_table').append(`<tr id="r${++rowIdx}">
                                            <td class="row-index">
                                                <p class="mt-2">${rowIdx}</p>
                                            </td>
                                            <td>
                                                <select class="js-example-basic-single form-select products" id="product${++ selectId}"
                                                    name="products[${rowIdx}]" data-width="100%">
                                                    <option selected disabled>Select product</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input value="{{ old('model') }}" id="category" class="form-control category"
                                                 placeholder="Category" type="text" readonly>
                                            </td>
                                            <td>
                                                <input value="{{ old('model') }}" id="transfer_qty${rowIdx}"
                                                    placeholder="Quantity" class="form-control qty" name="transfer_qty[${rowIdx}]"
                                                    type="text">
                                                    <div class="d-flex justify-content-end ">
                                                        <div class="badge bg-success xs mt-1 max_qty d-none"></div>
                                                    </div>
                                            </td>
                                            <td>
                                                <input value="{{ old('uom') }}" id="uom" class="form-control uom"  type="text" placeholder="UOM" readonly>
                                            </td>
                                            <td>
                                                <button id="addBtn" type="button" class="btn text-danger edit_remove "><i
                                                        class="mdi mdi-minus-circle fs-4 "></i></button>
                                            </td>
                                        </tr>`);
                var sel_product = $(`#product${selectId}`).empty();
                $.each(parent.find('option'), function(key, val) {
                    if (val.value == "") {
                        sel_product.append(
                            $('<option></option>').text(val.text)
                            .attr("value", val.value)
                            .attr("disabled", true).attr("selected", true));
                    } else {
                        sel_product.append(
                            $('<option></option>').text(val.text)
                            .attr("value", val.value).attr("data-category", $(this).attr(
                                "data-category")).attr("data-uom", $(this).attr("data-uom"))
                            .attr("data-max_val", $(this).attr("data-max_val"))
                        );
                    }
                })
                $(`#product${selectId}`).select2({
                    dropdownParent: $("#edit_consignment")
                });
            });
            $(document).on('click', '.edit_remove', function() {

                // row containing the clicked button
                var child = $(this).closest('tr').nextAll();

                // Iterating across all the rows 
                // obtained to change the index
                child.each(function() {
                    // Getting <tr> id.
                    var id = $(this).attr('id');

                    // Getting the <p> inside the .row-index class.
                    var idx = $(this).children('.row-index').children('p');

                    // Gets the row number from <tr> id.
                    var dig = parseInt(id.substring(1));


                    console.log(dig);
                    // Modifying row index.
                    idx.html(`${dig - 1}`);

                    // Modifying row id.
                    $(this).attr('id', `r${dig - 1}`);
                });

                // Removing the current row.
                $(this).closest('tr').remove();

                // Decreasing the total number of rows by 1.
                rowIdx--;
            });
        })

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
                    text: "you want to submit form",
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
                        // $('#warehouse_from').prop('disabled', false);
                        // $('#deliver_to').prop('disabled', false);
                        form.submit();
                    }
                })

            }
        });


        $(function() {

            $('form#edit_cons').on('submit', function(event) {
                //Add validation rule for dynamically generated name fields
                $('.products').each(function() {
                    $(this).rules("add", {
                        required: true,
                        uniqueSelection: true
                    });
                });
                $('#edit_cons .qty').each(function() {
                    // console.log($(this).parent().parent().find('.products'));
                    $(this).rules("add", {
                        required: true,
                        number: true,
                        max: $(this).parent().parent().find('.products').find(":selected")
                            .data('max_val'),
                        min: 1,
                        messages: {
                            max: "Quantity cannot be greater than stock",
                            min: "Quantity cannot be less then 1"
                        }

                    });
                });
            });

            $("#edit_cons").validate({
                rules: {
                    warehouse_from: {
                        required: true,
                    },
                    deliver_to: {
                        required: true,
                    },
                    delivery_location_id: {
                        required: true,
                        notEqualTo: "#edit_warehouse_from"
                    },
                    date: {
                        required: true,
                    }
                },
                messages: {},
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
                    if ($(element).hasClass("select2-hidden-accessible")) {
                        $(element).parent().find('.select2-container').addClass(
                            "is-invalid").removeClass("is-valid");
                    }
                    if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                        'radio') {
                        $(element).addClass("is-invalid").removeClass("is-valid");
                    }
                },
                unhighlight: function(element, errorClass) {
                    if ($(element).hasClass("select2-hidden-accessible")) {
                        $(element).parent().find('.select2-container').addClass("is-valid")
                            .removeClass("is-invalid");
                    }
                    if ($(element).prop('type') != 'checkbox' && $(element).prop('type') !=
                        'radio') {
                        $(element).addClass("is-valid").removeClass("is-invalid");
                    }
                },
            });
            $.validator.addMethod('uniqueSelection', function(value, element) {
                var selectedValues = [];
                $('.products').not(element).each(function() {
                    selectedValues.push($(this).val());
                });

                var currentValue = $(element).val();
                return $.inArray(currentValue, selectedValues) === -1;
            }, "Product cannot be same");
        });
    </script>
@endsection
