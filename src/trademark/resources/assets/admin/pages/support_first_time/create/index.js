$(document).ready(function () {
    var index = 1
    var indexCode = 1
    const CREATIVE_CLEAN = 3
    const SEMI_CLEAN = 4
    var dataSearch = []
    timer = null;
    var checkAddProd = 0
    let dataProduct = []
    let flugError = false
    let dataTable = []

    validation('#form', {
        'comment[0][content]': {
            maxlength: 500,
        },
        'comment[1][content]': {
            maxlength: 500,
        },
    }, {
        'comment[0][content]': {
            maxlength: errorMessageMaxLength500,
        },
        'comment[1][content]': {
            maxlength: errorMessageMaxLength500,
        },
    });

    // add product
    $(document).on('click', '#add_prod', function () {
        var isRequired = true
        var listProd = []

        let flugData = limitAddRow()
        if(!flugData) {
            return false;
        }

        prodName = listProd.filter(item => ((item.type == 1 || item.type == 2) && item.name !== '')).map(item => item.name)
        duplicateElement = prodName.filter((item, index) => prodName.indexOf(item) !== index);

        index = $('#dynamic tr').length;
        addProductDefault(index)
        // setting no number
        setNoNumber()
    })

    function limitAddRow() {
        let total = $('.item_product').length
        if(total >= limitAddRowConst) {
            $.confirm({
                title: '',
                content: errorMessageYouCanEnter100Items,
                buttons: {
                    cancel: {
                        text: confirmText,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    },
                }
            });
            return false;
        }
        return true;
    }

    //Delete item
    $('.deleteItem').on('click', function () {
        let txt = $(this).val()
        if (txt == deleteAllText) {
            $(this).val('復帰')
            $(this).addClass('btn_c')
            $(this).removeClass('btn_d')
        } else {
            $(this).val(deleteAllText)
            $(this).addClass('btn_d')
            $(this).removeClass('btn_c')
        }

        // 削除
        let index = $(this).data('index')
        $(this).closest('.item_product').find('td').toggleClass('bg_gray')
        $(this).closest('.item_product').find('td').first().removeClass('bg_gray')
        let el = $('.delete_item_hide_'+index)
        if (el.is(':checked')) {
            el.prop('checked', false)
        } else {
            el.prop('checked', true)
        }
    });

    $('textarea').on('change', function () {
        if ($(this).height() > 150) {
            $(this).css('overflow-y', 'scroll')
        }
    })

    $(document).on('click', '#btn_confirm', function () {
        $('#duplicate_modal').css('display', 'none')
    })

    $(document).on('click', '.close', function () {
        $('#validate_modal').css('display', 'none')
        $('#duplicate_modal').css('display', 'none')
    })

    $(document).on('click', '#btn_cancel', function () {
        $('#validate_modal').css('display', 'none')
    })

    $(document).on('click', '.close', function () {
        $('#flagRoleSeki').css('display', 'none')
        $('#duplicate_modal').css('display', 'none')
    })

    $(document).on('click', '#btn_cancel', function () {
        $('#flagRoleSeki').css('display', 'none')
    })

    $(document).on('click', '#btn_ok', function () {
        $('#validate_modal').css('display', 'none')
        index++;
        if (checkAddProd) {
            addProductFree(index)
        } else {
            addProductDefault(index)
        }
    })

    function disabledBtnSubmitSaveNotice() {
        dataTable = [];
        $('body').find('.item_product').each(function(index, item) {
            let valProdName = $(item).find('.prod_name').val()
            let valDistinction = $(item).find('.m-distinction').val()
            let keyProd = $(item).find('.remove-prod').attr('key-prod')
            if(valProdName !== '') {
                dataTable.push({
                    key_prod: keyProd,
                    prod_name: valProdName,
                })
            }
        });
        if(dataTable.length > 0) {
            $('.saveNotice').prop('disabled', false).css('cursor', 'pointer')
        } else {
            $('.saveNotice').prop('disabled', true).css('cursor', 'not-allowed')
        }
    }
    // disabledBtnSubmitSaveNotice()

    //submit form
    $('body').on('click', 'input[name=save]', function (e) {
        e.preventDefault();

        $('.alert').remove();

        if($(this).hasClass('saveDraft')) {
            //save data and show quoutes
            $('#code-button').val('')
        } else if($(this).hasClass('saveNotice')) {
            //save data and go to anken top
            $('#code-button').val(notice)
        }

        const allProductValues = []
        const allProduct = $('.input_product_name')
        $('.input_product_name').each(function () {
            if (!$(this).val() == '') {
                allProductValues.push({
                    id: $(this).attr('id'),
                    value: $(this).val()
                })
            }
        })
        let isDuplicateProductName = false
        let productNameNull = []
        $('.input_product_name').each(function (index, el) {
            if ($(el).val() == '') {
                productNameNull.push(index)
            }
            if (!!allProductValues.find(product => product.value === $(this).val() && product.id !== $(this).attr('id'))) isDuplicateProductName = true
        })

        //check type submit
        if($(this).hasClass('saveNotice')) {
            if (allProduct.length == productNameNull.length) {
                if($('body').find('.error').length == 0 && !validateRequiredAllChooseProduct(e)) {
                    $.confirm({
                        title: '',
                        content: errorRequiredProduct,
                        buttons: {
                            cancel: {
                                text: btnCancel,
                                btnClass: 'btn-default',
                                action: function () {
                                    scrollToElement($('.product-table').first());
                                }
                            }
                        }
                    });
                }
            } else if (isDuplicateProductName) {
                $.confirm({
                    title: '',
                    content: errorUniqueProduct,
                    buttons: {
                        cancel: {
                            text: confirmText,
                            btnClass: 'btn-default',
                            action: function () {
                                scrollToElement($('.product-table').first());
                            }
                        },
                    }
                });
            } else {
                if($('body').find('.error').length == 0 && !validateRequiredAllChooseProduct(e)) {
                    if ($("#form").valid()) {
                        $('.errorRequiredSftContentProd').html('')
                        loadingBox('open');
                        form.submit();
                    }
                }
            }
        } else {
            if ($('body').find('.error').length == 0 && $("#form").valid()) {
                form.submit();
            }
        }
    });

    //function validate request choose product all
    function validateRequiredAllChooseProduct(event) {
        if ($('.is_choice_admin:checked').length <= 0 || ($('.is_choice_admin').length > $('.is_choice_admin:checked').length)) {
            $('.errorRequiredSftContentProd').html(`<div class="error-validate" style="font-size: 14px">${errorCheckAll}</div>`)
            $([document.documentElement, document.body]).animate({
                scrollTop: $(".errorRequiredSftContentProd").offset().top - 100
            }, 1000);
            return true;
        }
        return false
    }

    $('.is_choice_admin').on('change', function() {
        if($('.is_choice_admin').length > 0 && ($('.is_choice_admin').length == $('.is_choice_admin:checked').length)) {
            $('.errorRequiredSftContentProd').html('')
        }
    });
    //Validate products
    var timeOut = null;
    $('body').on('keyup focus', 'input[class*=prod_name]', function (event) {
        clearTimeout(timeOut)
        self = $(this);
        timeOut = setTimeout(function(){
            let product = self.attr('key-prod')
            let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;

            let value = self.val();
            self.closest('.boxes').find('.error').remove();
            if (value.length > 200) {
                $('.search-suggest').remove();
                self.after('<div class="error">' + errorIsValid + '</div>')
            } else if (!regex.test(value) && value !== '') {
                $('.search-suggest').remove();
                self.after('<div class="error">' + errorIsValid + '</div>')
            } else {
                disabledBtnSubmitSaveNotice()
                let suggestAttr = self.attr('data-suggest')
                if (typeof suggestAttr !== typeof undefined && suggestAttr !== false) {
                    $('.search-suggest').remove()
                    $.ajax({
                        method: 'POST',
                        url: SuggestURL,
                        data_type: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            keyword: self.val(),
                            prod: product
                        },
                        success: function (res) {
                            $('.search-suggest').remove()
                            if (res?.status) {
                                $(`.name_prod_${res.data.prod}`).append(res.data.html)
                            }
                            disabledBtnSubmitSaveNotice()
                        },
                        error: function (data) {
                        }
                    });
                }
            }
        }, 1000);
    });
    //validate code
    $('body').on('change focusout focusin', 'input[class*=prod_code]', function() {
        let value = $(this).val()
        if ($(this).val().length) {
            if (value.length != 5) {
                $(this).next().filter('.error').remove()
                $(this).after(`<div class="error">${errorMessageFormatCode}</div>`)
                flugError = true
            } else if (!isValidProdCode(value) && value !== '') {
                $(this).next().filter('.error').remove()
                $(this).after(`<div class="error">${errorMessageFormatCode}</div>`)
                flugError = true
            } else {
                flugError = false
                $(this).next().filter('.error').remove()
            }
        } else {
            flugError = false
            $(this).next().filter('.error').remove()
        }
    })

    $(document).on('click', '[prod_value]', function () {
        const self = $(this);
        var prodValue = self.attr('prod_value')
        var keyType = self.attr('key_type')
        var prod = self.attr('key_item')
        //add value
        let prodId = self.data('id')
        self.closest('.boxes').find('[data-suggest]').val(prodValue)
        //call ajax get data item
        loadAjaxPost(SuggestURLItem, { id: prodId }, {
            beforeSend: function(){},
            success:function(result){
                if(result?.status) {
                    let itemProd = result.data

                    //set data table
                    let elementTypeProd = self.closest('.boxes').find('[type-prod]')
                    if($(elementTypeProd).length) {
                        if($(elementTypeProd).val() == SEMI_CLEAN) {
                            elementTypeProd.val(SEMI_CLEAN)
                        } else {
                            elementTypeProd.val(keyType)
                            self.closest('.boxes').find('[data-suggest]').prop('readonly', true)
                        }
                    } else {
                        self.closest('.boxes').append(`
            <input type="hidden" name="data[${prod}][type]" type-prod value="${keyType}" />
        `)
                        self.closest('.boxes').find('[data-suggest]').prop('readonly', true)
                    }
                    if (itemProd.m_distinction) {
                        $(`#distinction_${prod}`).html(itemProd.m_distinction.name)
                        $(`#distinction_${prod}`).append(`
            <input hidden name="data[${prod}][distinction]" class="m-distinction" value="${itemProd.m_distinction.id}" prod-distinction/>
            `)
                    } else {
                        $(`#distinction_${prod}`).html('')
                    }
                    //type_prod
                    let typeProdEl = $(`.prod_type_${prod}`)
                    if(typeProdEl.val() != SEMI_CLEAN) {
                        $(`.prod_type_${prod}`).val(itemProd.type)
                    }
                    if (itemProd.product_code) {
                        let codeName = itemProd.product_code.map(item => item.code_name).join(' ')
                        let codeIds = itemProd.product_code.map(item => item.m_code_id).join(',')
                        $(`.label_prod_code_${prod}`).html(codeName)
                        $(`.code_ids_${prod}`).val(codeIds)
                    } else {
                        $(`.label_prod_code_${prod}`).html('')
                        $(`.code_ids${prod}`).val('')
                    }
                    $('.search-suggest').remove();
                    disabledBtnSubmitSaveNotice()
                }
            },
            error: function (error) {}
        }, 'loading');
    })

    //click change to row pink: type = 4
    $('body').on('click', '#edit_name_prod', function () {
        let valueProd = $(this).closest('.boxes').find('.prod_name').val()
        if(valueProd) {
            $(this).closest('.boxes').find('[data-suggest]').prop('readonly', false)
            $(this).closest('.boxes').find('[type-prod]').val(SEMI_CLEAN)
            $(this).closest('tr').find('.boxes').addClass('bg_pink')
            $(this).closest('tr').find('.distinction').addClass('bg_pink')
            $(this).closest('tr').find('.prod_code').addClass('bg_pink')
            $(this).closest('tr').find('.prod_code').addClass('bg_pink')
            $(this).closest('.boxes').find('[data-suggest]').removeAttr('data-suggest')
            //hide button
            $(this).remove()
        } else {
            $.confirm({
                title: '',
                content: errorSelectProductNameAndEdit,
                buttons: {
                    cancel: {
                        text: backLabel,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    }
                }
            });
        }
    })

    // remove product
    $(document).on('click', '.remove-prod', function () {
        var prod = $(this).attr('key-prod')
        let itemDom = $(this).closest('.item_product')
        let valProdName = itemDom.find('.prod_name').val()
        let valDistinction = itemDom.find('.m-distinction').val()
        itemDom.remove()
        //set no number
        setNoNumber()
        disabledBtnSubmitSaveNotice()
    })

    // add product free
    $('#add_prod_free').on('click', function () {
        let flugData = limitAddRow()
        if(!flugData) {
            return false;
        }
        index = $('#dynamic tr').length;
        addProductFree(index)
        // setting no number
        setNoNumber()
    })

    $(document).on('click', '#add_code', function () {
        index = $(this).data('index')
        let indexCo = $(`#input-code-${index} .prod_code`).length;
        if (indexCo > 49) {
            $(this).remove()
        }
        $(`#input-code-${index}`).append(`
            <input type="text" class="em18 prod_code" value="" data-code name="data[${index}][code][${indexCo}][name]" nospace autocomplete="off" autocomplete="off"/>
        `)
    })
})

