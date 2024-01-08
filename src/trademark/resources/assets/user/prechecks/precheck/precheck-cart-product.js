// Check Box All
let bankTransferFee = 0
let mailingRegisCertFee = 0
let price5YearTo10Year = 0
const checkAll = $('.all-checkbox')
const itemCheckBox = $('input[data-foo="is_choice_user[]"]')
const productChecked = $('#product-checked').html($('input[data-foo="is_choice_user[]"]:checked').length)
const isPackageA = $('#package_a')
const isPackageB = $('#package_b')
const isPackageC = $('#package_c')
const namePackageA = $('#name_package_a').text()
const namePackageB = $('#name_package_b').text()
const namePackageC = $('#name_package_c').text()
const pricePackageA = $('#price_package_a').text().replaceAll(',', '')
const pricePackageB = $('#price_package_b').text().replaceAll(',', '')
const pricePackageC = $('#price_package_c').text().replaceAll(',', '')
const priceProductAddPackageA = $('#price_product_add_pack_a').text().replaceAll(',', '')
const priceProductAddPackageB = $('#price_product_add_pack_b').text().replaceAll(',', '')
const priceProductAddPackageC = $('#price_product_add_pack_c').text().replaceAll(',', '')
const cartNamePackage = $('#name_package')
const cartPricePackage = $('#price_package')
const cartPriceProductAddPackage = $('#price_product_add')
const bankTransferElement = $('#cost_bank_transfer_span')
const mailingRegisCertEl = $('#mailing_regis_cert_el')
const subTotal = $('#sub_total')
const tax = $('#tax')
const commission = $('#commission')
const feeSubmitRegister = $('#fee_submit_register')
const feeSubmitRegisterYear = $('#fee_submit_register_year')
const totalAmount = $('#total_amount')
const checkTimeRegister = $('#period_registration')
// value update payment
const valCostServiceBase = $('#cost_service_base')
const valCostServiceAddProd = $('#cost_service_add_prod')
const valSubtotal = $('#subtotal')
const valCommission = $('#request_commission')
const valTax = $('#request_tax')
const valCostPrintAppOneDistintion = $('#cost_print_application_one_distintion')
const valCost5YearOneDistintion = $('#cost_5_year_one_distintion')
const valCost10YearOneDistintion = $('#cost_10_year_one_distintion')
const valTotalAmount = $('#value_total_amount')
const valSubmitOle = $('#value_fee_submit_ole').val()
const priceFeeSubmit5Year = $('#price_fee_submit_5_year')
const priceFeeSubmit5YearOld = $('#price_fee_submit_5_year_old').val()

// Cart
packageOtherA = [ isPackageC, isPackageB ]
packageOtherB = [ isPackageA, isPackageC ]
packageOtherC = [ isPackageA, isPackageB ]
$('body').off('change', 'select[name=m_nation_id]')
// Call initial
init()

$('#recalculate_money').click(function () {
    init()
})

function init()
{
    $('#product_selected_count').text($('input[data-foo*=is_choice_user]:checked').length > 3 ? $('input[data-foo*=is_choice_user]:checked').length - 3 : 0)
    checkAll.on('change', function() {
        $('#product_selected_count').text($('input[data-foo*=is_choice_user]:checked').length > 3 ? $('input[data-foo*=is_choice_user]:checked').length - 3 : 0)

        const isCheckAll = $(this).prop('checked');
        itemCheckBox.prop('checked', isCheckAll)

        calculatePriceProductPack()
    })

    // Check Box One
    itemCheckBox.on('change', function() {
        const isCheckAll = itemCheckBox.length === $('input[data-foo="is_choice_user[]"]:checked').length;
        $('#product_selected_count').text($('input[data-foo*=is_choice_user]:checked').length > 3 ? $('input[data-foo*=is_choice_user]:checked').length - 3 : 0)
        $('#product-checked').text($('input[data-foo="is_choice_user[]"]:checked').length);
        checkAll.prop('checked', isCheckAll)
        calculatePriceProductPack()
    })

    calculatePackChange()
    onChangePaymentType()
    setEach3UpPriceProd()
    onChangeCheckTimeRegis()
    onChangePackageType()
    onChangeMailingRegisCert()
    onChangePeriodRegisCert()
    onChangeNation()
    costPrintApplicationOneDistintion(getTotalDistinction())
    calculatePriceProductPack()
}

