$(document).ready(function() {
    //ready
    showHideProductCheckbox()
    ajaxGetInfoPayment()
    showHideInfoNationIsJapan()
    showHideCostBankTransfer()
    getLabelTypePrecheckCart()

    //on change type_precheck
    $('.type_precheck').on('change', function() {
        showHideProductCheckbox()
        getLabelTypePrecheckCart()
        ajaxGetInfoPayment()
    });

    function getLabelTypePrecheckCart() {
        let typePrecheck = $('.type_precheck:checked').val()
        if(typePrecheck == typePrecheckSimple) {
            $('.label_type_precheck_cart').text(labelPrecheckSimple)
        } else {
            $('.label_type_precheck_cart').text(labelPrecheckGender)
        }
    }

    //on change all-checkbox
    $('.all-checkbox').on('change', function() {
        showHideProductCheckbox()
        ajaxGetInfoPayment()

        $(this).closest('th').find('.notice').remove();
    });

    //on change single-checkbox
    $('.single-checkbox').on('change', function() {
        showHideProductCheckbox()
        ajaxGetInfoPayment()
    });

    //on change m_nation_id
    $('#m_nation_id').on('change', function() {
        showHideInfoNationIsJapan()
    });

    //Change payment_type radio
    $('.payment_type').on('click', function() {
        //call ajax get info payment
        ajaxGetInfoPayment()
        showHideInfoNationIsJapan()
        showHideCostBankTransfer()
    });

    //get again info payment ajax
    $('.getInfoPayment').on('click', function () {
        //call ajax get info payment
        ajaxGetInfoPayment()
    });

    $('body').on('click', '[type=submit]', function (e) {
        e.preventDefault();

        if ($(this).hasClass('saveToShowQuotes')) {
            $('#param-code').val(_QUOTES)
        } else if($(this).hasClass('saveGoToAnkenTop')) {
            $('#param-code').val(_ANKEN)
        } else if($(this).hasClass('goToCommonPayment')) {
            $('#param-code').val(_PAYMENT)
        }

        let form = $('#form');
        form.valid();

        let hasError = form.find('.notice:visible,.error:visible,.error-validate:visible');
        if (hasError.length == 0 && form.valid()) {
            if ($(this).hasClass('saveToShowQuotes')) {
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
        }
    });

    //Ajax get info payment
    function ajaxGetInfoPayment() {
        let typePrecheck = $('.type_precheck:checked').val();
        let paymentType = $('.payment_type:checked').val();
        let idsProduct = [];
        $('.single-checkbox:checked').filter(function() {
            idsProduct.push(this.value)
        })
        //if choose type_precheck && choose product
        let data = { type_precheck: typePrecheck,  m_product_choose: idsProduct, payment_type: paymentType };
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: routeAjaxGetInfoPayment,
            method: 'POST',
            data_type: 'json',
            data: data,
        }).done(function(res) {
            if (res.status) {
                showHideInfoNationIsJapan()
                //update info payment in cart
                updateInfoPaymentCart(res.data)
            }
        });
    }

    //update infor payment cart from ajax
    function updateInfoPaymentCart(data) {
        $('.cost_service_base').text(numberFormat(data?.cost_service_base))
        $('.cost_service_add_prod').text(numberFormat(data?.cost_service_add_prod))
        $('.cost_bank_transfer').text(numberFormat(data?.cost_bank_transfer))
        $('.subtotal').text(numberFormat(data?.subtotal))
        $('.commission').text(numberFormat(data.commission))
        $('.tax').text(numberFormat(numberFormat(data?.tax)))
        $('.tax_percentage').text(data?.tax_percentage)
    }

    //format number price
    function numberFormat(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    }

    //hide show info:tax && tax bank transfer if nation is japanese
    function showHideInfoNationIsJapan() {
        if ($('#m_nation_id').val() == nationJPId) {
            $('.info-tax').show();
        } else {
            $('.info-tax').hide();
        }
    }

    //hide show cost_bank_transfer
    function showHideCostBankTransfer() {
        if ($('.payment_type:checked').val() == typePrecheckDetailedReport) {
            $('.tr_cost_bank_transfer').show();
        } else {
            $('.tr_cost_bank_transfer').hide();
        }
    }

    // Hide/Show product checkbox
    function showHideProductCheckbox() {
        let typePrecheck = $('.type_precheck:checked').val()

        $.each($('.product-item'), function () {
            let checkboxElement = $(this).find('input[name^=m_product_choose]');
            let isSimple = $(this).data('is_simple');
            let isDetail = $(this).data('is_detail');

            if(typePrecheck == typePrecheckSimple) {
                if (isSimple == 1) {
                    checkboxElement.css('display', 'none');
                } else {
                    checkboxElement.css('display', 'inline-block');
                }
            } else {
                if (isDetail == 1) {
                    checkboxElement.css('display', 'none');
                } else {
                    checkboxElement.css('display', 'inline-block');
                }
            }
        })

        $('input[name^=m_product_choose]:hidden').prop('checked', false);

        let allCheckboxVisible = $('input[name^=m_product_choose]:visible').length;
        let allCheckboxChecked = $('input[name^=m_product_choose]:visible:checked').length;
        if (allCheckboxVisible == allCheckboxChecked) {
            $('.all-checkbox').prop('checked', true);
        } else {
            $('.all-checkbox').prop('checked', false);
        }
    }
});
