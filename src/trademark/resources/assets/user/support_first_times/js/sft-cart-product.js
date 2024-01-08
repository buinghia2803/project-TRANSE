const BANK_TRANSFER = 2
class clsCartProduct {
    constructor() {
        $('body').off('change', 'select[name=m_nation_id]')
        const self = this
        this.initVariable()
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.init()
    }

    // initial when load
    init() {
        const self = this
        let countProductPackCheck = $('input[data-foo*=is_choice_user]:checked').length > 3 ? $('input[data-foo*=is_choice_user]:checked').length - 3 : 0;

        $('#product_selected_count').text(countProductPackCheck);
        $('input[name=reduce_number_distitions]').val(countProductPackCheck);
        $('input[name=reduce_number_distitions]').attr('value', countProductPackCheck);

        const countProd = $('input[name*=is_choice_user_]:checked').length
        $('#product_checked_count').text(countProd)

        self.itemCheckBox.change();
        self.cbPeriodRegister.change()

        this.updateCountDistintion();
        self.calculatePriceProductPack()

        const isCheckAll = this.itemCheckBox.length === $('body').find('input[data-foo="is_choice_user[]"]:checked').length;
        this.checkAll.prop('checked', isCheckAll)
        this.checkAll.off('change').on('change', function () {
            const isCheckAll = $(this).prop('checked');
            const countProd = $('input[name*=is_choice_user_]:checked').length
            $('#product_checked_count').text(countProd)
            $('body').find('input[data-foo="is_choice_user[]"]').prop('checked', isCheckAll)
            $('body').find('input[data-foo="is_choice_user[]"]').attr('checked', isCheckAll)
            $('#product-checked').text($('input[data-foo="is_choice_user[]"]:checked').length);
            self.itemCheckBox.change();
            self.cbPeriodRegister.change()
            self.updateCountDistintion()
            self.calculatePriceProductPack()
        })

        this.onChangeNation()
        this.onChangeCbProd()
        this.calculatePackChange()
        this.onChangePaymentType()
        this.setEach3UpPriceProd()
        this.onChangeCheckTimeRegis()
        this.onChangeMailingRegisCert()
        this.onChangePackageType()
    }

    onChangeCbProd() {
        // Check Box One
        const self = this
        $('body').on('change', 'input[data-foo="is_choice_user[]"]', function (value) {
            self.cbPeriodRegister.change()
            const countProd = $('input[name*=is_choice_user_]:checked').length
            $('#product_checked_count').text(countProd)
            $('#product-checked').text($('input[data-foo="is_choice_user[]"]:checked').length);
            const isCheckAll = self.itemCheckBox.length === $('input[data-foo="is_choice_user[]"]:checked').length;
            if(isCheckAll) {
                self.checkAll.prop('checked', isCheckAll)
                self.checkAll.attr('checked', isCheckAll)
            }

            self.updateCountDistintion()
            self.calculatePriceProductPack()
        })
    }

    //==================================================================
    // Handle event change of package type.
    //==================================================================
    onChangePackageType() {
        const self = this
        $('.package_type').on('change', function () {
            // 1: is pack A
            if($(this).is(':checked') && +$(this).val() == 1) {
                $('#tr_fee_submit_register_year').hide()
            } else {
                $('#tr_fee_submit_register_year').show()
            }

            if ($('.payment_type:checked').val() == BANK_TRANSFER) {
                $('.cost_bank_transfer_tr').removeClass('d-none')
            } else {
                $('.cost_bank_transfer_tr').addClass('d-none')
            }

            self.calculatePackChange()
            self.setEach3UpPriceProd()

            $('#period_registration').change()
            $('#is_mailing_register_cert').change()
        })

        $('.package_type:checked').change()
    }

