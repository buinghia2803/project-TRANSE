$.validator.setDefaults({
    errorClass: 'notice',
    focusInvalid: true,
    errorElement: "div",
    ignore: [".ignore"],
    errorPlacement: function (error, element) {
        // Using closest class parent ".show-error" to append error message
        // OR
        // Using closest class parent .show-error-box and it has class ".show-error" inside to append error message
        if (element.closest('.show-error').length > 0) {
            error.insertAfter(element.closest('.show-error'));
        } else if (element.closest('.show-error-box').length > 0) {
            element.closest('.show-error-box').find('.show-error').append(error);
        } else if (element.closest('.fRadio').length > 0) {
            error.insertAfter(element.closest('.fRadio').find('.radio-group'));
        } else if (element.closest('.eInfoTypeAcc').length > 0) {
            error.insertAfter(element.closest('.eInfoTypeAcc').find('.fInfoTypeAcc'));
        } else if (element.closest('.eInfoGender').length > 0) {
            error.insertAfter(element.closest('.eInfoGender').find('.fInfoGender'));
        } else if (element.closest('.eTypeAcc').length > 0) {
            error.insertAfter(element.closest('.eTypeAcc').find('.fTypeAcc'));
        } else if (element.closest('.eContactTypeAcc').length > 0) {
            error.insertAfter(element.closest('.eContactTypeAcc').find('.fContactTypeAcc'));
        } else if (element.closest('.eInfoMemberid').length > 0) {
            error.insertAfter(element.closest('.eInfoMemberid').find('.fInfoMemberid'));
        } else if (element.closest('.eInfoPostalCode').length > 0) {
            error.insertAfter(element.closest('.eInfoPostalCode').find('.fInfoPostalCode'));
        } else if (element.closest('.eContactPostalCode').length > 0) {
            error.insertAfter(element.closest('.eContactPostalCode').find('.fContactPostalCode'));
        } else if (element.closest('.addTextFieldInfoNameFurigana').length > 0) {
            error.insertAfter(element.closest('.addTextFieldInfoNameFurigana').find('#editName'));
        } else if (element.closest('.addTextFieldContactNameFurigana').length > 0) {
            error.insertAfter(element.closest('.addTextFieldContactNameFurigana').find('#editName2'));
        } else if (element.closest('.select-group').length > 0) {
            error.insertAfter(element.closest('.select-group'));
        } else if (element.attr("name") === 'payment_type') {
            error.insertAfter(".ul_payment_type");
        } else if (element.attr("name") === 'payer_type') {
            error.insertAfter(".ul_payer_type");
        } else if (element.attr("name") === 'postal_code') {
            error.insertAfter(".wp_postal_code");
        } else if (element.attr("name") === 'image_trademark') {
            error.insertAfter(".image_trademark_note");
        } else if (element.attr("name") === 'type_change') {
            error.insertAfter(".error_type_change");
        } else if (element.attr("name").includes('is_choice')) {
            if (!element.closest('.parent_table').find('.notice').length) {
                error.insertAfter(".planTbl" + element.closest('.parent_table').find('.plan_ids').val());
            }
            if (!element.closest('.parent_table_product').find('.notice').length) {
                error.insertAfter(".planCorrespondenceTblProduct");
            }
        } else {
            error.insertAfter(element);
        }
    },
    highlight: function (element, errorClass, validClass) { },
    unhighlight: function (element, errorClass, validClass) {
        $(element).parent().find('.notice').remove();
    },
    invalidHandler: function () { },
    submitHandler: function (form) {
        let has_error = $(form).find('.notice:visible,.error:visible').length;
        if (has_error > 0) {
            return false;
        }

        loadingBox('open');
        return true;
    }
});

$.validator.addMethod("isValidEmail", function (value) {
    if (value == '') return true;
    let email = /^[a-zA-Z0-9][a-zA-Z0-9_+-\.]{0,255}@[a-zA-Z0-9-\.]{2,}(\.[a-zA-Z0-9]{2,4}){1,2}$/;
    if (!email.test(value)) {
        return false;
    }
    return !(value.includes(',') ||
        value.includes('..') ||
        value.includes('-@') ||
        value.includes('@-') ||
        value.includes('.@') ||
        value.includes('@.'));
});

$.validator.addMethod("isValidDateJapan", function (value) {
    if (value == '') return true;
    let date = /^\d{4}年\d{2}月\d{2}日$/;
    return date.test(value);
});

