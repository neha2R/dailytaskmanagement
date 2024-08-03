
// for DT checkbox
$('#selectall').click(function (e) {
    $('#dataTableExample tbody :checkbox').prop('checked', $(this).is(':checked'));
    e.stopImmediatePropagation();
});
// for tab
$(document).ready(function(){ 
    $('.tab-a').click(function(){  
      $(".tab").removeClass('tab-active');
      $(".tab[data-id='"+$(this).attr('data-id')+"']").addClass("tab-active");
      $(".tab-a").removeClass('active-a');
      $(this).parent().find(".tab-a").addClass('active-a');
     });
});

// (function($) {
//     $.fn.multiStepForm = function(args) {
//         if (args === null || typeof args !== 'object' || $.isArray(args))
//             throw " : Called with Invalid argument";

//         var form = this;
//         var steps = form.find('h3');
//         // Initialize jQuery Steps plugin
//         form.steps({
//             headerTag: "h3",
//             bodyTag: "section",
//             transitionEffect: "slideLeft",
//             autoFocus: true,
//             enableCancelButton: false,
//             enableFinishButton: true,
//             labels: { // Customize labels, including the finish button text
//                 finish: "Save",
//                 next: "Next",
//                 previous: "Previous",
//             },
//             onStepChanging: function(event, currentIndex, newIndex) {
//                 if (currentIndex < newIndex) {
//                     // Moving to the next step
//                     if ('validations' in args && typeof args.validations === 'object' && !$
//                         .isArray(args.validations)) {
//                         if (!('noValidate' in args) || (typeof args.noValidate === 'boolean' &&
//                                 !args.noValidate)) {
//                             form.validate(args.validations);
//                             return form.valid();
//                         }
//                     }
//                 }
//                 return true;
//             },
//             onFinished: function (event, currentIndex) {
//                 // Submit the form when the custom finish button is clicked
//                 form.submit();
//             },
//         });
//         return form;
//     };
// }(jQuery));


(function ($) {
    $.fn.multiStepForm = function (args) {
        if (args === null || typeof args !== 'object' || $.isArray(args))
            throw " : Called with Invalid argument";

        var form = this;
        var steps = form.find('h3');

        // Initialize jQuery Steps plugin
        form.steps({
            headerTag: "h3",
            bodyTag: "section",
            transitionEffect: "slideLeft",
            autoFocus: true,
            enableCancelButton: false,
            enableFinishButton: false,
            onStepChanging: function (event, currentIndex, newIndex) {
                if (currentIndex < newIndex) {
                    // Moving to the next step
                    if ('validations' in args && typeof args.validations === 'object' && !$.isArray(args.validations)) {
                        if (!('noValidate' in args) || (typeof args.noValidate === 'boolean' && !args.noValidate)) {
                            form.validate(args.validations);
                            return form.valid();
                        }
                    }
                }
                return true;
            },
            onStepChanged: function (event, currentIndex, priorIndex) {
                // Step has changed
                fixStepIndicator(currentIndex);
            
            }
        });

        function fixStepIndicator(n) {
            steps.each(function (i, e) {
                i == n ? $(e).addClass('active') : $(e).removeClass('active');
                i == n ? $(e).prev().find('a').addClass('activated') : $(e).prev().find('a').removeClass('activated');
                i <= n ? $(e).prev().find('a').addClass('activated') : $(e).prev().find('a').removeClass('activated');
            });
        }
        return form;
    };
}(jQuery));