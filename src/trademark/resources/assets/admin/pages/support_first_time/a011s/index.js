$(document).ready(function() {
    if (isConfirmTrue == isConfirmOfSFT) {
        $.confirm({
            title: messageIsConfirmA011s,
            buttons: {
                ok: {
                    text: labelAnkenTop,
                    btnClass: 'btn-blue',
                    action: function () {
                        window.location.href=routeAnkenTop
                    }
                }
            }
        });
    }
})
