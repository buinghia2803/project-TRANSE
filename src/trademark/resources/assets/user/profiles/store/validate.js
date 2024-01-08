validation('#form', {
    'info_type_acc': {
        required: true,
    },
    'contact_type_acc': {
        required: true,
    },
    'info_name': {
        required: true,
        isValidInfoName: true,
    },
    'info_name_furigana': {
        required: true,
        isValidInfoNameFuV2: true,
    },
    'info_corporation_number': {
        isValidInfoCorporationNumber: true,
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
    },
    'info_address_three': {
        isValidInfoAddress: () => {
            return $('#info_nation_id').val() == idOfJapan;
        },
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
    },
    'contact_address_three': {
        isValidInfoAddress: () => {
            return $('#contact_nation_id').val() == idOfJapan;
        },
    },
    'info_phone': {
        required: true,
        isValidInfoPhone: true,
    },
    'contact_phone': {
        required: true,
        isValidInfoPhone: true,
    },
    'info_nation_id': {
        required: true,
    },
    'info_member_id': {
        required: true,
        isValidInfoMemberId: true,
    },
    'contact_nation_id': {
        required: true,
    },
    'password': {
        required: true,
        isValidInfoPassword: true,
    },
    'password_confirm': {
        required: true,
        equalTo: "#password"
    },
    'info_gender': {
        required: () => {
            let val = $('input[name=info_type_acc]:checked').val()
            let require = false;
            if (val == 2) {
                require = true;
            }
            return require;
        },
    },
    'info_birthday': {
        required: () => {
            let val = $('input[name=info_type_acc]:checked').val()
            let require = false;
            if (val == 2) {
                require = true;
            }
            return require;
        },
    },
    'info_question': {
        required: true,
        isValidInfoQuestion: true
    },
    'info_answer': {
        required: true,
        isValidInfoAnswer: true
    },
    'contact_name': {
        required: true,
        isValidInfoName: true,
    },
    'contact_name_furigana': {
        required: true,
        isValidInfoNameFuV2: true,
    },
    'contact_email_second': {
        isValidEmail: true,
        maxlength: 255,
    },
    'contact_email_second_confirm': {
        maxlength: 255,
        equalTo: "#contact_email_second",
        required: () => {
            let val = $('input[name=contact_email_second]').val()
            let require = false;
            if (val != '') {
                require = true;
            }
            return require;
        },
    },
    'contact_email_three': {
        isValidEmail: true,
        maxlength: 255,
        uniqueEmailSecondValid: '#contact_email_second'
    },
    'contact_email_three_confirm': {
        maxlength: 255,
        equalTo: "#contact_email_three",
        required: () => {
            let val = $('input[name=contact_email_three]').val()
            let require = false;
            if (val != '') {
                require = true;
            }
            return require;
        }
    },
    'contact_name_department': {
        maxlength: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = 255;
            }
            return require;
        }
    },
    'contact_name_department_furigana': {
        maxlength: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = 255;
            }
            return require;
        },
        isValidInfoNameFu: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = true;
            }
            return require;
        }
    },
    'contact_name_manager': {
        required: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = true;
            }
            return require;
        },
        maxlength: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = 255;
            }
            return require;
        }
    },
    'contact_name_manager_furigana': {
        required: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = true;
            }
            return require;
        },
        maxlength: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = 255;
            }
            return require;
        },
        isValidInfoNameFu: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = true;
            }
            return require;
        },
    },
    'agree': {
        required: true
    }
}, {
    'info_type_acc': {
        required: errorMessageRadioRequired,
    },
    'contact_type_acc': {
        required: errorMessageRadioRequired,
    },
    'info_name': {
        required: errorMessageRequired,
        isValidInfoName: errorMessageInfoNameRegex,
    },
    'info_name_furigana': {
        required: errorMessageRequired,
        isValidInfoNameFuV2: errorMessageInfoNameFuriganaRegex,
    },
    'info_corporation_number': {
        isValidInfoCorporationNumber: errorMessageInfoCorporationNumberRegex,
    },
    'info_postal_code': {
        required: errorMessageRequired,
        isValidInfoPostalCode: errorMessageInfoPostalCodeRegex,
    },
    'contact_postal_code': {
        required: errorMessageRequired,
        isValidInfoPostalCode: errorMessageInfoPostalCodeRegex,
    },
    'info_prefectures_id': {
        required: errorMessageRadioRequired,
    },
    'contact_prefectures_id': {
        required: errorMessageRadioRequired,
    },
    'info_address_second': {
        required: errorMessageRequired,
        isValidInfoAddress: errorMessageInfoAddressRegex,
    },
    'info_address_three': {
        isValidInfoAddress: errorMessageInfoAddressRegex,
    },
    'contact_address_second': {
        required: errorMessageRequired,
        isValidInfoAddress: errorMessageInfoAddressRegex,
    },
    'contact_address_three': {
        isValidInfoAddress: errorMessageInfoAddressRegex,
    },
    'info_phone': {
        required: errorMessageRequired,
        isValidInfoPhone: errorMessageInfoPhoneRegex,
    },
    'contact_phone': {
        required: errorMessageRequired,
        isValidInfoPhone: errorMessageInfoPhoneRegex,
    },
    'info_nation_id': {
        required: errorMessageRadioRequired,
    },
    'contact_nation_id': {
        required: errorMessageRadioRequired,
    },
    'info_member_id': {
        required: errorMessageRequired,
        isValidInfoMemberId: errorMessageInfoMemberIdRegex,
    },
    'password': {
        required: errorMessageRequired,
        isValidInfoPassword: errorMessageInfoPasswordIdRegex,
    },
    'password_confirm': {
        required: errorMessageRequired,
        equalTo: errorMessageInfoPasswordConfirmIdRegex
    },
    'info_gender': {
        required: errorMessageRequired,
    },
    'info_birthday': {
        required: errorMessageRadioRequired,
    },
    'info_question': {
        required: errorMessageRequired,
        isValidInfoQuestion: errorMessageInfoQuestionRegex
    },
    'info_answer': {
        required: errorMessageRequired,
        isValidInfoAnswer: errorMessageInfoAnswerRegex
    },
    'contact_name': {
        required: errorMessageRequired,
        isValidInfoName: errorMessageInfoNameRegex
    },
    'contact_name_furigana': {
        required: errorMessageRequired,
        isValidInfoNameFuV2: errorMessageInfoNameFuriganaRegex,
    },
    'contact_email_second': {
        isValidEmail: errorMessageEmailFormat,
        maxlength: errorMessageContactNameDepartment
    },
    'contact_email_second_confirm': {
        equalTo: errorMessageEmailDuplicate,
        required: errorMessageRequired,
        maxlength: errorMessageContactNameDepartment
    },
    'contact_email_three': {
        isValidEmail: errorMessageEmailFormat,
        maxlength: errorMessageContactNameDepartment,
        uniqueEmailSecondValid: errorMessageUniqueEmailSecond
    },
    'contact_email_three_confirm': {
        equalTo: errorMessageEmailDuplicate,
        required: errorMessageRequired,
        maxlength: errorMessageContactNameDepartment
    },
    'contact_name_department': {
        maxlength: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = errorMessageContactNameDepartment;
            }
            return require;
        }
    },
    'contact_name_department_furigana': {
        maxlength: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = errorMessageContactNameDepartmentFurigana;
            }
            return require;
        },
        isValidInfoNameFu: errorMessageInfoNameFuriganaRegex
    },
    'contact_name_manager': {
        required: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = errorMessageRequired;
            }
            return require;
        },
        maxlength: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = errorMessageContactNameDepartment;
            }
            return require;
        }
    },
    'contact_name_manager_furigana': {
        required: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = errorMessageRequired;
            }
            return require;
        },
        maxlength: () => {
            let val = $('input[name=contact_type_acc]:checked').val()
            let require = false;
            if (val != '' && val == 1) {
                require = errorMessageContactNameDepartmentFurigana;
            }
            return require;
        },
        isValidInfoNameFu: errorMessageInfoNameFuriganaRegex
    },
    'agree': {
        required: errorMessageAgreeRequired
    }
});
