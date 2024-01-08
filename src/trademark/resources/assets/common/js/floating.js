// フロートメニュー（右）
jQuery(function($) {

var nav    = $('.rightBox'),
    offset = nav.offset();

$(window).scroll(function () {
  if($(window).scrollTop() > offset.top - 0) {
    nav.addClass('fixed');
  } else {
    nav.removeClass('fixed');
  }
});

});