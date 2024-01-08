class preQuestionReplyKakunin {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
     * Do load class
     */
    doLoad() {
        this.onClickSubmitForm()
    }

    //click submit form u202kakunin
    onClickSubmitForm() {
        $('input[type=submit]').on('click', function() {
            if($(this).hasClass('saveDraftU202Kakunin')) {
                $('.from_page').val(u202KAKUNIN_DRAFT)
            } else {
                $('.from_page').val(u202KAKUNIN)
            }
        });
    }
}

new preQuestionReplyKakunin()
