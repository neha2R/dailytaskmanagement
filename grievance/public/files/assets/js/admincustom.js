$(document).ready(function(){
    var tabpane = $('#inquirytab').val();
    if (parseInt(tabpane)) {
        $('#home7').removeClass('active');
        $('#home7').attr('aria-expanded', false);
        $('#profile7').addClass('active');
        $('#profile7').attr('aria-expanded', true);
        $('#homelink').removeClass('active');
        $('#homelink').attr('aria-expanded', false);
        $('#profilelink').addClass('active');
        $('#profilelink').attr('aria-expanded', true);
    }
});