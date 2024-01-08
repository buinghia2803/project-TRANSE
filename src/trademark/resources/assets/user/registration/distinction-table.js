class clsDistinctTable {
    totalProduct = 0
    totalDistinct = 0
    constructor(parent) {
        this.__parent = parent
        const self = this
        this.initValidate()
        this.initVariables()
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.onChangeDistinct()
        this.totalDistinctRegis()
        this.totalProductRegis()
        this.checkDisabledMailingRegisCert()
    }

    checkDisabledMailingRegisCert() {
        if (appTrademark.is_mailing_regis_cert) {
            $('#product__mailing_regis_cert_input').prop('disabled', true)
            $('#product__mailing_regis_cert_input').addClass('disabled-checkbox')
        }
    }

    /**
     * Total distinction selected
     */
    totalDistinctRegis() {
        const total = $('.cb_distinction:checked').length;
        this.totalDistinctSelectedElement.text(total)
        this.totalDistinctionInput.val(total)
        this.totalDistinct = total
    }

    /**
     * Total product selected
     */
    totalProductRegis() {
        let total = 0;
        const distinctionsChecked  = $('.cb_distinction:checked')
        for (const item of distinctionsChecked) {
            const distinctID = $(item).data('distinct_id');
            total += productsDistinct[distinctID].length
        }

        this.totalProductSelectedElement.text(total)
        this.totalProduct = total
    }

    /**
     * Handle event change distinction
     */
    onChangeDistinct() {
        const self = this
        $('.cb_distinction').on('change', function () {
            self.totalDistinctRegis()
            self.totalProductRegis()
            if (typeof self.callbackChangeCheckbox == 'function') {
                self.callbackChangeCheckbox(self.__parent)
            }

            $('select[name=period_registration], input[name=period_registration]:checked').change()
        })
        $('.cb_distinction').change()
    }

    /**
     * initial validate before page loaded
     */
    initValidate() {}

    /**
     * Initial variable before page loaded
     */
    initVariables() {
        this.totalDistinctSelectedElement = $('.totalDistinctSelected')
        this.totalDistinctionInput = $('input[name=total_distinction]')
        this.totalProductSelectedElement = $('#totalProductSelected')
        this.totalDistinct = 0
        this.totalProduct = 0
        this.callbackChangeCheckbox = null
    }
}
