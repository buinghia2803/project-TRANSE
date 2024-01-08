let lengthAppend = $('.trademark-info').find('.append_html').find('.registrant_information').length;
if (lengthAppend >= 4) {
    $('.click_append').hide();
}

$('body').on('click', '.click_append', function(e) {
    e.preventDefault();

    let lengthAppend = $(this).closest('.trademark-info').find('.append_html').find('.registrant_information').length;
    if (lengthAppend == 4) {
        $(this).hide();
    }
    if (lengthAppend >= 5) {
        $(this).hide();
        return false;
    }

    let optionNation = ``;
    $.each(NATIONS, function (index, item) {
        optionNation += `<option value="${index}">${item}</option>`;
    });

    let optionPrefecture = ``;
    $.each(PREFECTURES, function (index, item) {
        optionPrefecture += `<option value="${index}">${item}</option>`;
    });

    $(".append_html").append(`
        <dl class="w16em clearfix registrant_information" data-delete_box>
            <hr>
            <dt>${label_type_acc} <span class="red">*</span></dt>
            <dd class="eTypeAcc">
                <ul class="r_c clearfix fTypeAcc">
                    <li>
                        <label><input type="radio" class="data-type_acc" name="data[${lengthAppend}][type_acc]" value="1" /> ${label_type_acc_1}</label>
                    </li>
                    <li>
                        <label><input type="radio" class="data-type_acc" name="data[${lengthAppend}][type_acc]" value="2" /> ${label_type_acc_2}</label>
                    </li>
                </ul>
            </dd>

            <dt>${label_name} <span class="red">*</span></dt>
            <dd><input type="text" class="data-name" name="data[${lengthAppend}][name]" /></dd>

            <dt>${label_m_nation_id}<span class="red">*</span></dt>
            <dd class="eNation">
                <select name="data[${lengthAppend}][m_nation_id]" class="data-m_nation_id">
                    <option value="">${label_select_default}</option>
                    ${optionNation}
                </select>
            </dd>

            <div class="showIfJapan hidden">
                <dt>${label_m_prefecture_id}<span class="red">*</span></dt>
                <input type="hidden" name="data[${lengthAppend}][id]" value="">
                <dd class="ePerfecture">
                    <select name="data[${lengthAppend}][m_prefecture_id]" class="data-m_prefecture_id">
                        <option value="">${label_select_default2}</option>
                        ${optionPrefecture}
                    </select>
                </dd>

                <dt>${label_address_second}<span class="red">*</span></dt>
                <dd>
                    <input type="text" class="data-address_second em30" name="data[${lengthAppend}][address_second]" /><br />
                    <span class="input_note">${label_note_address_second}</span>
                </dd>
            </div>

            <dt>
                ${label_address_three_1}<br />
                ${label_address_three_2}
            </dt>
            <dd>
                <input type="text" class="data-address_three em30" name="data[${lengthAppend}][address_three]" /><br />
                <span class="input_note">${label_note_address_three}</span>
            </dd>
            <input type="button" value="${label_delete}" class="small btn_d eol" data-delete_btn>
        </dl>
    `);
})

$('body').on('click', '[data-delete_btn]', function (e) {
    e.preventDefault();

    $(this).closest('[data-delete_box]').remove();

    let lengthAppend = $('.trademark-info').find('.append_html').find('.registrant_information').length;
    if (lengthAppend < 5) {
        $('.click_append').show();
    }
});

$('body').on('click', '.copy-trademark-info', function (e) {
    e.preventDefault();

    let firstItemInfo = $(this).closest('.trademark-info').find('.append_html').find('.registrant_information').first();
    let info = $(this).closest('.content').find('table').find('input[name=check]:checked').data('copy_info');
    if (info != undefined) {
        updateTrademarkInfo(info, firstItemInfo);
        closeModal('#' + $(this).closest('.modal').attr('id'));
    }
})

$('body').on('click', '#btn-click-copy', function(e) {
    e.preventDefault();

    let firstItemInfo = $(this).closest('.trademark-info').find('.append_html').find('.registrant_information').first();
    let info = $(this).data('copy_info');

    updateTrademarkInfo(info, firstItemInfo);
})

