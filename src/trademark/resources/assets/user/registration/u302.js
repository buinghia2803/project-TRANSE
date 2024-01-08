const BANK_TRANSFER = 2
class clsRegistration {
    constructor() {
        const self = this
        this.__clsDistinctTable = new clsDistinctTable(this)
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
        if(isBlockScreen) {
            this.disableInput()
        }
        this.initCalculate()
        this.onChangeRegisMailingCert()
        this.onChangeCheckBoxPeriodRegis()
        this.onChangePeriodRegistration()
        this.onChangePaymentType()
        this.openFile()
        this.onChangeTrademarkInfo()
        this.onChangeNationChange()
        this.__clsDistinctTable.callbackChangeCheckbox = this.callback
        this.onChangeNationPayer()
        this.calculateSubTotal()
        this.gonnaCommonPayment()
        this.gonnaAnkenTop()
        this.gonnaQuote()
        $('.cb_distinction').change()
    }

    disableInput() {
        const form = $('#form')
        form.find('a, input:not([name|=submit_confirm],[name|=draft_confirm]):not(.logout), button, textarea, select').addClass('disabled')
        form.find('a').attr('href', 'javascript:void(0)')
        form.find('a').attr('target', '')
        $('.checkQuestion').prop('disabled', true).addClass('disabled')
        form.find('input:not([name|=submit_confirm],[name|=draft_confirm]):not(#cart):not(.logout), textarea, select').prop('readonly', true).prop('disabled', true);
        $('[type=submit]:not(.logout)').prop('readonly', true).prop('disabled', true)
        form.find('input[name="_token"]').prop('readonly', false).prop('disabled', false);
        form.find('.data-hidden').each(function (idx, item) {
            $(item).prop('disabled', false);
        })
    }

    initCalculate() {
        $('#tr_sub_distinct').hide()
        $('#tr_change_name_fee').hide()
        $('#tr_change_address_fee').hide()
        $('#tr_regis_mailing_cert').hide()
        if (appTrademark.period_registration == PERIOD_REGISTRATION_TEN_YEAR) {
            $('#tr_register_term_change').hide();
        }
        const totalProduct = $('#totalProductSelected').text() - 3
        $('#est_product_each_add_fee').text(this.fmPrice(productAddOnFee.cost_service_base * Math.ceil(totalProduct/3)))
    }

    /**
     * The function calculates the subtotal of a registration procedure, including various fees and
     * taxes.
     */
    calculateSubTotal() {
        const totalAddOn = this.__clsDistinctTable.totalProduct - 3 > 0 ? this.__clsDistinctTable.totalProduct - 3 : 0
        this.productAddOnFee = productAddOnFee.cost_service_base * Math.ceil(totalAddOn/3)

        const subTotal = regisProcedureServiceFee.base_price + regisProcedureServiceFee.base_price * (setting.value/100)
            + this.subDistinctFee
            + this.productAddOnFee
            + this.periodRegistrationFee
            + this.regisMailingCertFee
            + this.bankTransferFee
            + this.changeNameFee
            + this.changeAddressFee

        $('#sub_total_text').text(this.fmPrice(subTotal))
        $('#subtotal').val(subTotal)

        this.calculateCommission(totalAddOn)
        this.calculateTax(totalAddOn);
        this.calculateTotal()
    }

    /**
     * The function calculates the total amount by adding the subtotal and a registration term change
     * fee and updates the total amount text and input field.
     */
    calculateTotal() {
        const subTotal = $('#subtotal').val()
        const totalAmount = +subTotal + +this.registerTermChangeTotalFee
        $('.total_amount_text').text(this.fmPrice(totalAmount))
        $('input[name=total_amount]').val(totalAmount)
    }


