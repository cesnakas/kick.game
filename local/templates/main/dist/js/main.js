// Bootstrap
$.getScript('/local/templates/main/dist/js/bootstrap.bundle.min.js');

// Header
if(window.location.pathname === '/' && ($(window).outerWidth() >= 992)) {
    let navbar = $('.navbar');
    navbar.removeClass('blur');
    $(function() {
        $(window).scroll(function() {
            if ($(window).scrollTop() <= 8) {
                // navbar.addClass('navbar-blur');
                navbar.removeClass('blur');
            } else {
                // navbar.removeClass('navbar-blur');
                navbar.addClass('blur');
            }
        });
    });
};