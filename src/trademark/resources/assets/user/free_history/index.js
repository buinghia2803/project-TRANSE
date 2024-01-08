const maxProduct = 30;
class SupportFirsTime {
    constructor() {
        this.initValidate();
        this.onClickSubmit();
        this.onChangePaymentType();
        this.onChangeNation();
        this.setCart();
    }

    /**
     * Init validate
     */
    initValidate() {
        const localRules = {
            'content_answer': {
                maxlength: 1000,
            },
        }

        const localMessages = {
            'content_answer': {
                maxlength: errorMessageMaxLength,
            },
        }

        if (typeof paymentRule !== 'undefined') {
            this.rules = { ...paymentRule, ...localRules }
            this.messages = { ...paymentMessage, ...localMessages }
        } else {
            this.rules = { ...localRules }
            this.messages = { ...localMessages }
        }

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    formatPrice(number, unit = '円') {
        number = new Intl.NumberFormat('ja-JP', {}).format(Math.floor(number));

        return number + unit;
    }

    setCart() {
        let priceService = priceData.priceService;
        let priceServiceFee = priceData.priceServiceFee;

        let priceBankTransfer = priceData.priceBankTransfer;
        let priceBankTransferFee = priceData.priceBankTransferFee;

        let actualFee = 0;

        // price_service
        $('.price_service').find('.price').html(this.formatPrice(priceServiceFee));
        actualFee += priceService;

        // Set cost_bank_transfer_fee
        $('.cost_bank_transfer_fee').find('.price').html(this.formatPrice(priceBankTransferFee));

        // Show/Hide cost_bank_transfer_fee
        let paymentType = $('input[name=payment_type]:checked').val();
        if (paymentType != undefined && paymentType == BANK_TRANSFER) {
            $('.cost_bank_transfer_fee').removeClass('hidden');
            actualFee += priceBankTransfer.base_price;
        } else {
            $('.cost_bank_transfer_fee').addClass('hidden');
            priceBankTransferFee = 0;
        }

        // Set subtotal_fee
        let priceSubtotal = priceServiceFee + priceBankTransferFee;
        $('.subtotal_fee').find('.price').html(this.formatPrice(priceSubtotal));

        $('.actual_fee').html(this.formatPrice(actualFee));

        let taxPercent = setting.value;
        let taxFee = (actualFee * taxPercent) / 100;
        $('.tax_fee').html(this.formatPrice(taxFee));

        let payerTypeNationId = $('[name=m_nation_id]').val();
        if (payerTypeNationId == idNationJP) {
            $('.subtotal_tax_fee').removeClass('hidden');
        } else {
            $('.subtotal_tax_fee').addClass('hidden');
        }

        // Set total_amount_fee
        let priceTotalAmount = priceSubtotal;

        $('[name="payment[cost_service_base]"]').val(priceServiceFee);
        $('[name="payment[cost_bank_transfer]"]').val(priceBankTransferFee);
        $('[name="payment[subtotal]"]').val(priceSubtotal);
        $('[name="payment[commission]"]').val(actualFee);
        $('[name="payment[tax]"]').val(taxFee);
        $('[name="payment[total_amount]"]').val(priceTotalAmount);
    }

    onChangePaymentType() {
        const self = this
        $('body').on('change', '[name=payment_type]', function () {
            self.setInputData = false;
            self.setCart();
        });
    }

    onChangeNation() {
        self = this;

        $('body').on('change', '[name=m_nation_id]', function () {
            self.setInputData = false;
            self.setCart();
        });
    }

    onClickSubmit() {
        $('body').on('click', 'input[type=submit]:not(.logout), [data-submit]', function (e) {
            e.preventDefault();
            const form = $('#form');

            form.valid();

            let hasError = form.find('.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {

                let submitType = $(this).data('submit');
                form.find('input[name=submit_type]').val(submitType);

                if ($(this).attr('data-submit') == redirectToQuote) {
                    form.attr('target' ,'_blank');
                    form.submit()
                    loadingBox('close');
                } else {
                    form.attr('target' ,'_self');
                    form.submit();
                }
            } else {
                let firstError = form.find('.notice:visible,.error:visible').first();
                window.scroll({
                    top: firstError.offset().top - 100,
                    behavior: 'smooth'
                });
            }
        });
    }
}

new SupportFirsTime()
