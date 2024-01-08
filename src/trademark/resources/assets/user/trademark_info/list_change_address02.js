//let
const BANK_TRANSFER = 2
const NOT_BANK_TRANSFER = 1
const TYPE_CHANGE_NAME = '1';
const TYPE_CHANGE_ADDRESS = '2';
const TYPE_CHANGE_DOUBLE = '3';
var type_change;
var trademark_info_id
var feeChangeNameElement = $('#fee_change_name')
var feeChangeAddressElement = $('#fee_change_address')
var subTotalElement = $('.subtotal')
var totalAmountElement = $('#total_amount')
var typeChange = $('.type_change')
var basePriceChangeAddress = $('#base_price_change_address')
var basePriceChangeName = $('#base_price_change_name')
var baseCostBankTransfer = $('#base_price_cost_bank_transfer')
var commissionElement = $('#commission')
var taxElement = $('#tax_input')
var costChangeNameElement = $('#cost_change_name')
var costChangeAddressElement = $('#cost_change_address')
var costChangeTransferElement = $('#cost_bank_transfer_input')
var consPrintNameElement = $('#cost_print_name')
var consPrintAddressElement = $('#cost_print_address')
var costPrintNameValue = $('#cost_print_name_input')
var costPrintAddressValue = $('#cost_print_address_input')
var oldFeeChangeAddress = $('#value_fee_change_address').val();
var oldCostPrintAddress = $('#value_cost_print_address').val();
var baseAddressChangeName = $('#base_address_change_name').val();
class clsChangeAddress02 {
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
        this.initValidation()
        this.init()
    }

    //==================================================================
    // Init Validation Form
    //==================================================================
    initValidation() {
        const localRules = {
            'type_change': {
                required: true,
            },
            'trademark_infos_address_second': {
                required: () => {
                    return $('select[name=change_info_register_m_nation_id]').val() == JapanID;
                },
                isValidInfoAddress: () => {
                    return $('select[name=change_info_register_m_nation_id]').val() == JapanID;
                },
                checkAddressSecond: 100,
            },
            'trademark_infos_address_three': {
                isValidInfoAddress: () => {
                    return $('select[name=change_info_register_m_nation_id]').val() == JapanID;
                },
                maxlength: 100,
            },
            'name': {
                required: () => {
                    return $('input[name=type_change]:checked').val() == typeChangeName || $(
                        'input[name=type_change]:checked').val() == typeChangeDouble;
                },
                isValidInfoAddress: () => {
                    return $('input[name=type_change]:checked').val() == typeChangeName || $(
                        'input[name=type_change]:checked').val() == typeChangeDouble;
                },
                maxlength: 50,
            },
            'representative_name': {
                required: () => {
                    return $('input[name=representative_name]').length > 0;
                },
                isFullwidth: true,
                maxlength: 50,
            },
        }
        const localMessages = {
            'type_change': {
                required: errorMessageRequiredTypeChange,
            },
            'trademark_infos_address_second': {
                required: errorMessageRequired,
                isValidInfoAddress: errorMessageFormat,
                checkAddressSecond: errorMessageFormat,
            },
            'trademark_infos_address_three': {
                isValidInfoAddress: errorMessageFormat,
                maxlength: errorMessageFormat,
            },
            'name': {
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

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.changeType();
        this.showCostBankTransfer();
        this.hiddenPrefectures();
        this.hiddenActualFeeAndTax();
        this.changeAddressFree();
        if (!changeInfoRegisterDraft) {
            this.hiddenDefault();
        } else {
            if (changeInfoRegisterDraft.payer_info.payment_type == BANK_TRANSFER) {
                $('#hidden_cost_bank_transfer').show()
            }
        }
        this.formSubmit();
        let type = $('input[name=type_change]:checked').val()
        this.showFormChangeTradeMarkInfo(type);
        this.onChangeNation()
        if ($('.type_change:checked')) {
            let trademarkInfoId = $('.type_change:checked').data('id-trademark-info')
            $('input[name=trademark_info_id]').val(trademarkInfoId)
        }
    }

    //==================================================================
    // Default hidden table when load
    //==================================================================
    hiddenDefault() {
        $('#table-change-name').hide();
        $('#table-change-address').hide();
        // Change Fee Cart
        $('#hidden_fee_change_address').hide();
        $('#hidden_fee_change_name').hide();
        // Hidden Cost Print
        $('#hidden_cost_print_name').hide();
        $('#hidden_cost_print_address').hide();
        // Hidden Actual Fee And Tax
        $('#hidden_actual_fee').hide();
        // Hidden Represetative Name
        $('#hidden_represetative_name').hide();
    }

    //==================================================================
    // Get info trademark info from ajax
    //==================================================================
    showInfoTradeMarkInfoAjax(tradeMarkInfoId) {
        $.ajax({
            method: 'GET',
            url: routeGetTradeMarkInfo,
            data: {
                trademark_info_id: tradeMarkInfoId
            },
            dataType: 'json'
        }).done(function (res) {
            if (Object.keys(res).length != 0) {
                $('#m_prefectures_id').text(res.m_prefecture.name)
                $('#address_second_2').text(res.address_second ? res.address_second : '')
                $('#address_three').text(res.address_three ? res.address_three : '')
                $('#name_change').text(res.name)
            }
        });
    }

    //==================================================================
    // Show form change trademark info
    //==================================================================
    showFormChangeTradeMarkInfo(type) {
        if (type == typeChangeName) {
            $('#table-change-name').show()
            $('#table-change-address').hide()

            $('.title-change-address').hide()
            $('.title-change-name-regis').show()

            // Change Fee Cart
            $('#hidden_fee_change_name').show()
            $('#hidden_fee_change_address').hide()
            // Hidden cost print
            $('#hidden_cost_print_name').show()
            $('#hidden_cost_print_address').hide();
            $('#hidden_represetative_name').show();
            if ($('input[name=payment_type]:checked').val() == BANK_TRANSFER) {
                $('#hidden_cost_bank_transfer').show()
                var subTotal = this.calculateSubTotal(feeChangeNameElement.text().replaceAll(',', ''), 0, costBankTransfer)
                this.calculateActualFee(basePriceChangeName.val(), 0, baseCostBankTransfer.val())
                this.changePriceSubmit(feeChangeNameElement.text().replaceAll(',', ''), 0, costBankTransfer, consPrintNameElement.text().replaceAll(',', ''), 0)
            } else {
                $('#hidden_cost_bank_transfer').hide()
                this.changePriceSubmit(feeChangeNameElement.text().replaceAll(',', ''), 0, 0, 0)
                var subTotal = this.calculateSubTotal(feeChangeNameElement.text().replaceAll(',', ''), 0)
                this.calculateActualFee(basePriceChangeName.val(), 0)
            }

            this.calculateTotalAmount(subTotal, consPrintNameElement.text().replaceAll(',', ''), 0)
        } else if (type == typeChangeAddress) {
            $('#table-change-name').hide()
            $('#table-change-address').show()

            $('.title-change-address').show()
            $('.title-change-name-regis').hide()

            // Change Fee Cart
            $('#hidden_fee_change_name').hide()
            $('#hidden_fee_change_address').show()
            // Hidden cost print
            $('#hidden_cost_print_address').show()
            $('#hidden_cost_print_name').hide()
            $('#hidden_represetative_name').show();
            if ($('input[name=payment_type]:checked').val() == BANK_TRANSFER) {
                $('#hidden_cost_bank_transfer').show()
                this.changePriceSubmit(0, feeChangeAddressElement.text().replaceAll(',', ''), costBankTransfer, 0, consPrintAddressElement.text().replaceAll(',', ''))
                var subTotal = this.calculateSubTotal(0, feeChangeAddressElement.text().replaceAll(',', ''), costBankTransfer)
                this.calculateActualFee(0, basePriceChangeAddress.val(), baseCostBankTransfer.val())
            } else {
                $('#hidden_cost_bank_transfer').hide()
                this.changePriceSubmit(0, feeChangeAddressElement.text().replaceAll(',', ''), 0, 0, consPrintAddressElement.text().replaceAll(',', ''))
                var subTotal = this.calculateSubTotal(0, feeChangeAddressElement.text().replaceAll(',', ''))
                this.calculateActualFee(0, basePriceChangeAddress.val())
            }
            this.calculateTotalAmount(subTotal, 0, consPrintNameElement.text().replaceAll(',', ''))
        } else if (type == typeChangeDouble) {
            $('#table-change-name').show()
            $('#table-change-address').show()

            $('.title-change-address').show()
            $('.title-change-name-regis').show()

            // Change Fee Cart
            $('#hidden_fee_change_address').show()
            $('#hidden_fee_change_name').show()
            // Hidden cost print
            $('#hidden_cost_print_name').show()
            $('#hidden_cost_print_address').show()
            $('#hidden_represetative_name').show();
            if ($('input[name=payment_type]:checked').val() == BANK_TRANSFER) {
                $('#hidden_cost_bank_transfer').show()
                this.changePriceSubmit(feeChangeNameElement.text().replaceAll(',', ''), feeChangeAddressElement.text().replaceAll(',', ''), costBankTransfer, consPrintNameElement.text().replaceAll(',', ''), consPrintAddressElement.text().replaceAll(',', ''))
                var subTotal = this.calculateSubTotal(feeChangeNameElement.text().replaceAll(',', ''), feeChangeAddressElement.text().replaceAll(',', ''), costBankTransfer)
                this.calculateActualFee(basePriceChangeName.val(), basePriceChangeAddress.val(), baseCostBankTransfer.val())
            } else {
                $('#hidden_cost_bank_transfer').hide()
                this.changePriceSubmit(feeChangeNameElement.text().replaceAll(',', ''), feeChangeAddressElement.text().replaceAll(',', ''), 0, consPrintNameElement.text().replaceAll(',', ''), consPrintAddressElement.text().replaceAll(',', ''))
                var subTotal = this.calculateSubTotal(feeChangeNameElement.text().replaceAll(',', ''), feeChangeAddressElement.text().replaceAll(',', ''))
                this.calculateActualFee(basePriceChangeName.val(), basePriceChangeAddress.val())
            }

            this.calculateTotalAmount(subTotal, consPrintNameElement.text().replaceAll(',', ''), consPrintAddressElement.text().replaceAll(',', ''))
        }
    }

    //==================================================================
    // Change Price Submit Form
    //==================================================================
    changePriceSubmit(costChangeName, costChangeAddress, costBankTransfer, costPrintName, costPriceAddress) {
        costChangeNameElement.val(costChangeName)
        costChangeAddressElement.val(costChangeAddress)
        costChangeTransferElement.val(costBankTransfer)
        costPrintNameValue.val(costPrintName)
        costPrintAddressValue.val(costPriceAddress)
    }

    //==================================================================
    // Handle event change of type.
    //==================================================================
    changeType() {
        const self = this
        $('.type_change').change(function () {
            let type_change = $(this).val()
            let trademark_info_id = $(this).data('id-trademark-info')
            $('input[name=trademark_info_id]').val(trademark_info_id)
            $('input[name=trademark_info_id]').attr('value', trademark_info_id)
            self.showFormChangeTradeMarkInfo(type_change)
            self.showInfoTradeMarkInfoAjax(trademark_info_id)
        });

        $('body').on('click', '.clear_trademark_info', function () {
            $('.type_change').prop('checked', false);

            $('#table-change-name').hide()
            $('#table-change-address').hide()

            $('.title-change-address').hide()
            $('.title-change-name-regis').hide()

            // Change Fee Cart
            $('#hidden_fee_change_address').hide()
            $('#hidden_fee_change_name').hide()
            // Hidden cost print
            $('#hidden_cost_print_name').hide()
            $('#hidden_cost_print_address').hide()
            $('#hidden_represetative_name').hide();

            if ($('input[name=payment_type]:checked').val() == BANK_TRANSFER) {
                $('#hidden_cost_bank_transfer').show()
                self.changePriceSubmit(0, 0, costBankTransfer, 0, 0)
                var subTotal = self.calculateSubTotal(0, 0, costBankTransfer)
                self.calculateActualFee(0, 0, baseCostBankTransfer.val())
            } else {
                $('#hidden_cost_bank_transfer').hide()
                self.changePriceSubmit(0, 0, 0, 0, 0)
                var subTotal = self.calculateSubTotal(0, 0)
                self.calculateActualFee(0, 0)
            }

            self.calculateTotalAmount(subTotal, 0, 0)
        });
    }

    //==================================================================
    // Show Cost Bank Transfer
    //==================================================================
    showCostBankTransfer() {
        const self = this
        $('.payment_type').on('change', function () {
            if (+$(this).val() === BANK_TRANSFER) {
                if ($('input[name=type_change]:checked').length > 0) {
                    switch ($('input[name=type_change]:checked').val()) {
                        case TYPE_CHANGE_NAME:
                            self.changePriceSubmit(feeChangeNameElement.text().replaceAll(',', ''), 0, costBankTransfer, consPrintNameElement.text().replaceAll(',', ''), 0)
                            var subTotal = self.calculateSubTotal(feeChangeNameElement.text().replaceAll(',', ''), 0, costBankTransfer)
                            self.calculateActualFee(basePriceChangeName.val(), 0, baseCostBankTransfer.val())
                            self.calculateTotalAmount(subTotal, consPrintNameElement.text().replaceAll(',', ''), 0)
                            break;

                        case TYPE_CHANGE_ADDRESS:
                            self.changePriceSubmit(0, feeChangeAddressElement.text().replaceAll(',', ''), costBankTransfer, 0, consPrintAddressElement.text().replaceAll(',', ''))
                            var subTotal = self.calculateSubTotal(0, feeChangeAddressElement.text().replaceAll(',', ''), costBankTransfer)
                            self.calculateActualFee(0, basePriceChangeAddress.val(), baseCostBankTransfer.val())
                            self.calculateTotalAmount(subTotal, 0, consPrintAddressElement.text().replaceAll(',', ''))

                            break;
                        case TYPE_CHANGE_DOUBLE:
                            self.changePriceSubmit(feeChangeNameElement.text().replaceAll(',', ''), feeChangeAddressElement.text().replaceAll(',', ''), costBankTransfer, consPrintNameElement.text().replaceAll(',', ''), consPrintAddressElement.text().replaceAll(',', ''))
                            var subTotal = self.calculateSubTotal(feeChangeNameElement.text().replaceAll(',', ''), feeChangeAddressElement.text().replaceAll(',', ''), costBankTransfer)
                            self.calculateActualFee(basePriceChangeName.val(), basePriceChangeAddress.val(), baseCostBankTransfer.val())
                            self.calculateTotalAmount(subTotal, consPrintNameElement.text().replaceAll(',', ''), consPrintAddressElement.text().replaceAll(',', ''))

                            break;
                    }
                } else {
                    $('.clear_trademark_info').click();
                }
                $('#hidden_cost_bank_transfer').show()
            } else {
                if ($('input[name=type_change]:checked').length > 0) {
                    switch ($('input[name=type_change]:checked').val()) {
                        case TYPE_CHANGE_NAME:
                            self.changePriceSubmit(feeChangeNameElement.text().replaceAll(',', ''), 0, 0, consPrintNameElement.text().replaceAll(',', ''), 0)
                            var subTotal = self.calculateSubTotal(feeChangeNameElement.text().replaceAll(',', ''), 0)
                            self.calculateActualFee(basePriceChangeName.val(), 0)
                            self.calculateTotalAmount(subTotal, consPrintNameElement.text().replaceAll(',', ''), 0)

                            break;
                        case TYPE_CHANGE_ADDRESS:
                            self.changePriceSubmit(0, feeChangeAddressElement.text().replaceAll(',', ''), 0, 0, consPrintAddressElement.text().replaceAll(',', ''))
                            var subTotal = self.calculateSubTotal(0, feeChangeAddressElement.text().replaceAll(',', ''))
                            self.calculateActualFee(0, basePriceChangeAddress.val())
                            self.calculateTotalAmount(subTotal, 0, consPrintAddressElement.text().replaceAll(',', ''))

                            break;
                        case TYPE_CHANGE_DOUBLE:
                            self.changePriceSubmit(feeChangeNameElement.text().replaceAll(',', ''), feeChangeAddressElement.text().replaceAll(',', ''), 0, consPrintNameElement.text().replaceAll(',', ''), consPrintAddressElement.text().replaceAll(',', ''))
                            var subTotal = self.calculateSubTotal(feeChangeNameElement.text().replaceAll(',', ''), feeChangeAddressElement.text().replaceAll(',', ''))
                            self.calculateActualFee(basePriceChangeName.val(), basePriceChangeAddress.val())
                            self.calculateTotalAmount(subTotal, consPrintNameElement.text().replaceAll(',', ''), consPrintAddressElement.text().replaceAll(',', ''))

                            break;
                    }
                } else {
                    $('.clear_trademark_info').click();
                }
                $('#hidden_cost_bank_transfer').hide()
            }
        })
    }

    //==================================================================
    // Change Address Free
    //==================================================================
    changeAddressFree() {
        const self = this;
        $('#is_change_address_free').on('change', function () {
            let type = $('input[name=type_change]:checked').val()
            if ($(this).is(":checked")) {
                // feeChangeAddressElement.text(0)
                // consPrintAddressElement.text(0)
                consPrintNameElement.text(0)
                // $('#price_change_address').text(0)
                self.showFormChangeTradeMarkInfo(type)
            } else {
                // feeChangeAddressElement.text(oldFeeChangeAddress)
                // consPrintAddressElement.text(oldCostPrintAddress)
                consPrintNameElement.text(oldCostPrintAddress)
                // $('#price_change_address').text(baseAddressChangeName)
                self.showFormChangeTradeMarkInfo(type)
            }
        })
    }

    //==================================================================
    // Calculate subtotal
    //==================================================================
    calculateSubTotal(feeChangeName, feeChangeAddress, costBankTransfer = 0) {
        let subTotal = Number(feeChangeName) + Number(feeChangeAddress) + Number(costBankTransfer);
        // if ($('input[name=is_change_address_free]:checked').length) {
            subTotal = Number(feeChangeName) + Number(feeChangeAddress) + Number(costBankTransfer);
        // }
        subTotalElement.text(new Intl.NumberFormat().format(subTotal))

        return subTotal;
    }

    //==================================================================
    // Calculate Total Amount
    //==================================================================
    calculateTotalAmount(subTotal, costPrintAddress, costPrintName) {
        let totalAmount = Number(subTotal) + Number(costPrintAddress) + Number(costPrintName)
        if ($('input[name=is_change_address_free]:checked').length) {
            totalAmount = Number(subTotal) + 0 + Number(costPrintName);
        }
        totalAmountElement.text(new Intl.NumberFormat().format(totalAmount))
        return totalAmount;
    }

    //==================================================================
    // Calculate actual fee , fee tax
    //==================================================================
    calculateActualFee(feeChangeName, feeChangeAddress, costBankTransfer = 0) {
        let actualFee = Number(feeChangeName) + Number(feeChangeAddress) + Number(costBankTransfer);
        let feeTax = (actualFee * setting.value) / 100
        $('#actual_fee').text(new Intl.NumberFormat().format(actualFee))
        $('#tax').text(new Intl.NumberFormat().format(feeTax))
        taxElement.val(feeTax)
        commissionElement.val(actualFee)

        return actualFee;
    }

    //==================================================================
    // Form Submit
    //==================================================================
    formSubmit() {
        // Click Submit
        $('#contents').on('click', 'input[type=submit]', function (e) {
            e.preventDefault();

            let form = $('#form');
            form.valid();
            let hasError = form.find('.notice:visible,.error:visible,.error-validate:visible');
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
    // Handling change event of trademark_infos_m_nation_id input.
    //==================================================================
    hiddenPrefectures() {
        let valueNation = $('#trademark_infos_m_nation_id').find("option:selected").val();
        if (valueNation != 1) {
            $('#hidden_prefectures').css('display', 'none');
            $('#hidden_change_address_free').hide()
        } else {
            $('#hidden_prefectures').css('display', '');
            $('#hidden_change_address_free').show()
        }
    }

    onChangeNation () {
        $('#trademark_infos_m_nation_id').on('change', function () {
            let valueNation = $('#trademark_infos_m_nation_id').find("option:selected").val();
            if (valueNation != idNationJP) {
                $('#hidden_prefectures').css('display', 'none');
                $('#hidden_change_address_free').hide()
            } else {
                $('#hidden_prefectures').css('display', '');
                $('#hidden_change_address_free').show()
            }
        })
    }

    //==================================================================
    // Handling change event of nation input.
    //==================================================================
    hiddenActualFeeAndTax() {
        $('#m_nation_id').on('change', function () {
            let valueNation = $('#m_nation_id').find("option:selected").val();
            if (valueNation != idNationJP) {
                $('#hidden_actual_fee').hide();
            } else {
                $('#hidden_actual_fee').show();
            }
        })
    }
}
new clsChangeAddress02();
