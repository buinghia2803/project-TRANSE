class ReConfirmFreeHistory {
    constructor() {
        this.initValidate();
    }

    /**
     * Init validate
     */
    initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        this.rules = {
            'comment_free02': {
                maxlength: 1000,
            },
        }

        this.messages = {
            'comment_free02': {
                maxlength: errorMessageMaxLength1000,
            },
        }

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }
}

new ReConfirmFreeHistory()
