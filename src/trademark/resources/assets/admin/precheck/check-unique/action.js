$(document).ready(function () {
    datas.forEach(element => {
        element['product'].forEach(item => {
            $(document).on('click','.add_' + item.id ,function () {
                let productCode = [];
                item.code.forEach(function (itemCode) {
                    productCode.push(itemCode.name + ' ')
                })
                $('.icon-add-sub').removeClass('sub_' + item.id)
                $('.icon-add-sub').addClass('sub_' + item.id)
                $('.text-product-code_'+ item.id).html(productCode)
                $('.icon-add-sub').html('-')
            })

            $(document).on('click', '.sub_' + item.id ,function () {
                let productCode = [];
                item.code.forEach(function (itemCode, key) {
                    if(key < 3) {
                        productCode.push(itemCode.name + ' ')
                    }
                })
                $('.icon-add-sub').removeClass('sub_' + item.id)
                $('.icon-add-sub').addClass('add_' + item.id)
                $('.icon-add-sub').html('+')
                $('.text-product-code_'+ item.id).html(productCode)
            })
        })
    });
})
