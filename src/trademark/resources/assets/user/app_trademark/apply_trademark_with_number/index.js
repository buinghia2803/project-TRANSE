var mDistinctionsChoose = []
var mProductsChoose = []
var infoCart = null
var redirectTo = ''

//call ajax
hideShowInfoFeeBankTransfer()
hideShowInfoCommission()
getTotalMProductAndMDistinctionChoose()
ajaxGetInfoPayment()
disabledRegisterTenYear()
hideShowCostRegistrationCertificateCart()
hideShowInfoPeriodRegistrationCart()
showLabelPackOnCart()
setAllCheckBoxDefault()

//check all product
$('body').on('click', '#check-all', function(e) {
    if($(this).is(':checked')) {
        $('.single-checkbox').prop('checked', true)
    } else {
        $('.single-checkbox').prop('checked', false)
    }
    getTotalMProductAndMDistinctionChoose()
    ajaxGetInfoPayment()

    validateNotCheckProduct(e, false)
});

//check single
$('body').on('click', '.single-checkbox', function(e) {
    setAllCheckBoxDefault()
    getTotalMProductAndMDistinctionChoose()
    ajaxGetInfoPayment()
    validateNotCheckProduct(e, false)
});

//on change is_mailing_regis_cert
$('#is_mailing_regis_cert').on('change', function() {
    hideShowCostRegistrationCertificateCart()
    ajaxGetInfoPayment()
});

//submit form
$('body').on('click', 'input[type=submit]', function (e) {
    //set url redirect to when submit
    if($(this).hasClass('submitRedirectToCommonPayment')) {
        redirectTo = redirectToCommonPayment
    } else if($(this).hasClass('submitRedirectToQuoute')) {
        redirectTo = redirectToQuote
    } else if($(this).hasClass('submitRedirectToU000AnkenTop')) {
        redirectTo = redirectToAnkenTop
    } else if($(this).hasClass('submitRedirectToU021')) {
        redirectTo = redirectToU021
    } else {
        redirectTo = ''
    }
    $('.redirect_to').val(redirectTo)

    if ($('.single-checkbox').length > 0) {
        $.each($('.single-checkbox'), function (item) {
            validateNotCheckProduct($(item), false);
        })
    } else {
        $('#error-table-choose-prod').show()
    }

    let form = $('#form');
    form.valid();
    let hasError = $('#form').find('.error-validate:visible,.notice:visible,.error:visible');
    if (hasError.length == 0 && form.valid()) {
        if($(this).hasClass('submitRedirectToQuoute')) {
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
})

//Set check all default
function setAllCheckBoxDefault() {
    if ($('.single-checkbox:checked').length == $('.single-checkbox').length) {
        $('#check-all').prop('checked', true);
    } else {
        $('#check-all').prop('checked', false);
    }
}

/**
 * Get total m_product choose
 */
function getTotalMProductAndMDistinctionChoose() {
    mProductsChoose = []
    mDistinctionsChoose = []
    $('.single-checkbox:checked').each(function(indx, item) {
        mProductsChoose.push($(item).val())
        //m_distinction
        let mDistinctionId = $(item).closest('tr').data('distinction-id')
        if (!mDistinctionsChoose.includes(mDistinctionId)) {
            mDistinctionsChoose.push(mDistinctionId)
        }
    })
    $('#product-checked').text(mProductsChoose.length)
    $('#total_distinction').text(mDistinctionsChoose.length)
    $('.input_total_distinction').val(mDistinctionsChoose.length)

    return mProductsChoose.length
}

//on change pack radio
$('.package_type').on('change', function() {
    showLabelPackOnCart()
    disabledRegisterTenYear()
    hideShowInfoPeriodRegistrationCart()
    ajaxGetInfoPayment()
});

function showLabelPackOnCart() {
    let labelPackByPack = ''
    let elementChecked = $('.package_type:checked').val()
    if(elementChecked == packA) {
        $('#tr_fee_submit_register_year').hide()
        labelPackByPack = labelPackA
    } else if(elementChecked == packB) {
        labelPackByPack = labelPackB
        $('#tr_fee_submit_register_year').show()
    } else {
        labelPackByPack = labelPackC
        $('#tr_fee_submit_register_year').show()
    }
    $('.label_text_by_pack').text(labelPackByPack)
}

//on change payment_type
$('.payment_type').on('change', function() {
    hideShowInfoFeeBankTransfer()
    ajaxGetInfoPayment()
});

//on change m_nation_id
$('#m_nation_id').on('change', function() {
    hideShowInfoCommission()
});

//on change #is_mailing_register_cert
$('#is_mailing_register_cert').on('change', function() {
    if($(this).is(':checked')) {
        $('.tr_cost_registration_certificate').show();
    } else {
        $('.tr_cost_registration_certificate').hide();
    }
    ajaxGetInfoPayment()
});

//on change period_registration
$('#period_registration').on('change', function() {
    hideShowInfoPeriodRegistrationCart()
    ajaxGetInfoPayment()
});


//on click recalculationCart
$('.recalculationCart').on('click', function() {
    ajaxGetInfoPayment()
});

function hideShowInfoPeriodRegistrationCart() {
    if($('#period_registration').is(':checked')) {
        $('.tr_change_5yrs_to_10yrs').show();
    } else {
        $('.tr_change_5yrs_to_10yrs').hide();
    }
}
//Hide show info fee bank transfer
function hideShowInfoFeeBankTransfer() {
    $('.tr_cost_bank_transfer').hide()
    if($('.payment_type_transfer').is(':checked')) {
        $('.tr_cost_bank_transfer').show()
    }
}

//Hide show info commission
function hideShowInfoCommission() {
    $('.tr_commission').hide()
    if($('#m_nation_id').val() == japanId) {
        $('.tr_commission').show()
    }
}

//hide show info cost_registration_certificate cart
function hideShowCostRegistrationCertificateCart() {
    if($('#is_mailing_regis_cert').is(':checked')) {
        $('.tr_cost_registration_certificate').show()
        return
    }
    $('.tr_cost_registration_certificate').hide()
}

//Get info payment cart
function ajaxGetInfoPayment() {
    let packageType = $('.package_type:checked').val();
    let paymentType = $('.payment_type:checked').val();
    let isMailingRegisterCert = $('#is_mailing_regis_cert').is(':checked') ? 1 : 0;
    let periodRegistration = $('#period_registration').is(':checked') ? 2 : 1;
    let totalDistinction = mDistinctionsChoose.length

    let data = {
        package_type: packageType,
        payment_type: paymentType,
        m_product_ids: mProductsChoose,
        is_mailing_register_cert: isMailingRegisterCert,
        period_registration: periodRegistration,
        total_distinction: totalDistinction
    };

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: routeAjaxGetInfoPaymentU031b,
        method: 'POST',
        data_type: 'json',
        data: data,
    }).done(function(res) {
        if (res.status) {
            showInfoCart(res.data)
        }
    });
}