    //==================================================================
    // Handling change event of nation input.
    //==================================================================
    onChangeNation() {
        const self = this
        $('#m_nation_id').on('change', function () {
            if (+$(this).val() === self.NATION_JAPAN_ID) {
                $('.breakdown-real-fee').css('display', 'block')
                $('.consumption_tax').removeClass('d-none')
            } else {
                $('.breakdown-real-fee').css('display', 'none')
                $('.consumption_tax').addClass('d-none')
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

        $('#m_nation_id').change()
    }

    //==================================================================
    // Handle event change of mailing register certification input.
    //==================================================================
    onChangeMailingRegisCert() {
        const self = this
        $('#is_mailing_register_cert').change(function () {
            $(this).closest('.eol').find('.error').remove();
            $(this).prop('disabled', false);

            if ($(this).is(':checked')) {
                const choosePackA = $('#package_a').is(':checked')

                if (choosePackA) {
                    $(this).prop('checked', false).prop('disabled', true);
                } else {
                    self.mailingRegisCertFee = Number($('#mailing_regis_cert_el').text().replaceAll(',', ''))
                    $('.tr_mailing_regis_cert_el').removeClass('d-none');
                }
            } else {
                self.mailingRegisCertFee = 0
                $('.tr_mailing_regis_cert_el').addClass('d-none')
            }
            self.calculatePriceProductPack()
            self.calculatePackChange()
        })

        $('#is_mailing_register_cert').change()
    }

    //==================================================================
    // Calculate total amount when change product of pack.
    //==================================================================
    calculatePriceProductPack() {
        const isCheckedPackageA = this.isPackageA.attr('checked');
        const isCheckedPackageB = this.isPackageB.attr('checked');
        const isCheckedPackageC = this.isPackageC.attr('checked');
        var chunks = [];
        for (var i = 0; i < $('input[data-foo="is_choice_user[]"]:checked').length;) {
            chunks.push($('input[data-foo="is_choice_user[]"]:checked').slice(i, i += 3));
        }

        const LengthProductChecked = chunks.length
        let countProductPackCheck = $('input[data-foo*=is_choice_user]:checked').length > 3 ? $('input[data-foo*=is_choice_user]:checked').length - 3 : 0;
        $('#product_selected_count').text(countProductPackCheck)
        $('input[name=reduce_number_distitions]').val(countProductPackCheck);
        $('input[name=reduce_number_distitions]').attr('value', countProductPackCheck);
        const valuePack = +$('.package_type:checked').val()
        switch (valuePack) {
            case 1:
                this.checkPriceProductPackage(isCheckedPackageA, this.priceProductAddPackageA, LengthProductChecked);
                break;
            case 2:
                this.checkPriceProductPackage(isCheckedPackageB, this.priceProductAddPackageB, LengthProductChecked);
                break;
            case 3:
                this.checkPriceProductPackage(isCheckedPackageC, this.priceProductAddPackageC, LengthProductChecked);
                break;
            default:
                break;
        }
    }

    //==================================================================
    // Handle event change of payment_type.
    //==================================================================
    onChangePaymentType() {
        const self = this
        $('.payment_type').on('change', function () {
            if ($(this).is(':checked') && +$(this).val() == BANK_TRANSFER) {
                self.bankTransferFee = +$('#cost_bank_transfer_span').text().replaceAll(',', '')
                $('.cost_bank_transfer_tr').removeClass('d-none')
            } else {
                self.bankTransferFee = 0
                $('.cost_bank_transfer_tr').addClass('d-none')
            }
            self.calculatePackChange()
        }).change()
    }

    //==================================================================
    // Calculate total amount when change product of pack.
    //==================================================================
    onChangeCheckTimeRegis() {
        const self = this
        this.cbPeriodRegister.on('change', function () {
            const isChecked = $(this).is(":checked");
            const choosePackA = $('#package_a').is(':checked')
            let feeSubmitRegisterYrs = 0
            let feeSubmitRegisterYrsUncheck = 0
            self.change5yrsTo10yrsFee = Math.floor(registerTermChange.base_price + (registerTermChange.base_price * setting.value / 100))

            $(this).closest('.eol').find('.error').remove();
            $(this).prop('disabled', false);
            if (isChecked && choosePackA) {
                $(this).prop('checked', false).prop('disabled', true);
            }

            if(self.cbPeriodRegister.is(':checked')) {
                feeSubmitRegisterYrs = feeSubmit.pof_1st_distinction_10yrs
                self.price5YearTo10Year = self.change5yrsTo10yrsFee
            } else {
                feeSubmitRegisterYrs = feeSubmit.pof_1st_distinction_5yrs
                self.price5YearTo10Year = 0
            }

            const commissionPrice = Math.floor((Number(self.cartPricePackage.text().replaceAll(',', '')) / (1 + setting.value/100))
            + (Number(self.cartPriceProductAddPackage.text().replaceAll(',', '')) / (1 + setting.value/100))
            + (self.bankTransferFee / (1 + setting.value /100))
            + (self.mailingRegisCertFee / (1 + setting.value /100))
            + (self.price5YearTo10Year / (1 + setting.value /100)))

            const _subTotal = Math.floor(Number(self.cartPricePackage.text().replaceAll(',', ''))
                + Number(self.cartPriceProductAddPackage.text().replaceAll(',', ''))
                + self.bankTransferFee
                + self.mailingRegisCertFee
                + self.price5YearTo10Year);

            self.subTotal.html(self.fmPice(_subTotal))

            self.tax.html(self.fmPice((self.subTotal.text().replaceAll(',', '') - commissionPrice)))
            self.commission.html(Intl.NumberFormat().format(commissionPrice));
            self.valSubtotal.val(_subTotal)
            self.valCommission.val(commissionPrice)
            self.valTax.val(_subTotal - commissionPrice)

            if (isChecked) {
                // if check 10年登録にする and checked product then add 5年登録を10年登録に期間変更 in modal 【お見積合計金額】
                if(!$('#package_a').is(':checked')) {
                    $('.tr_change_5yrs_to_10yrs').removeClass('d-none');
                    $('#change_5yrs_to_10yrs').text(self.fmPice(self.change5yrsTo10yrsFee))
                }
                $('#text_5yrs_10yrs').text(stampFee10yrs)
                $('#text_5yrs_10yrs2').text(stampFee10yrs)
                $('#regis5Ys1').html(self.fmPice(pricePackage[0][0].pof_1st_distinction_10yrs))
                $('#regis5Ys2').html(self.fmPice(pricePackage[0][0].pof_2nd_distinction_10yrs))

                self.feeSubmitRegisterYear.html(self.fmPice(self.distinctionGroup.length * feeSubmitRegisterYrs))
                self.valCost10YearOneDistintion.val(self.feeSubmitRegisterYear.text())
                self.totalAmount.html(
                    self.fmPice(_subTotal
                    + Number(self.feeSubmitRegister.text().replaceAll(',', ''))
                    + Number(self.feeSubmitRegisterYear.text().replaceAll(',', '')))
                )
                self.priceFeeSubmit5Year.text(self.fmPice(self.priceFeeSubmit10YearOld))
                self.valCost10YearOneDistintion.val(self.feeSubmitRegisterYear.html().replaceAll(',', ''))
                self.valTotalAmount.val(self.totalAmount.html().replaceAll(',', ''));
                self.valCost5YearOneDistintion.val('')

            } else {
                // if uncheck 10年登録にする then remove 5年登録を10年登録に期間変更 in modal 【お見積合計金額】
                $('.tr_change_5yrs_to_10yrs').addClass('d-none');
                $('#text_5yrs_10yrs').text(stampFee5yrs)
                $('#text_5yrs_10yrs2').text(stampFee5yrs)
                $('#regis5Ys1').html(self.fmPice(pricePackage[0][0].pof_1st_distinction_5yrs))
                $('#regis5Ys2').html(self.fmPice(pricePackage[0][0].pof_2nd_distinction_5yrs))
                if(self.distinctionGroup.length && self.priceFeeSubmit5Year) {
                    self.priceFeeSubmit5Year.html(self.fmPice(self.priceFeeSubmit5YearOld))
                    feeSubmitRegisterYrsUncheck = +self.priceFeeSubmit5Year.text().replaceAll(',', '') * self.distinctionGroup.length
                }
                self.feeSubmitRegisterYear.html(self.fmPice(feeSubmitRegisterYrsUncheck))
                self.totalAmount.html(
                    self.fmPice(_subTotal
                        + Number(self.feeSubmitRegister.text().replaceAll(',', ''))
                        + Number(self.feeSubmitRegisterYear.text().replaceAll(',', '')))
                )
                self.valCost5YearOneDistintion.val(self.feeSubmitRegisterYear.html().replaceAll(',', ''))
                self.valCost10YearOneDistintion.val('')
                self.valTotalAmount.val(self.totalAmount.html().replaceAll(',', ''));
            }

            $('#value_fee_submit_ole').attr('value', self.feeSubmitRegisterYear.text().replaceAll(',', ''))
        })

        this.cbPeriodRegister.change()
    }

    //==================================================================
    // Set price for Each3UpPriceProd
    //==================================================================
    setEach3UpPriceProd() {
        const val = Number($('.package_type:checked').attr('value'))
        switch (val) {
            case 1:
                $('#each_3_prod_pack').text(this.fmPice(this.priceProductAddPackageA))
                break;
            case 2:
                $('#each_3_prod_pack').text(this.fmPice(this.priceProductAddPackageB))
                break;
            case 3:
                $('#each_3_prod_pack').text(this.fmPice(this.priceProductAddPackageC))
                break;
        }
    }

    //==================================================================
    // Calculate subtotal, total amount when change pack.
    //==================================================================
    calculatePackChange() {
        const valuePack = +$('.package_type:checked').val()
        switch (valuePack) {
            case 1:
                $('.tr_change_5yrs_to_10yrs').addClass('d-none');
                $('#text_5yrs_10yrs').text(stampFee5yrs)
                this.cartNamePackageNote.text('（3商品名まで、商標出願）');
                this.price5YearTo10Year = 0
                this.priceFeeSubmit5Year.text(0)
                this.feeSubmitRegisterYear.text(0)
                this.calculatePrice(this.isPackageA, this.packageOtherA, this.namePackageA, this.pricePackageA, this.priceProductAddPackageA);
                break;
            case 2:
                $('#text_5yrs_10yrs').text(stampFee5yrs)
                this.cartNamePackageNote.text('（3商品名まで、商標出願＋登録手続）');
                this.updateCountDistintion()
                this.calculatePrice(this.isPackageB, this.packageOtherB, this.namePackageB, this.pricePackageB, this.priceProductAddPackageB);
                break;
            case 3:
                this.cartNamePackageNote.text('（3商品名まで、商標出願＋拒絶理由通知対応＋登録手続）');
                this.updateCountDistintion()
                this.calculatePrice(this.isPackageC, this.packageOtherC, this.namePackageC, this.pricePackageC, this.priceProductAddPackageC);
                break;
            default:
                this.calculatePrice()
                break;
        }
    }

    //==================================================================
    // Calculate subtotal, commission, products price , total price.
    //==================================================================
    fmPice(val) {
        return new Intl.NumberFormat('en-us').format(val)
    }

    //==================================================================
    // Calculate subtotal, commission, products price , total price.
    //==================================================================
    calculatePrice(packageEntry = null, packageOther = [], namePackageEntry = null, pricePackageEntry = null, priceProductAddPackageEntry = null) {
        if (packageEntry) {
            const isChecked = $(packageEntry).attr('checked', true);
            packageOther[0].attr('checked', false);
            packageOther[1].attr('checked', false);
            this.bankTransferFee = $('.payment_type_transfer').is(':checked') ? Number(this.bankTransferElement.text().replaceAll(',', '')) : 0
            var chunks = [];
            for (var i = 0; i < $('input[data-foo="is_choice_user[]"]:checked').length;) {
                chunks.push($('input[data-foo="is_choice_user[]"]:checked').slice(i, i += 3));
            }
            if (isChecked) {
                this.cartNamePackage.html(namePackageEntry)
                this.cartPricePackage.html(this.fmPice(pricePackageEntry))
                const eachThreeProdFee = this.fmPice(priceProductAddPackageEntry * (chunks.length - 1))
                if (chunks.length > 1) {
                    this.cartPriceProductAddPackage.html(eachThreeProdFee)
                    this.valCostServiceAddProd.val(priceProductAddPackageEntry);
                    this.valCostServiceAddProd.attr('value', priceProductAddPackageEntry);
                }

                const commissionPrice = Math.floor((Number(this.cartPricePackage.text().replaceAll(',', '')) / (1 + setting.value/100))
                    + (Number(this.cartPriceProductAddPackage.text().replaceAll(',', '')) / (1 + setting.value/100))
                    + (this.bankTransferFee / (1 + setting.value /100))
                    + (this.mailingRegisCertFee / (1 + setting.value /100))
                    + (this.price5YearTo10Year / (1 + setting.value /100)))

                const _subtotal = Math.floor(Number(this.cartPricePackage.text().replaceAll(',', ''))
                    + Number(this.cartPriceProductAddPackage.text().replaceAll(',', ''))
                    + this.bankTransferFee
                    + this.mailingRegisCertFee
                    + this.price5YearTo10Year)

                this.subTotal.text(this.fmPice(_subtotal))

                this.tax.html(this.fmPice((this.subTotal.text().replaceAll(',', '') - commissionPrice)))
                this.commission.html(Intl.NumberFormat().format(commissionPrice));

                this.totalAmount.html(this.fmPice(
                    _subtotal
                    + Number(this.feeSubmitRegister.text().replaceAll(',', ''))
                    + Number(this.feeSubmitRegisterYear.text().replaceAll(',', '')))
                )
                this.valCostServiceBase.val(pricePackageEntry);
                this.valSubtotal.val(this.subTotal.html().replaceAll(',', ''))
                this.valCommission.val(this.commission.html().replaceAll(',', ''))
                this.valTax.val(this.tax.html().replaceAll(',', ''));
                this.valCostPrintAppOneDistintion.val(this.feeSubmitRegister.html().replaceAll(',', ''))
                this.valCost5YearOneDistintion.val(this.feeSubmitRegisterYear.html().replaceAll(',', ''))
                this.valTotalAmount.val(this.totalAmount.html().replaceAll(',', ''));
            }
        } else {
            if(+$('.payment_type:checked').val() === BANK_TRANSFER) {
                this.commission.html(this.fmPice(+this.feeCommission.val() + +this.paymentFeeCommision.val()))
                this.tax.html(this.fmPice(+parseInt(this.feeTax.val()) + +this.paymentFeeTax.val()))
            }else {
                this.commission.html(this.fmPice(+this.feeCommission.val()))
                this.tax.html(this.fmPice(+parseInt(this.feeTax.val())))
                this.bankTransferFee = 0
            }
            const _subTotal = Math.floor(Number(this.cartPricePackage.text().replaceAll(',', ''))
                + Number(this.cartPriceProductAddPackage.text().replaceAll(',', ''))
                + this.bankTransferFee
                + this.mailingRegisCertFee
                + this.price5YearTo10Year);

            this.subTotal.html(this.fmPice(_subTotal))
            if (this.totalAmount) {
                this.totalAmount.html(this.fmPice(_subTotal
                    + Number(this.feeSubmitRegister.text().replaceAll(',', ''))
                    + Number(this.feeSubmitRegisterYear.text().replaceAll(',', '')))
                )
            }
        }

    }

    //==================================================================
    // Handle re-calculate price in payment modal.
    //==================================================================
    checkPriceProductPackage(packageCheck, priceProductAddPackage, lengthProductChecked) {
        this.bankTransferFee = $('.payment_type_transfer').is(':checked') ? Number(this.bankTransferElement.text().replaceAll(',', '')) : 0

        if (packageCheck) {
            if (lengthProductChecked > 1) {
                this.cartPriceProductAddPackage.html(this.fmPice(priceProductAddPackage * (lengthProductChecked - 1)))
            } else {
                this.cartPriceProductAddPackage.html(0)
            }
            const commissionPrice = Math.floor((Number(this.cartPricePackage.text().replaceAll(',', '')) / (1 + setting.value/100))
                + (Number(this.cartPriceProductAddPackage.text().replaceAll(',', '')) / (1 + setting.value/100))
                + (this.bankTransferFee / (1 + setting.value /100))
                + (this.mailingRegisCertFee / (1 + setting.value /100)))

            const _subTotal = Math.floor(Number(this.cartPricePackage.text().replaceAll(',', ''))
                + Number(this.cartPriceProductAddPackage.text().replaceAll(',', ''))
                + this.bankTransferFee
                + this.mailingRegisCertFee
                + this.price5YearTo10Year)

            this.subTotal.html(this.fmPice(_subTotal))

            this.totalAmount.html(this.fmPice(
                _subTotal
                + Number(this.feeSubmitRegister.text().replaceAll(',', ''))
                + Number(this.feeSubmitRegisterYear.text().replaceAll(',', '')))
            )

            this.tax.html(this.fmPice(this.subTotal.text().replaceAll(',', '') - commissionPrice))
            const pack = $('.package_type:checked').val()
            let priceProductAddPackageEntry = 0
            if(pack == 1) {
                priceProductAddPackageEntry = this.priceProductAddPackageA
            } else if(pack == 2) {
                priceProductAddPackageEntry = this.priceProductAddPackageB
            }else {
                priceProductAddPackageEntry = this.priceProductAddPackageC
            }
            this.valCostServiceAddProd.val(priceProductAddPackageEntry);
            this.valCostServiceAddProd.attr('value', priceProductAddPackageEntry);
            this.commission.html(this.fmPice(commissionPrice));
            this.valSubtotal.val(this.subTotal.html().replaceAll(',', ''))
            this.valCommission.val(this.commission.html().replaceAll(',', ''))
            this.valTax.val(this.tax.html().replaceAll(',', ''));
        }
    }

    /**
     * Update distinction
     */
    updateCountDistintion() {
        const self = this
        let feeSubmitRegisterYrs = 0
        const checkedProd = $('body').find('input[data-foo="is_choice_user[]"]:checked');
        if(checkedProd.length) {
            self.distinctionGroup = []
            checkedProd.each(function(key, item) {
                let distinctionID = $(item).closest('tr[data-distinction-id]').data('distinction-id');
                if (self.distinctionGroup.indexOf(distinctionID) == -1) {
                    self.distinctionGroup.push(distinctionID);
                }
            })
        } else {
            self.distinctionGroup = []
        }
        if(self.cbPeriodRegister.is(':checked')) {
            feeSubmitRegisterYrs = feeSubmit.pof_1st_distinction_10yrs
        } else {
            feeSubmitRegisterYrs = feeSubmit.pof_1st_distinction_5yrs
        }

        // if(self.distinctionGroup.length) {
        //     feeSubmitRegisterYrsUncheck = self.valSubmitOle
        // }

        let oneDivision = this.distinctionGroup.length > 0 ? 1 : 0
        $('#one_division').html(oneDivision)
        $('#total_distinction').html(this.distinctionGroup.length);
        $('#mDistintionPayment').html(this.distinctionGroup.length ? this.distinctionGroup.length - 1 : 0);
        $('#sumDistintion').html(self.distinctionGroup.length)
        $('#sumDistintion2').html(this.distinctionGroup.length)
        $('#sum_distintion').val(this.distinctionGroup.length)
        $('#sum_distintion').attr('value', this.distinctionGroup.length)
        if(pricePackage.length) {
            let feeRegist = (this.distinctionGroup.length ? pricePackage[0][2]['pof_1st_distinction_5yrs'] : 0) + ((this.distinctionGroup.length - 1 > 0 ? this.distinctionGroup.length - 1 : 0) * pricePackage[0][2]['pof_2nd_distinction_5yrs']);
            this.feeSubmitRegister.html(this.fmPice(feeRegist))
            $('input[name=cost_print_application_one_distintion]').val(feeRegist)
            $('input[name=cost_print_application_one_distintion]').attr('value', feeRegist)
        }

        this.feeSubmitRegisterYear.html(this.fmPice(this.distinctionGroup.length * feeSubmitRegisterYrs))
    }

    /**
    /**
     * Init variables of class.
     */
    initVariable() {
        // Check Box All
        this.bankTransferFee = 0
        this.mailingRegisCertFee = 0
        this.price5YearTo10Year = 0
        this.change5yrsTo10yrsFee = 0
        this.NATION_JAPAN_ID = 1
        this.distinctionGroup = [];
        this.checkAll = $('#check-all')
        this.itemCheckBox = $('body').find('input[data-foo="is_choice_user[]"]')
        this.feeTax  = $("input[name='tax']")
        this.feeCommission  = $("input[name='commission']")
        this.paymentFeeCommision  = $("input[name='payment_fee_commision']")
        this.paymentFeeTax  = $("input[name='payment_fee_tax']")
        this.productChecked = $('#product-checked').html($('input[data-foo="is_choice_user[]"]:checked').length)
        this.isPackageA = $('#package_a')
        this.isPackageB = $('#package_b')
        this.isPackageC = $('#package_c')
        this.namePackageA = $('#name_package_a').text()
        this.namePackageB = $('#name_package_b').text()
        this.namePackageC = $('#name_package_c').text()
        this.pricePackageA = $('#price_package_a').text().replaceAll(',', '')
        this.pricePackageB = $('#price_package_b').text().replaceAll(',', '')
        this.pricePackageC = $('#price_package_c').text().replaceAll(',', '')
        this.priceProductAddPackageA = $('#price_product_add_pack_a').text().replaceAll(',', '')
        this.priceProductAddPackageB = $('#price_product_add_pack_b').text().replaceAll(',', '')
        this.priceProductAddPackageC = $('#price_product_add_pack_c').text().replaceAll(',', '')
        this.cartNamePackage = $('#name_package')
        this.cartNamePackageNote = $('#name_package_note')
        this.cartPricePackage = $('#price_package')
        this.cartPriceProductAddPackage = $('#price_product_add')
        this.bankTransferElement = $('#cost_bank_transfer_span')
        this.mailingRegisCertEl = $('#mailing_regis_cert_el')
        this.subTotal = $('#sub_total')
        this.tax = $('#tax')
        this.commission = $('#commission')
        this.feeSubmitRegister = $('#fee_submit_register')
        this.feeSubmitRegisterYear = $('#fee_submit_register_year')
        this.totalAmount = $('#total_amount')
        this.cbPeriodRegister = $('#period_registration')
        this.actualFee = $('#actual_fee')
        this.consumptionTax = $('#consumption_tax')
        // value update payment
        this.valCostServiceBase = $('#cost_service_base')
        this.valCostServiceAddProd = $('#cost_service_add_prod')
        this.valSubtotal = $('#subtotal')
        this.valCommission = $('#request_commission')
        this.valTax = $('#request_tax')
        this.valCostPrintAppOneDistintion = $('#cost_print_application_one_distintion')
        this.valCost5YearOneDistintion = $('#cost_5_year_one_distintion')
        this.valCost10YearOneDistintion = $('#cost_10_year_one_distintion')
        this.valTotalAmount = $('#value_total_amount')
        this.valSubmitOle = $('#value_fee_submit_ole').val()
        this.priceFeeSubmit5Year = $('#price_fee_submit_5_year')
        this.priceFeeSubmit5YearOld = $('#price_fee_submit_5_year_old').val()
        this.priceFeeSubmit5YearOld2 = $('#price_fee_submit_5_year_old2').val()
        this.priceFeeSubmit10YearOld = $('#price_fee_submit_10_year_old').val()
        this.priceFeeSubmit10YearOld2 = $('#price_fee_submit_10_year_old2').val()
        this.costHajimeSupportEl = $('#cost_hajime_support')
        // Cart
        this.packageOtherA = [this.isPackageC, this.isPackageB]
        this.packageOtherB = [this.isPackageA, this.isPackageC]
        this.packageOtherC = [this.isPackageA, this.isPackageB]
    }
}
