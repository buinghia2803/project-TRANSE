var dataAjax
var routeAjax
var productIds = []

//redirect page to u031edit from u031b
$('.redirectToU031Edit').on('click', function (e) {
    $.confirm({
        title: 'お得なセットプランのご購入ができなくなります。進んでよろしいですか？',
        buttons: {
            cancel: {
                text: 'いいえ',
                btnClass: 'btn-default',
                action: function () {
                }
            },
            ok: {
                text: 'はい',
                btnClass: 'btn-blue',
                action: function () {
                    getProductChoose()
                    validateNotCheckProduct(e)
                    routeAjax = routeAjaxRedirectToU031EditFromU031b + '/'+ tradeMarkId
                    dataAjax = {
                        m_product_is: productIds,
                    }
                    ajaxFunction()
                }
            }
        }
    });
});

//redirect to u020b
$('.redirectToU020b').on('click', function() {
    getProductChoose()
    validateNotCheckProduct(e)
    routeAjax = routeAjaxRedirectToU020bEditFromU031b
    dataAjax = {
        trademark_id: tradeMarkId,
        m_product_is: productIds,
        folder_id: folderId
    }
    ajaxFunction()
});

//redirect to u031 pass
$('#redirect_to_u031pass').on('click', function () {
    openModal('#u031pass-modal');
})

function ajaxFunction() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: routeAjax,
        method: 'POST',
        data_type: 'json',
        data: dataAjax
    }).done(function(res) {
        if(res?.status) {
            window.location.href = res.router_redirect
        }
    });
}

function getProductChoose() {
    productIds = []
    $('.single-checkbox').map((index, el) => {
        if($(el).is(':checked')) {
            productIds.push($(el).val())
        }
    });
}
