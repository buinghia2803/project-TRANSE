$(function() {
    //year in select
    var seYear = $('#year');
    var date = new Date();
    var cur = date.getFullYear();

    seYear.append('<option value="">-- 年 --</option>');
    for (i = cur; i >= 1920; i--) {
        if(dataBirthDayOld?.year == i) {
            seYear.append('<option value="' + i + '" selected>' + i + '</option>');
        } else {
            seYear.append('<option value="' + i + '" >' + i + '</option>');
        }
    };

    //month in select
    var seMonth = $('#month');
    var date = new Date();

    var month = new Array();
    month[1] = "1月";
    month[2] = "2月";
    month[3] = "3月";
    month[4] = "4月";
    month[5] = "5月";
    month[6] = "6月";
    month[7] = "7月";
    month[8] = "8月";
    month[9] = "9月";
    month[10] = "10月";
    month[11] = "11月";
    month[12] = "12月";

    seMonth.append('<option value="">-- 月 --</option>');
    for (i = 12; i > 0; i--) {
        if(dataBirthDayOld?.month == i) {
            seMonth.append('<option value="' + i + '" selected>' + month[i] + '</option>');
        } else {
            seMonth.append('<option value="' + i + '">' + month[i] + '</option>');
        }
    };

    //day in select
    function dayList(month, year) {
        var day = new Date(year, month, 0);
        return day.getDate() ;
    }

    $('#year, #month').change(function() {
        //The code that gets the id is not written in jQuery to match the code below
        var y = document.getElementById('year');
        var m = document.getElementById('month');
        var d = document.getElementById('day');

        var year = y.options[y.selectedIndex].value;
        var month = m.options[m.selectedIndex].value;
        var day = d.options[d.selectedIndex].value;
        if (day == '') {
            var days = (year == ' ' || month == ' ') ? 31 : dayList(month, year);
            d.options.length = 0;
            d.options[d.options.length] = new Option('-- 日 --', '');
            for (var i = 1; i <= days; i++)
                if(dataBirthDayOld?.day == i) {
                    d.options[d.options.length] = new Option(`${i}日`, i, false, true);
                } else {
                    d.options[d.options.length] = new Option(`${i}日`, i);
                }

        }
    }).change();
});

let infoAddressThreeElement = $('#label_info_address_three')
let contactAddressThreeElement = $('#label_contact_address_three')

$("input[name=info_question]").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});
$("input[name=info_answer]").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});
$("input[name=contact_address_second]").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});
$("input[name=contact_address_three]").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});
$("input[name=contact_email_second]").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});
$("input[name=contact_email_second_confirm]").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});
$("input[name=contact_email_three]").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});
$("input[name=contact_email_three_confirm]").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});
$(".trimSpace").keyup(function() {
    const val = $(this).val().trim()
    $(this).val(val)
});

showGenderAndBirthday();

$("input[name=info_type_acc]").change(function() {
    showGenderAndBirthday()
})

function showGenderAndBirthday() {
    let dom = $('input[name=info_type_acc]:checked');
    if (dom.val() == 2) {
        $('#changeName').text('氏名')
        $('#changeNameFurigana').text('氏名')
        $('#changeMessage').text('※氏名（ふりがな含む）は、ご登録後の変更はできません。変更には別途ご申請が必要です。')
        $('#changeGenderAndBirthday').css('display', 'block')
    }
    if (dom.val() == 1 || !dom.val()) {
        $('#changeName').text('法人名')
        $('#changeNameFurigana').text('法人名')
        $('#changeMessage').text('※法人名（ふりがな含む）は、ご登録後の変更はできません。変更には別途ご申請が必要です。')
        $('#changeGenderAndBirthday').css('display', 'none')
    }
}

//=========
showHideEdepartmentAndManager()

function showHideEdepartmentAndManager() {
    let elementDom = $('input[name=contact_type_acc]:checked');
    if (elementDom.val() == 2 || !elementDom.val()) {
        $('#changeNameContact').text('氏名')
        $('#changeNameContactFurigana').text('氏名')
        $('#changEdepartmentAndManager .red').text('')
    }
    if (elementDom.val() == 1) {
        $('#changeNameContact').text('法人名')
        $('#changeNameContactFurigana').text('法人名')
        $('#changEdepartmentAndManager .red').text('*')
    }
}

$("input[name=contact_type_acc]").change(function() {
    showHideEdepartmentAndManager()
})

