if(statusRegister == statusRegisterTrue) {
    $.confirm({
        title: '',
        content: messagePrecheckReportSuccess,
        buttons: {
            ok: {
                text: okLabel,
                btnClass: 'btn-blue',
                action: function () {
                    loadingBox('open');
                    window.location.href = routeTop
                }
            }
        }
    });
} else {
    $(document).ready(function() {
        //set number checked default
        $('.totalChecked').text(countProductsIsChecked())
        setAllCheckBoxDefault()
        ajaxGetInfoPayment();
        showHideInfoNationIsJapan()
        showHideCostBankTransfer()
        getLabelTypePrecheckCart()

        //check all checkbox choose product
        $('.checkAllCheckBox').on('change', function() {
            if ($(this).is(':checked')) {
                $('input[name="m_product_choose[]"]').prop('checked', true);
            } else {
                $('input[name="m_product_choose[]"]').prop('checked', false);
            }
            $('.totalChecked').text(countProductsIsChecked())
            //call ajax get info payment
            ajaxGetInfoPayment()
        });

        //check single checkbox
        $('.checkSingleCheckBox').on('change', function () {
            //check all parent
            setAllCheckBoxDefault()
            $('.totalChecked').text(countProductsIsChecked())
            //call ajax get info payment
            ajaxGetInfoPayment()
        });

        //Set check all default
        function setAllCheckBoxDefault() {
            if ($('.checkSingleCheckBox:checked').length == $('.checkSingleCheckBox').length) {
                $('.checkAllCheckBox').prop('checked', true);
            } else {
                $('.checkAllCheckBox').prop('checked', false);
            }
        }

        //Change payment_type radio
        $('.payment_type').on('change', function() {
            showHideCostBankTransfer()
            //call ajax get info payment
            ajaxGetInfoPayment()
        });

        //Change type_precheck radio
        $('.type_precheck').on('change', function() {
            getLabelTypePrecheckCart()
            //call ajax get info payment
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

        //on change m_nation_id
        $('#m_nation_id').on('change', function() {
            showHideInfoNationIsJapan()
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

        //function get number checked choose product
        function countProductsIsChecked() {
            return $('input[name="m_product_choose[]"]:checked:visible').length
        }

        $('input[name="m_product_choose[]"]').on('change', function() {
            $('.totalChecked').text(countProductsIsChecked())
        })

        //Ajax get info payment
        function ajaxGetInfoPayment() {
            let typePrecheck = $('.type_precheck:checked').val();
            let idsProduct = [];
            let paymentType = $('.payment_type:checked').val();
            $('.checkSingleCheckBox:checked').filter(function() {
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
            $('.cost_service_base').text(fmPrice(data?.cost_service_base))
            $('.cost_service_add_prod').text(fmPrice(data?.cost_service_add_prod))

            $('.cost_bank_transfer').text(fmPrice(data?.cost_bank_transfer))
            $('.subtotal').text(fmPrice(data?.subtotal))
            $('.commission').text(fmPrice(data?.subtotal - data?.tax))
            $('.tax').text(fmPrice(data?.tax))
            $('.tax_percentage').text(Math.floor(data?.tax_percentage * 100)/100)
        }

        //format number price
        function numberFormat(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
        }

        //==================================================================
        // Calculate subtotal, commission, products price , total price.
        //==================================================================
        function fmPrice(val) {
            return new Intl.NumberFormat('en-us').format(Math.floor(val))
        }


        //hide show info:tax && tax bank transfer if nation is japanese
        function showHideInfoNationIsJapan() {
            if ($('#m_nation_id').val() == nationJPId) {
                $('.info-tax').show();
                $('.info-commission').show();
            } else {
                $('.info-tax').hide();
                $('.info-commission').hide();
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
    });
}

