let clickEdit = false
let clickEdit2 = false

function close_window() {
    close();
}

$('.btnDisplayTextField').click(function() {
    clickEdit = true
    $('.removeDisplayTextField').remove()
    $('.addTextFieldInfoName').append(`<input type="text" name="info_name" value="${infoName}" />`)
    $('.addTextFieldInfoNameFurigana').append(`
        <input type="text" name="info_name_furigana" value="${infoNameFurigana}" />
        <input type="hidden" name="clickEdit" value="${clickEdit}" />
        <input type="button" class="btn_a small" id="editName" value="保存" style="font-size: 1em !important"/>
    `)
})

$('.btnDisplayTextField2').click(function() {
    clickEdit2 = true
    $('.removeDisplayTextField2').remove()
    $('.addTextFieldContactName').append(`<input type="text" name="contact_name" value="${contactName}" />`)
    $('.addTextFieldContactNameFurigana').append(`
        <input type="text" name="contact_name_furigana" value="${contactNameFurigana}" />
        <input type="hidden" name="clickEdit2" value="${clickEdit2}" />
        <input type="button" class="btn_a small" id="editName2" value="保存" style="font-size: 1em !important"/>
    `)
})

validation('#form', {
    'info_name': {
        required: () => {
            let require = $('input[name=clickEdit]').val() ?? false
            if (require) {
                require = true;
            }
            return require;
        },
        isValidInfoName: true,
    },
    'info_name_furigana': {
        required: () => {
            let require = $('input[name=clickEdit]').val() ?? false
            if (require) {
                require = true;
            }
            return require;
        },
        isValidInfoNameFu: true,
    },
    'contact_name': {
        required: () => {
            let require = $('input[name=clickEdit2]').val() ?? false
            if (require) {
                require = true;
            }
            return require;
        },
        isValidInfoName: true,
    },
    'contact_name_furigana': {
        required: () => {
            let require = $('input[name=clickEdit2]').val() ?? false
            if (require) {
                require = true;
            }
            return require;
        },
        isValidInfoNameFu: true,
    },
},{
    'info_name': {
        required: errorMessageRequired,
        isValidInfoName: errorMessageInfoNameRegex,
    },
    'info_name_furigana': {
        required: errorMessageRequired,
        isValidInfoNameFu: errorMessageInfoNameFuriganaRegex,
    },
    'contact_name': {
        required: errorMessageRequired,
        isValidInfoName: errorMessageInfoNameRegex
    },
    'contact_name_furigana': {
        required: errorMessageRequired,
        isValidInfoNameFu: errorMessageInfoNameFuriganaRegex,
    },
})

$('body').on('click', '#editName, #editName2', function() {
    if($('#form').find('.notice').length > 0) {
        return
    }

    const infoNameVal = $("input[name=info_name]").val() ?? '';
    const infoNameFuVal = $("input[name=info_name_furigana]").val() ?? '';
    const contactNameVal = $("input[name=contact_name]").val() ?? '';
    const contactNameFuVal = $("input[name=contact_name_furigana]").val() ?? '';
    
    $.ajax({
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        data_type: 'json',
        data: {
            info_name: infoNameVal,
            info_name_furigana: infoNameFuVal,
            contact_name: contactNameVal,
            contact_name_furigana: contactNameFuVal,
            userId
        },
        success: function(res) {
            if (res.status) {
                location.reload()
            }
        },
        error: function(data) {
        }
    });
})
