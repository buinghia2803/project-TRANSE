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

formSubmit = function (type) {
    if (type == 'redirect_to_quote') {
        $('#form input[name=redirect_to]').attr('value', 'QUOTE').val('QUOTE')
    } else if(type == 'redirect_to_common_payment') {
        $('#form input[name=redirect_to]').attr('value', 'GTCP').val('GTCP');
    }

    $('.js-scrollable').find('.notice').remove();
    if(!$('.checkSingleCheckBox:checked').length){
        $('.js-scrollable').append(`<div class="notice mb15 notice-scrollable">${Common_E025}</div>`)
    }

    let form = $('#form');
    form.valid();
    let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
    if (hasError.length == 0 && form.valid()) {
        form.submit();
    } else {
        let firstError = hasError.first();
        scrollToElement(firstError, -100);
        return false;
    }
}

$('#redirect_to_quote').on('click', function (e) {
    e.preventDefault();

    formSubmit('redirect_to_quote');
})

$('.redirect_to_common_payment').on('click', function (e) {
    e.preventDefault();

    formSubmit('redirect_to_common_payment');
})

$('#redirec_to_anken_top').on('click', function (e) {
    $('body').find('.error-validate').remove();
    var validator = $("#form").validate();
    validator.element("input[name=type_trademark]");
    validator.element("input[name=name_trademark]");
    if ($("input[name=type_trademark]:checked", '#form').val() == 2) {
        validator.element("input[name=image_trademark]");
    }

    let form = $('#form');
    let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
    if (hasError.length > 0) {
        let firstError = hasError.first();
        scrollToElement(firstError, -100);
        return false;
    }

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
    dataForm.append('isAjax', 0)
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
        success: function (data) {
            if (data.status) {
                window.location.href = data.router_redirect
            }
        },
        error: function (data) {}
    });
})
