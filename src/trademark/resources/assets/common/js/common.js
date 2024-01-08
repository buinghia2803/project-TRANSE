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

    $('body').on('click', 'form#form-logout input[type=submit]', function (e) {
        e.preventDefault();
        $(this).closest('form').submit();
    })
});


/* smooth scroll */
$(function(){
  var headerHight = 20;
  if ('a[href^="#"],area[href^="#"]' == '') {
      $('a[href^="#"],area[href^="#"]').click(function(){
        var speed = 500;
        var href= $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top-headerHight;
        $("html, body").animate({scrollTop:position}, speed, "swing");
        return false;
      });
  }
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

/* table scroll */
window.addEventListener('DOMContentLoaded', function(){
    if (typeof ScrollHint !== "undefined") {
        new ScrollHint('.js-scrollable', {
            suggestiveShadow: true,
            i18n: {
                scrollable: 'スクロールできます'
            }
        });
    }
});

/* multiple-Select */
$(function () {
        $(".multi").multipleSelect({
            selectAllText: '全て選択',
            allSelected: '全て選択'
        });
});

$(document).ready(function () {
    //trim space input
    $(".remove_space_input").keyup(function(e) {
        e.preventDefault();
        let value = $(this).val();
        value = value.replace(' ', '');
        value = value.replace('　', '');
        $(this).val(value.trim());
    });

//disabled button when submit
    $(".disabled-btn-submit").submit(function() {
        $(".disabled-btn-submit").attr('disabled', true)
        setTimeout(function(){ $(".disabled-btn-submit").attr('disabled', false) }, 1000);
    });
});

// Main Js Common
$(document).ready(function() {
    // Input no space
    $('body').on('change keyup', '[nospace]', function (e) {
        e.preventDefault();
        let value = $(this).val();
        value = value.replace(' ', '');
        value = value.replace('　', '');
        $(this).val(value.trim());
    });

    // Close btn
    $('body').on('click', '[data-dismiss="alert"]', function (e) {
        e.preventDefault();
        $(this).closest('.alert-dismissible').remove()
    });

    $('.close').on('click', function () {
        $('.message-booking').remove()
    });

    // Onchange input file
    $('body').on('change', 'input[type=file]', function () {
        $(this).focusout();
    });

    // Data show table
    $('body').on('click', '[data-show_table]', function (e) {
        e.preventDefault();
        let table = $(this).data('show_table');
        $(table).find('tr.hidden').removeClass('hidden');
        $(this).remove();
    });

    $('.alert > button').on('click', function() {
        $(this).closest('.alert').fadeOut('slow');
    });

    $('body').on('click', '[data-back]', function (e) {
        e.preventDefault();
        let backURL = $(this).data('back');
        if (backURL.length == 0) {
            history.back();
        } else {
            window.location = backURL;
        }
    })
});

// Check modal payment.
$(document).ready(function() {
 $('body').on('change', 'input#cart', function() {
  const clientHeight = $('body').width();
  // If device is sp then can't scroll content and except estimateBox modal.
  if($(this).is(':checked') && clientHeight < 768) {
    $('.estimateBox').css({"bottom": 'none'})
  } else {
    if(clientHeight > 768) {
      $('.estimateBox').css({"bottom": 'none'})
    }else {
    }
  }
 })
})

// Check In Table
$(document).ready(function() {
    $('body').on('change', 'input[data-check_all]', function () {
        let key = $(this).data('key');
        let isChecked = $(this).prop('checked');

        if (isChecked) {
            $('input[data-check_item][data-key=' + key + ']:not(:disabled)').prop('checked', true).change();
        } else {
            $('input[data-check_item][data-key=' + key + ']:not(:disabled)').prop('checked', false).change();
        }
    });

    $('body').on('change', 'input[data-check_all_group]', function () {
        let key = $(this).data('key');
        let groupKey = $(this).data('check_all_group');
        let isChecked = $(this).prop('checked');

        if (isChecked) {
            $('input[data-check_item=' + groupKey + '][data-key=' + key + ']:not(:disabled)').prop('checked', true).change();
        } else {
            $('input[data-check_item=' + groupKey + '][data-key=' + key + ']:not(:disabled)').prop('checked', false).change();
        }
    });

    $('body').on('change', 'input[data-check_item]', function () {
        let key = $(this).data('key');
        let groupKey = $(this).data('check_item');

        // Check All in Group
        let allItemGroup = $('input[data-check_item=' + groupKey + '][data-key=' + key + ']:not(:disabled)');
        let allItemGroupChecked = $('input[data-check_item=' + groupKey + '][data-key=' + key + ']:checked');
        if (allItemGroup.length == allItemGroupChecked.length) {
            $('input[data-check_all_group=' + groupKey + '][data-key=' + key + ']:not(:disabled)').prop('checked', true);
        } else {
            $('input[data-check_all_group=' + groupKey + '][data-key=' + key + ']:not(:disabled)').prop('checked', false);
        }

        // Check All
        let allItem = $('input[data-check_item][data-key=' + key + ']:not(:disabled)');
        let allItemChecked = $('input[data-check_item][data-key=' + key + ']:checked');
        if (allItem.length == allItemChecked.length) {
            $('input[data-check_all][data-key=' + key + ']:not(:disabled)').prop('checked', true);
        } else {
            $('input[data-check_all][data-key=' + key + ']:not(:disabled)').prop('checked', false);
        }
    });
});

// Limit line
$(document).ready(function() {
    $.each($('[data-limit_line]'), function (item, index) {
        let heightLine = 22.4;
        let limitLine = $(this).data('limit_line');
        let heightElement = $(this).height();

        $(this).css('line-height', heightLine + 'px');

        if (heightElement > heightLine * limitLine) {
            $(this).css({
                'height': heightLine * limitLine,
                'overflow': 'hidden',
            });
            $(this).append('<span class="show-line">...</span>')
        }
    });

    $('body').on('click', '.show-line', function (e) {
        e.preventDefault();

        $(this).closest('[data-limit_line]').css({
            'height': 'auto',
        });
        $(this).remove();
    });

    $.each($('[data-limit_length]'), function (item, index) {
        let maxLength = $(this).data('limit_length');
        let content = $(this).text();
        if (content.length > maxLength) {
            $(this).attr('data-content', content).data('content', content);
            let shortContent = content.slice(0, maxLength);
            shortContent += '<span class="show-dot cursor-pointer">...</span>';
            $(this).html(shortContent);
        }
    });

    $('body').on('click', '.show-dot', function (e) {
        e.preventDefault();
        let content = $(this).closest('[data-limit_length]').data('content');
        $(this).closest('[data-limit_length]').html(content);
    });
});

jconfirm.defaults = {
    scrollToPreviousElement: false,
    scrollToPreviousElementAnimate: false,
}
