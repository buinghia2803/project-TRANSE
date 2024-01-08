$('.checkAllCheckBox,.checkSingleCheckBox,.all-checkbox,.single-checkbox').on('change', function() {
    $('body').find('.notice-scrollable').remove();
})

$('#redirect_to_u031pass').on('click', function (e) {
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
    $('#form').valid()
    if(!$('.checkSingleCheckBox:checked').length){
        if(!$('.js-scrollable').find('.notice').length) {
            $('.js-scrollable').append('<div class="notice mb15 notice-scrollable">選択してください。</div>')
        }
        document.querySelector('.js-scrollable').scrollIntoView({
            behavior: 'smooth'
        });
    }
    if($('#form .error').length) {
        document.querySelector('.error').scrollIntoView({
            behavior: 'smooth'
        });
    }
    if($('#form .error-validate').length) {
        document.querySelector('.error-validate').scrollIntoView({
            behavior: 'smooth'
        });
    }
})

$('.redirect_to_common_payment').on('click', function (e) {
    $('body').find('.notice-scrollable').remove();
    $('#form input[name=redirect_to]').attr('value', 'GTCP').val('GTCP');
    $('#form').valid();
    if(!$('.checkSingleCheckBox:checked').length){
        $('<div class="notice mb15 notice-scrollable">選択してください。</div>').insertAfter($('.js-scrollable'))

        document.querySelector('.js-scrollable').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
    if($('#form .error').length) {
        document.querySelector('.error').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
    if($('#form .error-validate').length) {
        document.querySelector('.error-validate').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
})

$('.redirec_to_anken_top').on('click', function (e) {
    $('body').find('.error-validate').remove();
    var validator = $("#form").validate();
    validator.element("input[name=type_trademark]");
    validator.element("input[name=name_trademark]");
    if ($("input[name=type_trademark]:checked", '#form').val() == 2) {
        validator.element("input[name=image_trademark]");
    }
    if ($('body').find('.error-validate').length) {
        document.querySelector('.error-validate').scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        if ($('body').find('.error').length) {
            document.querySelector('.error').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    } else {
        const dataForm = new FormData();
        $('#form').find('input[type=text], input[type=checkbox]:checked, input[type=radio]:checked, input[type=hidden],  select, checkbox, input[type=file]').each(function (key, item) {
            if ($(item).attr('name') && item.getAttribute('type') != 'file' && item.getAttribute('name') !== 'mDistrintions[]') {
                dataForm.append($(item).attr('name'), $(item).val())
            }
            if ($(item).attr('name') && item.getAttribute('type') == 'file') {
                dataForm.append($(item).attr('name'), item.files[0])
            }
        })
        dataForm.append('redirect_to', 'ANKEN_TOP')
        dataForm.append('submit_type', 'draft')
        dataForm.append('isAjax', true)
        $.ajax({
            method: 'POST',
            url: routePost,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: dataForm,
            dataType: "json",
            processData: false,
            contentType: false,
            encode: true,
            beforeSend: function () {
                loadingBox('open');
            },
            success: function (result) {
                if (result.status) {
                    window.location.href = result.router_redirect
                } else {
                    $.alert(result.message)
                    loadingBox('close');
                }
            },
            error: function (error) {
                loadingBox('close');
            }
        });
    }
})
