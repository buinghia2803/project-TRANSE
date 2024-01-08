$(document).ready(function() {
    $("#form").validate({
        rules: {
            'content1': {
                maxlength: 1000
            },
            'content2': {
                maxlength: 1000,
            },
        },
        messages: {
            'content1': {
                maxlength: errorMessageContentMaxLength,
            },
            'content2': {
                maxlength: errorMessageContentMaxLength,
            },
        },
        errorElement: "div",
    });

    $('body').on('change', '[name^=result_identification_detail]', function () {
        $('.product-list').next('.notice').remove();
    });

    $('body').on('click', '[type=submit]', function (e) {
        let value = $(this).attr('value');
        let productListBox = $('.product-list').first();

        productListBox.next('.notice').remove();
        if (value == CONFIRM) {
            let isSubmit = true;
            $.each($('[name^=result_identification_detail]'), function () {
                if ($(this).val().length == 0) {
                    isSubmit = false;
                }
            });
            if (isSubmit == false) {
                productListBox.after(`<span class="notice">${errorMessageRequiredResultIdentificationDetail}</span>`);
                scrollToElement(productListBox, -20);
                return false;
            }
        }

        let form = $('#form');
        form.valid();

        let hasError = form.find('.notice:visible,.error:visible,.error-validate:visible');
        if (hasError.length == 0 && form.valid()) {
            form.submit();
        } else {
            let firstError = hasError.first();
            scrollToElement(firstError, -100);
            return false;
        }
    });
});
