//Validate
var Validation = () => {
    return {
        init: function () {
            $('#edit-email-form').validate({
                errorElement: 'div',
                errorClass: 'error-validate',
                focusInvalid: false,
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                rules: {
                    'email': {
                        required: true,
                        maxlength: 255,
                        isValidEmail: true,
                    }
                },
                messages: {
                    'email': {
                        required: errorMessageIsValidRequired,
                        maxlength: errorMessageIsValidMaxLength255,
                        isValidEmail: errorMessageIsValidEmailFormat
                    }
                }
            });

        },
    }
}

Validation().init();
