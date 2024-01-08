$(document).ready(function() {
	var pagetop = $('.pagetop');
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
			pagetop.fadeIn();
		} else {
			pagetop.fadeOut();
		}
	});
	pagetop.click(function () {
		$('body, html').animate({ scrollTop: 0 }, 500);
		return false;
	});
});


/* smooth scroll */
$(function(){
  var headerHight = 20;
  $('a[href^="#"],area[href^="#"]').click(function(){
    var speed = 500;
    var href= $(this).attr("href");
    var target = $(href == "#" || href == "" ? 'html' : href);
    var position = target.offset().top-headerHight;
    $("html, body").animate({scrollTop:position}, speed, "swing");
    return false;
  });
});


// burger menu //
// header fixed //
$(document).ready(function(){
  var state = false;
  var scrollpos;
 
  $('#toggle').on('click', function(){
    if(state == false) {
      scrollpos = $(window).scrollTop();
      $('body').addClass('fixed-body').css({'top': -scrollpos});
      $('#wrapper').addClass('open');
      state = true;
    } else {
      $('body').removeClass('fixed-body').css({'top': 0});
      window.scrollTo( 0 , scrollpos );
      $('#wrapper').removeClass('open');
      state = false;
    }
  });
});