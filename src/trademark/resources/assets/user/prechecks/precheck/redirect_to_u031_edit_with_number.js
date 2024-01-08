$('.checkAllCheckBox,.checkSingleCheckBox,.all-checkbox,.single-checkbox').on('change', function() {
    $('body').find('.notice-scrollable').remove();
})
// Redirect u031_edit_with_number
$('#btn-redirect-u031_edit_with_number').on('click', function() {
    if(!$('input[data-foo="is_choice_user[]"]:checked').length){
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
    $.confirm({
        title: '',
        content: answer,
        buttons: {
            cancel: {
                text: no,
                btnClass: 'btn-default',
            },
            confirm: {
                text: yes,
                btnClass: 'btn-blue',
                action: function(){
                    let dom = $('.single-checkbox')
                    let productIds = []

                    dom.map((index, el) => {
                        productIds.push({
                            'id': $(el).val(),
                            'is_choice_user': $(el).prop('checked'),
                        })
                    })

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: route,
                        method: 'POST',
                        data_type: 'json',
                        data: {
                            m_product_ids: productIds,
                            precheck_id: precheck_id,
                            trademark_id: trademark_id
                        },
                    }).done(function(res) {
                        if(res?.status) {
                            window.location.href = res.router_redirect
                        }
                    });
                }
            },
        }
    });
})