    /**
     * The function calculates the commission fee for a registration procedure, including fees for
     * various services and product add-ons.
     * @param totalAddOn - The total number of add-ons purchased by the customer.
     */
    calculateCommission (totalAddOn) {
        let productAddOnCommissionFee = 0;
        if (totalAddOn) {
            productAddOnCommissionFee = productAddOnFee.commission * Math.ceil(totalAddOn/3)
        }

        const commission = regisProcedureServiceFee.base_price
            + this.regisMailingCertCommissionFee
            + this.periodRegistrationCommissionFee
            + this.bankTransferCommissionFee
            + this.subDistinctCommissionFee
            + this.changeAddressCommissionFee
            + this.changeNameCommissionFee
            + productAddOnCommissionFee

        $('#commission_text').text(this.fmPrice(commission))
        $('input[name=commission]').val(commission)
    }

    /**
     * The function calculates the total tax fee for a product with add-ons based on various fees and
     * taxes.
     * @param totalAddOn - The total number of add-ons for a product.
     */
    calculateTax(totalAddOn) {
        let productAddOnTaxFee = 0;
        if (totalAddOn) {
            productAddOnTaxFee = productAddOnFee.tax * Math.ceil(totalAddOn/3)
        }
        const tax = (regisProcedureServiceFee.base_price * (setting.value/100))
            + this.regisMailingCertTaxFee
            + this.bankTransferTaxFee
            + this.subDistinctTaxFee
            + this.periodRegistrationTaxFee
            + this.changeAddressTaxFee
            + this.changeNameTaxFee
            + productAddOnTaxFee

        $('#tax_price').text(this.fmPrice(tax))
        $('input[name=tax]').val(tax)
    }

    /**
     * The function shows or hides a commission tax information field based on the selected nation in a
     * dropdown menu.
     */
    onChangeNationPayer() {
        $('body').on('change', 'select[name=m_nation_id]', function() {
            if ($(this).val() == 1) {
                $('#tr_commission_tax_info').show(100)
            } else {
                $('#tr_commission_tax_info').hide(100)
            }
        })

        $('select[name=m_nation_id]').change()
    }

    /**
     * Checking data before submit
     */
    preSubmit() {
        const distinctChecked = $('.cb_distinction:checked').length;
        if (!distinctChecked && !$('.box-distinct-tbl').find('.notice').length) {
            $('.box-distinct-tbl').append('<div class="notice">&#xFEFF;選択してください。</div>')
            document.querySelector('.error, .notice').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            return
        }
        if (distinctChecked) {
            $('.box-distinct-tbl').find('.notice').remove()
        }

        $('#form').submit()
    }

    /**
     * The function toggles the visibility of a form element and updates certain fees based on whether
     * a checkbox is checked or not.
     */
    onChangeRegisMailingCert() {
        const self = this
        $('input[name=is_mailing_register_cert]:not(:disabled)').on('change', function () {
            if($(this).is(':checked')) {
                $('#tr_regis_mailing_cert').show()
                self.regisMailingCertFee = mailRegisterCert.cost_service_base
                self.regisMailingCertTaxFee = mailRegisterCert.tax;
                self.regisMailingCertCommissionFee = mailRegisterCert.commission;
            }else {
                $('#tr_regis_mailing_cert').hide()
                self.regisMailingCertFee = 0
                self.regisMailingCertTaxFee = 0;
                self.regisMailingCertCommissionFee = 0;
            }
            self.calculateSubTotal()
        })
        $('input[name=is_mailing_regis_cert]:not(:disabled)').change()
    }

