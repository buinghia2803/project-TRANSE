var Validation = () => {
    return {
        init: function () {
            $('#form').validate({
                errorElement: 'div',
                errorClass: 'error-validate',
                focusInvalid: false,
                rules: {
                    'comments[to_user]': {
                        maxlength: 500,
                    },
                    'comments[to_admin]': {
                        maxlength: 500,
                    },
                },
                messages: {
                    'comments[to_user]': {
                        maxlength: errorMessageMaxLength500,
                    },
                    'comments[to_admin]': {
                        maxlength: errorMessageMaxLength500,
                    },
                }
            });
        },
    }
}

Validation().init();


