var Validation = () => {
    return {
        init: function () {
            $('#form').validate({
                errorElement: 'div',
                errorClass: 'error-validate',
                focusInvalid: false,
                submitHandler: function (form) {
                    let error = false;
                    if ($('.sft_content_product_ids:checked').length <= 0 || ($('.sft_content_product_ids').length > $('.sft_content_product_ids:checked').length)) {
                        error = true
                    }
                    if(!error) {
                        $('.errorRequiredSftContentProd').html('')
                        form.submit();
                    } else {
                        $('.errorRequiredSftContentProd').html(`<div class="error-validate" style="font-size: 14px">${errorPleaseChooseProduct}</div>`)
                        $([document.documentElement, document.body]).animate({
                            scrollTop: $("#contents").offset().top
                        }, 1000);
                    }
                },
            });
        },
    }
}

Validation().init();


