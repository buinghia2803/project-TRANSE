const STATUS_WAITING_FOR_USER_CONFIRM = 2;
class clsApplyTrademark {
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
        this.init()
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.formSubmit();
        this.checkConfirm();
    }

    //==================================================================
    // Form Submit
    //==================================================================
    formSubmit() {
        // Click Submit
        $('#contents').on('click', 'input[type=submit]', function (e) {
            e.preventDefault();
            let form = $('#form');
            form.find('input[name=submit_type]').val($(this).data('submit'));
            form.submit();
        });
    }

    //==================================================================
    // Check Data
    //==================================================================
    checkConfirm() {
        if (trademark.data.length == 0 && authRole == role_admin) {
            $.confirm({
                title: '',
                content: messageModal,
                buttons: {
                    ok: {
                        text: '戻る',
                        btnClass: 'btn-blue',
                        action: function () {
                            loadingBox('open');
                            window.location.href = routeTop
                        }
                    }
                }
            });
        }
    }
}
new clsApplyTrademark()
