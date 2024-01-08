class a205Class {
    constructor() {
        const self = this
        this.initValidate()
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.setCodeSubmit();
        this.checkConditionScreen()
    }

    /**
     * Init validate.
     */
    initValidate() {
        let rules = {
            content: {
                maxlength: 1000
            }
        }
        let messages = {
            content: {
                maxlength: Common_E055
            }
        }

        //validate
        new clsValidation('#form', {rules: rules, messages: messages})
    }

    /**
     * Set code button when submit form
     */
    setCodeSubmit() {
        $('body').on('click', 'input[type=submit]', function (e) {
            e.preventDefault()
            if($(this).hasClass('submitSaveDraft')) {
                $('#code-submit').val(saveDraft)
            } else {
                $('#code-submit').val(saveSubmit)
            }
            $('#form').submit()
        });
    }

    /**
     * Check Condition Screen
     */
    checkConditionScreen() {
        if(flagRoleDocSubmission == flagRole2) {
            const form = $('#form').not('#form-logout');
            $('body').find('input, textarea, select , button').addClass('disabled').css('pointer-events', 'none');
            $('body').find('a').not('.logout, .home-page').addClass('disabled').css('pointer-events', 'none');
        }
    }
}

new a205Class()
