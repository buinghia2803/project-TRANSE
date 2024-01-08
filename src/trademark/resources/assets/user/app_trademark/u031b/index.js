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
    disabledIsMailingRegisCert()
    hideShowCostRegistrationCertificateCart()
    ajaxGetInfoPayment()
});

$('body').on('change', 'input[name=pack]', function (e) {
    var boxes = $('input[name=pack]:checked')
    $('#list_pack').next('.error_pack').remove()
    if (!boxes.length) {
        $('#list_pack').after(`<div class="notice red error_pack">${Common_E025}</div>`)
    }
})

//submit form
$('body').on('click', 'input[type=submit]', function (e) {
    e.preventDefault();

    //set url redirect to when submit
    if($(this).hasClass('submitRedirectToCommonPayment')) {
        validateNotCheckProduct(e, false)
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

    let form = $(this).closest('form');
    form.valid();

    $('input[name=pack]').change()

    let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
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
        let firstError = form.find('.error-validate:visible,.notice:visible,.error:visible').first();
        window.scroll({
            top: firstError.offset().top - 100,
            behavior: 'smooth'
        });
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
    disabledIsMailingRegisCert()
    hideShowInfoPeriodRegistrationCart()
    ajaxGetInfoPayment()
});

function showLabelPackOnCart() {
    let labelPackByPack = ''
    let elementChecked = $('.package_type:checked').val()
    // 1: is pack A
    if(elementChecked == packA) {
        labelPackByPack = labelPackA
        $('#tr_fee_submit_register_year').hide()
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
        total_distinction: totalDistinction,
        from_page: 'u031b'
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
    $('.cost_service_base').text(fmPice(data?.cost_service_base))
    $('.cost_service_add_prod').text(fmPice(data?.cost_service_add_prod))
    $('.cost_service_add_prod_default').text(fmPice(data?.cost_service_add_prod_default))
    $('.cost_registration_certificate').text(fmPice(data?.cost_registration_certificate))
    $('#change_5yrs_to_10yrs').text(fmPice(data?.cost_change_registration_period))
    $('.cost_bank_transfer').text(fmPice(data?.cost_bank_transfer))
    $('input[name=cost_bank_transfer]').val(data?.cost_bank_transfer)
    $('.subtotal').text(fmPice(data?.subtotal))
    $('.total_product_choose').text(data?.total_product_choose)
    $('.commission').text(fmPice(data?.commission))
    $('.tax').text(fmPice(data?.tax))
    $('.cost_print_application_one_distintion').text(fmPice(data?.cost_print_application_one_distintion))
    $('.cost_print_application_add_distintion').text(fmPice(data?.cost_print_application_add_distintion))
    $('.count_distintion_choose').text(mDistinctionsChoose.length)
    $('.count_cost_print_application_one_distintion').text(mDistinctionsChoose.length > 0 ? 1 : 0)
    $('.count_cost_print_application_add_distintion').text(mDistinctionsChoose.length > 0 ? mDistinctionsChoose.length - 1 : 0)
    $('.total_fee_register_for_csc').text(fmPice(data?.total_fee_register_for_csc))
    $('#sumDistintion2').text(mDistinctionsChoose.length)
    $('#text_5yrs_10yrs2').text($('#period_registration').is(':checked') ? 10 : 5)
    $('#cost_year_one_distintion').text(fmPice(data?.cost_year_one_distintion))
    if ($('.package_type:checked').val() != 1) {
        $('#fee_submit_register_year').text(fmPice(data?.fee_submit_register_year))
    } else {
        $('#fee_submit_register_year').text(0)
    }
    $('#total_amount').text(fmPice(data?.total_amount))
    $('.tax_percentage').text(Math.floor(data?.tax_percentage * 100)/100)
    $('.count_service_add_prod').text(data?.count_service_add_prod)
}

//format number price
function numberFormat(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

//==================================================================
// Calculate subtotal, commission, products price , total price.
//==================================================================
function fmPice(val) {
    return new Intl.NumberFormat('en-us').format(Math.floor(val))
}

//disabled register 10 year if choose pack A
function disabledRegisterTenYear() {
    if($('.package_type:checked').val() == packA) {
        $('#period_registration').prop('disabled', true).css('cursor', 'not-allowed').prop('checked', false);
    } else {
        $('#period_registration').prop('disabled', false).css('cursor', 'pointer');
    }
}

//disabled is_mailing_regis_cert if choose pack A
function disabledIsMailingRegisCert() {
    let el = $('#is_mailing_regis_cert');
    el.closest('.eol').find('.error').remove();

    el.prop('disabled', false).css('cursor', 'pointer');

    if (el.is(':checked')) {
        const choosePackA = $('#package_a').is(':checked')

        el.prop('disabled', false);
        if (choosePackA) {
            el.prop('disabled', true).css('cursor', 'not-allowed').prop('checked', false);
        }
    }
}

//validate not check product  productIdsChoose-16
function validateNotCheckProduct(e, flug) {
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