$('input[name=info_member_id]').on('change', function () {
    if($(this).val() == '') {
        $('#err_info_member_id').text('')
    }
})
function checkID() {
    let error = false
    const valMemberId = $("input[name=info_member_id]").val()
    if (valMemberId != '') {
        $.ajax({
            url: routeCheckId,
            async: false,
            type: "post",
            data: {
                valMemberId,
                tokenAuthen,
                _token: _token
            },
            success: function(data) {
                if(data.response == true) {
                    $('#check-icon').html('')
                    $('#err_info_member_id').text(errorMessageUniqueMemberID)
                    loadingBox('close')
                    error = true
                } else {
                    $('#check-icon').html(
                        '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="25" height="25" x="0px" y="0px" viewBox="0 0 122.88 109.76" style="enable-background:new 0 0 122.88 109.76" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#01A601;}</style><g><path class="st0" d="M0,52.88l22.68-0.3c8.76,5.05,16.6,11.59,23.35,19.86C63.49,43.49,83.55,19.77,105.6,0h17.28 C92.05,34.25,66.89,70.92,46.77,109.76C36.01,86.69,20.96,67.27,0,52.88L0,52.88z"/></g></svg>'
                    )
                    $('#err_info_member_id').text('')
                    error = false
                }
            },
            error: function (data) {
            }
        });
    } else {
        $('#err_info_member_id').text('')
    }

    return error
};
$('#info_member_id_button').click(function() {
    if ($('#info_member_id-error').css('display') == 'none' || $('#info_member_id-error').length == 0) {
        checkID();
    } else {
        $('#check-icon').html('')
    }
});

$('#form').submit(function(e) {
    if(checkID()) {
        e.preventDefault();
    }
});
$('#clickCopy').click(function() {
    const info_type_acc = $("input[name=info_type_acc]:checked").val()
    const info_name = $("input[name=info_name]").val()
    const info_name_furigana = $("input[name=info_name_furigana]").val()
    const info_nation_id = $("select#info_nation_id option:checked").val();
    const info_postal_code = $("input[name=info_postal_code]").val()
    const info_prefectures_id = $("select#info_prefectures_id option:checked").val();
    const info_address_second = $("input[name=info_address_second]").val()
    const info_address_three = $("input[name=info_address_three]").val()
    const info_phone = $("input[name=info_phone]").val()

    const contact_type_acc = $("input[name=contact_type_acc]").filter(`[value=${info_type_acc}]`).prop(
        'checked', true)

    $("input[name=contact_name]").val(info_name)
    $("input[name=contact_name_furigana]").val(info_name_furigana)
    $(`select#contact_nation_id option[value='${info_nation_id}']`).prop('selected', true)
    $("input[name=contact_postal_code]").val(info_postal_code)
    $(`select#contact_prefectures_id option[value='${info_prefectures_id}']`).prop('selected', true)
    $("input[name=contact_address_second]").val(info_address_second)
    $("input[name=contact_address_three]").val(info_address_three)
    $("input[name=contact_phone]").val(info_phone)

    let element = $('.contactChildNation');
    hideShowChildNation(element, info_nation_id, idOfJapan, contactAddressThreeElement)
    showHideEdepartmentAndManager()
})

$('body').on('change', '#year, #month, #day', function(e) {
    e.preventDefault();
    checkBirthday();
});

function checkBirthday() {
    let year = $('#year').val()
    let month = $('#month').val()
    let day = $('#day').val()
    if (year.length > 0 && month.length > 0 && day.length > 0) {
        $('#info_birthday').val(year + '-' + month + '-' + day).change();
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

//on change nation
$('#info_nation_id').on('change', function() {
    let element = $('.infoChildNation');
    let idNation = $('#info_nation_id :selected').val();
    hideShowChildNation(element, idNation, idOfJapan, infoAddressThreeElement)
});

//submit show contact_prefectures_id & contact_address_second
$('#showContactPostalCode').on('click', function() {
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

//on change nation
$('#contact_nation_id').on('change', function() {
    let element = $('.contactChildNation');
    let idNation = $('#contact_nation_id :selected').val();
    hideShowChildNation(element, idNation, idOfJapan, contactAddressThreeElement)
});

let elementInfo = $('.infoChildNation');
let idCurrentInfo = $('#info_nation_id :selected').val();
hideShowChildNation(elementInfo, idCurrentInfo, idOfJapan, infoAddressThreeElement)

let elementContact = $('.contactChildNation');
let idCurrentContact = $('#contact_nation_id :selected').val();
hideShowChildNation(elementContact, idCurrentContact, idOfJapan, contactAddressThreeElement)

function hideShowChildNation(element, idCurrent, idJp, labelAddressThreeElement) {
    if (idCurrent == idJp) {
        labelAddressThreeElement.text(labelInfoAddressThree)
        element.show()
    } else {
        labelAddressThreeElement.text(labelLocationOrAddress)
        element.hide()
    }
}
