class clsA205Shu {
    constructor() {
        const self = this
        this.initValidation();
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.setCodeSubmit()
    }

    //==================================================================
    // Init Validation Form
    //==================================================================
    initValidation() {
        const localRules = {
            'check_submit': {
                required: true,
            },
            'content': {
                maxlengthTextarea: 1000
            },
        }
        const localMessages = {
            'check_submit': {
                required: messageRequireChecked,
            },
            'content': {
                maxlengthTextarea: messageMaxLength,
            },
        }
        this.rules = { ...localRules }
        this.messages = { ...localMessages }

        new clsValidation('#form', { rules: this.rules, messages: this.messages }, true);
    }

    /**
     * Set code button when submit form
     */
    setCodeSubmit() {
        $('body').on('click', 'input[type=submit]', function (e) {
            e.preventDefault()
            if ($('body').hasClass('error')) {
                return false
            }
            if($(this).hasClass('submitSaveDraft')) {
                $('#code-submit').val(saveDraft)
            } else {
                $('#code-submit').val(saveSubmit)
            }
            $('#form').submit()
        });
    }
}

new clsA205Shu();