    /**
     * Handle event change nation
     */
    onChangeNationChange() {
        $('body').on('change', 'select[name=trademark_info_nation_id]', function () {
            if ($(this).val() != 1) {
                $('#trademark_info_m_prefecture').hide()
                $('#trademark_info_address_second_box').hide()
            } else {
                $('#trademark_info_m_prefecture').show()
                $('#trademark_info_address_second_box').show()
            }
        })

        $('select[name=trademark_info_nation_id]').change()
    }
    /**
     * Handle event change Period Registration
     */
    onChangePeriodRegistration() {
        const self = this
        $('select[name=period_registration], input[name=period_registration]').on('change', function() {
            let val = $(this).val()

            if (appTrademark.pack == 1) {
                $('#tr_reg_period_change_fee').show()

                if (val == PERIOD_REGISTRATION_FIVE_YEAR) {
                    $('.period_registration_fee').text(self.fmPrice(regisProcedureServiceFee.pof_1st_distinction_5yrs * self.__clsDistinctTable.totalDistinct) + '円')
                } else if(val == PERIOD_REGISTRATION_TEN_YEAR) {
                    $('.period_registration_fee').text(self.fmPrice(regisProcedureServiceFee.pof_1st_distinction_10yrs * self.__clsDistinctTable.totalDistinct) + '円')
                }

                self.periodRegistrationFee = 0;
                self.periodRegistrationTaxFee = 0;
                self.periodRegistrationCommissionFee = 0;

                const changeRegisterPeriod = regisProcedureServiceFee.base_price + regisProcedureServiceFee.base_price * (setting.value /100);
                $('#reg_period_change_fee').text(self.fmPrice(changeRegisterPeriod))
                $('input[name=reg_period_change_fee]').val(changeRegisterPeriod)
            } else {
                $('#tr_reg_period_change_fee').show()

                if($(this).attr('type') == 'checkbox' && !$(this).is(':checked')) {
                    val = PERIOD_REGISTRATION_FIVE_YEAR
                }

                if (val == PERIOD_REGISTRATION_FIVE_YEAR) {
                    $('.period_registration_fee').text(self.fmPrice(registerTermChange.pof_1st_distinction_5yrs * self.__clsDistinctTable.totalDistinct) + '円')
                    $('input[name=period_change_fee]').val(0)
                } else if(val == PERIOD_REGISTRATION_TEN_YEAR) {
                    $('.period_registration_fee').text(self.fmPrice(registerTermChange.pof_1st_distinction_10yrs * self.__clsDistinctTable.totalDistinct) + '円')
                    $('input[name=period_change_fee]').val(registerTermChange.pof_1st_distinction_10yrs * self.__clsDistinctTable.totalDistinct)
                }

                self.periodRegistrationFee = registerTermChange.base_price + (registerTermChange.base_price * setting.value/100);
                self.periodRegistrationTaxFee = registerTermChange.base_price * setting.value/100;
                self.periodRegistrationCommissionFee = registerTermChange.base_price;

                let changeRegisterPeriod = registerTermChange.base_price + registerTermChange.base_price * (setting.value /100)
                if (val == PERIOD_REGISTRATION_FIVE_YEAR) {
                    self.periodRegistrationFee = 0;
                    self.periodRegistrationTaxFee = 0;
                    self.periodRegistrationCommissionFee = 0;
                    changeRegisterPeriod = 0;
                    $('#tr_reg_period_change_fee').hide();
                }
                $('#reg_period_change_fee').text(self.fmPrice(changeRegisterPeriod))
                $('input[name=reg_period_change_fee]').val(changeRegisterPeriod)
            }

            self.changeTermAMSFee(val)
            self.calculateSubTotal()
        })

        $('select[name=period_registration], input[name=period_registration]').change()
    }

