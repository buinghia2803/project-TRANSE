$(document).ready(function() {
    //ready
    getTotalCheclboxIsChecked();
    checkedAllDefault();

    //Check box all table
    $('.all-checkbox').click(function() {
        if($(this).is(':checked')) {
            $('.single-checkbox').prop('checked', true)
        } else {
            $('.single-checkbox').prop('checked', false)
        }
        getTotalCheclboxIsChecked()
    })

    //Single checkbox
    $('.single-checkbox').on('click', function() {
        if ($('.single-checkbox:checked').length == $('.single-checkbox').length) {
            $('.all-checkbox').prop('checked', true)
        } else {
            $('.all-checkbox').prop('checked', false)
        }
        getTotalCheclboxIsChecked()
    })

    // get total checkbox is checked
   function getTotalCheclboxIsChecked() {
       let totalChecked = $('.single-checkbox:checked:visible').length
       $('.total-checkbox-checked').text(totalChecked)
   }

    $('.toggle-info').hide();
   //show hide collapse-div
    $('.hideShowClick').on('click dblclick', function() {
        $('.toggle-info').stop().slideToggle('slow');
        ($('.icon-text').text()) == '+' ? $('.icon-text').text('-') : $('.icon-text').text('+') ;
    });

    //check all checkbox default
    function checkedAllDefault() {
        if ($('.single-checkbox:checked').length == $('.single-checkbox').length) {
            $('.all-checkbox').prop('checked', true)
        } else {
            $('.all-checkbox').prop('checked', false)
        }

    }

});
