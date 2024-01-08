let rules = {
    'type_trademark': {
        required: true
    },
    'name_trademark': {
        required: true,
        maxlength: 30,
        regexSpecialCharacter: true,
        invalidFullWidthCharacter: 30
    },
    'image_trademark': {
        formatFile: true,
        formatFileSize: 3
    },
    'reference_number': {
        maxlength: 30,
    },
    'product_name[]': {
        required: true,
        maxlength: 255
    }
}
if (paymentRule != undefined) {
    rules = {...rules, ...paymentRule};
}

let messages = {
    'type_trademark': {
        required: errorMessageIsValidRequired
    },
    'name_trademark': {
        required: errorMessageIsValidRequired,
        regexSpecialCharacter: errorMessageTrademarkNameInvalid,
        invalidFullWidthCharacter: errorMessageInvalidCharacter
    },
    'image_trademark': {
        required: errorMessageIsValidRequired,
        formatFile: errorMessageInvalidFormatFile,
        formatFileSize: errorMessageInvalidFormatFile
    },
    'reference_number': {
        // required: errorMessageIsValidRequired,
        invalidFullWidthCharacter: errorMessageInvalidCharacterRefer
    },
    'product_name[]': {
        required: errorMessageIsValidRequired,
        maxlength: errorMessageContentMaxLength
    }
}
if (paymentMessage != undefined) {
    messages = {...messages, ...paymentMessage};
}

var Validation = () => {
    return {
        init: function () {
            $('#form').validate({
                errorElement: 'div',
                errorClass: 'notice',
                focusInvalid: true,
                errorPlacement: function(error, element) {
                    if (element.attr("name") == 'type_trademark') {
                        error.insertAfter(".radio-group");
                    } else if (element.attr("name") == 'payment_type') {
                        error.insertAfter(".ul_payment_type");
                    } else if (element.attr("name") == 'payer_type') {
                        error.insertAfter(".ul_payer_type");
                    } else if(element.attr("name") == 'postal_code') {
                        error.insertAfter(".wp_postal_code");
                    } else {
                        error.insertAfter(element);
                    }
                },
                rules: rules,
                messages: messages,
            });
        },
    }
}

Validation().init();
