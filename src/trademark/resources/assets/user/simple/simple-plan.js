const DATA_CHECKED = 1
const BANK_TRANSFER = 2
const PRODUCT_CHECKED = 1
const COMPLEDEVALUATION_FALSE = 0
class clsPlan {
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
        if (!checkResponseDeadlineOver && (typeof select01over != 'undefined' && checkRoute == select01over || typeof simple01over != 'undefined' && checkRoute == simple01over) ||
            nameScreen.includes(checkRoute)) {
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
        this.changeRegisterBeforeDeadline();
        this.ajaxGetInfoPayment();
        this.formSubmit();
        this.hide();
        this.changeRegisterProd();
        this.hiddenActualFeeAndTax();

        openAllFileAttach(trademarkDocument);
    }

    //==================================================================
    // Default hidden table when load
    //==================================================================
    hide() {
        $('.hidden_ext_period').hide()
        $('#hidden_cost_bank_transfer').hide()
        if (typeof flag != 'undefined' && flag == 'select_01_n') {
            $('#hidden_cost_service_base').hide();
        }
        let valueNation = $('#m_nation_id').find("option:selected").val();
        if (valueNation != 1) {
            $('#hidden_commission_tax').hide();
        } else {
            $('#hidden_commission_tax').show();
        }
        if ($('#register_before_deadline').is(':checked')) {
            $('#hidden_discount').show()
            $('.hidden_ext_period').show()
        } else {
            $('#hidden_discount').hide()
            $('.hidden_ext_period').hide()
        }
        if ($('input[name=payment_type]:checked').val() == BANK_TRANSFER) {
            $('#hidden_cost_bank_transfer').show()
        } else {
            $('#hidden_cost_bank_transfer').hide()
        }
    }

