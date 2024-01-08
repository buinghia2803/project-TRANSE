let rules = {}
if (paymentRule != undefined) {
    rules = {...rules, ...paymentRule, ...trademarkInfoRules};
}

let messages = {}
if (paymentMessage != undefined) {
    messages = {...messages, ...paymentMessage, ...trademarkInfoMessages};
}

var Validation = () => {
    return {
        init: function () {
            $('#form').validate({
                errorElement: 'div',
                errorClass: 'error-validate',
                focusInvalid: true,

                errorPlacement: function(error, element) {
                    if (element.attr("name") === 'type_precheck') {
                        error.insertAfter(".error_type_precheck");
                    } else if(element.attr("name") === 'm_product_ids[]') {
                        error.insertAfter(".error-m-product-id");
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
                    } else if(element.attr("name") === 'type_trademark') {
                        error.insertAfter(".show-error");
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