//show info cart
function showInfoCart(data) {
    $('.cost_service_base').text(numberFormat(data?.cost_service_base))
    $('.cost_service_add_prod').text(numberFormat(data?.cost_service_add_prod))
    $('.cost_service_add_prod_default').text(numberFormat(data?.cost_service_add_prod_default))
    $('.cost_registration_certificate').text(numberFormat(data?.cost_registration_certificate))
    $('#change_5yrs_to_10yrs').text(numberFormat(data?.cost_change_registration_period))
    $('.cost_bank_transfer').text(numberFormat(data?.cost_bank_transfer))
    $('.subtotal').text(numberFormat(data?.subtotal))
    $('.total_product_choose').text(data?.total_product_choose)
    $('.commission').text(numberFormat(data?.commission))
    $('.tax').text(numberFormat(data?.tax))
    $('.cost_print_application_one_distintion').text(numberFormat(data?.cost_print_application_one_distintion))
    $('.cost_print_application_add_distintion').text(numberFormat(data?.cost_print_application_add_distintion))
    $('.count_distintion_choose').text(mDistinctionsChoose.length)
    $('.count_cost_print_application_one_distintion').text(mDistinctionsChoose.length > 0 ? 1 : 0)
    $('.count_cost_print_application_add_distintion').text(mDistinctionsChoose.length > 0 ? mDistinctionsChoose.length - 1 : 0)
    $('.total_fee_register_for_csc').text(numberFormat(data?.total_fee_register_for_csc))
    $('#sumDistintion2').text(mDistinctionsChoose.length)
    $('#text_5yrs_10yrs2').text($('#period_registration').is(':checked') ? 10 : 5)
    $('#cost_year_one_distintion').text(numberFormat(data?.cost_year_one_distintion))
    $('.cost_year_one_distintion').val(data?.cost_year_one_distintion)
    $('#fee_submit_register_year').text(numberFormat(data?.fee_submit_register_year))
    $('#value_fee_submit_ole').val(data?.fee_submit_register_year)
    $('#total_amount').text(numberFormat(data?.total_amount))
    $('.tax_percentage').text(data?.tax_percentage)
    $('.count_service_add_prod').text(data?.count_service_add_prod)
    $('.cost_prod_price').text(numberFormat(data?.cost_prod_price))
    $('.sum_prod_price').text(numberFormat(data?.sum_prod_price))
}

//format number price
function numberFormat(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

//disabled register 10 year if choose pack A
function disabledRegisterTenYear() {
    if($('.package_type:checked').val() == packA) {
        $('#period_registration').prop('disabled', true).css('cursor', 'not-allowed').prop('checked', false);
        $('.error-period_registration').hide()

        $('#is_mailing_regis_cert').prop('disabled', true).css('cursor', 'not-allowed').prop('checked', false);
    } else {
        $('#period_registration').prop('disabled', false).css('cursor', 'pointer');
        $('.error-period_registration').hide()

        $('#is_mailing_regis_cert').prop('disabled', false).css('cursor', 'pointer');
        $('#is_mailing_regis_cert').closest('.eol').find('.error').remove();
    }
}

//validate not check product  productIdsChoose-16
function validateNotCheckProduct(e, flug) {
    if ($('.single-checkbox').length > 0) {
        if($('.single-checkbox:checked').length == 0){
            $('#error-table-choose-prod').show()
            if(flug) {
                e.stopPropagation();
                e.preventDefault();
                document.querySelector('.js-scrollable').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        } else {
            $('#error-table-choose-prod').hide()
        }
    }
}
