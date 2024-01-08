//delete payment
$('.deletePayment').on('click', function (event) {
    event.preventDefault()
    let paymentId = $(this).data('id-payment')
    $.ajax({
        method: 'POST',
        url: routeDeletePayment,
        data_type: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            payment_id: paymentId
        },
        success: function (data) {
            if(data?.res) {
                let el = $(`.tr-id-${paymentId}`)
                $(el).hide('slow', function () {
                    el.remove()
                })

                // Showing message
                $.confirm({
                    title: '',
                    content: RETURNED_UNPROCESSED,
                    buttons: {
                        ok: {
                            text: 'OK',
                            btnClass: 'btn-blue',
                            action: function () {}
                        }
                    }
                });
            }
        },
        error: function (data) {
            // location.reload();
        }
    });
})
