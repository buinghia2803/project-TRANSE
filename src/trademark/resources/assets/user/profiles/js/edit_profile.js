$(document).ready(function() {
    //hide show nation default
    let infoChildNation = $('.infoChildNation');
    let idNation = $('#info_nation_id :selected').val();
    let idOfJapan = $('#nation-japan-id').val()
    let infoAddressThreeElement = $('#label_info_address_three')
    let contactAddressThreeElement = $('#label_contact_address_three')

    hideShowInfoChildNation(infoChildNation, idNation, idOfJapan, infoAddressThreeElement)

    //on change nation
    $('#info_nation_id').on('change', function() {
        $('#info_postal_code').val('')
        $('#hiddenValuePrefectures').val('')
        $('#info_prefectures_id').val('')
        $('#info_address_second').val('')
        $('#hiddenValueAddressSecond').val('')

        let element = $('.infoChildNation');
        let idNation = $('#info_nation_id :selected').val();
        hideShowInfoChildNation(element, idNation, idOfJapan, infoAddressThreeElement)
    });

    //hide show contactNation default
    let infoChildContactNation = $('.infoChildContactNation');
    let contactNationId = $('#contact_nation_id :selected').val();
    hideShowInfoChildNation(infoChildContactNation, contactNationId, idOfJapan, contactAddressThreeElement)

    //on change contactNation
    $('#contact_nation_id').on('change', function() {
        $('#contact_postal_code').val('')
        $('#hiddenValueContactPrefectures').val('')
        $('#contact_prefectures_id').val('')
        $('#contact_address_second').val('')
        $('#hiddenValueContactAddressSecond').val('')

        let element = $('.infoChildContactNation');
        let infoChildContactNation = $('#contact_nation_id :selected').val();
        hideShowInfoChildNation(element, infoChildContactNation, idOfJapan, contactAddressThreeElement)
    });

    function hideShowInfoChildNation(element, idCurrent, idJp, labelAddressThreeElement) {
        if (idCurrent == idJp) {
            labelAddressThreeElement.text(labelInfoAddressThree)
            element.show()
        } else {
            labelAddressThreeElement.text(labelLocationOrAddress)
            element.hide()
        }
    }

    //submit show info_prefectures_id & info_address_second
    $('#showInfoPostalCode').on('click', function() {
        let valuePrefecture = $('#hiddenValuePrefectures').val()
        let valueAddressSecond = $('#hiddenValueAddressSecond').val()
        if (valuePrefecture != '') {
            $("#info_prefectures_id option").filter(function() {
                return $(this).text().trim() == valuePrefecture;
            }).prop("selected", true);
        }
        if (valueAddressSecond != '') {
            $('#info_address_second').val(valueAddressSecond)
        }
    });

    //click handleButtonContactPostalCode show: contact_prefectures_id, contact_address_second
    $('#handleButtonContactPostalCode').on('click', function() {
        let valuePrefecture = $('#hiddenValueContactPrefectures').val()
        let valueAddressSecond = $('#hiddenValueContactAddressSecond').val()
        if (valuePrefecture != '') {
            $("#contact_prefectures_id option").filter(function() {
                return $(this).text().trim() == valuePrefecture;
            }).prop("selected", true);
        }
        if (valueAddressSecond != '') {
            $('#contact_address_second').val(valueAddressSecond)
        }
    });

    //ajax check btn-check-member-id
    $('#info_member_id').on('keyup', function() {
        $('.id-member-done').hide()
    });

    $('.btn-check-member-id').on('click', function() {
        let urlRouter = $(this).data('route')
        checkAjaxMemberId(urlRouter)
    });

    function checkAjaxMemberId(urlRouter) {
        let idMember = $('#info_member_id').val()
        let infoMemberId = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d-._@]{8,30}$/;
        if (infoMemberId.test(idMember)) {
            let data = { info_member_id: idMember }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: urlRouter,
                method: 'POST',
                data_type: 'json',
                data: data
            }).done(function(data) {
                $('#res-ajax-check-member-id').val(data.res)
                if ($('#res-ajax-check-member-id').val() == 'true') {
                    $('.id-member-done').hide()
                    $('#form-edit-profile').submit()
                } else {
                    $('.id-member-done').show()
                    $('#info_member_id-error').hide()
                }
            })
        } else {
            $('.id-member-done').hide()
        }
    }

    //Change contact_type_acc
    $('.contact_type_acc').on('change', function() {
        let valContactTypeAcc = $(this).val()
        if (valContactTypeAcc == contactTypeAccGroup) {
            $('.label_contact_name').text(labelCompanyNameTypeGroup)
            $('.label_contact_name_furigana').text(labelCompanyNameFuriganaTypeGroup)
        } else {
            $('.label_contact_name').text(labelCompanyNameTypeIndividual)
            $('.label_contact_name_furigana').text(labelCompanyNameFuriganaTypeIndividual)
        }
    });

    //handle Copy Info Member
    $('.handleCopyInfoMember').on('click', function() {
        let inForTypeAcc = $('#info_type_acc').val()
        if (inForTypeAcc == contactTypeAccGroup) {
            $('.contact_type_acc_1').click()
        } else {
            $('.contact_type_acc_2').click()
        }
        $('#contact_name').val($('#info_name').val())
        $('#contact_name_furigana').val($('#info_name_furigana').val())
        $('#contact_nation_id').val($('#info_nation_id').val())
        $('#contact_postal_code').val($('#info_postal_code').val())

        $('#contact_prefectures_id').val($('#info_prefectures_id').val())
        $('#hiddenValueContactPrefectures').val($('#hiddenValuePrefectures').val())

        $('#contact_address_second').val($('#info_address_second').val())
        $('#hiddenValueContactAddressSecond').val($('#hiddenValueAddressSecond').val())

        $('#contact_address_three').val($('#info_address_three').val())
        $('#contact_phone').val($('#info_phone').val())

        let element = $('.infoChildContactNation');
        let infoChildContactNation = $('#contact_nation_id :selected').val();
        hideShowInfoChildNation(element, infoChildContactNation, idOfJapan, contactAddressThreeElement)
    });

    //hideShowInfoDetailGroupContact - hide show: contact_name_furigana,contact_name_department_furigana, contact_name_manager, contact_name_manager_furigana
    // let contactTypeAccChecked = $('.contact_type_acc:checked').val()
    // if (contactTypeAccChecked == contactTypeAccIndividual) {
    //     $('.hideShowInfoDetailGroupContact').hide()
    // } else {
    //     $('.hideShowInfoDetailGroupContact').show()
    // }
    $('.contact_type_acc').on('change', function() {
        if ($('.contact_type_acc:checked').val() == contactTypeAccIndividual) {
            // $('.hideShowInfoDetailGroupContact').hide()
            $('.hideShowInfoDetailGroupContact .red').text('')
        } else {
            // $('.hideShowInfoDetailGroupContact').show()
            $('.hideShowInfoDetailGroupContact .red').text('*')
        }
    });
});
