var Validation = () => {
    return {
        init: function () {
            let idOfJapan = $('#nation_japan_id').val()
            $('#form').validate({
                errorElement: 'div',
                errorClass: 'error-validate',
                focusInvalid: false,
                errorPlacement: function(error, element) {
                    if (element.attr("name") === 'payment_type') {
                        error.insertAfter(".ul_payment_type");
                    } else if(element.attr("name") === 'payer_type') {
                        error.insertAfter(".ul_payer_type");
                    } else if(element.attr("name") === 'postal_code') {
                        error.insertAfter(".wp_postal_code");
                    } else {
                        error.insertAfter(element);
                    }
                },
                rules: {
                    'payment_type': {
                        required: true
                    },
                    'payer_type': {
                        required: true
                    },
                    'm_nation_id': {
                        required: true
                    },
                    'payer_name': {
                        required: true,
                        isFullwidth: true,
                    },
                    'payer_name_furigana': {
                        required: true
                    },
                    'postal_code': {
                        required: () => {
                            return $('#m_nation_id').val() == idOfJapan;
                        },
                        isValidInfoPostalCode: true
                    },
                    'm_prefecture_id': {
                        required: () => {
                            return $('#m_nation_id').val() == idOfJapan;
                        }
                    },
                    'address_second': {
                        required: () => {
                            return $('#m_nation_id').val() == idOfJapan;
                        },
                        // isValidInfoAddress: true,
                        isFullwidth: true,
                    },
                    'address_three': {
                        required: true,
                        isValidInfoAddress: () => {
                            return $('#m_nation_id').val() == idOfJapan;
                        },
                    }
                },
                messages: {
                    'payment_type': {
                        required: errorMessageIsValidRequired
                    },
                    'payer_type': {
                        required: errorMessageIsValidRequired
                    },
                    'm_nation_id': {
                        required: errorMessageIsValidRequired
                    },
                    'payer_name': {
                        required: errorMessageIsValidRequired,
                        isFullwidth: errorMessageIsValidInfoAddressFormat
                    },
                    'payer_name_furigana': {
                        required: errorMessageIsValidRequired
                    },
                    'postal_code': {
                        required: errorMessageIsValidRequired,
                        isValidInfoPostalCode: errorMessageIsValidInfoPostalCode
                    },
                    'm_prefecture_id': {
                        required: errorMessageIsValidRequired
                    },
                    'address_second': {
                        required: errorMessageIsValidRequired,
                        // isValidInfoAddress: errorMessageIsValidInfoAddressFormat
                        isFullwidth: errorMessageIsValidInfoAddressFormat
                    },
                    'address_three': {
                        required: errorMessageIsValidRequired,
                        isValidInfoAddress: errorMessageIsValidInfoAddressFormat
                    }
                }
            });
        },
    }
}

Validation().init();
