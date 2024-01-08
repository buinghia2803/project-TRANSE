class ReSupervisor {
    constructor() {
        this.initValidate();
        this.changeDescriptionDocumentsMiss();
        this.onClickSubmit();
    }

    /**
     * Init validate
     */
    initValidate() {
        // common203Rule, common203Message is constant in file common
        this.rules = {
            'sending_docs_deadline': {
                required: true,
            },
            'content': {
                maxlengthTextarea: 1000,
            },
        }

        this.messages = {
            'sending_docs_deadline': {
                required: errorMessageRequiredDocDeadline,
                min: errorMessageMinDocDeadline,
                max: errorMessageMinDocDeadline,
            },
            'content': {
                maxlengthTextarea: errorMessageMaxLength1000,
            },
        }

        new clsValidation('#form', {rules: this.rules, messages: this.messages})
    }

    changeDescriptionDocumentsMiss() {
        $('body').on('change keyup', '[data-description_documents_miss]', function (e) {
            e.preventDefault();
            let value = $(this).val();

            $(this).parent().find('.error, .notice').remove();

            if (value.length == 0) {
                $(this).after(`<div class="error mt-0">${errorMessageRequired}</div>`);
            } else if(value.length > 255) {
                $(this).after(`<div class="error mt-0">${errorMessageMaxLength255}</div>`);
            }
        })
    }

    onClickSubmit() {
        $('body').on('click', 'input[type=submit]', function (e) {
            const form = $('#form');

            $('[data-description_documents_miss]').change();

            let has_error = form.find('.notice:visible,.error:visible');
            if (has_error.length == 0 && form.valid()) {
                form.submit();
            } else {
                let firstError = has_error.first();
                window.scroll({
                    top: firstError.offset().top - 100,
                    behavior: 'smooth'
                });

                return false;
            }
        });
    }
}

new ReSupervisor;
