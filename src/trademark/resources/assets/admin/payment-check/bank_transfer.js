jQuery.map(listPayment.data, function (item) {
    $('#comment_' + item.id).on('change', function () {
        if ($('#comment_' + item.id).val().length > 500) {
            $('#error_comment_' + item.id).empty().append(`<p style="color:red">${errorMessageMaxLength}</p>`)
        } else {
            $('#error_comment_' + item.id).empty()
        }
    })

    $('#click_confirm_' + item.id).on('click', function (e) {
        let form = $('#form')
        e.preventDefault();
        if (!$('#error_comment_' + item.id).text()) {
            $.confirm({
                title: '',
                content: contentConfirm,
                buttons: {
                    cancel: {
                        text: NO,
                        btnClass: 'btn-default',
                        action: function () { }
                    },
                    ok: {
                        text: YES,
                        btnClass: 'btn-blue',
                        action: function () {
                            $("input[name='id']").val(item.id)
                            $("input[name='comment']").val($('#comment_' + item.id).val())
                            $("input[name='type_submit']").val('confirm')
                            $("input[name='payment_date']").val($('#payment_date_' + item.id).val())
                            loadingBox('open');
                            form.submit();
                        }
                    }
                }
            });
        }
    })

    $('#handle_' + item.id).on('click', function (e) {
        let form = $('#form');
        e.preventDefault();
        if (!$('#error_comment_' + item.id).text()) {
            $.confirm({
                title: '',
                content: contentHandle,
                buttons: {
                    cancel: {
                        text: NO,
                        btnClass: 'btn-default',
                        action: function () { }
                    },
                    ok: {
                        text: YES,
                        btnClass: 'btn-blue',
                        action: function () {
                            $("input[name='id']").val(item.id)
                            $("input[name='type_submit']").val('handle')
                            $("input[name='comment']").val($('#comment_' + item.id).val())
                            $("input[name='payment_date']").val($('#payment_date_' + item.id).val())
                            loadingBox('open');
                            form.submit();
                        }
                    }
                }
            });
        }
    })
})

