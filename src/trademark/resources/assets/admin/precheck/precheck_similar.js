$(document).ready(function () {
    $('.open-modal').on('click', function () {
        data.forEach((element, key) => {
            element.precheckProduct.forEach((item, key1) => {
                value = $('#result_similar_detail'+item.id+'-'+item.precheck.id).val();
                if(value) {
                    element.precheckProduct[key1].result_similar_detail = value
                } else {
                    element.precheckProduct[key1].result_similar_detail = null
                }
            })
        })
    })
})
