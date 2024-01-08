class refusalResponsePlaneSupervisorSashiClass {
    constructor() {
        const self = this
        this.initValidate()

        window.addEventListener('load', function() {
            self.doLoad()
        })
    }

    doLoad() {
        this.loadData()
        this.initValidate()
        this.showMoreCode()
    }

    loadData() {
        //set route iframe a203check
        $('.src-iframe').prop('src', routeA203Check)

        //is_confirm = 1 && is_reject = 0
        if(isConfirmCurrent == isConfirmTrue && isRejectCurrent == trademarkPlanIsRejectFalse) {
            $.confirm({
                title: '',
                content: Common_E035,
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

}

var refusalResponsePlaneSupervisorSashi = new refusalResponsePlaneSupervisorSashiClass();
