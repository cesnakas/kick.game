// Bootstrap
$.getScript('https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js');

// Header
if(window.location.pathname === '/' && ($(window).outerWidth() >= 992)) {
    let navbar = $('.navbar');
    navbar.addClass('navbar-blur');
    $(function() {
        // var navbar = $('.navbar');
        $(window).scroll(function() {
            if($(window).scrollTop() <= 8){
                navbar.addClass('navbar-blur');
            } else {
                navbar.removeClass('navbar-blur');
            }
        });
    });
};