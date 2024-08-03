




function showLoader(){
    var pre = document.createElement("div");
    pre.setAttribute("id",'spin');
    pre.innerHTML = '<div class="cssload-container"><div class="cssload-speeding-wheel"></div></div>';
    document.body.insertBefore(pre, document.body.firstChild);
}
showLoader();

function hideLoader(){
    document.body.firstChild.className += "d-none";
}

document.addEventListener("DOMContentLoaded", function(event) {
    hideLoader();
});
document.addEventListener('submit', function (event) {
    if (!event.target.classList.contains('ajax-form')) {
        showLoader();
    }
});