$('body').on('click', '.open-modal', function (e) {
    e.preventDefault();
    let resultDetail = {};
    if (dataIdentification.length > 0) {
        dataIdentification.map((element, key) => {
            element['product'].map(function (product) {
                product.code.map(function (row, key2) {
                        resultDetail[row.id +'-'+ product.id] = $('#result_similar_detail' + product.id + '-' + row.id).val();
                });
            })
        })
    }
    loadAjaxPost(openModalUrl, { id: id, precheck_id: precheckPresentId, data: resultDetail }, {
        beforeSend: function () { },
        success: function (result) {
            $('.content').addClass('loaded');
            $('.content').html(result);
            openModal('#precheck-modal');
        },
        error: function (error) { }
    }, 'loading');

})
