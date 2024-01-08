const NATION_JP = 1;
class clsRegistrationChangeAddress {
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
        const paymentRule = {
            'modify_name': {
                required: true,
                isFullwidth: true,
                maxlength: 50,
            },
            'm_nation_id_select': {
                required: true
            },
            'm_prefecture_id_select': {
                required: () => {
                    return $('#m_nation_id').val() == NATION_JP;
                }
            },
            'address_second_input': {
                required: () => {
                    return $('#m_nation_id').val() == NATION_JP;
                },
                isValidInfoAddress: true
            },
            'address_three_input': {
                required: true,
                isValidInfoAddress: true
            },
        };

        const paymentMessage = {
            'modify_name': {
                required: errorMessagePaymentRequired,
                isFullwidth: errorMessagePaymentCharacterPayer,
                maxLength: errorMessagePaymentCharacterPayer
            },
            'm_nation_id_select': {
                required: errorMessagePaymentRequired
            },
            'm_prefecture_id_select': {
                required: errorMessagePaymentRequired
            },
            'address_second_input': {
                required: errorMessagePaymentRequired,
                isValidInfoAddress: errorMessagePaymentInfoAddressFormat
            },
            'address_three_input': {
                required: errorMessagePaymentRequired,
                isValidInfoAddress: errorMessagePaymentInfoAddressFormat
            },
        };
        this.rules = { ...paymentRule }
        this.messages = { ...paymentMessage }

        new clsValidation('#form', { rules: this.rules, messages: this.messages }, true);
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.modifyName();
        this.modifyAddress();
        this.checkAddressJp();
        this.attributeDefault();
        this.changeTypeAcc()
    }

    //==================================================================
    // Atribute Default
    //==================================================================
    attributeDefault(){
        if ($('select[name=m_nation_id_select]').val() == NATION_JP) {
            $('#address_jp').show();
        } else{
            $('#address_jp').hide();
        }

        $('.modify_name').prop('disabled', true);
        $('.modify_address').prop('disabled', true);
    }

    changeTypeAcc() {
        $('.modify_group').on('click', function (e) {
            $('input[name=type_acc]').val($(this).val())
        })
        $('.modify_user').on('click', function (e) {
            $('input[name=type_acc]').val($(this).val())
        })
    }

    //==================================================================
    // Event Click Modify Name
    //==================================================================
    modifyName() {
        $('#enable_name').on('click', function () {
            $('.modify_name').prop('disabled', false);
        })
        $('input[name=modify_name]').on('change keyup', function (e) {
            $('input[name=name]').val($(this).val())
        })
    }

    //==================================================================
    // Event Click Modify Address
    //==================================================================
    modifyAddress(){
        $('#enable_address').on('click', function () {
            $('.modify_address').prop('disabled', false);
            $('#m_nation_id').change()
        })
        $('#m_nation_id').on('change', function () {
            $('input[name=m_nation_id]').val($(this).val())
        })
        $('select[name=m_prefecture_id_select]').on('change', function () {
            $('input[name=m_prefecture_id]').val($(this).val())
        })
        $('input[name=address_second_input]').on('change', function () {
            $('input[name=address_second]').val($(this).val())
        })
        $('input[name=address_three_input]').on('change', function () {
            $('input[name=address_three]').val($(this).val())
        })
    }

    //==================================================================
    // Check Andress
    //==================================================================
    checkAddressJp(){
        $('#m_nation_id').on('change' , function(){
            if($(this).val() == NATION_JP){
                $('input[name=m_prefecture_id]').val($('select[name=m_prefecture_id_select]').val())
                $('#address_jp').show();
            } else {
                $('#address_jp').hide();
                $('input[name=m_prefecture_id]').val('')
                $('input[name=address_second]').val('')
            }
        })
    }
}
new clsRegistrationChangeAddress()
