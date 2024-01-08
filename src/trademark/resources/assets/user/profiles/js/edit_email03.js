//Validate
var Validation = () => {
    return {
        init: function () {
            $('#form').validate({
                errorElement: 'div',
                errorClass: 'error-validate',
                focusInvalid: false,
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    $('.disabled-btn-submit').attr('disabled', true);
                    form.submit();
                },
                rules: {
                    'code': {
                        required: true,
                        isValidCode: true
                    }
                },
                messages: {
                    'code': {
                        required: errorMessageIsValidRequired,
                        isValidCode: errorMessageIsValidCodeFormat
                    }
                }
            });
            $.validator.addMethod("isValidCode", function (value) {
                if (value != '') {
                    let regex = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{12}$/;
                    return regex.test(value);
                }
                return true;
            });
        },
    }
}

Validation().init();

//trim space input
$(".remove_space_input").keyup(function(e) {
    e.preventDefault();
    let value = $(this).val();
    value = value.replace(' ', '');
    value = value.replace('　', '');
    $(this).val(value.trim());
});

