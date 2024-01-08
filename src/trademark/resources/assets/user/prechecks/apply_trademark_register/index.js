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

    const eachThreeProdFee = fmPice(pricePackageEachA['base_price_multiplication_tax'] * (chunks.length - 1))
    const eachThreeProdFeeNotTax = fmPice(pricePackageEachA['base_price'] * (chunks.length - 1))

    if (chunks.length > 1) {
        $('.price_product_add').html(eachThreeProdFee)
        $('.price_product_add_not_tax').html(eachThreeProdFeeNotTax)
        $("input[name='cost_service_add_prod']").val(pricePackageEachA['base_price_multiplication_tax']);
    } else {
        $('.price_product_add').html('0')
        $('.price_product_add_not_tax').html('0')
        $("input[name='cost_service_add_prod']").val(0);
    }
}

// Change Payment Type
function onChangePaymentType() {
    $('.payment_type').on('change', function () {
        if (+$(this).val() === BANK_TRANSFER && $('.payment_type_transfer').is(':checked')) {
            $('.cost_bank_transfer_tr').removeClass('d-none')
            $('#cost_bank_transfer_span').text(fmPice(priceCostBank['base_price_multiplication_tax']))
            $('#cost_bank_transfer_span_not_tax').text(fmPice(priceCostBank['base_price']))
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

// Function get number distinction checked choose product
function getTotalDistinction() {
    let nameData = []
    let uniqueNames = [];
    $('.checkSingleCheckBox:checked').filter(function(index, el) {
        const nameDis = $(this).closest('tr').find('.data-m_distinction_id').val()
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
    $('#totalSub').text(fmPice(totalSub))
    $('#totalSubNotTax').text(fmPice(totalSubNotTax))
    $('#priceTax').text(fmPice(totalSub - totalSubNotTax))

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
    $('.totalDis5Yrs').text(fmPice(priceDisMinus5yrs))

     const pricePrint5yrs = 0;
     // Pack A is not show
    // const pricePrint5yrs = print5yrs['pof_1st_distinction_5yrs'] * totalDis
    // $('.pricePrint').text(fmPice(pricePrint5yrs))

    $('#total').text(fmPice(totalSub + pricePrint5yrs + priceDisMinus5yrs))

    $("input[name='subtotal']").val(totalSub);
    $("input[name='commission']").val(totalSubNotTax);
    $("input[name='tax']").val(totalSub - totalSubNotTax);
    $("input[name='cost_print_application_one_distintion']").val(priceDisMinus5yrs);
    $("input[name='cost_5_year_one_distintion']").val(pricePrint5yrs);
    $("input[name='total_amount']").val(totalSub + pricePrint5yrs + priceDisMinus5yrs);
    $("input[name='total_distinction']").val(totalDis);
    $("input[name='sum_distintion']").val(totalDis);
}

//==================================================================
// Calculate subtotal, commission, products price , total price.
//==================================================================
function fmPice(val) {
    return new Intl.NumberFormat('en-us').format(Math.floor(val))
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
    lengthAppend = $(this).closest('.table_product_choose').find('.before_html_product').length;
    lengthAppend = lengthAppend + 1;

    $('.checkAllCheckBox').prop('checked', false)
    $(this).parent().parent().before(`
        <tr class="before_html_product">
            <td class="eDis">
                <select name="prod[${lengthAppend}][m_distinction_id]" class="data-m_distinction_id distinction-mr w-100">
                    <option value="">${label_select_default}</option>
                    ${optionDISTINCTION}
                </select>
            </td>
            <td class="boxes">
                <input type="text" name="prod[${lengthAppend}][name_product]" class="data-name_product name_prod_${lengthAppend} customer_boxes" key-prod="${lengthAppend}" data-suggest>
            </td>
            <td class="center"><input type="checkbox" name="prod[${lengthAppend}][check]" value="1" class="checkSingleCheckBox single-checkbox"/></td>
        </tr>
    `);

    if (lengthAppend == 500) {
        $(this).remove();
    }
})

// change select distinction add attr
$('body').on('change', '.data-m_distinction_id', function () {
    if ($(this).find("option:selected").text() === '選択') {
        $('.total-dis').text(getTotalDistinction())
        totalSub()
        return false
    }
    $('.total-dis').text(getTotalDistinction())
    totalSub()
})

$('body').on('keyup focus', '[data-suggest]', function (e) {
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

$('body').on('click', 'div[prod_value]', function () {
    let el = $(this)
    var prodId = el.attr('product-id')
    let boxes = el.closest('.before_html_product')

    loadAjaxPost(SuggestURLItem, { id: prodId }, {
        beforeSend: function(){},
        success:function(result){
            if(result?.status) {
                let itemProd = result.data

                boxes.find('.data-name_product').val(itemProd.name)

                // m_distinction
                if(itemProd.m_distinction) {
                    boxes.find('.data-m_distinction_id').val(itemProd.m_distinction.id);

                    $('.total-dis').text(getTotalDistinction())
                    totalSub()
                }

                validProduct()
            }
        },
        error: function (error) {}
    }, 'loading');
})

$(document).bind("click", function (t) {
    $('.search-suggest').remove()
})

$('body').on('click', 'input[type=submit],button[type=submit]', function(e) {
    validProduct();

    let form = $('#form');
    form.valid();

    let hasError = $('#form').find('.error-validate:visible,.notice:visible,.error:visible');
    if (hasError.length == 0 && form.valid()) {
        if ($(this).attr('id') == 'redirect_to_quote') {
            form.attr('target' ,'_blank');
            form.submit()
            loadingBox('close');
        } else {
            form.attr('target' ,'_self');
            form.submit();
        }
    } else {
        let firstError = hasError.first();
        scrollToElement(firstError, -100);
        return false;
    }
});

// Validation
function validProduct() {
    let hasValue = false;
    let hasChecked = false;
    let hasDuplicate = false;
    let productNameArray = [];

    $.each($('.before_html_product'), function () {
        let nameProduct = $(this).find('.data-name_product').val();
        let distinctionID = $(this).find('.data-m_distinction_id').val();
        let checkedProd = $(this).find('.single-checkbox').prop('checked');

        if (nameProduct.length > 0 && distinctionID.length > 0) {
            hasValue = true;

            if (productNameArray.includes(nameProduct)) {
                hasDuplicate = true;
            }

            if (checkedProd == true) {
                hasChecked = true;
            }

            productNameArray.push(nameProduct);
        }

        $(this).find('select.data-m_distinction_id').next('.notice').remove();
        $(this).find('input.data-name_product').next('.notice').remove();

        if (nameProduct.length > 0) {
            if (distinctionID.length == 0) {
                $(this).find('select.data-m_distinction_id').after(`<div class="notice mb15 notice-scrollable">${Common_E001}</div>`)
            } else {
                let regex = /^([ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９々ー－・。（）「」]+\s)*[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９々ー－・。（）「」]+$/;

                if (nameProduct.length > 200) {
                    $(this).find('input.data-name_product').after('<div class="notice mb15 notice-scrollable">'+ support_U011_E001 +'</div>');
                } else if (!regex.test(nameProduct)) {
                    $(this).find('input.data-name_product').after('<div class="notice mb15 notice-scrollable">'+ support_U011_E001 +'</div>');
                }
            }
        } else if (nameProduct.length == 0 & distinctionID.length > 0) {
            $(this).find('input.data-name_product').after(`<div class="notice mb15 notice-scrollable">${Common_E001}</div>`)
        }
    });

    $('.js-scrollable').next('.notice').remove();
    if (hasDuplicate == true) {
        $('.js-scrollable').after(`<div class="notice mb15 notice-scrollable">${duplicateProductName}</div>`)
    } else if (hasValue == false || hasChecked == false) {
        $('.js-scrollable').after(`<div class="notice mb15 notice-scrollable">${Common_E025}</div>`)
    }
}

$('body').on('change keyup focusout', '.data-name_product', function() {
    validProduct();
});

$('body').on('change', '.data-m_distinction_id', function() {
    $(this).closest('.before_html_product').find('.data-name_product').val('');

    validProduct();
});

$('body').on('click', '.search-suggest__list .item', function() {
    $(this).closest('.boxes').find('.error').remove();
    $(this).closest('.before_html_product').find('.eDis').find('.error').remove();
});
