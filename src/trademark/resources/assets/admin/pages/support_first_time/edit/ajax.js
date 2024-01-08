var timeOut = null;
$('body').on('keyup focus', '[data-suggest]', function(event) {
    clearTimeout(timeOut)
    self = $(this);
    timeOut = setTimeout(function(){
        let product = self.attr('key-prod')
        if($('.is_block_'+product).is(':checked')) {
            return false
        }
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
            success: function(res) {
                $('.search-suggest').remove()
                if (res?.status) {
                    $(`.name_prod_${res.data.prod}`).append(res.data.html)
                }
            },
            error: function(data) {
                // location.reload();
            }
        }, 1000);
    });
});

$(document).on('click', '[prod_value]', function() {
    const self = $(this)
    let prodValue = self.attr('prod_value')
    let keyType = self.attr('key_type')
    let prod = self.attr('key_item')
    let prodId = self.data('id')

    loadAjaxPost(SuggestURLItem, { id: prodId }, {
        success:function(result){
            if(result?.status) {
                let itemProd = result.data

                //update data
                $('.m_product_name_edit_'+prod).val(prodValue)
                $('.m_product_id_edit_'+prod).val(prodId)

                self.closest('.boxes').append(`<input type="hidden" name="data[${prod}][data_edit][type]" type-prod value="${keyType}" />`)

                //type_prod
                let typeProdEl = $(`.product_type_${prod}`)
                if(typeProdEl.val() != mProductType4) {
                    $(`.product_type_${prod}`).val(keyType)
                }

                if (itemProd.m_distinction) {
                    $(`.label_m_distinction_edit_${prod}`).text(itemProd.m_distinction.name)
                    $(`.m_distinction_edit_${prod}`).val(itemProd.m_distinction.id)
                } else {
                    $(`#distinction_${prod}`).html('')
                }

                if (itemProd.product_code) {
                    let code = itemProd.product_code.map(item => item.code_name)
                    $(`.label_m_code_edit_${prod}`).html(code.join(' '))
                    $(`.m_code_edit_${prod}`).val(code.join(','))
                } else {
                    $(`#prod_code_${prod}`).html('')
                }

                $('.search-suggest').remove()
            }
        }
    });
});
