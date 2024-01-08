var Validation = () => {
    return {
        init: function () {
            let idOfJapan = $('#nation-japan-id').val()
            $('#form-edit-profile').validate({
                errorElement: 'div',
                errorClass: 'notice',
                focusInvalid: false,
                errorPlacement: function(error, element) {
                    if (element.attr("name") === 'info_postal_code') {
                        error.insertAfter(".wp_info_postal_code");
                    } else if(element.attr("name") === 'info_member_id') {
                        error.insertAfter(".wp_info_member_id");
                    } else if(element.attr("name") === 'contact_type_acc') {
                        error.insertAfter(".ul_contact_type_acc");
                    } else if (element.attr("name") === 'contact_postal_code') {
                        error.insertAfter(".wp_contact_postal_code");
                    } else {
                        error.insertAfter(element);
                    }
                },
                rules: {
                    'info_nation_id': {
                        required: true,
                    },
                    'info_postal_code': {
                        required: () => {
                            return $('#info_nation_id').val() == idOfJapan;
                        },
                        isValidInfoPostalCode: true,
                    },
                    'info_prefectures_id': {
                        required: () => {
                            return $('#info_nation_id').val() == idOfJapan;
                        }
                    },
                    'info_address_second': {
                        required: () => {
                            return $('#info_nation_id').val() == idOfJapan;
                        },
                        isValidInfoAddress: true,
                        maxlength: 100
                    },
                    'info_address_three': {
                        required: false,
                        isValidInfoAddress: () => {
                            return $('#info_nation_id').val() == idOfJapan;
                        },
                        maxlength: 100
                    },
                    'info_phone': {
                        required: true,
                        isValidInfoPhone: true,
                    },
                    'info_member_id': {
                        required: true,
                        isValidInfoMemberId: true,
                        checkExistsMemberId: true
                    },
                    'password': {
                        required: () => {
                            return $("#re_password").val()!="";
                        },
                        isValidPasswordIfRequire: true
                    },
                    're_password': {
                        equalTo: '#password'
                    },
                    'info_question': {
                        required: true,
                        isValidInfoQuestion: true
                    },
                    'info_answer': {
                        required: true,
                        isValidInfoAnswer: true
                    },
                    'contact_type_acc': {
                        required: true,
                    },
                    'contact_name': {
                        required: true,
                        isValidInfoName: true
                    },
                    'contact_name_furigana': {
                        required: true,
                        isValidInfoNameFuV2: true
                    },
                    'contact_name_department': {
                        maxlength: 50,
                        isValidInfoName: true
                    },
                    'contact_name_department_furigana': {
                        maxlength: 255,
                        isValidInfoNameFu: true
                    },
                    'contact_name_manager': {
                        requiredIfContactTypeAccIsGroup: true,
                        maxlength: 255,
                        isValidInfoName: true
                    },
                    'contact_name_manager_furigana': {
                        requiredIfContactTypeAccIsGroup: true,
                        maxlength: 255,
                        isValidInfoNameFu: true
                    },
                    'contact_nation_id': {
                        required: true,
                    },
                    'contact_postal_code': {
                        required: () => {
                            return $('#contact_nation_id').val() == idOfJapan;
                        },
                        isValidInfoPostalCode: true,
                    },
                    'contact_prefectures_id': {
                        required: () => {
                            return $('#contact_nation_id').val() == idOfJapan;
                        }
                    },
                    'contact_address_second': {
                        required: () => {
                            return $('#contact_nation_id').val() == idOfJapan;
                        },
                        isValidInfoAddress: true,
                        maxlength: 100
                    },
                    'contact_address_three': {
                        isValidInfoAddress: () => {
                            return $('#contact_nation_id').val() == idOfJapan;
                        },
                        maxlength: 100
                    },
                    'contact_phone': {
                        required: true,
                        isValidInfoPhone: true
                    },
                    'contact_email_second': {
                        maxlength: 255,
                        isValidEmail: true,
                    },
                    'contact_email_second_confirm': {
                        equalTo: '#contact_email_second',
                    },
                    'contact_email_three': {
                        maxlength: 255,
                        isValidEmail: true,
                        uniqueEmailSecondValid: '#contact_email_second'
                    },
                    'contact_email_three_confirm': {
                        equalTo: '#contact_email_three',
                    }
                },
                messages: {
                    'info_nation_id': {
                        required: errorMessageRadioSelectCheckboxRequired,
                    },
                    'info_postal_code': {
                        required: errorMessageIsValidRequired,
                        isValidInfoPostalCode: errorMessageIsValidInfoPostalCode
                    },
                    'info_prefectures_id': {
                        required: errorMessageRadioSelectCheckboxRequired,
                    },
                    'info_address_second': {
                        required: errorMessageIsValidRequired,
                        isValidInfoAddress: errorMessageIsValidInfoAddressFormat,
                        maxlength: errorMessageIsValidInfoAddressFormat
                    },
                    'info_address_three': {
                        required: errorMessageIsValidRequired,
                        isValidInfoAddress: errorMessageIsValidInfoAddressFormat,
                        maxlength: errorMessageIsValidInfoAddressFormat
                    },
                    'info_phone': {
                        required: errorMessageIsValidRequired,
                        isValidInfoPhone: errorMessageIsValidInfoPhoneFormat,
                    },
                    'info_member_id': {
                        required: errorMessageIsValidInfoMemberIdRequired,
                        isValidInfoMemberId: errorMessageIsValidInfoMemberIdFormat,
                        checkExistsMemberId: errorMessageIsValidInfoMemberIdExists
                    },
                    'password': {
                        required: errorMessageIsValidRequired,
                        isValidPasswordIfRequire: errorMessageIsValidPasswordFormat
                    },
                    're_password': {
                        equalTo: errorMessageIsValidPasswordEqualTo
                    },
                    'info_question': {
                        required: errorMessageIsValidRequired,
                        isValidInfoQuestion: errorMessageIsValidInfoQuestionFormat
                    },
                    'info_answer': {
                        required: errorMessageIsValidRequired,
                        isValidInfoAnswer: errorMessageIsValidInfoAnswerFormat
                    },
                    'contact_type_acc': {
                        required: errorMessageRadioSelectCheckboxRequired,
                    },
                    'contact_name': {
                        required: errorMessageIsValidRequired,
                        isValidInfoName: errorMessageIsValidContactNameFormat
                    },
                    'contact_name_furigana': {
                        required: errorMessageIsValidRequired,
                        isValidInfoNameFuV2: errorMessageInfoNameFuriganaRegex
                    },
                    'contact_name_department': {
                        maxlength: errorMessageIsValidContactNameFormat,
                        isValidInfoName: errorMessageIsValidContactNameFormat
                    },
                    'contact_name_department_furigana': {
                        maxlength: errorMessageIsValidMaxLength255,
                        isValidInfoNameFu: errorMessageIsValidContactNameFuriganaFormat
                    },
                    'contact_name_manager': {
                        requiredIfContactTypeAccIsGroup: errorMessageIsValidRequired,
                        maxlength: errorMessageIsValidEmailMaxLength255,
                        isValidInfoName: errorMessageIsValidContactNameFormat
                    },
                    'contact_name_manager_furigana': {
                        requiredIfContactTypeAccIsGroup: errorMessageIsValidRequired,
                        maxlength: errorMessageIsValidMaxLength255,
                        isValidInfoNameFu: errorMessageIsValidContactNameFuriganaFormat
                    },
                    'contact_nation_id': {
                        required: errorMessageRadioSelectCheckboxRequired,
                    },
                    'contact_postal_code': {
                        required: errorMessageIsValidRequired,
                        isValidInfoPostalCode: errorMessageIsValidInfoPostalCode,
                    },
                    'contact_prefectures_id': {
                        required: errorMessageRadioSelectCheckboxRequired,
                    },
                    'contact_address_second': {
                        required: errorMessageIsValidRequired,
                        isValidInfoAddress: errorMessageIsValidInfoAddressFormat,
                        maxlength: errorMessageIsValidInfoAddressFormat
                    },
                    'contact_address_three': {
                        isValidInfoAddress: errorMessageIsValidInfoAddressFormat,
                        maxlength: errorMessageIsValidInfoAddressFormat
                    },
                    'contact_phone': {
                        required: errorMessageIsValidRequired,
                        isValidInfoPhone: errorMessageIsValidInfoPhoneFormat
                    },
                    'contact_email_second': {
                        isValidEmail: errorMessageIsValidEmailFormat,
                        maxlength: errorMessageIsValidEmailMaxLength255
                    },
                    'contact_email_second_confirm': {
                        equalTo: errorMessageIsValidEmailConfirmEqualTo,
                    },
                    'contact_email_three': {
                        isValidEmail: errorMessageIsValidEmailFormat,
                        maxlength: errorMessageIsValidEmailMaxLength255,
                        uniqueEmailSecondValid: errorMessageUniqueEmailSecond
                    },
                    'contact_email_three_confirm': {
                        equalTo: errorMessageIsValidEmailConfirmEqualTo
                    }
                },
            });

            $.validator.addMethod("isValidPasswordIfRequire", function (value) {
                if (value != '') {
                    let password = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/;
                    return password.test(value);
                }
                return true;
            });

            $.validator.addMethod("requiredIfContactTypeAccIsGroup", function (value) {
               let valueContactTypeAcc = $('.contact_type_acc:checked').val()
               let valueTypeGroup = $('#contact_type_acc_group').val()
                if (valueContactTypeAcc == valueTypeGroup && value == '') {
                    return false;
                }
                return true
            });

            $.validator.addMethod("checkExistsMemberId", function (value) {
                if ($('#res-ajax-check-member-id').val() == 'false') {
                    return true;
                }
                return false;
            });
        },
    }
}

Validation().init();
