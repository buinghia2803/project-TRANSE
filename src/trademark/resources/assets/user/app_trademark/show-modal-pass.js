Array.prototype.diff = function(a) {
    return this.filter(function(i) {return a.indexOf(i) < 0;});
};

$('body').on('click', '#close', function (e) {
    e.preventDefault();
    window.parent.closeModal('#u031pass-modal');
    $('.error-table').html('')

})

$('body').on('click', '.add_prod', function (e) {
    e.preventDefault();
    let productHTML = '';
    let productID = $('input[name=products]:checked');
    let productTable = window.parent.$('.table_product_choose');

    if (productID.length == 0) {
        $('.error-table').html(ErrorApplicationU031E004)
        return false;
    } else {
        $('.error-table').html('')
    }
    let trTable = productTable.find('tr[data-id]');
    let idArray = [];
    $.each(trTable, function (index, item) {
        idArray.push($(item).data('id'));
    })
    let arrayProducts = []
    let arrayProductName = []
    let arrayProductIds = []
    $.each(productID, function (index, item) {
        let val = $(this).val();
        val = JSON.parse(val);
        if(!inArray(val.product_id, arrayProductIds)) {
            arrayProducts.push(val)
            arrayProductIds.push(val.product_id)
        }
    })
    //Add row of table
    $.each(arrayProducts, function (index, item) {
        let val = item
        if(!inArray(val.product_id, idArray)) {
            arrayProductName.push(val.product_name);

            if(fromPage == 'u021b_31' || fromPage == 'u021b') {
                productHTML += `<tr class="before_html_product add_new add_new_${val.product_id}" data-id="${val.product_id}">
                        <td class="eDis">第${val.distinction_name}類</td>
                        <td class="boxes boxes_1">${val.product_name}</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td class="center">
                          <input type="checkbox" name="is_choice_user[]" data-foo="is_choice_user[]" class="single-checkbox single-checkbox-${val.product_id}" value="${val.product_id}" data-name-distinction="${val.distinction_id}">
                          <input type="hidden" name="productIds[]" value="${val.product_id}">
                       </td>
                    </tr>`;
            } else if(fromPage == 'u031edit' || fromPage == 'u031_edit_with_number') {
                productHTML += `<tr class="before_html_product add_new add_new_${val.product_id}" data-id="${val.product_id}">
                        <td class="eDis">第${val.distinction_name}類</td>
                        <td class="boxes boxes_1">${val.product_name}</td>
                        <td class="center">
                            <input type="checkbox" name="m_product_ids[]" value="${val.product_id}" class="checkSingleCheckBox single-checkbox" data-name-distinction="第${val.distinction_name}類">
                            <input type="hidden" name="mProducts[]" value="${val.product_id}">
                        </td>
                    </tr>`;
            } else if(fromPage == 'u031') {
                let randomStr = Math.random().toString(13);
                productHTML += `<tr class="before_html_product add_new add_new_${val.product_id}" data-id="${val.product_id}">
                        <td class="eDis">第${val.distinction_name}類</td>
                        <td class="boxes boxes_1">${val.product_name}</td>
                        <td class="center">
                            <input type="checkbox" name="prod[${randomStr}][check]" value="${val.product_id}" class="checkSingleCheckBox single-checkbox">
                            <input type="hidden" name="prod[${randomStr}][m_distinction_id]" value="${val.distinction_id}" class="data-m_distinction_id">
                            <input type="hidden" name="prod[${randomStr}][name_product]" value="${val.product_name}" class="data-name_product">
                        </td>
                    </tr>`;
            } else if( fromPage == 'u011b' || fromPage == 'u011b_31') {
                productHTML += `<tr class="before_html_product add_new add_new_${val.product_id}" data-distinction-id="${val.distinction_id}" data-id="${val.product_id}">
                        <td class="eDis">第${val.distinction_name}類</td>
                        <td class="boxes boxes_1">${val.product_name}</td>
                        <td class="center">
                            <input type="checkbox" class="checkSingleCheckBox single-checkbox is_choice_user_${val.product_id}" data-product_id="${val.product_id}" name="is_choice_user_${val.product_id}"
                                data-foo="is_choice_user[]" value="${val.product_id}" />
                            <input type="hidden" name="productIds[]" value="${val.product_id}" />
                        </td>
                    </tr>`;
            } else if(fromPage == 'u031b') {
                productHTML += `<tr class="before_html_product add_new add_new_${val.product_id}" data-distinction-id="${val.distinction_id}" data-id="${val.product_id}">
                        <td class="eDis td-distinction">${constNo}${val.distinction_name}${constKind}</td>
                        <td class="boxes boxes_1">${val.product_name}</td>
                        <td class="center">
                            <input type="checkbox" class="single-checkbox productIdsChoose-${val.product_id}" name="m_product_ids_choose[]"
                                data-foo="is_apply[]" value="${val.product_id}" />
                            <input type="hidden" name="m_product_ids[]" value="${val.product_id}" />
                        </td>
                    </tr>`;
            }
        } else {
            productHTML += ``
        }
    });
    productTable.find('tr.add-product').before(productHTML);
    $('#close').click();

    //u011b
    if(fromPage == 'u011b') {
        window.parent.classHajimeSupportCustomer.setAllCheckBoxDefault()
    }
    //u011b_31
    if(fromPage == 'u011b_31') {
        window.parent.classClsHajimeSupportAMS.setAllCheckBoxDefault()
    }
    //u031b
    if(fromPage == 'u031b') {
        window.parent.setAllCheckBoxDefault();
    }

    //u021b or u021b_31
    if(fromPage == 'u021b' || fromPage == 'u021b_31') {
        window.parent.checkedAllDefault();
    }

    if(fromPage == 'u020a') {
        window.parent.appendKeyword(arrayProductName);
    }

    $("input[name=products]").prop('checked', false)
});

