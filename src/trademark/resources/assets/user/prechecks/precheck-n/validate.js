let rules = {
    'type_precheck': {
        required: true,
    },
    'm_product_choose[]': {
        required: true
    },
}
if (paymentRule != undefined) {
    rules = {...rules, ...paymentRule};
}

let messages = {
    'type_precheck': {
        required: errorMessageIsValidRequiredRadioSelect
    },
    'm_product_choose[]': {
        required: errorMessageIsValidRequiredRadioSelect
    },
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
                focusInvalid: false,
                errorPlacement: function(error, element) {
                    if (element.attr("name") === 'type_precheck') {
                        error.insertAfter(".error_type_precheck");
                    } else if(element.attr("name") === 'payer_type') {
                        error.insertAfter(".ul_payer_type");
                    } else if(element.attr("name") === 'postal_code') {
                        error.insertAfter(".wp_postal_code");
                    } else if (element.attr("name") === 'payment_type') {
                        error.insertAfter(".ul_payment_type");
                    } else if(element.attr("name") === 'payer_type') {
                        error.insertAfter(".ul_payer_type");
                    } else if(element.attr("name") === 'postal_code') {
                        error.insertAfter(".wp_postal_code");
                    }  else if(element.attr("name") === 'm_product_choose[]') {
                        error.insertAfter(".error-product");
                    } else {
                        error.insertAfter(element);
                    }
                },
                rules: rules,
                messages: messages
            });
        },
    }
}

Validation().init();
