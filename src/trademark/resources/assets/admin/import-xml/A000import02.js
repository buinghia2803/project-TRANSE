class A000import02 {
    constructor() {
        const self = this
        this.notConfirmNull = false;
        this.notConfirmClose = false;
        this.notConfirmDuplicate = false;
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
    * Do load all action and element.
    */
    doLoad() {
        this.preSubmit()
    }

    /**
    * Pre submit
    */
    preSubmit() {
        const self = this
        $('.btn_submit').on('click', function (e) {
            let messagesError = ''

            if ($('input[name=is_confirm_null]:unchecked').length > 0) {
                self.notConfirmNull = true
                self.notConfirmClose = false
                self.notConfirmDuplicate = false
                messagesError = errorMsgNotConfirmNullData
            } else if ($('input[name=is_confirm_close]:unchecked').length > 0  && $('#trademarkCloseTbl body tr').length >= 0) {
                self.notConfirmClose = true
                self.notConfirmNull = false
                self.notConfirmDuplicate = false
                messagesError = errorMsgNotConfirmCloseData
            } else if ($('input[name=is_confirm_duplicate]:unchecked').length > 0 &&  $('#trademarkDuplicateTbl body tr').length >= 0) {
                self.notConfirmDuplicate = true
                self.notConfirmNull = false
                self.notConfirmClose = false
                messagesError = errorMsgNotConfirmDuplicateData
            } else {
                self.notConfirmDuplicate = false
                self.notConfirmNull = false
                self.notConfirmClose = false
            }

            if(self.notConfirmNull || self.notConfirmClose || self.notConfirmDuplicate) {
                $.confirm({
                    title: '',
                    content: messagesError,
                    buttons: {
                        ok: {
                            text: YES,
                            btnClass: 'btn-blue',
                            action: function () {}
                        }
                    }
                });
                e.preventDefault()
            } else {
                $('#form').submit()
            }
        })
    }
}

new A000import02()