validation('#form', {
    'type_trademark': {
        required: true,
    },
    'name_trademark': {
        required: true,
        maxlength: 30,
        regexSpecialCharacter: true,
        validateHalfWidth: true,
    },
    'reference_number': {
        required: true,
        maxlength: 30,
        validateHalfWidth: true,
        regexSpecialCharacter: true,
    },
    'image_trademark': {
        required: () => {
            return $("input[name='type_trademark']").val() == 2;
        },
        formatFile: true,
        checkFileSize: 3000000
    },
}, {
    type_trademark: {
        required: errorMessageRequired,
    },
    name_trademark: {
        required: errorMessageRequired,
        maxlength: errorMessageMaxCharacter,
        regexSpecialCharacter: errorMessageTrademarkNameInvalid,
        validateHalfWidth: errorMessageHalfWidth
    },
    reference_number: {
        required: errorMessageRequired,
        maxlength: errorMessageMaxCharacter,
        validateHalfWidth: errorMessageHalfWidth,
        regexSpecialCharacter: errorMessageTrademarkNameInvalid,
    },
    image_trademark: {
        required: errorMessageRequired,
        formatFile: errorMessageTrademarkImageInvalid,
        checkFileSize: errorMessageTrademarkImageInvalid
    }
})