//==================================================================
// Handle event change of package type.
//==================================================================
function onChangePackageType() {
    $('.package_type').on('change', function() {
        // 1: is pack A
        if($(this).is(':checked') && +$(this).val() == 1) {
            $('#tr_fee_submit_register_year').hide()
            $('#fee_submit_register_year').text(0)
        } else {
            $('#tr_fee_submit_register_year').show()
        }

        setEach3UpPriceProd()
        costPrintApplicationOneDistintion(getTotalDistinction())
        calculatePackChange()

        $('#period_registration').change()
        $('#is_mailing_register_cert').change()
    })

    $('.package_type:checked').change()
}

//==================================================================
// Handle event change of mailing register certification input.
//==================================================================
function onChangeMailingRegisCert() {
    $('#is_mailing_register_cert').change(function() {
        $(this).closest('.eol').find('.error').remove();

        if($(this).is(':checked')) {
            const choosePackA = $('#package_a').is(':checked')

            if (choosePackA) {
                $(this).closest('.eol').append(`<span class="error d-block">${support_U011_E007}</span>`);
            } else {
                mailingRegisCertFee = Number($('#mailing_regis_cert_el').text().replaceAll(',', ''))
                $('.d_none_mailing_regis_cert').removeClass('d-none')
            }
        } else {
            mailingRegisCertFee = 0
            $('.d_none_mailing_regis_cert').addClass('d-none')
        }
        calculatePackChange()
        calculatePriceProductPack()
    }).change()
}

//==================================================================
// Handle event change of period registration input.
//==================================================================
function onChangePeriodRegisCert() {
    $('#period_registration').change(function() {
        if($(this).is(':checked')) {
            price5YearTo10Year = Number($('#change_5yrs_to_10yrs').text().replaceAll(',', ''))
            $('.tr_change_5yrs_to_10yrs').removeClass('d-none')
        } else {
            $('.tr_change_5yrs_to_10yrs').addClass('d-none')
            price5YearTo10Year = 0
        }
        calculatePackChange()
        calculatePriceProductPack()
    }).change()
}

//==================================================================
// Handle event change of payment_type.
//==================================================================
function onChangePaymentType() {
    $('.payment_type').on('change', function () {
        if($(this).is(':checked') && $(this).val() == BANK_TRANSFER) {
            bankTransferFee = +$('#cost_bank_transfer_span').val()
            $('.cost_bank_transfer_tr').removeClass('d-none')
        } else {
            $('.cost_bank_transfer_tr').addClass('d-none')
            bankTransferFee = 0
        }
        calculatePackChange()
    }).change()
}

//==================================================================
// Handle event on change of input check time register.
//==================================================================
function onChangeCheckTimeRegis() {
    checkTimeRegister.on('change', function() {
        const isChecked = $(this).is(":checked");
        const choosePackA = $('#package_a').is(':checked')

        $(this).closest('.eol').find('.error').remove();
        if (isChecked && choosePackA) {
            $(this).closest('.eol').append(`<span class="error d-block">${support_U011_E007}</span>`);
        }

        if (checkTimeRegister.is(':checked')) {
            $('.tr_change_5yrs_to_10yrs').removeClass('d-none');
            $('#text_5yrs_10yrs2').text(stampFee10yrs)
            //$('#fee_submit_register_year') click 10 year
            if($('.package_type:checked').val() != 1) {
                feeSubmitRegisterYear.html(fmPice(getTotalDistinction() * feeSubmit.pof_1st_distinction_10yrs))
            } else {
                feeSubmitRegisterYear.text(0)
            }
            //$('#cost_10_year_one_distintion') add value input
            valCost10YearOneDistintion.val(feeSubmitRegisterYear.text())
            //$('#total_amount') view (subTotal + feeSubmitRegisterYear)
            totalAmount.html(
                fmPice(Number(subTotal.text().replaceAll(',', ''))
                + Number(feeSubmitRegisterYear.text().replaceAll(',', ''))
                + Number(feeSubmitRegister.text().replaceAll(',', '')))
            )
            // $('#price_fee_submit_5_year') add price 10 year view
            priceFeeSubmit5Year.html(fmPice(feeSubmit.pof_1st_distinction_10yrs))
            valCost10YearOneDistintion.val(feeSubmitRegisterYear.html().replaceAll(',', ''))
            valTotalAmount.val(totalAmount.html().replaceAll(',', ''));
            //$('#cost_5_year_one_distintion')
            valCost5YearOneDistintion.val('')
        } else {
            $('.tr_change_5yrs_to_10yrs').addClass('d-none');
            $('#text_5yrs_10yrs2').text(stampFee5yrs)
            if($('.package_type:checked').val() != 1) {
                feeSubmitRegisterYear.html(fmPice(getTotalDistinction() * feeSubmit.pof_1st_distinction_5yrs))
            } else {
                feeSubmitRegisterYear.text(0)
            }
            priceFeeSubmit5Year.html(fmPice(priceFeeSubmit5YearOld))
            totalAmount.html(
                fmPice(Number(subTotal.text().replaceAll(',', ''))
                + Number(feeSubmitRegister.text().replaceAll(',', ''))
                + Number(feeSubmitRegisterYear.text().replaceAll(',', '')))
            )

            valCost5YearOneDistintion.val(feeSubmitRegisterYear.html().replaceAll(',', ''))
            valCost10YearOneDistintion.val('')
            valTotalAmount.val(totalAmount.html().replaceAll(',', ''));
        }

        costPrintApplicationOneDistintion(getTotalDistinction())
    })
}

