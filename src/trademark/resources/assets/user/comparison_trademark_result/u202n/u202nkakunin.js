class preQuestionReReplyKakunin {
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
        this.clickFilePDF()
    }

    //click submit form u202kakunin
    onClickSubmitForm() {
        $('input[type=submit]').on('click', function() {
            if($(this).hasClass('saveDraftU202N_Kakunin')) {
                $('.from_page').val(u202N_KAKUNIN_DRAFT)
            } else if($(this).hasClass('saveU202N_Kakunin')) {
                $('.from_page').val(u202N_KAKUNIN)
            }
        });
    }

    //click open pdf
    clickFilePDF() {
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    }
}

new preQuestionReReplyKakunin()
