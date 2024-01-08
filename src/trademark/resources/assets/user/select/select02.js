const BANK_TRANSFER = 2;
class Select02 {
    validation = null
    constructor() {
        const self = this
        $('body').off('change', 'select[name=m_nation_id]')
        this.initValidate()
        this.initVariables()
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        if(statusRegister == 2) {
            $.confirm({
                title: '',
                content: '登録可能性評価レポート＆拒絶理由通知対応お申込みが完了しました。',
                buttons: {
                    ok: {
                        text: 'OK',
                        btnClass: 'btn-blue',
                        action: function () {
                            loadingBox('open');
                            window.location.href = routeTop
                        }
                    }
                }
            });
        }
        this.changeChoiceProd()
        this.countProdChecked()
        this.setRankChoose()
        this.calculateTotalPriceTbl()
        this.changePaymentMethod()
        this.onChangeNation()
        this.onClickSubmit()

        openAllFileAttach(trademarkDocument);
    }

    /**
     * Init validate
     */
    initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        this.validation = new clsValidation('#form', { rules: paymentRule, messages: paymentMessage })
    }

    onClickSubmit() {
        $('body').on('click', '[type=submit]', function () {
            let id = $(this).attr('id');

            if (id == 'redirect_to_quote') {
                $('#form input[name=redirect_to]').attr('value', constQuote)
                $('#form input[name=redirect_to]').val(constQuote)
            }

            $('#product_tbl').next('.error').remove()
            if($('input.is_choice:checked').length <= 0) {
                $('#product_tbl').after(`<div id="product_tbl-error" class="error">${errorMessageCommon_E025}</div>`)
            }

            let form = $('#form');
            form.valid();
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {
                if (id == 'redirect_to_quote') {
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

    /**
     * Set Rank choose.
     */
    setRankChoose() {
        const self = this
        this.chooseACount = 0
        this.chooseBECount = 0
        $('input.is_choice:checked').each(function (idx, item) {
            const rank = $(item).data('rank');
            if(rank == 'A') {
                self.chooseACount += 1
            }else {
                self.chooseBECount += 1
            }
        })

        this.estBoxProdCountA.text(this.chooseACount)
        this.estBoxProdCountBE.text(this.chooseBECount)

        this.elChooseACount.val(this.chooseACount)
        this.elChooseACount.attr('value', this.chooseACount)
        this.elChooseBECount.val(this.chooseBECount)
        this.elChooseBECount.attr('value', this.chooseBECount)

        this.calSelectPlanBEPrice()
        this.calSubTotal()
        this.showHidePayerInfo()

        $('#product_tbl').next('.error').remove()
        if($('input.is_choice:checked').length <= 0) {
            $('#product_tbl').after(`<div id="product_tbl-error" class="error">${errorMessageCommon_E025}</div>`)
        }
    }

    /**
     * Calculate select plan B->E price
     */
    calSelectPlanBEPrice() {
        this.selectPlanBEPrice.text(this.fmPrice(
            +this.chooseBECount * +this.basePriceSelectPlanBE.text().replaceAll(',', '')
        ))
    }

    /**
     * calculating subtotal
     */
    calSubTotal() {
        const subtotal = +this.selectPlanAPrice.text().trim().replaceAll(',', '')
            + +this.selectPlanBEPrice.text().trim().replaceAll(',', '')
            + +this.bankTransferFee

        this.subTotal.text( this.fmPrice(subtotal))
        this.subTotalVal.val(subtotal)
        this.calTotalCommission()
        this.calTotalTax()

        this.subTotalVal.attr('value', subtotal)
    }

    /**
     * Calculating tax
     */
    calTotalTax () {
        const tax = +this.subTotal.text().replaceAll(',', '') - +this.commission.text().replaceAll(',', '');
        this.tax.text(this.fmPrice(tax))
        this.taxVal.val(tax)
        this.taxVal.attr('value', tax)
    }

    /**
     * Calculating Commission
     */
    calTotalCommission () {
        const commission = Math.round(+this.subTotal.text().replaceAll(',', '') / (1 + setting.value/100))
        this.commission.text(this.fmPrice(commission))
        this.commissionVal.val(commission)
        this.commissionVal.attr('value', commission)
    }

    /**
     * Count prod is checked
     */
    countProdChecked() {
        this.prodCount.html($('.is_choice:checked').length)
    }

    /**
     * Calculate total price
     */
     calculateTotalPriceTbl() {
        let totalPrice = 0;
        $('.is_choice:checked').each(function (key, item) {
            totalPrice += +$(item).closest('.lb_choice').find('.price_rank').text().replaceAll(',','')
        })

        this.totalPriceChoice.html(this.fmPrice(totalPrice))
    }

    /**
     * Format number
     * @param {String|Number} val
     */
    fmPrice(val) {
        return new Intl.NumberFormat('en-us').format(Math.floor(val))
    }

    /**
     * Handling change choice prod event.
     */
    changeChoiceProd() {
        const self = this
        $('body').on('change', '.is_choice', function () {
            if($(this).data('rank') == 'E') {
                $.confirm({
                    title: '',
                    content: 'ランクEの商品・サービス名が選択されました。',
                    buttons: {
                        ok: {
                            text: 'OK',
                            btnClass: 'btn-blue',
                            action: function () {}
                        }
                    }
                });
            }

            self.setRankChoose()
            self.countProdChecked()
            self.calculateTotalPriceTbl()
        })

        $('body').on('change', '.is_not_choice', function() {
            self.setRankChoose()
            self.countProdChecked()
            self.calculateTotalPriceTbl()
        })
    }

    /**
     * Handling change nation event.
     */
    onChangeNation() {
        const self = this
        this.mNationId.on('change', function () {
            if (+$(this).val() === self.NATION_JAPAN_ID) {
                $('.breakdown-real-fee').removeClass('d-none')
                $('.consumption_tax')
            } else {
                $('.breakdown-real-fee').addClass('d-none')
            }

            let nationID = $(this).val();
            if (nationID == JapanID) {
                $('.showHideInfoAddress').css('display', 'block');
                $('.taxt').removeClass('hidden')
            } else {
                $('.showHideInfoAddress').css('display', 'none');
                $('.taxt').addClass('hidden')
            }
        })

        this.mNationId.change()
    }

    /**
     * Handling change payment method event.
     */
    changePaymentMethod() {
        const self = this
        this.paymentType.on('change', function () {
            if($('.payment_type:checked').val() == BANK_TRANSFER) {
                self.costBankTransferTr.removeClass('d-none')
                self.bankTransferFee = paymentFee.cost_service_base
            }else {
                self.costBankTransferTr.addClass('d-none')
                self.bankTransferFee = 0
            }
            self.calSubTotal()
        }).change()
    }

    showHidePayerInfo() {
        let subTotal = this.subTotal.text().replaceAll(',', '');
        if (subTotal == 0) {
            $('.payer-info-box').addClass('hidden');
            $('.estimateBox').addClass('hidden');
        } else {
            $('.payer-info-box').removeClass('hidden');
            $('.estimateBox').removeClass('hidden');
        }
    }

    /**
     * Init variable
     */
    initVariables() {
        this.NATION_JAPAN_ID = 1
        this.chooseACount = 0
        this.chooseBECount = 0
        this.bankTransferFee = 0
        this.elChooseACount = $('#chooseACount')
        this.elChooseBECount = $('#chooseBECount')
        this.prodCount = $('#prod_count')
        this.totalPriceChoice = $('#total_prod_price')
        this.costBankTransferTr = $('.cost_bank_transfer_tr')
        this.costBankTransferSpan = $('.cost_bank_transfer_span')
        this.paymentType = $('.payment_type')
        this.mNationId = $('#m_nation_id')
        this.estBoxProdCountA = $('#est_box_prod_count_A')
        this.estBoxProdCountBE = $('#est_box_prod_count_B_E')
        this.selectPlanAPrice = $('#select_plan_A_price')
        this.selectPlanBEPrice = $('#select_plan_B_E_price')
        this.basePriceSelectPlanA = $('#base_price_select_plan_A')
        this.basePriceSelectPlanBE = $('#base_price_select_plan_B_E')
        this.costServiceBase = $('#cost_service_base')
        this.costServiceAddProd = $('#cost_service_add_prod')

        this.subTotal = $('#sub_total_text')
        this.commission = $('#commission')
        this.tax = $('#tax')

        this.subTotalVal = $('#subtotal')
        this.commissionVal = $('#commission_val')
        this.taxVal = $('#tax_val')
    }
}

new Select02();