//==================================================================
// Set price for Each3UpPriceProd
//==================================================================
function setEach3UpPriceProd() {
    const val = Number($('.package_type:checked').attr('value'))
    switch (val) {
        case 1:
            $('#each_3_prod_pack').html(fmPice(priceProductAddPackageA))
            valCostServiceAddProd.val(priceProductAddPackageA)
            break;
        case 2:
            $('#each_3_prod_pack').html(fmPice(priceProductAddPackageB))
            valCostServiceAddProd.val(priceProductAddPackageB)
            break;
        case 3:
            $('#each_3_prod_pack').html(fmPice(priceProductAddPackageC))
            valCostServiceAddProd.val(priceProductAddPackageC)
            break;
    }
}

//==================================================================
// Calculate subtotal, total amount when change pack.
//==================================================================
function calculatePackChange() {
    const valuePack = +$('.package_type:checked').val()
    switch (valuePack) {
        case 1:
            calculatePrice(isPackageA, packageOtherA, namePackageA, pricePackageA, priceProductAddPackageA);
            break;
        case 2:
            calculatePrice(isPackageB, packageOtherB, namePackageB, pricePackageB, priceProductAddPackageB);
            break;
        case 3:
            calculatePrice(isPackageC, packageOtherC, namePackageC, pricePackageC, priceProductAddPackageC);
            break;
        default:
            break;
    }
}

//==================================================================
// Calculate total amount when change product of pack.
//==================================================================
function calculatePriceProductPack() {
    const isCheckedPackageA = isPackageA.attr('checked');
    const isCheckedPackageB = isPackageB.attr('checked');
    const isCheckedPackageC = isPackageC.attr('checked');
    var chunks = [];
    for (var i = 0; i < $('input[data-foo="is_choice_user[]"]:checked').length;) {
        chunks.push($('input[data-foo="is_choice_user[]"]:checked').slice(i, i += 3));
    }

    const LengthProductChecked = chunks.length
    let countProductPackCheck = $('input[data-foo*=is_choice_user]:checked').length > 3 ? $('input[data-foo*=is_choice_user]:checked').length - 3 : 0;
    const valuePack = +$('.package_type:checked').val()
    switch (valuePack) {
        case 1:
            checkPriceProductPackage(isCheckedPackageA, priceProductAddPackageA, LengthProductChecked);
            break;
        case 2:
            checkPriceProductPackage(isCheckedPackageB, priceProductAddPackageB, LengthProductChecked);
            break;
        case 3:
            checkPriceProductPackage(isCheckedPackageC, priceProductAddPackageC, LengthProductChecked);
            break;
        default:
            break;
    }
}