    /**
     * This function changes the registration term fee based on the selected period and trademark pack.
     * @param val - The value of the selected registration period (either PERIOD_REGISTRATION_FIVE_YEAR
     * or PERIOD_REGISTRATION_TEN_YEAR).
     */
    changeTermAMSFee (val) {
        if (appTrademark.pack == PACK_A) {
            // Pack A and register 5 or 10 year
            if (val == PERIOD_REGISTRATION_FIVE_YEAR) {
                $('#register_change_year').text(Year5Registration)
                this.registerTermChangeOneProd = regisProcedureServiceFee.pof_1st_distinction_5yrs
                this.registerTermChangeTotalFee = this.registerTermChangeOneProd * this.__clsDistinctTable.totalDistinct
            } else {
                $('#register_change_year').text(Year10Registration)
                this.registerTermChangeOneProd = regisProcedureServiceFee.pof_1st_distinction_10yrs
                this.registerTermChangeTotalFee = this.registerTermChangeOneProd * this.__clsDistinctTable.totalDistinct
            }
        } else {
            // Pack B, C and register 5 or 10 year
            if (val == PERIOD_REGISTRATION_FIVE_YEAR) {
                $('#register_change_year').text(Year5Registration)
                this.registerTermChangeOneProd = registerTermChange.pof_1st_distinction_5yrs
                if (appTrademark.period_registration == PERIOD_REGISTRATION_FIVE_YEAR) {
                    this.registerTermChangeTotalFee = 0
                }
                // this.registerTermChangeTotalFee = this.registerTermChangeOneProd * this.__clsDistinctTable.totalDistinct
            } else {
                $('#register_change_year').text(Year10Registration)
                $('#tr_register_term_change').show(100)
                this.registerTermChangeOneProd = registerTermChange.pof_1st_distinction_10yrs
                this.registerTermChangeTotalFee = this.registerTermChangeOneProd * this.__clsDistinctTable.totalDistinct
            }
        }

        $('#register_term_change_one_distinct').text(this.fmPrice(this.registerTermChangeOneProd))
        $('#register_term_change').text(this.fmPrice(this.registerTermChangeTotalFee))
    }

    callback(__self) {
        const selfClsDistinctTable = this
        __self.productEachAddCount = selfClsDistinctTable.totalProduct - 3 > 0 ? selfClsDistinctTable.totalProduct - 3 : 0
        __self.setProductEachAddCount()
        const countUncheckProd = $('.cb_distinction').not(':checked').length
        if (countUncheckProd) {
            __self.subDistinctFee = reduceNumberDistinctFee.cost_service_base
            __self.subDistinctTaxFee = reduceNumberDistinctFee.tax
            __self.subDistinctCommissionFee = reduceNumberDistinctFee.commission
            $('#tr_sub_distinct').show(300)
            $('input[name=sub_distinct_fee]').val(reduceNumberDistinctFee.cost_service_base)
        } else {
            __self.subDistinctFee = 0
            __self.subDistinctTaxFee = 0
            __self.subDistinctCommissionFee = 0
            $('#tr_sub_distinct').hide(300)
            $('input[name=sub_distinct_fee]').val(0)
        }

        let val = 0
        const element = $('input[name=period_registration], select[name=period_registration]')[0];
        if ($(element).attr('type') == 'checkbox' && $(element).is(':checked')) {
            val = $(element).val()
        } else if($(element).attr('type') == 'checkbox') {
            val = 1
        } else {
            val = $(element).val()
        }

        if ($('input[name=period_registration]').is(':checked')) {
            const periodRegistrationFee  = registerTermChange.pof_1st_distinction_10yrs * selfClsDistinctTable.totalDistinct
            const val = $('input[name=period_registration]:checked').val()
            if (appTrademark.period_registration == val) {
                $('.period_registration_fee').text('支払済')
            } else {
                $('.period_registration_fee').text(__self.fmPrice(periodRegistrationFee) + '円')
                $('input[name=period_change_fee]').val(periodRegistrationFee)
            }
        }
        __self.changeTermAMSFee(val)
        __self.calculateSubTotal()
    }

    /**
     * The function sets the product add-on fee based on the product each add count and updates the
     * corresponding input fields and display elements.
     */
    setProductEachAddCount() {
        this.productAddOnFee = productAddOnFee.cost_service_base * Math.ceil(this.productEachAddCount/3)
        $('#est_product_each_add_fee').text(this.fmPrice(this.productAddOnFee))
        $('#total_product_each_add').text(this.productEachAddCount)
        $('input[name=total_product_each_add]').val(this.productEachAddCount)
        $('input[name=product_each_add_fee]').val(this.productAddOnFee)
    }

    /**
     * Calculate subtotal, commission, products price , total price.
     * @param {*} val
     * @returns Number
     */
    fmPrice(val) {
        return new Intl.NumberFormat('en-us').format(Math.floor(val))
    }

