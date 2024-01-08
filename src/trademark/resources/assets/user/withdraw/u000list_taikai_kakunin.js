class clsWithdrawConfirm {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.sortFunction();
    }

    /**
     * When the user clicks on a sort_field element, send an ajax request to the server, and then replace
     * the contents of the tbody element with the response from the server.
     */
    sortFunction() {
        $('body').on('click', '.sort_field', function () {
            const type = $(this).data('type');
            const sortField = $(this).data('sort_field');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeSortAjax,
                method: 'GET',
                data_type: 'json',
                data: {
                    type: type,
                    sort_field: sortField
                },
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                $('#list_table tbody').html(res.html)
                loadingBox('close')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }
}
new clsWithdrawConfirm()