//==================================================================
// Calculate subtotal, commission, products price , total price.
//==================================================================
function calculatePrice( packageEntry, packageOther = [], namePackageEntry, pricePackageEntry, priceProductAddPackageEntry) {
    const isChecked = $(packageEntry).attr('checked', true);
    packageOther[0].attr('checked', false);
    packageOther[1].attr('checked', false);
    bankTransferFee = $('.payment_type_transfer').is(':checked') ? Number(bankTransferElement.text().replaceAll(',', '')) : 0

    var chunks = [];
    for (var i = 0; i < $('input[data-foo="is_choice_user[]"]:checked').length;) {
        chunks.push($('input[data-foo="is_choice_user[]"]:checked').slice(i, i += 3));
    }

    if (isChecked) {
        cartNamePackage.html(namePackageEntry)
        cartPricePackage.html(fmPice(pricePackageEntry))
        const eachThreeProdFee = fmPice(priceProductAddPackageEntry * (chunks.length - 1))
        if (chunks.length > 1) {
            cartPriceProductAddPackage.html(eachThreeProdFee)
            $('input[name=price_product_add]').attr('value', eachThreeProdFee.replaceAll(',', ''))
            // valCostServiceAddProd.val(priceProductAddPackageEntry);
        }
        const commissionPrice = (Number(cartPricePackage.text().replaceAll(',', '')) / (1 + setting.value/100))
            + (Number(cartPriceProductAddPackage.text().replaceAll(',', '')) / (1 + setting.value/100))
            + (bankTransferFee / (1 + setting.value /100))
            + (mailingRegisCertFee / (1 + setting.value /100))
            + (price5YearTo10Year / (1 + setting.value /100))

        subTotal.html(
            fmPice(
                Number(cartPricePackage.text().replaceAll(',', ''))
                + Number(cartPriceProductAddPackage.text().replaceAll(',', ''))
                + bankTransferFee
                + mailingRegisCertFee
                + price5YearTo10Year
            )
        )
        tax.html(fmPice(Number((commissionPrice * setting.value) / 100)))
        commission.html(fmPice(commissionPrice));

        totalAmount.html(fmPice(Number(subTotal.text().replaceAll(',', ''))
            + Number(feeSubmitRegister.text().replaceAll(',', ''))
            + Number(feeSubmitRegisterYear.text().replaceAll(',', '')))
        )
        valCostServiceBase.val(pricePackageEntry);
        valSubtotal.val(subTotal.html().replaceAll(',', ''))
        valCommission.val(commission.html().replaceAll(',', ''))
        valTax.val(tax.html().replaceAll(',', ''));
        valCostPrintAppOneDistintion.val(feeSubmitRegister.html().replaceAll(',', ''))
        valTotalAmount.val(totalAmount.html().replaceAll(',', ''));
    }
}

//==================================================================
// Handle re-calculate price in payment modal.
//==================================================================
function checkPriceProductPackage(packageCheck, priceProductAddPackage, lengthProductChecked) {
    bankTransferFee = $('.payment_type_transfer').is(':checked') ? Number(bankTransferElement.text().replaceAll(',', '')) : 0

    if (packageCheck) {
        if (lengthProductChecked > 1) {
            const cartPrice = fmPice(priceProductAddPackage * (lengthProductChecked - 1))
            cartPriceProductAddPackage.html(cartPrice)
            $('input[name=price_product_add]').attr('value', cartPrice.replaceAll(',', ''))
        } else {
            cartPriceProductAddPackage.html(0)
            $('input[name=price_product_add]').attr('value', 0)
        }
        const commissionPrice = (Number(cartPricePackage.text().replaceAll(',', '')) / (1 + setting.value/100))
            + (Number(cartPriceProductAddPackage.text().replaceAll(',', '')) / (1 + setting.value/100))
            + (bankTransferFee / (1 + setting.value /100))
            + (mailingRegisCertFee / (1 + setting.value /100))
            + (price5YearTo10Year / (1 + setting.value /100))
        subTotal.html(
            fmPice(Number(cartPricePackage.text().replaceAll(',', ''))
                + Number(cartPriceProductAddPackage.text().replaceAll(',', ''))
                + bankTransferFee
                + mailingRegisCertFee
                + price5YearTo10Year
            )
        )
        totalAmount.html(fmPice(
            Number(subTotal.text().replaceAll(',', ''))
            + Number(feeSubmitRegister.text().replaceAll(',', ''))
            + Number(feeSubmitRegisterYear.text().replaceAll(',', '')))
        )
        tax.html(fmPice(Number(commissionPrice * setting.value) / 100))
        // valCostServiceAddProd.val(cartPriceProductAddPackage.text().replaceAll(',', ''));
        commission.html(fmPice(commissionPrice));
        valSubtotal.val(subTotal.html().replaceAll(',', ''))
        valCommission.val(commission.html().replaceAll(',', ''))
        valTax.val(tax.html().replaceAll(',', ''));
    }
}

