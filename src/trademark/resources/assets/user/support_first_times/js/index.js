const maxProduct = 30;
class SupportFirsTime {
    constructor() {
        this.action = action
        this.invalid = true
        this.invalidRequire = true
        this.rules = {}
        this.messages = {}
        this.initValidate()
        this.clsCart = new clsCartProduct()
        const self = this

        // this.elBankTransferFee = $('.cost_bank_transfer_tr')
        this.elSubTotal = $('.subtotal input[name="subtotal"]')
        this.txtSubtotal = $('.subtotal strong')

        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
     * Init validate
     */
    initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        this.rules = {...paymentRule, ...trademarkInfoRules }
        this.messages = {...paymentMessage, ...trademarkInfoMessages }

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.doInitDefault()
        this.addProduct()
        this.doSubmit()
        this.changeProductName()
    }

    /**
     * Init variable or action when load.
     */
    doInitDefault() {
        // this.elBankTransferFee.addClass('d-none')
    }

    /**
     * Handling add new product.
     */
    addProduct() {
        $('#add_product_name').click(function () {
            const countInput = $('#product_name_form').find('.product_name').length
            // お考えの商品・サービス名は30個まで。文字制限は25文字（全角のみ）まで。
            if(countInput < maxProduct) {
                $('#product_name_form').append(`
                    <dd class="product_name">
                        <input type="text" name="product_names[]" class="em30" />
                    </dd>
                `)
            }
            if (countInput >= maxProduct - 1) {
                $('#add_product_name').css('display', 'none')
            }
        })
    }

    /**
     * Event change product name input
     */
    changeProductName() {
        const self = this
        $('body').on('change focusout', 'input[name*=product_names]', function() {
            $(this).closest('.product_name').find('.error').remove()

            if($(this).val().length > 0) {
                if($(this).val().length > 25) {
                    $(this).closest('.product_name').append(`
                        <div id="product_names-error" class="error">${errorMessageContentMaxLength25}</div>
                    `)
                }
            }
        })
    }

    /**
     * Check before submit
     */
    preSubmit() {
        $('input[name*=product_names]').change();

        let checkValueExists = false
        $('input[name*=product_names]').each(function() {
            if ($( this ).val()) {
                checkValueExists = true
            }
        })

        if(!checkValueExists) {
            $('.product_name').first().append(`<div id="product_names-error" class="error">${Common_E001}</div>`)
        }

        $('#form').valid();

        let has_error = $('#form').find('.notice:visible,.error:visible');
        if (has_error.length == 0) {
            $('#request_commission').attr('value', $('#commission').text().replaceAll(',', ''))
            $('#request_tax').attr('value', $('#tax').text().replaceAll(',', ''))
            $('#request_tax').attr('value', $('#tax').text().replaceAll(',', ''))
            $('input[name=subtotal]').attr('value', $('#sub_total').text().replaceAll(',', ''))
            $('#form').submit()
        } else {
            let firstError = has_error.first();
            window.scroll({
                top: firstError.offset().top - 100,
                behavior: 'smooth'
            });
        }
    }


    /**
     * Handling submit event
     */
    doSubmit() {
        const self = this
        $('.btn_save_temp').on('click', function() {
            const currentAction =  self.action
            $('#form').attr('action', currentAction + '?redirect=anken-top')
            self.preSubmit()
        });

        $('.btn_save_quote').on('click', function () {
            const currentAction =  self.action
            $('#form').attr('action', currentAction + '?redirect=quote')
            $('#form').attr('target' ,'_blank');
            self.preSubmit()
            loadingBox('close');
            $('#form').attr('target' ,'_self');
        })

        $('.btn_submit_payment').on('click', function () {
            self.preSubmit()
        })
    }
}

new SupportFirsTime()
