
class clsValidation {
    rules = {}
    message = {}

    /**
     * Init validation for form.
     * @param {String} formID
     * @param {Object} config
     * @param {Boolean} override
     */
    constructor(formID = '#form', config = { rules: {}, messages: {}}, override = false) {
        const self = this
        this.formID = formID
        $(function(){
            self.doInitValidation(config, override);
        }, false)
    }

    /**
     * Do init validate for form
     * @param {String} formID
     * @param {Object} config
     * @param {Boolean} override
     */
    doInitValidation(config, override) {
        try {
            if(override) {
                // If rule, message is empty then return error
                if(config && this.isEmpty(config.rules)) {
                    throw 'rules is empty!';
                } else if(config && this.isEmpty(config.messages)) {
                    throw 'messages is empty!';
                }
                // Is override
                this.rules = config.rules
                this.messages = config.messages
            } else {
                // Is merge both old and new
                this.rules = Object.assign({ ...this.rules }, config.rules)
                this.messages = Object.assign({ ...this.messages }, config.messages)
            }

            if($(this.formID).length) {
                validation(this.formID, this.rules , this.messages)
            }else {
                throw "Form isn't exist!"
            }

        } catch (error) {
            console.error(error)
        }
    }

    /**
     * Check empty of object
     * @param {*} obj
     * @returns {Boolean}
     */
    isEmpty(obj) {
        for(var prop in obj) {
            if(Object.prototype.hasOwnProperty.call(obj, prop)) {
                return false;
            }
        }

        return JSON.stringify(obj) === JSON.stringify({});
    }
}
