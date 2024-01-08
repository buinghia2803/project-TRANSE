const BANK_TRANSFER = 2
const NATION_JP = 1
class clsExtensionPeriod {
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
        if (countRegisterTrademarkRenewals || !checkResponseDeadline) {
            this.initValidation();
        }
    }

    //==================================================================
    // Init Validation Form
    //==================================================================
    initValidation() {
        this.rules = { ...paymentRule }
        this.messages = { ...paymentMessage }

        new clsValidation('#form', { rules: this.rules, messages: this.messages }, true);
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.changePaymentTye();
        this.ajaxGetInfoPayment();
        this.hide();
        this.formSubmit();
        this.changeNation();

        openAllFileAttach(trademarkDocument, '#openAllFileAttach');
    }

    //==================================================================
    // Form Submit
    //==================================================================
    formSubmit() {
        $('body').on('click', 'input[type=submit]', function (e) {
            e.preventDefault();

            let form = $('#form');
            form.valid();
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {
                form.find('input[name=submit_type]').val($(this).data('submit'));
                if ($(this).attr('data-submit') == redirectToQuote) {
                    form.attr('target' ,'_blank');
                    form.submit()
                    loadingBox('close');
                } else {
                    form.attr('target' ,'_self');
                    form.submit();
                }
            } else {
                let firstError = hasError.first();
                scrollToElement(firstError, -100);
                return false;
            }
        });
    }

    //==================================================================
    // Hide Default
    //==================================================================
    hide() {
        if ($('input[name=payment_type]:checked').val() == BANK_TRANSFER) {
            $('#hidden_cost_bank_transfer').show()
        } else {
            $('#hidden_cost_bank_transfer').hide()
        }
        let valueNation = $('#m_nation_id').find("option:selected").val();
        if (valueNation != NATION_JP) {
            $('#hidden_commission_tax').hide();
        } else {
            $('#hidden_commission_tax').show();
        }
    }

    //==================================================================
    // Event Change Nation
    //==================================================================
    changeNation() {
        $('#m_nation_id').on('change', function () {
            if ($(this).val() == NATION_JP) {
                $('#hidden_commission_tax').show();
            } else {
                $('#hidden_commission_tax').hide();
            }
        })
    }

    //==================================================================
    // Get Data To Form
    //==================================================================
    getData(dataCost) {
        let data = {
            cost_service: dataCost.cost_service,
            tax: dataCost.tax,
            payment_type: dataCost.payment_type,
            cost_bank_transfer: $('input[name=payment_type]:checked').val() == BANK_TRANSFER ? dataCost.cost_bank_transfer : 0,
        }

        return data
    }

    //==================================================================
    // Call Ajax Get Data Info Payment
    //==================================================================
    ajaxGetInfoPayment() {
        let data = this.getData(dataCost);
        let seft = this;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: routeAjax,
            method: 'POST',
            data_type: 'json',
            data: data,
            async: false
        }).done(function (res) {
            if (res) {
                if (res.total == 0) {
                    $('.hidden_common').hide();
                } else {
                    $('.hidden_common').show();
                }
                seft.showInfo(res);
            }
        });
    }

    //==================================================================
    // Show Data Cart
    //==================================================================
    showInfo(res) {
        $('.cost_service').text(this.numberFormat(res.cost_service_base))
        $('#cost_bank_transfer').text(this.numberFormat(res.cost_bank_transfer))
        $('#commission').text(this.numberFormat(res.commission))
        $('#tax').text(this.numberFormat(res.tax))
        $('.cost_print_fee').text(this.numberFormat(res.print_fee))
        $('#total_amount').text(this.numberFormat(res.total_amount))
        $('#tax_percent').text(this.numberFormat(res.tax_percent))
    }

    //==================================================================
    // Format Number
    //==================================================================
    numberFormat(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    //==================================================================
    // Event Change Payment Type
    //==================================================================
    changePaymentTye() {
        let seft = this;
        $('input[name=payment_type]').on('change', function () {
            if ($(this).val() == BANK_TRANSFER) {
                $('#hidden_cost_bank_transfer').show()
            } else {
                $('#hidden_cost_bank_transfer').hide()
            }
            dataCost.payment_type = $(this).val()
            seft.ajaxGetInfoPayment();
        })

        $('input[name=payment_type]:checked').change();
    }
}

new clsExtensionPeriod()
