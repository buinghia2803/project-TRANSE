class clsInputNumber {
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
    }

    //==================================================================
    // Init Validation Form
    //==================================================================
    initValidation() {
        const localRule = {
            'date_register': {
                required: true,
            },
            'register_number': {
                required: true,
                maxlength: 255,
            },
        };

        const localMessage = {
            'date_register': {
                required: errorMessageRequired,
            },
            'register_number': {
                required: errorMessageRequired,
                maxlength: errorMessageFormatErorr,
            },
        };
        this.rules = { ...localRule }
        this.messages = { ...localMessage }

        new clsValidation('#form', { rules: this.rules, messages: this.messages }, true);
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.checkDateRegister();
        this.checkDefault();
        this.formSubmit();
    }

    //==================================================================
    // Check Data Default
    //==================================================================
    checkDefault() {
        $(`input[name='date_register']`).change();
    }

    //==================================================================
    // Event Check Date Register
    //==================================================================
    checkDateRegister() {
        $('#date_register').on('change', function () {
            $('#message_error').remove();
            const date = $(this).val();

            const dateFormat = new Date(date);
            const dateAddDays = new Date(dateFormat.setDate(dateFormat.getDate() + 40));
            let month = ("0" + (dateAddDays.getMonth() + 1)).slice(-2);
            let day = ("0" + dateAddDays.getDate()).slice(-2);
            const formatDateAddDays = [dateAddDays.getFullYear(), month, day].join("-");

            if (date > now) {
                $('#erorr_date_register').append(`<p id="message_error" class="red">${errorMessageA303_E001}</p>`);
            } else if (date < sendDate) {
                $('#erorr_date_register').append(`<p id="message_error" class="red">${errorMessageA303_E002}</p>`);
            } else if (formatDateAddDays <= now) {
                $('#erorr_date_register').append(`<p id="message_error" class="red">${errorMessageA303_E006}</p>`);
            } else {
                $('#message_error').remove();
            }
        })
    }

    //==================================================================
    // Form Submit
    //==================================================================
    formSubmit() {
        $('#contents').on('click', 'input[type=submit]', function (e) {
            e.preventDefault();
            let form = $('#form');
            if ($('#message_error').text() == '') {
                $(`input[name='date_register']`).change();
                form.submit();
            }
        });
    }
}
new clsInputNumber()
