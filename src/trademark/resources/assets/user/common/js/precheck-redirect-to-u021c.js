
$('.checkAllCheckBox,.checkSingleCheckBox,.all-checkbox,.single-checkbox').on('change', function() {
    $('body').find('.notice-scrollable').remove();
})
$(document).ready(function() {
    // Redirect u021c
    $('#btn-redirect-u021c').on('click', function(e) {
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
        let route = $(this).data('route');
        let productIds = []
        dom.map((index, el) => {
            productIds.push($(el).val())
        })

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: route,
            method: 'POST',
            data_type: 'json',
            data: {
                m_product_is: productIds,
                support_first_time_id: sft_id,
                trademark_id,
            },
        }).done(function(res) {
            if(res?.status) {
                window.location.href = res.router_redirect
            }
        });
    })
});
