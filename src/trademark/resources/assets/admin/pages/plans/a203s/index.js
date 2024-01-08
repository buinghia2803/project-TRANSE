class refusalResponsePlaneSupervisorClass {
    constructor() {
        const self = this
        this.initValidate()
        self.doLoad()
    }

    doLoad() {
        this.initValidate()
        this.showMoreCode()
        this.onClickSubmit()
    }

    loadData() {
        if((isConfirmCurrent == isConfirmTrue)) {
            let contentModal = ''
            if(isRejectCurrent == trademarkPlanIsRejectFalse) {
                contentModal = Common_E035
            }
            if(isRejectCurrent == trademarkPlanIsRejectTrue) {
                contentModal = Hoshin_A203_S001
            }
            $.confirm({
                title: '',
                content: contentModal,
                buttons: {
                    ok: {
                        text: labelBack,
                        btnClass: 'btn-blue',
                        action: function () {
                            window.location.href=routeA00Top
                        }
                    }
                }
            });
        }
    }

    /**
     * Validate
     */
    initValidate() {
        this.rules = {
            content: {
                maxlength: 1000
            }
        }
        this.messages = {
            content: {
                maxlength: errorMessageMaxLength1000
            }
        }
        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    showMoreCode() {
        $('.button-show-more-code').on('click', function (event) {
            event.preventDefault();
            $(this).closest('tr').find('.show-more-codes').stop().slideToggle();
            $(this).remove()
        })
    }

    onClickSubmit() {
        $('body').on('click', 'input[type=submit],button[type=submit]', function (e) {
            const form = $('#form');

            let has_error = form.find('.notice:visible,.error:visible');
            if (has_error.length == 0 && form.valid()) {
                let redirectTo = $(this).data('redirect') ?? '';
                $('input[name=redirect_to]').val(redirectTo);

                form.submit();
            } else {
                let firstError = has_error.first();
                scrollToElement(firstError, -100);
                return false;
            }
        });
    }
}

var refusalResponsePlaneSupervisor = new refusalResponsePlaneSupervisorClass();
