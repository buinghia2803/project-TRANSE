class a205Class {
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
        this.showModalViewPage()
    }

    /**
     * Show modal where view page
     */
    showModalViewPage() {
        //doc_submissions: flag_role = 2 && is tanto
        if((flagRoleDocSubmission == flagRole2) && isRoleTanto) {
            $.confirm({
                title: '',
                content: Common_E034,
                buttons: {
                    ok: {
                        text: labelBack,
                        btnClass: 'btn-blue',
                        action: function () {
                            window.location.href = routeA000top
                        }
                    }
                }
            });
        } else if(isConfirmDocSubmission == isConfirmTrue) {
            //doc_submissions: is_confirm = 1
            $.confirm({
                title: '',
                content: Common_E035,
                buttons: {
                    ok: {
                        text: labelBack,
                        btnClass: 'btn-blue',
                        action: function () {
                            window.location.href = routeA000top
                        }
                    }
                }
            });
        }

    }
}

new a205Class()
