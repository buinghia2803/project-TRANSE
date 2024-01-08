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
        this.openDocumentType8()
    }

    openDocumentType8() {
        $('.document_type8').on('click', function () {
            const filePdftype8 = $('.click_url_type8')
            for (const object of filePdftype8) {
                object.click()
            }
        })
    }
}

new clsRegistration()
