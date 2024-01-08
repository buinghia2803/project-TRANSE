class GoodsMasterDetailClass {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    doLoad() {
        this.initValidate()
        this.sortData()
        this.addNewCode()
        this.deleteCode()
        this.onChangeIsParent()
        this.onChangeParentId()
    }

    initValidate() {
        $('body').on('change', '.m_product_name, .m_code_name, .parent_id', function() {
            $('.row-item').each(function (index, el) {
                $(el).find('.error').remove()

                //1.valid product name
                let valProdName = $(el).find('.m_product_name').val()
                if(valProdName.length <= 0) {
                    $(el).find('.m_product_name').after(`<div class="error">${Common_E001}</div>`)
                } else if(!isValidFullwidthListProd(valProdName)) {
                    $(el).find('.m_product_name').after(`<div class="error">${support_U011_E001}</div>`)
                } else if(valProdName.length > 200) {
                    $(el).find('.m_product_name').after(`<div class="error">${support_U011_E001}</div>`)
                }

                //2.valid code
                $(el).find('.item-code').each(function(k, itemCode) {
                    if(itemCode) {
                        let valCode = $(itemCode).find('.m_code_name').val()
                        if(valCode.length <= 0) {
                            $(itemCode).append(`<div class="error">${Common_E001}</div>`)
                        } else {
                            if(!isValidProdCode(valCode)) {
                                $(itemCode).append(`<div class="error">${support_A011_E003}</div>`)
                            }
                        }
                    }
                });

                //3.valid parent_id
                let valProdNumberParent = $(el).find('.parent_id').val()
                if(valProdNumberParent.length > 0) {
                    let rex = /^[a-zA-Z0-9]*$/;
                    if(!rex.test(valProdNumberParent)) {
                        $(el).find('.parent_id').after(`<div class="error">${a000goods_master_detail_E0001}</div>`)
                    } else if(valProdNumberParent.length != 6) {
                        $(el).find('.parent_id').after(`<div class="error">${a000goods_master_detail_E0001}</div>`)
                    }
                }
            });
        })
    }

    sortData() {
        $('.sort-btn').on('click', function (e) {
            e.preventDefault();
            let dataTarget = $(this).data('target')
            let targetSort = $(this).data('sort')
            if (dataTarget && targetSort) {
                $(`#${dataTarget}`).val(targetSort)
            }
            $('#form-sort-table').submit()
        })
    }

    addNewCode() {
        $('.add_code').on('click', function () {
            let elRow = $(this).closest('.row-item');
            let indexRow = elRow.data('row');
            let lengthItem = elRow.find('.item-code').not('.item-code-delete').length;
            let lastItem = elRow.find('.item-code').last();
            let indexNewCode;
            if(lastItem && lastItem.data('key') >= lengthItem) {
                indexNewCode = lastItem.data('key') + 1;
            } else {
                indexNewCode = lengthItem;
            }
            let html = `<div class="item-code item-code-${indexNewCode} mt-1" data-key="${indexNewCode}">
                <input type="text" value="" name="data[${indexRow}][m_codes][${indexNewCode}][name]" class="em06 m_code_name">
                <a class="delete delete-code" href="javascript:void(0)">×</a><br>
                </div>`;
            elRow.find('.wp_codes').append(html)
        });
    }

    deleteCode() {
        $('body').on('click', '.delete-code', function() {
            let elItemCode = $(this).closest('.item-code');
            let elCodeId = elItemCode.find('.m_code_id');
            if(elCodeId && elCodeId.val()) {
                //delete on database
                elItemCode.find('.status_delete').val(statusIsDelete)
                elItemCode.addClass('item-code-delete')
            } else {
                //remove dom
                elItemCode.remove()
            }
        })
    }

    onChangeIsParent() {
        $('.is_parent').on('change', function() {
            let tdParent = $(this).closest('td')
            if($(this).is(':checked')) {
                tdParent.find('.parent_id').prop('disabled', true)
                tdParent.find('.parent_id').val('')
            } else {
                tdParent.find('.parent_id').prop('disabled', false)
            }
        });
    }

    onChangeParentId() {
        $('.parent_id').on('change', function() {
            let tdParent = $(this).closest('td')
            if($(this).val().length > 0) {
                tdParent.find('.is_parent').prop('disabled', true)
            } else {
                tdParent.find('.is_parent').prop('disabled', false)
            }
            //call ajax check data exists
            let value = $(this).val()
            if(value.length > 0) {
                let trDom = $(this).closest('.row-item')
                let mProductId = null;
                let domProdId = $(trDom).find('#m_product_id_old')
                if (domProdId) {
                    mProductId = domProdId.val()
                }
                let rex = /^[a-zA-Z0-9]*$/;
                if(!rex.test(value)) {
                    $(trDom).find('.parent_id').after(`<div class="error">${a000goods_master_detail_E0001}</div>`)
                } else if(value.length != 6) {
                        $(el).find('.parent_id').after(`<div class="error">${a000goods_master_detail_E0001}</div>`)
                } else {
                    let params = {
                        value: value,
                        m_product_id: mProductId
                    }

                    loadAjaxPost(routeCheckNumberProductAjax, params, {
                        beforeSend: function(){},
                        success:function(result){
                            if(!result || !result?.status) {
                                $(trDom).find('.parent_id').after(`<div class="error">${a000goods_master_detail_E0002}</div>`)
                            }
                        },
                        error: function (error) {}
                    }, 'loading');
                }
            }
        });
    }
}

new GoodsMasterDetailClass();
