$('#redirect-to-u031edit').on('click', function () {
    const route = $(this).data('route');

    let dom = $('.single-checkbox:checked')
    if(!dom.length){
        if(!$('.js-scrollable').find('.notice').length) {
            $('.js-scrollable').append('<div class="notice mb15 notice-scrollable">選択してください。</div>')
        }
        e.stopPropagation();
        e.preventDefault();
        document.querySelector('.js-scrollable').scrollIntoView({
            behavior: 'smooth'
        });

        return
    }

    if($('#form .error').length) {
        document.querySelector('.error').scrollIntoView({
            behavior: 'smooth'
        });
    }

    if($('.notice, div[id*="-error"]').not('.d-none').length) {
        $('.notice, div[id*="-error"]').not('.d-none')[0].scrollIntoView({
            behavior: 'smooth'
        });
    }

    let productIds = []
    dom.map((index, el) => {
        productIds.push($(el).val())
    })

    $.confirm({
        title: '',
        content: labelModal,
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
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: route,
                        method: 'POST',
                        data_type: 'json',
                        data: {
                            m_product_is: productIds,
                        },
                    }).done(function(res) {
                        if(res?.status) {
                            window.location.href = res.router_redirect
                        }
                    });
                }
            }
        }
    });
});
