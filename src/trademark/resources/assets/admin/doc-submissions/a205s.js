const IS_REJECT = 1;
const IS_CANCEL = 1;
class clsSupevisor {
    constructor() {
        const self = this
        // this.initVariable()
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.init();
        this.initValidation();
            this.removeValidate();
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

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.checkConfirm();
        this.formSubmit();
        if(flag == 'a205s'){
            this.removeValidate()
        }
    }

    //==================================================================
    // Form Submit
    //==================================================================
    formSubmit() {
        let form = $('#form');
        $('body').on('click', 'input[type=submit]', function (e) {
            e.preventDefault();
            if ($('body').hasClass('error')) {
                return false;
            }
            form.find('input[name=submit_type]').val($(this).data('submit'));
            form.submit();

        });
    }

    //==================================================================
    // Form Remove Validate
    //==================================================================
    removeValidate() {
        let form = $('#form');
        $('.clear_validate').on('click', function (e) {
            e.preventDefault();
            form.find('input[name=submit_type]').val($(this).data('submit'));
            form.unbind("submit").submit();
            form.submit();
        })
    }

    //==================================================================
    // Check Data
    //==================================================================
    checkConfirm() {
        if (docSubmission && docSubmission.is_reject == IS_REJECT && flag != 'a205s') {
            $.confirm({
                title: '',
                content: messagePopup,
                buttons: {
                    ok: {
                        text: '戻る',
                        btnClass: 'btn-blue',
                        action: function () {
                            loadingBox('open');
                            window.location.href = urlA000top
                        }
                    }
                }
            });
        }

        if (docSubmission && docSubmission.is_confirm == IS_CANCEL && flag != 'a205s') {
            $.confirm({
                title: '',
                content: messageIsConfirm,
                buttons: {
                    ok: {
                        text: '戻る',
                        btnClass: 'btn-blue',
                        action: function () {
                            loadingBox('open');
                            window.location.href = urlA000top
                        }
                    }
                }
            });
        }
    }
}

new clsSupevisor()
