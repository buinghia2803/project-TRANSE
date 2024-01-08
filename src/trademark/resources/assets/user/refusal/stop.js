const DATA_CHECKED = 1
const BANK_TRANSFER = 2
class clsStop {
    constructor() {
        const self = this
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
    }

    /**
     * Init validate
     */
    initValidation() {
        this.rules = {
            reason_cancel: {
                maxlength: 255,
            }
        }
        this.messages = {
            reason_cancel: {
                maxlength: Common_E031,
            }
        }

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
    }
}
new clsStop()
