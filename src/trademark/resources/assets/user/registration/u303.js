const BANK_TRANSFER = 2
class clsRegistration {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.openDocumentType6()
        this.openDocumentType2()
        this.openDocumentType7()
    }

    openDocumentType6() {
        $('.document_type6').on('click', function () {
            const filePdftype6 = $(this).parent().find('.click_url_type6')
            for (const object of filePdftype6) {
                object.click()
            }
        })
    }
    openDocumentType2() {
        $('.document_type2').on('click', function () {
            const filePdftype2 = $(this).parent().find('.click_url_type2')
            for (const object of filePdftype2) {
                object.click()
            }
        })
    }

    openDocumentType7() {
        $('.document_type7').on('click', function () {
            const filePdftype7 = $(this).parent().find('.click_url_type7')
            for (const object of filePdftype7) {
                object.click()
            }
        })
    }
}

new clsRegistration()
