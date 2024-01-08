var dataAjax
var routeAjax
var productIds = []

//redirect page to u031edit from u031b
$('.redirectToU031EditWithNumber').on('click', function (e) {
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
                    routeAjax = routeAjaxRedirectToU031EditWithNumberFromU031b
                    dataAjax = {
                        m_product_ids: productIds,
                        trademark_id: tradeMarkId
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

    if (productIds.length == 0) {
        window.location = routeAjaxRedirectToU020aFromU031d;
        return false;
    }

    validateNotCheckProduct(e)
    routeAjax = routeAjaxRedirectToU020bEditFromU031d
    dataAjax = {
        m_product_is: productIds,
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
