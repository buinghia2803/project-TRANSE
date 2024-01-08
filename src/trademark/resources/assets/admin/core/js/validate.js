$.validator.setDefaults({
    errorClass: 'error',
    focusInvalid: false,
    errorElement: "div",
    ignore: [".ignore"],
    errorPlacement: function (error, element) {
        if (element.closest('.fCheckbox').length > 0) {
            error.insertAfter(element.closest('.fCheckbox').find('.checkbox-group'));
        } else if (element.closest('.fRadio').length > 0) {
            error.insertAfter(element.closest('.fRadio').find('.radio-group'));
        } else if (element.closest('.fSelect2').length > 0) {
            error.insertAfter(element.closest('.fSelect2').find('.select-group'));
        } else if (element.closest('.image-group').hasClass('image-group')) {
            error.insertAfter(element.closest('.image-group').find('.image-button'));
        } else if (element.closest('.file-group').hasClass('file-group')) {
            error.insertAfter(element.closest('.file-group').find('.file-button'));
        } else if ($(element).closest('.fEditor').length > 0) {
            element.closest('.fEditor').find('.tox').addClass('border-error');
            error.insertAfter(element.closest('.fEditor').find('.tox'));
        } else if (element.closest('.fStar').length > 0) {
            error.insertAfter(element.closest('.fStar').find('.star-group'));
        } else if (element.closest('.fCustom').length > 0) {
            element.closest('.fCustom').find('.fBox').append(error);
        } else if (element.closest('.tTag').length > 0) {
            element.closest('.tTag').find('.select-group').append(error);
        } else {
            error.insertAfter(element);
        }
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
        $(element).closest('.fSelect2').find('.select2-selection').removeClass('border-normal').addClass('border-error');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
        $(element).closest('.form-group').find('.error').remove();
        $(element).closest('.fEditor').find('.tox').removeClass('border-error');
    },
    invalidHandler: function () {},
    submitHandler: function (form) {
        let has_error = $(form).find('.error').length;
        if (has_error > 0) {
            return false;
        }

        loadingBox('open');
        return true;
    }
});

$.validator.addMethod("isValidEmail", function (value) {
    let email = /^[\s]{0,60}[a-zA-Z0-9][a-zA-Z0-9_+-\.]{0,60}@[a-zA-Z0-9-\.]{2,}(\.[a-zA-Z0-9]{2,4}){1,2}[\s]{0,60}$/;
    if (!email.test(value)) {
        return false;
    }
    return !(value.includes(',') ||
        value.includes('..') ||
        value.includes('-@') ||
        value.includes('@-') ||
        value.includes('.@') ||
        value.includes('@.'));

});

$.validator.addMethod("isValidPhone", function (value) {
    let phone = /^[\s]{0,60}\+?\d{10,13}[\s]{0,60}$/;
    return phone.test(value);
});

$.validator.addMethod("isValidPassword", function (value) {
    let password = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/;
    return password.test(value);
});

$.validator.addMethod("requiredEditor", function (value, element) {
    return value.length > 0;
});

$.validator.addMethod("requiredFile", function (value, element) {
    let valueAttr = $(element).attr('value');
    return !(value.length == 0 && valueAttr.length == 0);
});

$.validator.addMethod("requiredStar", function (value, element) {
    return (value != 0);
});

$.validator.addMethod("requiredAddress", function (value, element, param) {
    let isValid = true;

    param.push('#' + $(element).attr('id'))
    $.each(param, function (index, item) {
        let val = $(item).val();
        if (val.length == 0) {
            isValid = false;
        }
    });

    return isValid;
});

validation = function (formID, rules, messages) {
    $(formID).validate({
        rules: rules,
        messages: messages,
    });
}
