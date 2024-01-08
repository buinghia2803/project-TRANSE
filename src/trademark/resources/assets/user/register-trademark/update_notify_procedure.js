class UpdateNotifyProcedure {
    constructor() {
        this.firstLoad = nextRegisterTrademark != null ? false : true;
        this.setInputData = true;

        this.initValidation();
        this.onClickSubmit();
        this.changeType();
        this.onChangeAddressFree();
        this.onChangePaymentType();
        this.onCheckedDistinction();
        this.onChangePeriodRegistration();
        this.onChangeNation();
        this.setCart();
    }

    initValidation() {
        const localRules = {
            'trademark_info_address_second': {
                required: () => {
                    let typeChange = $('[name=type_change]:checked').val();
                    return (typeChange == typeChangeAddress || typeChange == typeChangeDouble) && $('[name=trademark_info_nation_id]').val() == JapanID;
                },
                isValidInfoAddress: () => {
                    let typeChange = $('[name=type_change]:checked').val();
                    return (typeChange == typeChangeAddress || typeChange == typeChangeDouble) && $('[name=trademark_info_nation_id]').val() == JapanID;
                },
                maxlength: 100,
            },
            'trademark_info_address_three': {
                isValidInfoAddress: () => {
                    let typeChange = $('[name=type_change]:checked').val();
                    return (typeChange == typeChangeAddress || typeChange == typeChangeDouble) && $('[name=trademark_info_nation_id]').val() == JapanID;
                },
                maxlength: 100,
            },
            'trademark_info_name': {
                required: () => {
                    let typeChange = $('[name=type_change]:checked').val();
                    return typeChange == typeChangeName || typeChange == typeChangeDouble;
                },
                isValidInfoAddress: () => {
                    let typeChange = $('[name=type_change]:checked').val()
                    return typeChange == typeChangeName || typeChange == typeChangeDouble;
                },
                maxlength: 50,
            },
            'representative_name': {
                required: () => {
                    return $('input[name=representative_name]:visible').length > 0;
                },
                isFullwidth: true,
                maxlength: 50,
            },
        }

        const localMessages = {
            'trademark_info_address_second': {
                required: errorMessageRequired,
                isValidInfoAddress: errorMessageFormat,
                maxlength: errorMessageFormat,
            },
            'trademark_info_address_three': {
                isValidInfoAddress: errorMessageFormat,
                maxlength: errorMessageFormat,
            },
            'trademark_info_name': {
                required: errorMessageRequired,
                isValidInfoAddress: errorMessageFormatName,
                maxlength: errorMessageFormatName
            },
            'representative_name': {
                required: errorMessageRequired,
                isFullwidth: errorMessageFormatRespresentativeName,
                maxlength: errorMessageFormatRespresentativeName
            }
        }

        this.rules = { ...paymentRule, ...localRules }
        this.messages = { ...paymentMessage, ...localMessages }

        new clsValidation('#form', { rules: this.rules, messages: this.messages }, true);
    }

    formatPrice(number, unit = '円') {
        number = new Intl.NumberFormat('ja-JP', {}).format(Math.floor(number));

        return number + unit;
    }

    setCart() {
        let priceService = priceData.priceService;
        let priceServiceFee = priceData.priceServiceFee;
        let priceServiceAddProd = priceData.priceServiceAddProd;
        let priceServiceAddProdFee = priceData.priceServiceAddProdFee;
        let priceServiceChangeAddress = priceData.priceServiceChangeAddress;
        let priceServiceChangeAddressFee = priceData.priceServiceChangeAddressFee;
        let priceServiceChangeName = priceData.priceServiceChangeName;
        let priceServiceChangeNameFee = priceData.priceServiceChangeNameFee;
        let priceBankTransfer = priceData.priceBankTransfer;
        let priceBankTransferFee = priceData.priceBankTransferFee;
        let priceReduceDistinction = priceData.priceReduceDistinction;
        let priceReduceDistinctionFee = priceData.priceReduceDistinctionFee;

        let actualFee = 0;

        let distinctionItem = $('.distinction-item');
        let checkedDistinctionItem = $('[name^=distinctions]:checked');
        let distinctionItemAddition = checkedDistinctionItem.length - 3;
        distinctionItemAddition = distinctionItemAddition >= 0 ? distinctionItemAddition : 0;

        let periodRegistration = $('[name=period_registration]').val();
        let year = 10;
        let oneDistinctionFee = priceService.pof_1st_distinction_10yrs;
        if (periodRegistration == PERIOD_REGISTRATION_5_YEAR) {
            oneDistinctionFee = priceService.pof_1st_distinction_5yrs;
            year = 5;
        }

        // ON PAGE
        $('#product-table').find('.total_distinction').html(checkedDistinctionItem.length);
        let costYearOneDistinctionFee = oneDistinctionFee * checkedDistinctionItem.length;
        $('.cost_year_one_distinction_fee').find('.price').html(this.formatPrice(costYearOneDistinctionFee));

        // CART

        // price_service
        $('.price_service').find('.price').html(this.formatPrice(priceServiceFee));
        actualFee += priceService.base_price;

        let priceAllServiceAddProdFee = priceServiceAddProdFee * distinctionItemAddition;
        actualFee += priceServiceAddProd.base_price * distinctionItemAddition;
        $('.price_service_add_prod').find('.price').html(this.formatPrice(priceAllServiceAddProdFee));

        // Set reduce_distinctions
        $('.reduce_distinctions').find('.price').html(this.formatPrice(priceReduceDistinctionFee));
        actualFee += priceReduceDistinctionFee;

        // Set change_name_fee
        $('.change_name_fee').find('.price').html(this.formatPrice(priceServiceChangeNameFee));

        // Set change_address_fee
        $('.change_address_fee').find('.price').html(this.formatPrice(priceServiceChangeAddressFee));

        // Set cost_bank_transfer_fee
        $('.cost_bank_transfer_fee').find('.price').html(this.formatPrice(priceBankTransferFee));

        // Set print_price_service_fee
        let printPriceServiceFee = checkedDistinctionItem.length * oneDistinctionFee;
        $('.print_price_service_fee').find('.price').html(this.formatPrice(printPriceServiceFee));

        // Set print_change_name_fee
        let printServiceChangeNameFee = priceServiceChangeName.pof_1st_distinction_5yrs;
        $('.print_change_name_fee').find('.price').html(this.formatPrice(printServiceChangeNameFee));

        // Set print_change_address_fee
        let printServiceChangeAddressFee = priceServiceChangeAddress.pof_1st_distinction_5yrs;
        $('.print_change_address_fee').find('.price').html(this.formatPrice(printServiceChangeAddressFee));

        // Show/Hide price_service_add_prod
        if (distinctionItemAddition == 0) {
            $('.price_service_add_prod').addClass('hidden');
        } else {
            $('.price_service_add_prod').removeClass('hidden');
        }

        // Show/Hide reduce_distinctions
        if (distinctionItem.length == checkedDistinctionItem.length) {
            $('.reduce_distinctions').addClass('hidden');
            actualFee -= priceReduceDistinctionFee;
            priceReduceDistinctionFee = 0;
        } else {
            $('.reduce_distinctions').removeClass('hidden');
        }

        // Show/Hide change_name_fee change_address_fee
        let typeChangeElement = $('.type_change:checked');
        let typeChange = 0;
        if (typeChangeElement.length > 0) {
            typeChange = typeChangeElement.val();
            let registerTrademarkID = typeChangeElement.data('id');

            $('[name=id_register_trademark_choice]').val(registerTrademarkID);
            this.showFormChangeTradeMarkInfo(typeChange);

            if (this.setInputData == true) {
                if (this.firstLoad == false) {
                    this.showInfoTradeMarkInfo(nextRegisterTrademark, typeChange);
                } else {
                    let registerTrademark = registerTrademarks.find(function (item) {
                        return item.id == registerTrademarkID;
                    });

                    this.showInfoTradeMarkInfo(registerTrademark, typeChange);
                }
            }
        } else {
            $('[name=id_register_trademark_choice]').val('');
            this.showFormChangeTradeMarkInfo(typeChange);
        }
        this.setInputData = true;
        this.firstLoad = true;

        if (typeChange == typeChangeName) {
            $('.change_address_fee').addClass('hidden');
            priceServiceChangeAddressFee = 0;

            $('.change_name_fee').removeClass('hidden');
            actualFee += priceServiceChangeName.base_price;

            $('.print_change_name_fee').removeClass('hidden');
            $('.print_change_address_fee').addClass('hidden');
            printServiceChangeAddressFee = 0;
        } else if (typeChange == typeChangeAddress) {
            $('.change_name_fee').addClass('hidden');
            priceServiceChangeNameFee = 0;

            $('.change_address_fee').removeClass('hidden');
            actualFee += priceServiceChangeAddress.base_price;

            $('.print_change_address_fee').removeClass('hidden');
            $('.print_change_name_fee').addClass('hidden');
            printServiceChangeNameFee = 0;
        } else if (typeChange == typeChangeDouble) {
            $('.change_name_fee').removeClass('hidden');
            actualFee += priceServiceChangeName.base_price;

            $('.change_address_fee').removeClass('hidden');
            actualFee += priceServiceChangeAddress.base_price;

            $('.print_change_address_fee').removeClass('hidden');
            $('.print_change_name_fee').removeClass('hidden');
        } else {
            $('.change_address_fee').addClass('hidden');
            priceServiceChangeAddressFee = 0;

            $('.change_name_fee').addClass('hidden');
            priceServiceChangeNameFee = 0;

            $('.print_change_name_fee').addClass('hidden');
            printServiceChangeNameFee = 0;

            $('.print_change_address_fee').addClass('hidden');
            printServiceChangeAddressFee = 0;
        }

        // Is Change Address Free
        let isChangeAddressFree = $('[name=is_change_address_free]').prop('checked');
        if (isChangeAddressFree) {
            actualFee -= priceServiceChangeAddress.base_price;
            priceServiceChangeAddressFee = 0;
            $('.change_address_fee').find('.price').html(this.formatPrice(priceServiceChangeAddressFee));

            printServiceChangeAddressFee = 0;
            $('.print_change_address_fee').find('.price').html(this.formatPrice(printServiceChangeAddressFee));
        }

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
        let priceSubtotal = priceServiceFee + priceAllServiceAddProdFee + priceReduceDistinctionFee + priceServiceChangeNameFee + priceServiceChangeAddressFee + priceBankTransferFee;
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

        // Set total_fee
        let priceTotal = printPriceServiceFee + printServiceChangeNameFee + printServiceChangeAddressFee;
        $('.total_fee').find('.price').html(this.formatPrice(priceTotal));

        // Set total_amount_fee
        let priceTotalAmount = priceSubtotal + priceTotal;
        $('.total_amount_fee').find('.price').html(this.formatPrice(priceTotalAmount));

        // Update Label
        this.updateLabel({
            total_subtotal_item: checkedDistinctionItem.length,
            total_total_item: checkedDistinctionItem.length,
            one_distinction_fee: this.formatPrice(oneDistinctionFee),
            total_distinction: checkedDistinctionItem.length,
            distinction_addition: distinctionItemAddition,
            service_add_prod: this.formatPrice(priceServiceAddProdFee),
            year: year,
        });

        // Update Input Hidden
        $('[name="payment[cost_service_base]"]').val(priceServiceFee);
        $('[name="payment[cost_service_add_prod]"]').val(priceServiceAddProdFee);
        $('[name="payment[reduce_distinctions]"]').val(priceReduceDistinctionFee);
        $('[name="payment[cost_change_name]"]').val(priceServiceChangeNameFee);
        $('[name="payment[cost_change_address]"]').val(priceServiceChangeAddressFee);
        $('[name="payment[cost_bank_transfer]"]').val(priceBankTransferFee);
        $('[name="payment[subtotal]"]').val(priceSubtotal);
        $('[name="payment[commission]"]').val(actualFee);
        $('[name="payment[tax]"]').val(taxFee);
        $('[name="payment[cost_print_name]"]').val(printServiceChangeNameFee);
        $('[name="payment[cost_print_address]"]').val(printServiceChangeAddressFee);
        $('[name="payment[total_amount]"]').val(priceTotalAmount);

        if (periodRegistration == PERIOD_REGISTRATION_5_YEAR) {
            $('[name="payment[cost_5_year_one_distintion]"]').val(priceService.pof_1st_distinction_5yrs);
            $('[name="payment[cost_10_year_one_distintion]"]').val(0);
        } else {
            $('[name="payment[cost_5_year_one_distintion]"]').val(0);
            $('[name="payment[cost_10_year_one_distintion]"]').val(priceService.pof_1st_distinction_10yrs);
        }
    }

    updateLabel(data = []) {
        let table = $('#estimate-box-table');
        let dataText = table.find('[data-text]');
        $.each(dataText, function () {
            let text = $(this).data('text');

            $.each(data, function (index, item) {
                text = text.replaceAll('{' + index + '}', item);
            });

            $(this).html(text);
        });
    }

    changeType() {
        const self = this;
        $('body').on('change', '.type_change', function () {
            self.setCart();
        });

        $('body').on('click', '.clear_trademark_info', function () {
            $('.type_change').prop('checked', false);

            self.setCart();
        });
    }

    onChangeAddressFree() {
        const self = this
        $('body').on('change', '[name=is_change_address_free]', function () {
            self.setInputData = false;
            self.setCart();
        });
    }

    onChangePaymentType() {
        const self = this
        $('body').on('change', '[name=payment_type]', function () {
            self.setInputData = false;
            self.setCart();
        });

        if (isDisableBankTransfer) {
            $('[name=payment_type]').prop('checked', false);
        }
    }

    onCheckedDistinction() {
        const self = this
        $('body').on('change', '[name^=distinctions]', function () {
            self.setInputData = false;
            self.setCart();
        });
    }

    onChangePeriodRegistration() {
        const self = this
        $('body').on('change', '[name=period_registration]', function () {
            self.setInputData = false;
            self.setCart();
        });
    }

    onChangeNation () {
        self = this;
        $('body').on('change', '[name=trademark_info_nation_id]', function () {
            let valueNation = $(this).val();
            if (valueNation != idNationJP) {
                $('#hidden_prefectures').addClass('hidden');
            } else {
                $('#hidden_prefectures').removeClass('hidden');
            }
        });

        $('body').on('change', '[name=m_nation_id]', function () {
            self.setInputData = false;
            self.setCart();
        });
    }

    showInfoTradeMarkInfo(registerTrademark, type) {
        let trademarkInfoName = registerTrademark.trademark_info_name;
        let trademarkInfoNationId = registerTrademark.trademark_info_nation_id;
        let trademarkInfoAddressFirst = registerTrademark.trademark_info_address_first;
        let trademarkInfoAddressSecond = registerTrademark.trademark_info_address_second;
        let trademarkInfoAddressThree = registerTrademark.trademark_info_address_three;

        let trademarkInfoAddressFirstText = prefectureData[trademarkInfoAddressFirst];
        if (trademarkInfoAddressFirstText == undefined) {
            trademarkInfoAddressFirstText = '';
        }
        if (trademarkInfoAddressFirst == null) {
            trademarkInfoAddressFirst = Object.keys(prefectureData)[0];
        }

        let trademarkInfoAddressSecondText = trademarkInfoAddressSecond;
        if (trademarkInfoAddressSecondText == null) {
            trademarkInfoAddressSecondText = '';
        }

        let trademarkInfoAddressThreeText = trademarkInfoAddressThree;
        if (trademarkInfoAddressThreeText == null) {
            trademarkInfoAddressThreeText = '';
        }
        let trademarkInfoAddress = trademarkInfoAddressFirstText + trademarkInfoAddressSecondText + trademarkInfoAddressThreeText;

        if (type == typeChangeName) {
            $('.trademark_info_name').html(trademarkInfoName);
            $('[name=trademark_info_name]').val(trademarkInfoName);
        } else if (type == typeChangeAddress) {
            $('.trademark_info_address').html(trademarkInfoAddress);
            $('[name=trademark_info_nation_id]').val(trademarkInfoNationId);
            $('[name=trademark_info_address_first]').val(trademarkInfoAddressFirst);
            $('[name=trademark_info_address_second]').val(trademarkInfoAddressSecond);
            $('[name=trademark_info_address_three]').val(trademarkInfoAddressThree);
        } else if (type == typeChangeDouble) {
            $('.trademark_info_name').html(trademarkInfoName);
            $('[name=trademark_info_name]').val(trademarkInfoName);

            $('.trademark_info_address').html(trademarkInfoAddress);
            $('[name=trademark_info_nation_id]').val(trademarkInfoNationId);
            $('[name=trademark_info_address_first]').val(trademarkInfoAddressFirst);
            $('[name=trademark_info_address_second]').val(trademarkInfoAddressSecond);
            $('[name=trademark_info_address_three]').val(trademarkInfoAddressThree);
        }

        $('[name=trademark_info_nation_id]').change();

        let infoTypeAcc = registerTrademark.info_type_acc;
        $('[name=info_type_acc]').val(infoTypeAcc);
        if (infoTypeAcc == TYPE_ACC_COMPANY) {
            $('[name=representative_name]').closest('dl').removeClass('hidden');
            $('.representative_name_desc_2').removeClass('hidden');
        } else {
            $('[name=representative_name]').closest('dl').addClass('hidden');
            $('.representative_name_desc_2').addClass('hidden');
        }
    }

    showFormChangeTradeMarkInfo(type) {
        if (type == typeChangeName) {
            $('.change-name').removeClass('hidden');
            $('.change-address').addClass('hidden');
        } else if (type == typeChangeAddress) {
            $('.change-name').addClass('hidden');
            $('.change-address').removeClass('hidden');
        } else if (type == typeChangeDouble) {
            $('.change-name').removeClass('hidden');
            $('.change-address').removeClass('hidden');
        } else {
            $('.change-name').addClass('hidden');
            $('.change-address').addClass('hidden');
        }

        if (type == typeChangeName || type == typeChangeAddress || type == typeChangeDouble) {
            $('.representative_name_bock').removeClass('hidden');
        } else {
            $('.representative_name_bock').addClass('hidden');
        }
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

new UpdateNotifyProcedure;
