$('body').on('change', '.checkAllCheckBox, .checkSingleCheckBox, .all-checkbox, .single-checkbox', function() {
    $('body').find('.notice-scrollable').remove();
})
$('body').off('change', 'select[name=m_nation_id]')
//set number checked default
$('.totalChecked').text(countProductsIsChecked())
$('.total-dis').text(getTotalDistinction())

onChangePaymentType()
onchangeNation()
setAllCheckBoxDefault()
showPriceProdGrowFour()
chuckPriceAddProd()
totalSub(getTotalDistinction())

let priceThreeProdFeeNotTax = 0
//check all checkbox choose product
$('body').on('change', '.checkAllCheckBox', function() {
    if ($(this).is(':checked')) {
        $('.checkSingleCheckBox').prop('checked', true);
    } else {
        $('.checkSingleCheckBox').prop('checked', false);
    }
    $('.totalChecked').text(countProductsIsChecked())
    $('.total-dis').text(getTotalDistinction())
    showPriceProdGrowFour()
    chuckPriceAddProd()
    totalSub(getTotalDistinction())
});

//check single checkbox
$('body').on('change', '.checkSingleCheckBox', function () {
    //check all parent
    setAllCheckBoxDefault()
    showPriceProdGrowFour()
    chuckPriceAddProd()
    totalSub(getTotalDistinction())
    $('.totalChecked').text(countProductsIsChecked())
    $('.total-dis').text(getTotalDistinction())
});

//Set check all default
function setAllCheckBoxDefault() {
    if ($('.checkSingleCheckBox:checked').length == $('.checkSingleCheckBox').length && $('.checkSingleCheckBox:checked').length != 0 && $('.checkSingleCheckBox').length != 0) {
        $('.checkAllCheckBox').prop('checked', true);
    } else {
        $('.checkAllCheckBox').prop('checked', false);
    }
}

function showPriceProdGrowFour() {
    if ($('.checkSingleCheckBox:checked').length >= 4) {
        $('#cost_prod_grow_four').removeClass('d-none')
        let countProductPackCheck = $('.checkSingleCheckBox:checked').length > 3 ? $('.checkSingleCheckBox:checked').length - 3 : 0;
        $('.product_selected_count').text(countProductPackCheck)
    } else {
        $('#cost_prod_grow_four').addClass('d-none')
    }
}

// Chuck price add prod
function chuckPriceAddProd() {
    var chunks = [];

    for (var i = 0; i < $('.checkSingleCheckBox:checked').length;) {
        chunks.push($('.checkSingleCheckBox:checked').slice(i, i += 3));
    }

    const eachThreeProdFee = fmPrice(pricePackageEachA['base_price_multiplication_tax'] * (chunks.length - 1))
    const eachThreeProdFeeNotTax = fmPrice(pricePackageEachA['base_price'] * (chunks.length - 1))

    if (chunks.length > 1) {
        $('.price_product_add').html(eachThreeProdFee)
        $('.price_product_add_not_tax').html(eachThreeProdFeeNotTax)
        // $("input[name='cost_service_add_prod']").val(pricePackageEachA['base_price_multiplication_tax'] * (chunks.length - 1));
    } else {
        $('.price_product_add').html('0')
        $('.price_product_add_not_tax').html('0')
        // $("input[name='cost_service_add_prod']").val(0);
    }
}

// Change Payment Type
function onChangePaymentType() {
    $('.payment_type').on('change', function () {
        if (+$(this).val() === BANK_TRANSFER && $('.payment_type_transfer').is(':checked')) {
            $('.cost_bank_transfer_tr').removeClass('d-none')
            $('#cost_bank_transfer_span').text(fmPrice(priceCostBank['base_price_multiplication_tax']))
            $('#cost_bank_transfer_span_not_tax').text(fmPrice(priceCostBank['base_price']))
            totalSub(getTotalDistinction())
            $("input[name='cost_bank_transfer']").val(priceCostBank['base_price_multiplication_tax']);
        } else {
            $('.cost_bank_transfer_tr').addClass('d-none')
            $('#cost_bank_transfer_span').text('0')
            $('#cost_bank_transfer_span_not_tax').text('0')
            totalSub(getTotalDistinction())
            $("input[name='cost_bank_transfer']").val(0);
        }
    }).change()
}

//==================================================================
// Calculate subtotal, commission, products price , total price.
//==================================================================
function fmPrice(val) {
    return new Intl.NumberFormat('en-us').format(Math.floor(val))
}