function addProductDefault(index) {
    const urlParam = new URLSearchParams(window.location.search)
    var html = `
        <tr id="prod${index}" class="item_product">
            <td class="center">
            <span class="no-number">${index}</span><br />
                <input type="button" value="${deleteText}" class="small btn_d remove-prod" id="remove-product" key-prod="${index}" />
                <input type="text" hidden name="support_first_time_id" value="${urlParam.get('support_first_time_id')}"/>
            </td>
            <td class="boxes name_prod_${index}" data-name>
                <input type="button" value="${editText}" class="btn_a mb05" id="edit_name_prod"/>
                <input type="text" nospace autocomplete="off" class="em40 prod_name input_product_name" name="data[${index}][name]" data-suggest id="product_name_${index}" key-prod="${index}"/>
                <input type="hidden" name="data[${index}][type]" class="prod_type prod_type_${index}" type-prod value=""/>
            </td>
            <td class="center distinction" id="distinction_${index}"></td>
            <td class="prod_code" id="prod_code_${index}">
                <span class="label_prod_code_${index}"></span>
                <input type="hidden" name="data[${index}][code_ids]" value="" class="code_ids code_ids_${index}" />
            </td>
        </tr>
    `;
    $('#dynamic').append(html)
}

function addProductFree(index) {
    const urlParam = new URLSearchParams(window.location.search)
    const CREATIVE_CLEAN = 3
    let distinction = JSON.parse(DISTINCTION);
    let distinctionOption = '';
    $.each(distinction, function (item, value) {
        distinctionOption += `<option value="${value.id}" name="distinction">${value.name}</option>`;
    })
    var html =
        `<tr id="prod${index}" class="item_product">
        <td class="center">
            <span class="no-number">${index}</span><br />
            <input type="button" value="${deleteText}" class="small btn_d remove-prod" id="remove-product" key-prod="${index}"/>
            <input type="text" hidden name="support_first_time_id" value="${urlParam.get('support_first_time_id')}"/>
        </td>
        <td class="boxes bg_yellow">
            <input type="text" class="em40 prod_name input_product_name" autocomplete="off" value="" name="data[${index}][name]" key-prod="${index}" nospace/><br />
            <input hidden type="text" class="em40" name="data[${index}][type]" type-prod value="${CREATIVE_CLEAN}" />
        </td>
        <td class="center bg_yellow">
            <select name="data[${index}][distinction]" class="m-distinction">
                @foreach($distinction as $value)
                    ${distinctionOption}
                @endforeach
            </select>
        </td>
        <td class="bg_yellow">
            <div data-code id="input-code-${index}">
                <input type="text" class="em18 prod_code" value="" autocomplete="off" name="data[${index}][code][0][name]" nospace autocomplete="off"/>
            </div>
            <button type="button" id="add_code" class="add_code" data-index="${index}">${addCodeText}</button>
        </td>
    </tr>`
    $('#dynamic').append(html)
}

//set no number
function setNoNumber() {
    $('#dynamic tr').map(function (index, el) {
        $(el).find('.no-number').text(index + 1)
    });
}

$(document.body).click( function() {
    $('.search-suggest').hide()
});
