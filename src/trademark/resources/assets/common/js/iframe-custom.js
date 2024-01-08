$(document).ready(function() {
    var pagetop = $('.pagetop-custom');
    $('.iframe-common').scroll(function () {
        if ($(this).scrollTop() > 100) {
            pagetop.fadeIn();
        } else {
            pagetop.fadeOut();
        }
    });
    pagetop.click(function () {
        $('.iframe-common').animate({ scrollTop: 0 }, 500);
        return false;
    });
});