updateTrademarkInfo = function (data, item) {
    const {
        type_acc,
        name,
        nation_id,
        prefectures_id,
        address_second,
        address_three
    } = data;

    item.find('.data-type_acc').filter(`[value=${type_acc}]`).prop("checked", true);
    item.find('.data-name').val(name);
    item.find('.data-m_nation_id option[value='+ nation_id +']').prop("selected", true);
    item.find('.data-m_prefecture_id option[value='+ prefectures_id +']').prop("selected", true);
    item.find('.data-address_second').val(address_second);
    item.find('.data-address_three').val(address_three);

    $('input.data-type_acc').change();
    $('input.data-name').change();
    $('select.data-m_nation_id').change();
    $('select.data-m_prefecture_id').change();
    $('input.data-address_second').change();
    $('input.data-address_three').change();
}
function triggerChangeInput() {
    $('input.data-type_acc').change();
    $('input.data-name').change();
    $('select.data-m_nation_id').change();
    $('select.data-m_prefecture_id').change();
    $('input.data-address_second').change();
    $('input.data-address_three').change();
}

$('body').on('click', 'input[type=submit],button[type=submit]', function(e) {
    $('input.data-type_acc').change();
    $('input.data-name').change();
    $('select.data-m_nation_id').change();
    $('select.data-m_prefecture_id').change();
    $('input.data-address_second').change();
    $('input.data-address_three').change();
});

// Validation

const regexCheck = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９・ー－]+$/;

$('body').on('change', 'input.data-type_acc', function() {
    let name = $(this).attr('name');
    let value = $('input[name="' + name + '"]:checked').val();

    if(value == undefined) {
        $(this).closest('.eTypeAcc').find('.error').remove();
        $(this).closest('.eTypeAcc').append('<div class="error">'+ errorMessageTrademarkRequired +'</div>')
    } else {
        $(this).closest('.eTypeAcc').find('.error').remove();
    }
});

$('body').on('change keyup', 'input.data-name', function() {
    let value = $(this).val();
    $(this).parent().find('.error').remove();

    if(value.length == 0) {
        $(this).after('<div class="error">'+ errorMessageTrademarkRequired +'</div>');
    } else if (value.length > 50) {
        $(this).after('<div class="error">'+ errorMessageTrademarkNameMaxLengthText +'</div>');
    } else if (!regexCheck.test(value)) {
        $(this).after('<div class="error">'+ errorMessageTrademarkNameRegex +'</div>');
    }
});

$('body').on('change', 'select.data-m_nation_id', function() {
    let value = $(this).val();
    $(this).closest('.eNation').find('.error').remove();
    $(this).closest('.registrant_information').find('.showIfJapan').addClass('hidden');

    if(!value) {
        $(this).closest('.eNation').append('<div class="error">'+ errorMessageTrademarkRequired +'</div>')
    } else {
        if (value == TrademarkInfoJapanID) {
            $(this).closest('.registrant_information').find('.showIfJapan').removeClass('hidden');
        }
    }
});

$('body').on('change', 'select.data-m_prefecture_id', function() {
    let value = $(this).val();
    $(this).closest('.ePerfecture').find('.error').remove();

    let mNation = $(this).closest('.registrant_information').find('select.data-m_nation_id').val();
    if(!value && mNation == TrademarkInfoJapanID) {
        $(this).closest('.ePerfecture').append('<div class="error">'+ errorMessageTrademarkRequired +'</div>')
    }
});

$('body').on('change keyup', 'input.data-address_second', function() {
    let value = $(this).val();
    $(this).parent().find('.error').remove();
    if(value.length == 0) {
        $(this).after('<div class="error">'+ errorMessageTrademarkRequired +'</div>');
    } else if (!regexCheck.test(value)) {
        $(this).after('<div class="error">'+ errorMessageTrademarkAddressRegex +'</div>');
    }
});

$('body').on('change keyup', 'input.data-address_three', function() {
    let value = $(this).val();
    $(this).parent().find('.error').remove();

    let mNation = $(this).closest('.registrant_information').find('select.data-m_nation_id').val();
    if (value.length > 0 && !regexCheck.test(value) && mNation == TrademarkInfoJapanID) {
        $(this).after('<div class="error">'+ errorMessageTrademarkAddressRegex +'</div>');
    }
});

$('body').find('.data-m_nation_id').each(function() {
    if($(this).val()) {
        $(this).change()
    }
})