    /**
     * Showing mailing registration information.
     */
    onChangeCheckBoxPeriodRegis() {
        const self = this;
        if (appTrademark.pack == 1) {
            $('.cb_mailing_regis_cert').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#mailing_registration_info_form').show(400);
                    self.regisMailingCertFee = mailingRegisterCertFee;
                    self.regisMailingCertTaxFee = mailRegisterCert.tax;
                    self.regisMailingCertCommissionFee = mailRegisterCert.commission;

                    $('#product__mailing_regis_cert_fee').text(self.fmPrice(mailingRegisterCertFee) + '円');
                } else {
                    $('#mailing_registration_info_form').hide(400);
                    $('#mailing_registration_info_form').find('.notice,.error').remove();

                    $('#mailing_registration_info_form').find('input[name=regist_cert_postal_code]').val('');
                    $('#mailing_registration_info_form').find('input[name=regist_cert_address]').val('');
                    $('#mailing_registration_info_form').find('input[name=regist_cert_payer_name]').val('');

                    self.regisMailingCertFee = 0;
                    self.regisMailingCertTaxFee = 0;
                    self.regisMailingCertCommissionFee = 0;
                    $('#product__mailing_regis_cert_fee').text(self.fmPrice(0) + '円');
                }

                self.calculateSubTotal()
            })
            $('.cb_mailing_regis_cert').change()
        } else {
            $('.cb_mailing_regis_cert:not(:disabled)').on('change', function () {
                if($(this).is(':checked')) {
                    $('#mailing_registration_info_form').show(400);
                    $('#is_mailing_register_cert_price').text(self.fmPrice(mailingRegisterCertFee))
                    self.regisMailingCertFee = mailingRegisterCertFee;
                    self.regisMailingCertTaxFee = mailRegisterCert.tax;
                    self.regisMailingCertCommissionFee = mailRegisterCert.commission;
                } else {
                    $('#mailing_registration_info_form').hide(400);
                    self.regisMailingCertFee = 0;
                    self.regisMailingCertTaxFee = 0;
                    self.regisMailingCertCommissionFee = 0;
                    $('#is_mailing_register_cert_price').text(0)
                }

                self.calculateSubTotal()
            })

            $('.cb_mailing_regis_cert:not(:disabled)').change()
        }
    }

    /**
     * The function fills in data for a trademark based on its ID.
     * @param id - The parameter "id" is a variable that represents the ID of a trademark information
     * object. It is used to retrieve the corresponding trademark information from an array called
     * "trademarkInfos" and fill in the relevant data into HTML elements with specific IDs.
     */
    fillData(id) {
        const trademarkInfo = trademarkInfos.find(item => item.id = id)

        let addressInfo = '';
        if (trademarkInfo.m_nation_id == NATION_JAPAN_ID) {
            addressInfo = prefectures[trademarkInfo.m_prefecture_id] + trademarkInfo.address_second + trademarkInfo.address_three;
        } else {
            addressInfo = nations[trademarkInfo.m_nation_id] + trademarkInfo.address_three;
        }

        $('#address_infor').text(addressInfo)
        $('#trademark_info_address_full').text(trademarkInfo.name)
        $('input[name=trademark_info_id]').val(id)
        $('input[name=trademark_info_id]').attr('value', id)
    }

    /**
     * Showing form change trademark info.
     */
    onChangeTrademarkInfo() {
        const self = this
        $('.change_trademark_info').on('change', function () {
            const val = $(this).val()
            self.fillData($(this).data('trademark_info_id'))
            switch (+val) {
                case 1:
                    self.changeAddressFee = 0
                    self.changeAddressTaxFee = 0;
                    self.changeAddressCommissionFee = 0;

                    self.changeNameFee = Math.floor(changeNameFee.cost_service_base)
                    self.changeNameTaxFee = Math.floor(changeNameFee.tax);
                    self.changeNameCommissionFee = Math.floor(changeNameFee.commission);
                    $('#tr_change_name_fee').show(200)
                    $('#tr_change_address_fee').hide(200)
                    $('#change_address_tbl').hide(400)
                    $('#change_name_tbl').show(400)

                    $('input[name=change_address_fee]').val(0);
                    $('input[name=change_name_fee]').val(Math.floor(self.changeNameFee))
                    break;
                case 2:
                    self.changeNameFee = 0;
                    self.changeNameTaxFee = 0;
                    self.changeNameCommissionFee = 0;

                    self.changeAddressFee = Math.floor(changeAddressFee.cost_service_base)
                    self.changeAddressTaxFee = Math.floor(changeAddressFee.tax);
                    self.changeAddressCommissionFee = Math.floor(changeAddressFee.commission);
                    $('#tr_change_address_fee').show(200)
                    $('#tr_change_name_fee').hide(200)
                    $('#change_address_tbl').show(400)
                    $('#change_name_tbl').hide(400)

                    $('input[name=change_address_fee]').val(Math.floor(self.changeAddressFee));
                    $('input[name=change_name_fee]').val(0)
                    break;
                case 3:
                    self.changeAddressFee = Math.floor(changeAddressFee.cost_service_base)
                    self.changeAddressTaxFee = Math.floor(changeAddressFee.tax)
                    self.changeAddressCommissionFee = Math.floor(changeAddressFee.commission)

                    self.changeNameFee = Math.floor(changeNameFee.cost_service_base)
                    self.changeNameTaxFee = Math.floor(changeNameFee.tax)
                    self.changeNameCommissionFee = Math.floor(changeNameFee.commission)

                    $('input[name=change_name_fee]').val(Math.floor(self.changeNameFee))
                    $('input[name=change_address_fee]').val(Math.floor(self.changeAddressFee));
                    $('#change_address_tbl').show(400)
                    $('#change_name_tbl').show(400)
                    $('#tr_change_name_fee').show(200)
                    $('#tr_change_address_fee').show(200)
                    break;
                default:
                    break;
            }

            self.calculateSubTotal()
        })

        $('.change_trademark_info:checked').change()

        $('body').on('click', '.clear_trademark_info', function () {
            $('.change_trademark_info').prop('checked', false);

            self.changeNameFee = 0;
            self.changeNameTaxFee = 0;
            self.changeNameCommissionFee = 0;

            self.changeAddressFee = 0
            self.changeAddressTaxFee = 0;
            self.changeAddressCommissionFee = 0;

            $('input[name=change_name_fee]').val(0)
            $('input[name=change_address_fee]').val(0);
            $('#change_address_tbl').hide()
            $('#change_name_tbl').hide()
            $('#tr_change_name_fee').hide()
            $('#tr_change_address_fee').hide()

            self.calculateSubTotal()
        });
    }

    /**
     * handling change event of payment type
     */
    onChangePaymentType() {
        const self = this
        if ($('input[name=payment_type]:checked').val() == BANK_TRANSFER) {
            $('#tr_payment_bank_transfer').show(200)
        } else {
            $('#tr_payment_bank_transfer').hide(200)
        }
        $('input[name=payment_type]').on('change', function () {
            if($(this).val() == BANK_TRANSFER) {
                $('#tr_payment_bank_transfer').show(200)
                self.bankTransferFee = Math.floor(paymentFee.cost_service_base)
                self.bankTransferTaxFee = Math.floor(paymentFee.tax)
                self.bankTransferCommissionFee = Math.floor(paymentFee.commission)
                self.calculateSubTotal()
            } else {
                $('#tr_payment_bank_transfer').hide(200)
                self.bankTransferFee = 0
                self.bankTransferTaxFee = 0;
                self.bankTransferCommissionFee = 0;
                self.calculateSubTotal()
            }
        })
        $('input[name=payment_type]:checked').change()
    }

    gonnaCommonPayment() {
        const self = this;
        $('.redirect_to_common_payment').on('click', function () {
            $('input[name=redirect_to]').val(COMMON_PAYMENT)
            $('input[name=redirect_to]').attr('value', COMMON_PAYMENT)
            self.preSubmit()
        })
    }

    gonnaAnkenTop() {
        const self = this;
        $('#redirect_to_anken_top').on('click', function () {
            $('input[name=redirect_to]').val(U000ANKEN_TOP)
            $('input[name=redirect_to]').attr('value', U000ANKEN_TOP)

            self.preSubmit()
        })
    }

    gonnaQuote() {
        const self = this;
        $('#redirect_to_quote').on('click', function () {
            $('input[name=redirect_to]').val(QUOTE)
            $('input[name=redirect_to]').attr('value', QUOTE)
            $('#form').attr('target' ,'_blank');
            self.preSubmit()
            loadingBox('close');
            $('#form').attr('target' ,'_self');
        })
    }

    /**
     * Open file
     */
    openFile() {
        const self = this
        $('#open_file_pdf').on('click', function () {
            if (Array.isArray(trademarkDocuments) && trademarkDocuments.length) {
                for (const trademarkDoc of trademarkDocuments) {
                    if (trademarkDoc.url) {
                        const newTab = window.open(trademarkDoc.url)
                        const error = self.checkPopupBlocker(newTab)
                        if (error) {
                            break;
                        }
                    }
                }

            }
        })
    }

    /**
     *  Check pop-up block is enable
     * @param {*} popupWindow
     * @returns
     */
    isPopupBlocked(popupWindow) {
        if ((popupWindow.innerHeight > 0) == false) {
            this.displayError();
            return true
        }

        return false
    }

    /**
     * Check pop-up blocker
     * @param {*} popupWindow
     * @returns
     */
    checkPopupBlocker(popupWindow) {
        const self = this
        let error = false
        if (popupWindow) {
            popupWindow.onload = function () {
                return self.isPopupBlocked(popupWindow);

            };
        } else {
            self.displayError();
            return true
        }

        return error;
    }

    /**
     * Show error
     */
    displayError() {
        $.confirm({
            title: '',
            content: messageContentModal,
            buttons: {
                ok: {
                    text: 'OK',
                    btnClass: 'btn-blue',
                    action: function () { }
                }
            }
        });
    }

    /**
     * Initial variable before page loaded
     */
    initVariables() {
        this.productEachAddCount = null;
        this.productAddOnFee = 0;

        this.periodRegistrationFee = 0;
        this.periodRegistrationTaxFee = 0;
        this.periodRegistrationCommissionFee = 0;

        this.regisMailingCertFee = 0;
        this.regisMailingCertTaxFee = 0;
        this.regisMailingCertCommissionFee = 0;

        this.bankTransferFee = 0;
        this.bankTransferTaxFee = 0;
        this.bankTransferCommissionFee = 0;

        this.changeAddressFee = 0;
        this.changeAddressTaxFee = 0;
        this.changeAddressCommissionFee = 0;

        this.changeNameFee = 0;
        this.changeNameTaxFee = 0;
        this.changeNameCommissionFee = 0;

        this.registerTermChangeTotalFee = 0;
        this.registerTermChangeOneProd = 0;

        this.subDistinctFee = 0
        this.subDistinctTaxFee = 0
        this.subDistinctCommissionFee = 0
    }

    /**
     * initial validate before page loaded
     */
    initValidate() {
        const rules = {
            trademark_info_name: {
                required: () => { return !!$('.change_trademark_info:checked').length && ($('.change_trademark_info:checked').val() == 1 || $('.change_trademark_info:checked').val() == 3) },
                isValidInfoAddress: () => {
                    return !!$('.change_trademark_info:checked').length && ($('.change_trademark_info:checked').val() == 2 || $('.change_trademark_info:checked').val() == 3)
                },
                maxlength: 50
            },
            trademark_info_m_prefecture_id: {
                required: () => {
                    return $('select[name=trademark_info_nation_id]').val() == 1 && !!$('.change_trademark_info:checked').length && ($('.change_trademark_info:checked').val() == 2 || $('.change_trademark_info:checked').val() == 3) && $('select[name=trademark_info_nation_id]').val() == 1
                }
            },
            trademark_info_address_second: {
                required: () => {
                    return $('select[name=trademark_info_nation_id]').val() == 1 && !!$('.change_trademark_info:checked').length && ($('.change_trademark_info:checked').val() == 2 || $('.change_trademark_info:checked').val() == 3)
                },
                isValidInfoAddress: () => {
                    return $('select[name=trademark_info_nation_id]').val() == 1 && !!$('.change_trademark_info:checked').length && ($('.change_trademark_info:checked').val() == 2 || $('.change_trademark_info:checked').val() == 3)
                },
                maxlength: 100,
            },
            trademark_info_address_three: {
                isValidInfoAddress: () => {
                    return $('select[name=trademark_info_nation_id]').val() == 1 && !!$('.change_trademark_info:checked').length && ($('.change_trademark_info:checked').val() == 2 || $('.change_trademark_info:checked').val() == 3)
                },
                maxlength: 100
            },
            regist_cert_nation_id: {
                required: () => { return $('#product__mailing_regis_cert_input:checked').length > 0 },
            },
            regist_cert_postal_code: {
                required: () => {
                    let registCertNationID = $('select[name=regist_cert_nation_id]').val();
                    return  registCertNationID == NATION_JAPAN_ID && $('#product__mailing_regis_cert_input:checked').length > 0
                },
                isValidInfoPostalCode: () => {
                    let registCertNationID = $('select[name=regist_cert_nation_id]').val();
                    return registCertNationID == NATION_JAPAN_ID && $('#product__mailing_regis_cert_input:checked').length > 0
                },
            },
            regist_cert_address: {
                required: () => { return $('#product__mailing_regis_cert_input:checked').length > 0 },
                isFullwidth: () => {
                    let registCertNationID = $('select[name=regist_cert_nation_id]').val();
                    return registCertNationID == NATION_JAPAN_ID && $('#product__mailing_regis_cert_input:checked').length > 0
                },
            },
            regist_cert_payer_name: {
                required: () => { return $('#product__mailing_regis_cert_input:checked').length > 0 },
                isFullwidth: () => {
                    let registCertNationID = $('select[name=regist_cert_nation_id]').val();
                    return registCertNationID == NATION_JAPAN_ID && $('#product__mailing_regis_cert_input:checked').length > 0
                },
            },
        }
        const messages = {
            trademark_info_name: {
                required: errorMessageRequired,
                isValidInfoAddress: errorMessageFormatName,
                maxlength: errorMessageContentMaxLength
            },
            trademark_info_m_prefecture_id: {
                required: errorMessageRequired
            },
            trademark_info_address_second: {
                required: errorMessageRequired,
                isValidInfoAddress: errorMessageFormat,
                maxlength: errorMessageContentMaxLength100
            },
            trademark_info_address_three: {
                isValidInfoAddress: errorMessageFormat,
                maxlength: errorMessageContentMaxLength100
            },
            regist_cert_nation_id: {
                required: errorMessageRequired
            },
            regist_cert_postal_code: {
                required: errorMessageRequired,
                isValidInfoPostalCode: errorMessageIsValidInfoPostalCode
            },
            regist_cert_address: {
                required: errorMessageRequired,
                isFullwidth: errorMessageIsValidInfoAddressFormat
            },
            regist_cert_payer_name: {
                required: errorMessageRequired,
                isFullwidth: errorMessageIsValidInfoAddressFormat
            },
        }

        if (!hideButtonSubmit) {
            new clsValidation('#form', { rules: {...rules, ...paymentRule}, messages: {...messages, ...paymentMessage} })
        } else {
            new clsValidation('#form', { rules: rules, messages: messages })
        }
    }
}

new clsRegistration()