// Function get number distinction checked choose product
function getTotalDistinction() {
    let nameData = []
    let uniqueNames = [];
    $('.checkSingleCheckBox:checked').filter(function(index, el) {
        const nameDis = el.dataset.hasOwnProperty('nameDistinction') ? el.dataset.nameDistinction : $(el).data('name-distinction')
        nameData.push(nameDis)
    })
    $.each(nameData, function(i, ele){
        if (ele != '') {
            if($.inArray(ele, uniqueNames) === -1) uniqueNames.push(ele);
        }
    });
    return uniqueNames.length
}

// Total sub
function totalSub(distinction) {
    const totalSub = pricePackageA['base_price_multiplication_tax'] + (+$('#cost_bank_transfer_span').text().replaceAll(',', '')) + (+$('.price_product_add').text().replaceAll(',', ''))
    const totalSubNotTax = pricePackageA['base_price'] + (+$('#cost_bank_transfer_span_not_tax').text().replaceAll(',', '')) + (+$('.price_product_add_not_tax').text().replaceAll(',', ''))
    $('#totalSub').text(fmPrice(totalSub))
    $('#totalSubNotTax').text(fmPrice(totalSubNotTax))
    $('#priceTax').text(fmPrice(totalSub - totalSubNotTax))

    let totalDis = getTotalDistinction()
    let oneDivision = totalDis > 0 ? 1 : 0
    let totalDisMinus = totalDis - 1
    $('#one_division').html(oneDivision)
    if (totalDisMinus > 0) {
        $('.total-dis-minus').text(totalDisMinus)
    } else {
        totalDisMinus = 0
        $('.total-dis-minus').text('0')
    }

    const priceDisMinus5yrs = ( pricePackageA['pof_1st_distinction_5yrs'] * oneDivision ) + ( pricePackageA['pof_2nd_distinction_5yrs'] * totalDisMinus )
    $('.totalDis5Yrs').text(fmPrice(priceDisMinus5yrs))

    const pricePrint5yrs = 0;
    // const pricePrint5yrs = print5yrs['pof_1st_distinction_5yrs'] * totalDis
    // $('.pricePrint').text(fmPrice(pricePrint5yrs))

    // $('#total').text(fmPrice(totalSub + pricePrint5yrs + priceDisMinus5yrs))
    $('#total').text(fmPrice(totalSub + pricePrint5yrs + priceDisMinus5yrs))

    $("input[name='subtotal']").val(totalSub);
    $("input[name='commission']").val(totalSubNotTax);
    $("input[name='tax']").val(totalSub - totalSubNotTax);
    $("input[name='cost_print_application_one_distintion']").val(priceDisMinus5yrs);
    $("input[name='cost_5_year_one_distintion']").val(pricePrint5yrs);
    $("input[name='total_amount']").val(totalSub + pricePrint5yrs + priceDisMinus5yrs);
    $("input[name='total_distinction']").val(totalDis);
    $("input[name='sum_distintion']").val(totalDis);
}

function onchangeNation() {
    $('#m_nation_id').on('change', function () {
        if (+$(this).val() === NATION_JAPAN_ID) {
            $('.consumption_tax').removeClass('d-none')
        } else {
            $('.consumption_tax').addClass('d-none')
        }
        let nationID = $(this).val();
        if (nationID == JapanID) {
            $('.showHideInfoAddress').css('display', 'block');
            $('.taxt').removeClass('hidden')
        } else {
            $('.showHideInfoAddress').css('display', 'none');
            $('.taxt').addClass('hidden')
        }
    }).change()
}

//function get number checked choose product
function countProductsIsChecked() {
    return $('.checkSingleCheckBox:checked').length
}

// Click before html
$('.add-product-service').click(function () {
    let optionDISTINCTION = ``;
    $.each(DISTINCTIONS, function (index, item) {
        optionDISTINCTION += `<option value="${index}">第${item}類</option>`;
    });

    let lengthAppend = 0
    lengthAppend = $(this).closest('.table_product_choose').find('.before_html_product').length + 1;
    $('.checkAllCheckBox').prop('checked', false)
    $(this).parent().parent().before(`
        <tr class="before_html_product">
            <td class="eDis">
                <select name="prod[${lengthAppend}][m_distinction_id]" class="data-m_distinction_id distinction-mr w-100">
                    <option value="">${label_select_default}</option>
                    ${optionDISTINCTION}
                </select>
            </td>
            <td class="boxes boxes_${lengthAppend}">
                <input type="text" name="prod[${lengthAppend}][name_product]" class="data-name_product name_prod_${lengthAppend} customer_boxes" key-prod="${lengthAppend}" data-suggest>
                <input type="hidden" name="prod[${lengthAppend}][m_product_id]" class="m_product_id m_product_id_${lengthAppend}" value="" />
            </td>
            <td class="center"><input type="checkbox" name="prod[${lengthAppend}][check]" value="1" class="checkSingleCheckBox single-checkbox" data-name-distinction=""/></td>
        </tr>
    `);

    if (lengthAppend == 500) {
        $(this).remove();
    }
})

