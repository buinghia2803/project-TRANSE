$('.checkAllCheckBox,.checkSingleCheckBox,.all-checkbox,.single-checkbox').on('change', function() {
    $('body').find('.notice-scrollable').remove();
})

$('#redirect_to_u031pass').on('click', function (e) {
    arrayProductSelect = []
    isChoiUser = []
    openModal('#u031pass-modal');
})

// To do , Redirect to u032_cancel
$('#stop_applying').on('click', function () {
    $(this).prop('href', routeCancel);
})

$('#redirect_to_quote').on('click', function (e) {
    $('body').find('.notice-scrollable').remove();

    $('#form input[name=redirect_to]').attr('value', 'QUOTE')
    $('#form input[name=redirect_to]').val('QUOTE')

    if(!$('.checkSingleCheckBox:checked').length){
        if(!$('.js-scrollable').find('.notice').length) {
            $('.js-scrollable').append('<div class="notice mb15 notice-scrollable">選択してください。</div>')
        }
    }

    $('#form').valid()
})

$('.redirect_to_common_payment').on('click', function (e) {
    $('body').find('.notice-scrollable').remove();

    $('#form input[name=redirect_to]').attr('value', 'GTCP')
    $('#form input[name=redirect_to]').val('GTCP')

    if(!$('.checkSingleCheckBox:checked').length){
        $('<div class="notice mb15 notice-scrollable">選択してください。</div>').insertAfter($('.js-scrollable'))
    }

    $('#form').valid();
})

$('#redirec_to_anken_top').on('click', function (e) {
    $('body').find('.notice-scrollable').remove();

    $('#form input[name=redirect_to]').attr('value', 'ANKEN_TOP')
    $('#form input[name=redirect_to]').val('ANKEN_TOP')

    if(!$('.checkSingleCheckBox:checked').length){
        $('<div class="notice mb15 notice-scrollable">選択してください。</div>').insertAfter($('.js-scrollable'))
    }

    $('#form').valid()
})