$('#toggle-btn').click(function() {
    if ($('#toggle-example').is(':hidden')) {
        $('#toggle-example').show();
    } else {
        $('#toggle-example').hide();
    }
});

    getTotalCheclboxIsChecked();
    checkedAllDefault();
    //Check box all table
    $('.all-checkbox').click(function() {
        if ($(this).is(':checked')) {
            $('.single-checkbox').prop('checked', true)
        } else {
            $('.single-checkbox').prop('checked', false)
        }
        getTotalCheclboxIsChecked()
        getTotalDistinction()
        $('.total-dis').text(getTotalDistinction())
        $('#sum_distintion').attr('value',getTotalDistinction())
        costPrintApplicationOneDistintion(getTotalDistinction())
    })

    //Single checkbox
    $('body').on('click', '.single-checkbox', function() {
        if ($('.single-checkbox:checked').length == $('.single-checkbox').length) {
            $('.all-checkbox').prop('checked', true)
        } else {
            $('.all-checkbox').prop('checked', false)
        }
        getTotalCheclboxIsChecked()
        getTotalDistinction()
        $('.total-dis').text(getTotalDistinction())
        $('#sum_distintion').attr('value',getTotalDistinction())
        costPrintApplicationOneDistintion(getTotalDistinction())
    })

    // get total checkbox is checked
    function getTotalCheclboxIsChecked() {
        let totalChecked = $('.single-checkbox:checked').length
        $('.total-checkbox-checked').text(totalChecked)
    }

    function costPrintApplicationOneDistintion(param) {
        let oneDivision = param > 0 ? 1 : 0
        let oneDivisionMinus = param - 1 > 0 ? param - 1 : 0
        $('#one_division').html(oneDivision)
        $('#mDistintionPayment').html(oneDivisionMinus)

        let feeRegist = (oneDivision ? pricePackage[0][2]['pof_1st_distinction_5yrs'] : 0) + (oneDivisionMinus * pricePackage[0][2]['pof_2nd_distinction_5yrs']);
        $('#fee_submit_register').html(fmPice(feeRegist))

        let feeSubmitRegisterYrs = 0
        if($('#period_registration').is(':checked')) {
            feeSubmitRegisterYrs = feeSubmit.pof_1st_distinction_10yrs
        } else {
            feeSubmitRegisterYrs = feeSubmit.pof_1st_distinction_5yrs
        }
        $('#sumDistintion2').html(param)
        if ($('.package_type:checked').val() != 1) {
            $('#fee_submit_register_year').html(fmPice(param * feeSubmitRegisterYrs))
        } else {
            $('#fee_submit_register_year').text(0)
        }
    }

    $('.toggle-info').hide();
    //show hide collapse-div
    $('.hideShowClick').on('click dblclick', function() {
        $('.toggle-info').stop().slideToggle('slow');
        ($('.icon-text').text()) == '+' ? $('.icon-text').text('-'): $('.icon-text').text('+');
    });
    //check all checkbox default
    function checkedAllDefault() {
        if ($('.single-checkbox:checked').length == $('.single-checkbox').length) {
            $('.all-checkbox').prop('checked', true)
        } else {
            $('.all-checkbox').prop('checked', false)
        }
    }
    $('.total-dis').text(getTotalDistinction())
    $('#sum_distintion').attr('value',getTotalDistinction())

    function getTotalDistinction() {
        let nameData = []
        let uniqueNames = [];
        $('.single-checkbox:checked').filter(function(index, el) {
            let nameDis = $(el).data('name-distinction')
            nameData.push(nameDis)
        })
        $.each(nameData, function(i, ele){
            if($.inArray(ele, uniqueNames) === -1) uniqueNames.push(ele);
        });
        return uniqueNames.length

    }

//hide show commission if nation is japanese
function onChangeNation() {
    $('#m_nation_id').change(function () {
        if ($(this).val() == nationJPId) {
            $('.breakdown-real-fee').css('display', 'block')
            $('.commission_is_ja').show();
        } else {
            $('.breakdown-real-fee').css('display', 'none')
            $('.commission_is_ja').hide();
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
// Calculate subtotal, commission, products price , total price.
//==================================================================
function fmPice(val) {
    return new Intl.NumberFormat('en-us').format(Math.floor(val))
}
