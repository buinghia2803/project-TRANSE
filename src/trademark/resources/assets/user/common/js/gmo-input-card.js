class GMOInputCard {
    constructor() {
        const self = this
        this.initValidate()
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
    * Do load all action and element.
    */
    doLoad() {
        this.autoReplaceCardNum();
        this.changeExpireYear();
        this.returnBack();
        this.submit();
        this.checkValidateCVC()
    }

    /**
     * Auto replace card number
     */
    autoReplaceCardNum() {
        $('input[name=card_number]').off('focusout').on('focusout', function () {
            const val = $(this).val().replace(/[^\d]/g, "").replace(/(.{4})/g, '$1 ')
            $(this).val(val)
            $(this).attr('value', val)
        })
    }

    /**
     * Return back
     */
    returnBack() {
        // Go to back
        $('#btn-return-back').click(function () {
            window.location.href = urlCommonPayment+ '?s=' + $('input[name=secret]').attr('value')
        })
    }

    /**
     * Submit form
     */
    submit() {
        $('#btn-submit').click(function () {
            $('#cardForm').submit()
        })
    }

    /**
     * If the key pressed is 'e', then prevent the default action and return.
     * @returns The function is being returned.
     */
    checkValidateCVC() {
        $('input[name=card_cvc]').on('keydown', function (e) {
            if(e.key == 'e') {
                e.preventDefault()
                return
            }
        })
    }
    /**
     * Change expire year.
     */
    changeExpireYear() {
        $('select[name=expire_year]').change(function() {
            let options = '';
            if ($(this).val() == currentYear) {
                for (const month of months) {
                    if (month >= currentMonth) {
                        options += `<option ${month == currentMonth ? 'selected' : ''} value="${month}">${month < 10 ? '0' + month : month}</option>`
                    }
                }
                $('select[name=expire_month]').html(options)
            } else {
                const oldVal = $('select[name=expire_month]').val()
                for (const month of months) {
                    options += `<option ${month == currentMonth || oldVal == month ? 'selected' : ''} value="${month}">${month < 10 ? '0' + month : month}</option>`
                }
                $('select[name=expire_month]').html(options)
            }
        })
        $('select[name=expire_year]').change()
    }

    /**
     * Init validate
     */
    initValidate() {
        const rules = {
            'card_number': {
                required: true,
                invalidCardFormat: true,
                checkMaxLengthCardNumber: 20,
                checkMinLengthCardNumber: 16,
            },
            'card_name': {
                required: true,
                validCardName: true,
                maxlength: 255,
            },
            'card_cvc': {
                required: true,
                maxlength: 4,
                minlength: 3,
            },
            'expire_year': {
                required: true,
            },
            'expire_month': {
                required: true,
            },
        }

        const messages = {
            'card_number': {
                required: errorMessageRequired,
                invalidCardFormat: errorMessageFormatError,
                checkMaxLengthCardNumber: errorMessageFormatError,
                checkMinLengthCardNumber: errorMessageFormatError,
            },
            'card_name': {
                required: errorMessageRequired,
                validCardName: errorMessageCardName,
                maxlength: errorMessageMaxLength255,
            },
            'card_cvc': {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength4,
                minlength: errorMessageFormatError,
            },
            'expire_year': {
                required: errorMessageRequired,
            },
            'expire_month': {
                required: errorMessageRequired,
            },
        }

        new clsValidation('#cardForm', { rules: rules, messages: messages })
    }
}

new GMOInputCard();
