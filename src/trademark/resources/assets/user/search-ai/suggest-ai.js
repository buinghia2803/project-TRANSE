$(document).ready(function () {

    disableAddedItem();

    // Add to list
    $('body').on('click', '[data-add_product]', function (e) {
        e.preventDefault();
        let box = $(this).closest('tr');
        let id = box.data('prod_id');
        let distinction = box.find('.distinction').text();
        let name = box.find('.prod_name').text();

        if ($(this).closest('#suggest-product-box').length > 0) {
            if (!inArray(id, getListSuggest())) {
                appendSuggestList(id, distinction, name);
                disableAddedItem();
            }
        } else {
            if (!inArray(id, getListIdAddition())) {
                appendListAddMore(id, distinction, name);
                disableAddedItem();
            }
        }
    });

    // Remove to list
    $('body').on('click', '[data-remove_product]', function (e) {
        e.preventDefault();
        let box = $(this).closest('tr');
        let id = box.data('prod_id');

        updateMatchItem(id, 'enable');
        updateSuggestItem(id, 'enable');
        box.remove();
    });

    // Click Suggest AI
    $('body').on('click', '#suggest-ai', function (e) {
        e.preventDefault();

        const additionalProduct = [];
        $('#additional-list').find('tr[data-prod_id]').each(function () {
            additionalProduct.push($(this).find('.prod_name').text());
        });

        const suggestProduct = [];
        $('#list-suggest-product').find('tr[data-prod_id]').each(function () {
            suggestProduct.push($(this).data('name'));
        });

        if (additionalProduct.length == 0 && suggestProduct.length == 0) {
            $.alert(ErrorMessageNotFoundProd);
        } else {
            loadAjaxPost(SEARCH_AI_AJAX_URL, {
                'additional_product': additionalProduct,
                'suggest_product': suggestProduct,
            }, {
                beforeSend: function(){},
                success:function(result){
                    appendSearchAIBox(result);
                    $('#suggest-ai-box').removeClass('hidden');
                },
                error: function (error) {}
            });
        }
    });

    // Click Submit
    $('body').on('click', 'input[type=submit]:not(.logout)', function (e) {
        e.preventDefault();
        let form = $('#suggest-form');
        let prod_additional_ids = getListIdAddition();
        let prod_suggest_ids = getListIdSuggest();
        let dataSubmit = $(this).data('submit');

        if (dataSubmit == SEARCH_AI_PRECHECK) {
            if (prod_additional_ids.length == 0 && prod_suggest_ids.length == 0) {
                $.alert(ErrorMessageSelectProd);

                return false;
            }
        }

        let formaction = $(this).attr('formaction');
        if(formaction != undefined && formaction.length > 0) {
            form.attr('action', formaction);
        }

        form.find('input[name=prod_additional_ids]').val(prod_additional_ids);
        form.find('input[name=prod_suggest_ids]').val(prod_suggest_ids);
        form.find('input[name=submit_type]').val(dataSubmit);

        if (dataSubmit == SUBMIT_EDIT) {
            loadingBox('open');
            form.submit();
        } else if(dataSubmit == SEARCH_AI_REGISTER && IS_TRADEMARK_IMAGE == '1') {
            $(this).parent().find('.red').remove();
            $(this).after(`<div class="red">${errorMessageTrademarkImage}</div>`);

            return false;
        } else if (IS_MAX_FOLDER == 'true') {
            $.confirm({
                title: '',
                content: DELETE_POPUP_TITLE,
                buttons: {
                    cancel: {
                        text: NO,
                        btnClass: 'btn-default',
                        action: function () {}
                    },
                    ok: {
                        text: YES,
                        btnClass: 'btn-blue',
                        action: function () {
                            loadingBox('open');
                            form.submit();
                        }
                    }
                }
            });
        } else {
            loadingBox('open');
            form.submit();
        }
    });
})

function getListIdAddition() {
    let arrayID = [];
    $('#additional-list').find('tr[data-prod_id]').each(function () {
        arrayID.push($(this).data('prod_id'));
    });

    return arrayID;
}

