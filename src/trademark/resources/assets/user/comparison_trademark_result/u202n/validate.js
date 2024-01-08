let rules = {}
let messages = {}
class ValidationForm {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
     * Do load class
     */
    doLoad() {
        this.initValidate()
    }

    /**
     * Init validate
     */
    initValidate() {
        this.fileValidate()
        validation('#form', rules, messages)
    }

    /**
     * Validate for file upload
     */
    fileValidate() {
        $('input[type=file], textarea:not(hidden)').each(function (key, item) {
            //if item is textarea answer
            if($(item).hasClass('textarea-answer')) {
                rules[$(item).attr('name')] = {
                    required: true,
                    maxlength: 500
                }

                messages[$(item).attr('name')] = {
                    required: errorMessageRequired,
                    maxlength: errorMessageMaxLength500,
                }
            }
        })
    }
}

new ValidationForm()
