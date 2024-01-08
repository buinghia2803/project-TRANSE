$(document).ready(function() {
    $('#toggle-btn').click(function() {
        if ($('#toggle-example').is(':hidden')) {
            $('#toggle-example').show();
        } else {
            $('#toggle-example').hide();
        }
    });

    //ready
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
    })

    //Single checkbox
    $('.single-checkbox').on('click', function() {
        if ($('.single-checkbox:checked').length == $('.single-checkbox').length) {
            $('.all-checkbox').prop('checked', true)
        } else {
            $('.all-checkbox').prop('checked', false)
        }
        getTotalCheclboxIsChecked()
        getTotalDistinction()
    })

    // get total checkbox is checked
    function getTotalCheclboxIsChecked() {
        let totalChecked = $('.single-checkbox:checked').length
        $('.total-checkbox-checked').text(totalChecked)
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

    //ready
    ajaxGetInfoPayment()
    showHideInfoNationIsJapan()

    //on change type_package
    $('.type_package').on('change', function() {
        ajaxGetInfoPayment()
    });

    //on change all-checkbox
    $('.all-checkbox').on('change', function() {
        ajaxGetInfoPayment()
    });

    //on change single-checkbox
    $('.single-checkbox').on('change', function() {
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
    });

    //get again info payment ajax
    $('.getInfoPayment').on('click', function () {
        //call ajax get info payment
        ajaxGetInfoPayment()
    });

    //on change is_mailing_register_cert ajax
    $('#is_mailing_register_cert').on('click', function () {
        ajaxGetInfoPayment()
    });

    //on change period_registration ajax
    $('#period_registration').on('click', function () {
        ajaxGetInfoPayment()
    });

    //Ajax get info payment
    function ajaxGetInfoPayment() {
        let nameData = []
        let uniqueNames = [];
        $('.single-checkbox:checked').filter(function(index, el) {
            let nameDis = $(el).data('name-distinction')
            nameData.push(nameDis)
        })
        $.each(nameData, function(i, ele){
            if($.inArray(ele, uniqueNames) === -1) uniqueNames.push(ele);
        });

        let totalDistinction = uniqueNames.length
        let typePackage = $('.type_package:checked').val();
        let paymentType = $('.payment_type:checked').val();
        let isMailingRegisterCert = $('#is_mailing_register_cert:checked').val() ?? 0;
        let periodRegistration = $('#period_registration:checked').val() ?? 1;
        let idsProduct = [];
        $('.single-checkbox:checked').filter(function() {
            idsProduct.push(this.value)
        })

        //if choose type_package && choose product
        let data = {
            type_package: typePackage,
            payment_type: paymentType,
            is_mailing_register_cert: isMailingRegisterCert,
            period_registration: periodRegistration,
            m_product_ids: idsProduct,
            total_distinction: totalDistinction
        };

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
        $('.subtotal').text(numberFormat(numberFormat(data?.subtotal)))
        $('.commission').text(numberFormat(numberFormat(data?.commission)))
        $('.cost_bank_transfer').text(numberFormat(data?.cost_bank_transfer))
        $('.tax').text(numberFormat(numberFormat(data?.tax)))
        $('.tax_percentage').text(numberFormat(numberFormat(data?.tax_percentage)))
        $('.cost_is_mailing_register_cert').text(numberFormat(numberFormat(data?.cost_is_mailing_register_cert)))
        $('.cost_period_registration').text(numberFormat(numberFormat(data?.cost_period_registration)))
        $('.total').text(numberFormat(numberFormat(data?.total)))
    }

    //format number price
    function numberFormat(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    }

    //hide show info:tax && tax bank transfer if nation is japanese
    function showHideInfoNationIsJapan() {
        if ($('#m_nation_id').val() == nationJPId) {
            $('.info-tax').show();
            if ($('.payment_type:checked').val() == typePrecheckDetailedReport) {
                $('.tr_cost_bank_transfer').show();
            } else {
                $('.tr_cost_bank_transfer').hide();
            }
        } else {
            $('.info-tax').hide();
            $('.tr_cost_bank_transfer').hide();
        }
    }
});