function getListSuggest() {
    let arrayID = [];
    $('#list-suggest-product').find('tr[data-prod_id]').each(function () {
        arrayID.push($(this).data('prod_id'));
    });

    return arrayID;
}

function getListIdSuggest() {
    let arrayID = [];
    $('#list-suggest-product').find('tr[data-prod_id]').each(function () {
        arrayID.push($(this).data('prod_id'));
    });

    return arrayID;
}

function disableAddedItem() {
    let addedID = getListIdAddition();
    addedID.map(function (item) {
        updateMatchItem(item, 'disable');
        updateSuggestItem(item, 'disable');
    });

    let suggestID = getListSuggest();
    suggestID.map(function (item) {
        updateMatchItem(item, 'disable');
        updateSuggestItem(item, 'disable');
    });
}

function updateMatchItem(id, action = 'disable') {
    $('.match-table').each(function () {
        let matchItem = $(this).find('tr[data-prod_id='+ id +']');
        switch (action) {
            case "disable":
                matchItem.find('[data-add_product]').css('display', 'none');
                matchItem.find('[data-added_product]').css('display', 'inline-block');
                matchItem.find('.distinction').css('color', '#cccccc');
                matchItem.find('.prod_name').css('color', '#cccccc');
                break;
            case "enable":
                matchItem.find('[data-add_product]').css('display', 'inline-block');
                matchItem.find('[data-added_product]').css('display', 'none');
                matchItem.find('.distinction').css('color', '#000000');
                matchItem.find('.prod_name').css('color', '#000000');
                break;
        }
    })
}

function updateSuggestItem(id, action = 'disable') {
    $('.suggest-table').each(function () {
        let suggestItem = $(this).find('tr[data-prod_id='+ id +']');
        switch (action) {
            case "disable":
                suggestItem.css('display', 'none');
                break;
            case "enable":
                // suggestItem.css('display', 'table-row');
                break;
        }
    })
}

function appendListAddMore(id, distinction, name) {
    $('#additional-list').find('tbody').append(`
        <tr data-prod_id="${id}" data-name="${name}">
            <td class="distinction">${distinction}</td>
            <td class="prod_name">${name}</td>
            <td class="center"><input type="button" value="${ DELETE_PRODUCT }" data-remove_product class="small btn_d"/></td>
        </tr>
    `);
}

function appendSuggestList(id, distinction, name) {
    $('#list-suggest-product').find('tbody').append(`
        <tr data-prod_id="${id}" data-name="${name}">
            <td class="distinction">${distinction}</td>
            <td class="prod_name">${name}</td>
        </tr>
    `);
}

function appendSearchAIBox(data) {
    let additionalProduct = data.additional_product;
    $('#addition-product-box').empty();
    $.each(additionalProduct, function (index, item) {
        let keyword = item.keyword;
        let products = item.products;
        $('#addition-product-box').append(htmlProductBox(keyword, products));
    });

    let suggestProduct = data.suggest_product;
    $('#suggest-product-box').empty();
    $.each(suggestProduct, function (index, item) {
        let keyword = item.keyword;
        let products = item.products;
        $('#suggest-product-box').append(htmlProductBox(keyword, products));
    });

    disableAddedItem();
}

function htmlProductBox(keyword, products) {
    if (products.length > 0) {
        let productHtml = ``;
        $.each(products, function (index, item) {
            productHtml += `
                <tr data-prod_id="${item.id}">
                    <td class="center">
                        <input type="button" value="${ADD}" data-add_product class="small btn_b"/>
                    </td>
                    <td><span class="distinction">${item.m_distinction.name}</span></td>
                    <td><span class="prod_name">${item.name}</span></td>
                </tr>
            `;
        });

        return `
            <p class="mb00">${PRODUCT_NAME}： ${keyword}</p>
            <table class="normal_b mb20 suggest-table" style="width: 100%;">
                <tr>
                    <th class="w-100px">${ADD_TO_LIST}</th>
                    <th class="w-50px">${DISTINCTION_NAME}</th>
                    <th>${PRODUCT_NAME}</th>
                </tr>
                ${productHtml}
            </table>
        `;
    }
}
