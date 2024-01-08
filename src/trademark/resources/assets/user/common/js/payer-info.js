// On change m_nation_id
$('body').on('change', 'select[name=m_nation_id]', function() {
    let nationID = $(this).val();
    if (nationID == JapanID) {
        $('.showHideInfoAddress').css('display', 'block');
        $('.taxt').removeClass('hidden')
    } else {
        $('.showHideInfoAddress').css('display', 'none');
        $('.taxt').addClass('hidden')
    }
    if(callClear) {
        setDefaultInfoAddress();
    }
    callClear = true
});

$('select[name=m_nation_id]').change();

$('.postal_code_button').on('click', function() {
    let valuePrefecture = $('#hiddenValuePrefecture').val()
    let valueAddressSecond = $('#hiddenAddressSecond').val()
    let valueStreetAddressSecond = $('#hiddenStreetAddressSecond').val()
    if (valuePrefecture != '') {
        $("#m_prefecture_id option").filter(function() {
            return $(this).text() == valuePrefecture;
        }).prop("selected", true);
    }

    let addressSecond = '';
    if (valueAddressSecond != '') {
        addressSecond += valueAddressSecond;
    }
    if (valueStreetAddressSecond != '') {
        addressSecond += valueStreetAddressSecond;
    }
    $('#address_second').val(addressSecond)
});

// Copy Info Contact Of User
$('.copyInfoContactOfUser').on('click', function() {
    callAjaxSetIfoPayer('info-from-contact')
});

// Copy Info Member Of User
$('.copyInfoMemberOfUser').on('click', function() {
    callAjaxSetIfoPayer('info-from-member')
});

// Set default info
function setDefaultInfoAddress() {
    $('input[name=postal_code]').val('')
    $('select[name=m_prefecture_id]').val('')
    $('#hiddenValuePrefecture').val('')
    $('input[name=address_second]').val('')
    $('#hiddenAddressSecond').val('')
    $('input[name=address_three]').val('')
}

function callAjaxSetIfoPayer(type) {
    loadAjaxGet(AjaxGetInfoUser, {
        beforeSend: function(){},
        success:function(result){
            if (result.response) {
                let res = result.response;

                if (type == 'info-from-contact') {
                    payer_name = res.contact_name;
                    name_furigana = res.contact_name_furigana;
                    nation_id = res.contact_nation_id;
                    type_acc = res.contact_type_acc;
                    postal_code = res.contact_postal_code;
                    prefectures_id = res.contact_prefectures_id;
                    if(nation_id == JapanID) {
                        address_second = res.contact_address_second;
                    }
                    address_three = res.contact_address_three;
                } else {
                    payer_name = res.info_name;
                    name_furigana = res.info_name_furigana;
                    nation_id = res.info_nation_id;
                    type_acc = res.info_type_acc;
                    postal_code = res.info_postal_code;
                    prefectures_id = res.info_prefectures_id;
                    if(nation_id == JapanID) {
                        address_second = res.info_address_second;
                    }
                    address_three = res.info_address_three;
                }

                $('input[name=payer_name]').val(payer_name).change();
                $('input[name=payer_name_furigana]').val(name_furigana).change();

                $("input[name=payer_type][value="+type_acc+"]").prop('checked', true).change();
                $("select[name=m_nation_id]").val(nation_id).change();
                $('select[name=m_nation_id]').change();

                if (nation_id == JapanID) {
                    $('input[name=postal_code]').val(postal_code).change();
                    $("select[name=m_prefecture_id]").val(prefectures_id).change();
                    $('input[name=address_second]').val(address_second).change();
                }
                $('input[name=address_three]').val(address_three).change();

                $('input[name=payer_name]').closest('form').valid();
            }
        },
        error: function (error) {}
    }, 'loading');
}
