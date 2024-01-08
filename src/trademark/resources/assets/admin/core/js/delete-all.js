$('body').on('change', '.check-one', function (e) {
    e.preventDefault();
    let checkOne = $(this).closest('table').find('.check-one');
    let checkOneChecked = $(this).closest('table').find('.check-one:checked');

    // Action for button check-all
    if (checkOne.length == checkOneChecked.length) {
        $('.check-all').prop('checked', true);
    } else {
        $('.check-all').prop('checked', false);
    }

    // Action for button submit
    if(checkOneChecked.length > 0) {
        $('#delete-all-form button[type=submit]').prop('disabled', false);

        let ids = [];
        checkOneChecked.each(function (index, item) {
            let value = $(item).val();
            ids.push(value);
        });
        ids = ids.join(',');
        $('#delete-all-form input[name=ids]').val(ids);
    } else {
        $('#delete-all-form button[type=submit]').prop('disabled', true);
        $('#delete-all-form input[name=ids]').val('');
    }
});

$('body').on('change', '.check-all', function (e) {
    e.preventDefault();
    let checkOne = $(this).closest('table').find('.check-one');
    let checkOneChecked = $(this).closest('table').find('.check-one:checked');
    if (checkOne.length == checkOneChecked.length) {
        checkOne.prop('checked', false);
    } else {
        checkOne.prop('checked', true);
    }
    $('.check-one').change();
});

$('body').on('click', '#delete-all-form button[type=submit]', function (e) {
    e.preventDefault();
    let form = $(this).closest('form');
    let confirmText = $(this).data('confirm');
    $.confirm({
        title: '',
        content: confirmText,
        buttons: {
            cancel: {
                text: Lang.close,
                action: function action() {}
            },
            confirm: {
                text: Lang.confirm,
                btnClass: 'btn-danger',
                action: function action() {
                    loadingBox('open');
                    form.submit();
                }
            }
        }
    });
});