$.validator.addMethod("isValidPhone", function (value) {
    let phone = /^[\s]{0,60}\+?\d{10,13}[\s]{0,60}$/;
    return phone.test(value);
});

$.validator.addMethod("isValidPassword", function (value) {
    let password = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/;
    return password.test(value);
});

$.validator.addMethod("isValidNameRegistrantInformation", function (value) {
    let name = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９-]{1,50}$/;
    return name.test(value);
});
$.validator.addMethod("isValidAddressRegistrantInformation", function (value) {
    let Address = /^[ぁ-んァ-ン一-龥-ａ-ｚＡ-Ｚ０-９-]{0,100}$/;

    return Address.test(value);
});
$.validator.addMethod("isValidInfoName", function (value) {
    if (value == '') return true;
    let infoName = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９－・ー]{1,50}$/;
    return infoName.test(value);
});
$.validator.addMethod("isValidInfoNameFu", function (value) {
    if (value == '') return true;
    let infoNameFu = /^[ぁ-んａ-ｚＡ-Ｚ０-９・ー]{1,50}$/;

    return infoNameFu.test(value);
});
$.validator.addMethod("isValidInfoNameFuV2", function (value) {
    if (value == '') return true;
    let infoNameFu = /^[ぁ-んァ-ン一ａ-ｚＡ-Ｚ－・ー]{1,50}$/;

    return infoNameFu.test(value);
});
$.validator.addMethod("isValidFurigana", function (value) {
    if (value == '') return true;
    let regex = /^[ぁ-ん－ー・]+$/;

    return regex.test(value);
});
$.validator.addMethod("isValidInfoCorporationNumber", function (value) {
    if (value == '') return true;
    let infoCorporationNumber = /^[0-9]{13}$/;
    return infoCorporationNumber.test(value);
});
$.validator.addMethod("isValidInfoPostalCode", function (value, e) {
    if (value != '') {
        const val = value.replace(' ', '').replace('　', '').trim();
        $(e).val(val)
        $(e).attr('value', val)
        let infoPostalCode = /^[0-9]{1,7}$/;

        return infoPostalCode.test(val);
    }

    return true;
});

$.validator.addMethod("isFullwidth", function (value, element, arg) {
    if (arg === false) {
        return true;
    }

    let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー－＝￥」「；’・。、＜＞？”：｝｛｜]+$/;
    if (value.length == 0) return true;
    return regex.test(value);
});

$.validator.addMethod("isFullwidthEnter", function (value) {
    let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜\n]+$/;
    if (value.length == 0) return true;
    return regex.test(value);
});

$.validator.addMethod("checkEnter", function (value) {
    let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;
    let valueRegex = value.replace(/\n/g, "");
    if (valueRegex.length == 0) return true;
    return regex.test(valueRegex);
});

$.validator.addMethod("isOnlySpaceNameFullwidth", function (value) {
    let onlySpace = /^([ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+\s)*[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;
    if (value.length == 0) return true;
    return onlySpace.test(value);
});

$.validator.addMethod("isFullwidthSpecial", function (value) {
    let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９・－ー々]+$/;
    if (value.length == 0) return true;
    return regex.test(value);
});

$.validator.addMethod("isValidInfoAddress", function (value, element, arg) {
    if (arg === false) {
        return true;
    }
    const val = value.replace(' ', '').replace('　', '').trim();
    if (val != '') {
        let infoAddress = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９・－ー]+$/;
        return infoAddress.test(val);
    }

    return true;
});

$.validator.addMethod("isValidInfoPhone", function (value) {
    let infoPhone = /^[0-9]{1,11}$/;
    return infoPhone.test(value);
});
$.validator.addMethod("isValidInfoMemberId", function (value) {
    let infoMemberId = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d-._@]{8,30}$/;
    return infoMemberId.test(value);
});
$.validator.addMethod("isValidInfoPassword", function (value) {
    let password = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/;
    return password.test(value);
});
$.validator.addMethod("isValidInfoQuestion", function (value) {
    let question = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９～！＠＃＄％＾＆（）＿ー＋｜｝｛：”？＞＜、。・；’」「]{1,100}$/;
    return question.test(value);
});

