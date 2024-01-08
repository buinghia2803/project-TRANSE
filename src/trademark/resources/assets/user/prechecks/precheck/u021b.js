$('.checkAllCheckBox,.checkSingleCheckBox,.all-checkbox,.single-checkbox').on('change', function() {
    $('body').find('.notice-scrollable').remove();
})
// Ajax Send Session
$('.rollback_suggest_ai').on('click', function (e) {
    let idProdChoice = []
    const route = $(this).data('route')
    $('input[data-foo="is_choice_user[]"]:checked').filter(function(index, el) {
        idProdChoice.push($(el).val())
    })
    if(!$('input[data-foo="is_choice_user[]"]:checked').length){
        if(!$('.js-scrollable').find('.notice').length) {
            $('.js-scrollable').append('<div class="notice mb15 notice-scrollable">選択してください。</div>')
        }
        e.stopPropagation();
        e.preventDefault();
        document.querySelector('.js-scrollable').scrollIntoView({
            behavior: 'smooth'
        });

        return
    }

    loadAjaxPost(route, {
        m_product_is: idProdChoice,
        precheck_id: precheck_id,
        trademark_id: trademark_id,
    }, {
        beforeSend: function(){},
        success:function(result){
            if(result?.status) {
                window.location.href = result.router_redirect
            }
        },
        error: function (error) {}
    }, 'loading');
})

$('#redirect_to_u031pass').on('click', function () {
    arrayProductSelect = []
    isChoiUser = []
    openModal('#u031pass-modal');
})

$('#redirect_to_quote').on('click', function (e) {
    $('#form input[name=redirect_to]').attr('value', 'QUOTE')
    $('#form input[name=redirect_to]').val('QUOTE')
    if(!$('input[data-foo="is_choice_user[]"]:checked').length){
        if(!$('.js-scrollable').find('.notice').length) {
            $('.js-scrollable').append('<div class="notice mb15 notice-scrollable">選択してください。</div>')
        }
        e.stopPropagation();
        e.preventDefault();
        document.querySelector('.js-scrollable').scrollIntoView({
            behavior: 'smooth'
        });

        return
    }

    const form = $('#form');
    $('input.data-type_acc').change();
    $('input.data-name').change();
    $('select.data-m_nation_id').change();
    $('select.data-m_prefecture_id').change();
    $('input.data-address_second').change();
    $('input.data-address_three').change();

    let hasError = form.find('.notice:visible,.error:visible,.error-validate:visible');
    if (hasError.length == 0) {
        $('#form').attr('target' ,'_blank');
        $('#form').submit()
        loadingBox('close');
        $('#form').attr('target' ,'_self');
    } else {
        let firstError = form.find('.notice:visible,.error:visible,.error-validate:visible').first();
        window.scroll({
            top: firstError.offset().top - 100,
            behavior: 'smooth'
        });
    }
})

$('.redirect_to_common_payment').on('click', function (e) {
    $('#form input[name=redirect_to]').attr('value', 'GTCP')
    $('#form input[name=redirect_to]').val('GTCP')
    if(!$('input[data-foo="is_choice_user[]"]:checked').length){
        $('<div class="notice mb15 notice-scrollable">選択してください。</div>').insertAfter($('.js-scrollable'))
        e.stopPropagation();
        e.preventDefault();
        document.querySelector('.js-scrollable').scrollIntoView({
            behavior: 'smooth'
        });

        return
    }

    const form = $('#form');
    $('input.data-type_acc').change();
    $('input.data-name').change();
    $('select.data-m_nation_id').change();
    $('select.data-m_prefecture_id').change();
    $('input.data-address_second').change();
    $('input.data-address_three').change();

    let hasError = form.find('.notice:visible,.error:visible,.error-validate:visible');
    if (hasError.length == 0) {
        form.submit();
    } else {
        let firstError = form.find('.notice:visible,.error:visible,.error-validate:visible').first();
        window.scroll({
            top: firstError.offset().top - 100,
            behavior: 'smooth'
        });
    }
})

$('#redirec_to_anken_top').on('click', function (e) {
    $('#form input[name=redirect_to]').attr('value', 'ANKEN_TOP')
    $('#form input[name=redirect_to]').val('ANKEN_TOP')
    if(!$('input[data-foo="is_choice_user[]"]:checked').length){
        $('<div class="notice mb15 notice-scrollable">選択してください。</div>').insertAfter($('.js-scrollable'))
        e.stopPropagation();
        e.preventDefault();
        document.querySelector('.js-scrollable').scrollIntoView({
            behavior: 'smooth'
        });

        return
    }
    $('#form').submit()
})

// To do , Redirect to u032_cancel
$('#stop_applying').on('click', function () {
    $(this).prop('href', routeCancel);
})

hasSave = false;
$('.table_product_choose .product-item').each(function () {
    let checkbox = $(this).find('.single-checkbox');

    if (checkbox.prop('checked')) {
        hasSave = true;
    }
})
if (hasSave == false) {
    $('.table_product_choose .product-item').each(function () {
        let historySimple = $(this).find('.history-simple')[0].innerText;
        let lastSimple = $(this).find('.last-simple')[0].innerText;
        let historyDetail = $(this).find('.history-detail')[0].innerText;
        let lastDetail = $(this).find('.last-detail')[0].innerText;
        let checkbox = $(this).find('.single-checkbox');

        let isChecked = true;
        if (historySimple == simpleOptions[1]) {
            isChecked = false;
        }
        if (lastSimple == simpleOptions[1]) {
            isChecked = false;
        }
        if (inArray(historyDetail, [listRanking[3], listRanking[4]])) {
            isChecked = false;
        }
        if (inArray(lastDetail, [listRanking[3], listRanking[4]])) {
            isChecked = false;
        }

        checkbox.prop('checked', isChecked);
    });

    getTotalCheclboxIsChecked();
    $('.total-dis').text(getTotalDistinction())
}