// change select distinction add attr
$('body').on('change', '.data-m_distinction_id', function () {
    if ($(this).find("option:selected").text() === '選択') {
        $(this).closest('.before_html_product').find('.checkSingleCheckBox').attr('data-name-distinction', '');
        $('.total-dis').text(getTotalDistinction())
        totalSub()
        return false
    }
    $(this).closest('.before_html_product').find('.checkSingleCheckBox').attr('data-name-distinction', $(this).find("option:selected").text());
    $('.total-dis').text(getTotalDistinction())
    totalSub()
})

let timer = [];
$('body').on('keyup focus', '[data-suggest]', function (event) {
    e = $(this);
    value = e.val();
    let index = $(this).attr('key-prod')
    const distinction = $(this).closest('tr').find('select').val();
    let dataSearch = [...products]
    if (distinction) {
        dataSearch = products.filter(item => item.m_distinction_id == distinction)
    }
    let searchTrial = dataSearch
    if (value) {
        searchTrial = dataSearch.filter(item => value && item.name.includes(value))
    }

    html = '<div class="search-suggest" id="suggest_search"><div class="search-suggest__list">';
    for (const prod of searchTrial) {
        html += `<div class="item" data-id="${prod.id}" prod_value="name" product-id="${prod.id}" key_item="${index}" key_type="${prod.type}">${prod.name}</div>`
    }
    html += '</div></div>'


    if($('.search-suggest').length){
        $('.search-suggest').remove()
    }

    setTimeout(() => {
        $(this).closest(`td.boxes`).append(html)
    }, 200)
});

$(document).on('click', '[prod_value]', function () {
    var prodValue = $(this).attr('prod_value')
    var prodId = $(this).attr('product-id')
    var keyType = $(this).attr('key_type')
    var prod = $(this).attr('key_item')

    loadAjaxPost(SuggestURLItem, { id: prodId }, {
        beforeSend: function(){},
        success:function(result){
            if(result?.status) {
                let itemProd = result.data

                $(`.m_product_id_${prod}`).val(prodId)
                $(`.boxes_${prod}`).find('.data-name_product').val(itemProd.name)
                //m_distinction
                if(itemProd.m_distinction) {
                    $(`.boxes_${prod}`).closest('.before_html_product').find('select').val(itemProd.m_distinction.id);
                    $(`.boxes_${prod}`).closest('.before_html_product').find('.checkSingleCheckBox').attr('data-name-distinction', `第${itemProd.m_distinction.id}類`);
                    $('.total-dis').text(getTotalDistinction())
                    totalSub()
                }
            }
        },
        error: function (error) {}
    }, 'loading');
})

$(document).bind("click", function (t) {
    $('.search-suggest').remove()
})

// Validation
regex = /^([ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９々ー－・。（）「」]+\s)*[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９々ー－・。（）「」]+$/;
$('body').on('change keyup', 'input.data-name_product', function() {
    let value = $(this).val();
    $(this).parent().find('.error').remove();

    if(value.length == 0) {
        $(this).after('<div class="error">'+ Common_E001 +'</div>');
    } else if (value.length > 200) {
        $(this).after('<div class="error">'+ support_U011_E001 +'</div>');
    } else if (!regex.test(value)) {
        $(this).after('<div class="error">'+ support_U011_E001 +'</div>');
    }
});

$('body').on('change', 'select.data-m_distinction_id', function() {
    let value = $(this).val();
    $(this).closest('.eDis').find('.error').remove();
    if(!value) {
        $(this).closest('.eDis').append('<div class="error">'+ Common_E025 +'</div>')
    }
});

$('body').on('click', '.search-suggest__list .item', function() {
    $(this).closest('.boxes').find('.error').remove();
    $(this).closest('.before_html_product').find('.eDis').find('.error').remove();
});

$('body').on('change', '.data-name_product', function () {
    $(this).closest('.boxes').find('.m_product_id').val('');
})

$('body').on('change', '.data-m_distinction_id', function () {
    $(this).closest('.before_html_product').find('.boxes').find('.m_product_id').val('');
})