$.validator.addMethod("isValidInfoAnswer", function (value) {
    let answer = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９～！＠＃＄％＾＆（）＿ー＋｜｝｛：”？＞＜、。・；’」「]{1,50}$/;
    return answer.test(value);
});
$.validator.addMethod("isValidEmailOrNull", function (value) {
    let email = /^[\s]{0,60}[a-zA-Z0-9][a-zA-Z0-9_+-\.]{0,60}@[a-zA-Z0-9-\.]{2,}(\.[a-zA-Z0-9]{2,4}){1,2}[\s]{0,60}$/;
    if (value == '') {
        return true
    }
    if (!email.test(value)) {
        return false;
    }
    return !(value.includes(',') ||
        value.includes('..') ||
        value.includes('-@') ||
        value.includes('@-') ||
        value.includes('.@') ||
        value.includes('@.'));
});
$.validator.addMethod("isValidPostalCode", function (value) {
    let regex = /^[0-9]+$/;
});

$.validator.addMethod('regexSpecialCharacter', function (value) {
    const regex = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
    if (regex.test(value)) {
        return false
    }

    return true
});

$.validator.addMethod("isValidCode", function (value) {
    let code = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{12}$/;
    return code.test(value);
});

$.validator.addMethod("validateHalfWidth", function (value) {
    let infoAddress = /^[ぁ-んァ-ン一-龥a-zA-Zａ-ｚＡ-Ｚ０-９-・－]{0,100}$/;
    return infoAddress.test(value);
});

$.validator.addMethod('formatFile', function (value, element) {
    let file = element.files[0];
    if (file === null || file === '' || file === undefined) {
        return true;
    }

    const regex = /\.(gif|png|jpg|jpeg)$/
    return regex.test(value)
});

$.validator.addMethod('formatFilePDF', function (value, element) {
    let file = element.files[0];
    if (file === null || file === '' || file === undefined) {
        return true;
    }

    const regex = /\.(pdf)$/
    return regex.test(value)
});

$.validator.addMethod("checkFileSize", function (value, element, arg) {
    let file = element.files[0];
    if (file === null || file === '' || file === undefined || arg === false) {
        return true;
    } else if (file.size <= arg) {
        return true;
    }

    return false;
});

$.validator.addMethod("checkAddressSecond", function (value, e, param) {
    if (!(value.length > param && $('select[name=change_info_register_m_nation_id]').val() == JapanID)) {
        return true
    }

    return false;
});

$.validator.addMethod("invalidFullWidthCharacter", function (value, e, param) {
    const regex = /^[ぁ-んァ-ン一-龥]*$/gu
    if (value.length > param) {
        return false
    }
    $(e).val(value.trim())
    $(e).attr('value', value.trim())

    return regex.test(value.trim());
});

$.validator.addMethod("checkMaxLengthCardNumber", function (value, e, param) {
    const lengthValue = value.replaceAll(" ", '').length;
    if (!(lengthValue > param)) {
        return true
    }

    return false;
});

$.validator.addMethod("checkMinLengthCardNumber", function (value, e, param) {
    const lengthValue = value.replaceAll(" ", '').length;
    if (!(lengthValue < param)) {
        return true
    }

    return false;
});


$.validator.addMethod("invalidCardFormat", function (value, e, param) {
    // val.trim().replace(/[^\d]/g, "").replace(/(.{4})/g, '$1 ').trim()
    let valid = false
    const regex = /[\d ]/gu
    if (regex.test(value)) {
        valid = true
    }

    return valid;
});

$.validator.addMethod("formatFileSize", function (value, element, arg) {
    let file = element.files[0];
    if (file === null || file === '' || file === undefined || arg === false) {
        return true;
    } else if ((file.size / 1024) / 1024 <= arg) {
        return true;
    }

    return false;
});

$.validator.addMethod("maxlengthTextarea", function (value, e, maxlength) {
    let replaceValue = value.replace(/\n/g, "");

    return replaceValue.length <= maxlength;
});

//validate compare unique email
$.validator.addMethod("uniqueEmailSecondValid", function (value, e, arg) {
    let valueCompare = $(arg).val()
    if(value.length && valueCompare.length) {
        if(value === valueCompare) {
            return false;
        }
    }
    return true;
})
// Validate card name
$.validator.addMethod("validCardName", function (value) {
    let infoAddress = /^[ぁ-んァ-ンー一-龥a-zA-Zａ-ｚＡ-Ｚ　 ]{0,255}$/;

    return infoAddress.test(value);
});

validation = function (formID, rules, messages) {
    $(formID).validate({
        rules: rules,
        messages: messages,
    });
}
