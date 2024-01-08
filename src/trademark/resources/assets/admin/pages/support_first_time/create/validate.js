var Validation = () => {
    return {
        init: function () {
            $('#form').validate({
                errorElement: 'div',
                errorClass: 'error-validate',
                focusInvalid: false,
                rules: {
                    'comment[0][content]': {
                        maxlength: 500,
                    },
                    'comment[1][content]': {
                        maxlength: 500,
                    },
                },
                messages: {
                    'comment[0][content]': {
                        maxlength: errorMessageMaxLength500,
                    },
                    'comment[1][content]': {
                        maxlength: errorMessageMaxLength500,
                    },
                }
            });
        },
    }
}

Validation().init();


