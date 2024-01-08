$(document).ready(function() {
    let idOfJapan = $('#nation_japan_id').val()
    //showHideInfoAddress
    if ($('#m_nation_id:selected').val() == idOfJapan) {
        $('.showHideInfoAddress').show()
    } else {
        $('.showHideInfoAddress').hide()
    }


    $('#m_nation_id').on('change', function() {
        let valueNation = $(this).val()
        let idOfJapan = $('#nation_japan_id').val()
        showHideInfoAddressFunc(valueNation, idOfJapan)
    })

    function showHideInfoAddressFunc(valueNation, idOfJapan) {
        if (valueNation == idOfJapan) {
            $('.showHideInfoAddress').show()
        } else {
            $('.showHideInfoAddress').hide()
        }
        setDefaultInfoAddress()
    }

    $('.postal_code_button').on('click', function() {
        let valuePrefecture = $('#hiddenValuePrefecture').val()
        let valueAddressSecond = $('#hiddenAddressSecond').val()
        if (valuePrefecture != '') {
            $("#m_prefecture_id option").filter(function() {
                return $(this).text() == valuePrefecture;
            }).prop("selected", true);
        }
        if (valueAddressSecond != '') {
            $('#address_second').val(valueAddressSecond)
        }
    });

    //copy Info Contact Of User
    $('.copyInfoContactOfUser').on('click', function() {
        callAjaxSetIfoPayer('info-from-contact')
    });

    //copy Info Member Of User
    $('.copyInfoMemberOfUser').on('click', function() {
        callAjaxSetIfoPayer('info-from-member')
    });

    function callAjaxSetIfoPayer(type) {
        let idOfJapan = $('#nation_japan_id').val()
        $.ajax({
            url: routeGetInfoUserAjax,
            method: 'GET',
            data_type: 'json',
        }).done(function(data) {
            if (data.response) {
                let res = data.response
                if (type == 'info-from-contact') {
                    if (res.contact_nation_id == idOfJapan) {
                        $('.showHideInfoAddress').show()
                    } else {
                        $('.showHideInfoAddress').hide()
                    }
                    $('#payer_name').val(res.contact_name)
                    $('#payer_name').attr('value', res.contact_name)
                    $('#payer_name_furigana').val(res.contact_name_furigana)
                    $('#payer_name_furigana').attr('value', res.contact_name_furigana)
                    $("#m_nation_id option").filter(function() {
                        return $(this).val() == res.contact_nation_id;
                    }).prop("selected", true);

                    $(".payer_type").filter(function() {
                        return $(this).val() == res.contact_type_acc;
                    }).prop("checked", true);

                    $('#postal_code').val(res.contact_postal_code)
                    $('#m_prefecture_id').val(res.contact_prefectures_id)
                    $('#address_second').val(res.contact_address_second)
                    $('#address_three').val(res.contact_address_three)

                    $('#postal_code').attr('value', res.contact_postal_code)
                    $('#m_prefecture_id').attr('value', res.contact_prefectures_id)
                    $('#address_second').attr('value', res.contact_address_second)
                    $('#address_three').attr('value', res.contact_address_three)
                } else {
                    if (res.info_nation_id == idOfJapan) {
                        $('.showHideInfoAddress').show()
                    } else {
                        $('.showHideInfoAddress').hide()
                    }
                    $('#payer_name').val(res.info_name)
                    $('#payer_name_furigana').val(res.info_name_furigana)
                    $('#payer_name_furigana').attr('value', res.info_name_furigana)
                    $("#m_nation_id option").filter(function() {
                        return $(this).val() == res.info_nation_id;
                    }).prop("selected", true);

                    $(".payer_type").filter(function() {
                        return $(this).val() == res.info_type_acc;
                    }).prop("checked", true);

                    $('#postal_code').val(res.info_postal_code)
                    $('#m_prefecture_id').val(res.info_prefectures_id)
                    $('#address_second').val(res.info_address_second)
                    $('#address_three').val(res.info_address_three)

                    $('#postal_code').attr('value', res.info_postal_code)
                    $('#m_prefecture_id').attr('value', res.info_prefectures_id)
                    $('#address_second').attr('value', res.info_address_second)
                    $('#address_three').attr('value', res.info_address_three)
                }
            }
        });
    }

    //set default info
    function setDefaultInfoAddress() {
        $('#postal_code').val('')
        $('#m_prefecture_id').val('')
        $('#hiddenValuePrefecture').val('')
        $('#address_second').val('')
        $('#hiddenAddressSecond').val('')
        $('#address_three').val('')
    }

});
