// npm package: datatables.net-bs5
// github link: https://github.com/DataTables/Dist-DataTables-Bootstrap5

$(function () {
    "use strict";

    $(function () {
        $("#custom_datatable").DataTable({
            aLengthMenu: [
                [10, 30, 50, -1],
                [10, 30, 50, "All"],
            ],
            iDisplayLength: 10,
            language: {
                search: "",
            },
            responsive: true,
        });

        $("#dataTableExample").DataTable({
            aLengthMenu: [
                [10, 30, 50, -1],
                [10, 30, 50, "All"],
            ],
            iDisplayLength: 10,
            language: {
                search: "",
            },
            ordering: false,
            responsive: true,
        });

        $("#dataTableExample1").DataTable({
            aLengthMenu: [
                [10, 30, 50, -1],
                [10, 30, 50, "All"],
            ],
            iDisplayLength: 10,
            language: {
                search: "",
            },
            responsive: true,
            ordering: false,

        });
        $("#dataTableExample2").DataTable({
            aLengthMenu: [
                [10, 30, 50, -1],
                [10, 30, 50, "All"],
            ],
            iDisplayLength: 10,
            language: {
                search: "",
            },
            responsive: true,
            ordering: false,

        });
        $("#dataTableExample3").DataTable({
            aLengthMenu: [
                [10, 30, 50, -1],
                [10, 30, 50, "All"],
            ],
            iDisplayLength: 10,
            language: {
                search: "",
            },
            responsive: true,
        });
        $("#dataTableExample4").DataTable({
            aLengthMenu: [
                [10, 30, 50, -1],
                [10, 30, 50, "All"],
            ],
            iDisplayLength: 10,
            language: {
                search: "",
            },
            responsive: true,
            ordering: false,

        });

        $("#dataTableExample").each(function () {
            var datatable = $(this);
            // SEARCH - Add the placeholder for Search and Turn this into in-line form control
            var search_input = datatable
                .closest(".dataTables_wrapper")
                .find("div[id$=_filter] input");
            search_input.attr("placeholder", "Search");
            search_input.removeClass("form-control-sm");
            // LENGTH - Inline-Form control
            var length_sel = datatable
                .closest(".dataTables_wrapper")
                .find("div[id$=_length] select");
            length_sel.removeClass("form-control-sm");
        });

        $("#dataTableExample1").each(function () {
            var datatable = $(this);
            // SEARCH - Add the placeholder for Search and Turn this into in-line form control
            var search_input = datatable
                .closest(".dataTables_wrapper")
                .find("div[id$=_filter] input");
            search_input.attr("placeholder", "Search");
            search_input.removeClass("form-control-sm");
            // LENGTH - Inline-Form control
            var length_sel = datatable
                .closest(".dataTables_wrapper")
                .find("div[id$=_length] select");
            length_sel.removeClass("form-control-sm");
        });

        // $('.datatableForTabs').each(function () {
        //   var datatable = $(this);
        //   // SEARCH - Add the placeholder for Search and Turn this into in-line form control
        //   var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
        //   search_input.attr('placeholder', 'Search');
        //   search_input.removeClass('form-control-sm');
        //   // LENGTH - Inline-Form control
        //   var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
        //   length_sel.removeClass('form-control-sm');
        // });

        //   if ($('.datatableForTabs').length) {
        //     $('.datatableForTabs').each(function () {
        //       $('.datatableForTabs').DataTable({
        //         "aLengthMenu": [
        //           [10, 30, 50, -1],
        //           [10, 30, 50, "All"]
        //         ],
        //         "iDisplayLength": 10,
        //         "language": {
        //           search: ""
        //         },
        //         responsive: true
        //       });

        //     });

        //   }
    });
});