    //==================================================================
    // Handling change event of nation input.
    //==================================================================
    hiddenActualFeeAndTax() {
        $('#m_nation_id').on('change', function () {
            let valueNation = $('#m_nation_id').find("option:selected").val();
            if (valueNation != 1) {
                $('#hidden_commission_tax').hide();
            } else {
                $('#hidden_commission_tax').show();
            }
        })
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

            seft.ajaxGetInfoPayment();
        })
    }
    //==================================================================
    // Event Change Payment Type
    //==================================================================
    changeRegisterProd() {
        let seft = this;
        if (typeof flag != 'undefined') {
            $('input.register_prod_rdo').on('change', function () {
                seft.ajaxGetInfoPayment();
                if ($(this).is(':checked')) {
                    if ($(this).val() == PRODUCT_CHECKED) {
                        $(this).closest('tr').find('.fee_response_refusal').text('0' + labelPriceProduct + seft.numberFormat(costServiceAddProd) + '円')
                    } else  {
                        $(this).closest('tr').find('.fee_response_refusal').text('-')
                    }
                }

                $('#product_tbl').next('.error').remove()
                if ($('#product_tbl').length) {
                    let totalProdChoose = $('input.register_prod_apply:checked').length;
                    if(!totalProdChoose) {
                        $('#product_tbl').after('<div id="product_tbl-error" class="error">選択してください。</div>')
                    }
                }
            })

            $('input.register_prod_rdo').change()
        }
    }

    //==================================================================
    // Check Product
    //==================================================================
    checkProduct(prodId) {
        let seft = this;
        const dataCompledEvaluation = $(`.is_register_prod_${prodId}:checked`).data('completed_evaluation');
        if(dataCompledEvaluation != 'undefined' && dataCompledEvaluation == COMPLEDEVALUATION_FALSE){
            if ($(`.is_register_prod_${prodId}:checked`).val() == PRODUCT_CHECKED) {
                $('#fee_response_refusal_' + prodId).text('0' + labelPriceProduct + seft.numberFormat(costServiceAddProd) + '円')
            } else {
                $('#fee_response_refusal_' + prodId).text('-')
            }
        }
    }

    //==================================================================
    // Event Change Register Before Deadline
    //==================================================================
    changeRegisterBeforeDeadline() {
        let seft = this;
        $('#register_before_deadline').on('change', function () {
            if ($(this).is(':checked')) {
                $('#hidden_discount').show()
                $('.hidden_ext_period').show()
            } else {
                $('#hidden_discount').hide()
                $('.hidden_ext_period').hide()
            }
            seft.ajaxGetInfoPayment();
        })
    }

    //==================================================================
    // Get Data To Form
    //==================================================================
    getData(dataCost) {
        let isRegisterBeforeDeadline = $('#register_before_deadline').is(':checked') ? DATA_CHECKED : 0;
        let isRegister = [];
        let prodChecked = [];
        let numberProduct = [];

        if (typeof (prodIds) != "undefined" && prodIds !== null && typeof flag != 'undefined') {
            jQuery.map(prodIds, function (prodId) {
                let valueRegister = $(`.is_register_prod_${prodId}:checked`).val();

                isRegister.push(valueRegister);
            })
            prodChecked = isRegister.filter(obj => {
                if (obj == '1') {

                    return true;
                }

                return false
            }).length
            numberProduct = isRegister.filter(obj => {
                if (obj == '1' || obj == '0') {

                    return true;
                }

                return false
            }).length
        }

        let data = {
            is_register_before_deadline: isRegisterBeforeDeadline,
            cost_service_base: dataCost.cost_service_base,
            cost_service_add_prod: dataCost.cost_service_add_prod,
            extension_of_period_before_expiry: isRegisterBeforeDeadline == DATA_CHECKED ? dataCost.extension_of_period_before_expiry : 0,
            cost_prior_deadline_base: isRegisterBeforeDeadline == DATA_CHECKED ? dataCost.cost_prior_deadline_base : 0,
            cost_bank_transfer: $('input[name=payment_type]:checked').val() == BANK_TRANSFER ? dataCost.cost_bank_transfer : 0,
            number_distinct: dataCost.number_distinct ?? 0,
            print_fee: isRegisterBeforeDeadline == DATA_CHECKED ? dataCost.print_fee : 0,
            price_discount: isRegisterBeforeDeadline == DATA_CHECKED ? dataCost.price_discount : 0,
            prod_checked: prodChecked ?? [],
            flag: dataCost.flag ?? '',
            numberProduct: numberProduct ?? [],
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
            url: routeAjaxCalculatorCart,
            method: 'POST',
            data_type: 'json',
            data: data,
        }).done(function (res) {
            if (res) {
                seft.showInfo(res);
            }
        });
    }

    //==================================================================
    // Show Data Cart
    //==================================================================
    showInfo(res) {
        $('#cost_service_base').text(this.numberFormat(res.cost_service_base))
        $('#cost_service_add_prod').text(this.numberFormat(res.cost_service_add_prod));
        $('#extension_of_period_before_expiry').text(this.numberFormat(res.extension_of_period_before_expiry));
        $('#sub_total').text(this.numberFormat(res.sub_total));
        $('#commission').text(this.numberFormat(res.commission));
        $('#tax').text(this.numberFormat(res.tax));
        $('#print_fee').text(this.numberFormat(res.print_fee));
        $('#total_amount').text(this.numberFormat(res.total_amount));
        $('#number_payment').text(this.numberFormat(res.cost_service_base + res.cost_service_add_prod));
        $('#money_take_report').text(this.numberFormat(res.money_take_report));
        if (res.prod_checked) {
            $('.amount_product').text(res.prod_checked)
        } else {
            $('.amount_product').text(res.number_distinct)
        }
        $('.total_checked_table').text(res.total_checked_table)
        // Value Form Submit
        $("#cost_service_base_input").val(res.cost_service_base)
        // $("#cost_service_add_prod_input").val(res.cost_service_add_prod)
        $("#cost_bank_transfer_input").val(res.cost_bank_transfer)
        $("#extension_of_period_before_expiry_input").val(res.extension_of_period_before_expiry)
        $("#application_discount_input").val(res.application_discount)
        $("#subtotal_input").val(res.sub_total)
        $("#commission_input").val(res.commission)
        $("#tax_input").val(res.tax)
        $("#print_fee_input").val(res.print_fee)
        $("#total_amount_input").val(res.total_amount)
    }

    //==================================================================
    // Format Number
    //==================================================================
    numberFormat(num) {
        return new Intl.NumberFormat('en-us').format(Math.floor(num))
    }

    //==================================================================
    // Form Submit
    //==================================================================
    formSubmit() {
        $('#contents').on('click', 'input[type=submit]', function (e) {
            e.preventDefault();

            $('#product_tbl').next('.error').remove()
            if ($('#product_tbl').length) {
                if ($('input.register_prod_rdo').length > 0) {
                    let totalUnRegisterProd = $('input.register_prod_rdo[value=0]').length;
                    let totalUnRegisterProdChoose = $('input.register_prod_rdo:checked[value=0]').length;
                    if(totalUnRegisterProd == totalUnRegisterProdChoose) {
                        $('#product_tbl').after('<div id="product_tbl-error" class="error">選択してください。</div>')
                    }
                }
            }

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
}
new clsPlan()
