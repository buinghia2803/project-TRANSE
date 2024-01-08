class CommonPayment {
    constructor() {
        const self = this
        this.initValidate()
        this.initVariable()
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }
    /**
    * Do load all action and element.
    */
    doLoad() {
        this.setInfoProdTbl()
        if(messageServer) {
            $.confirm({
                title: '',
                content: messagePaymentSuccess,
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

    /**
     * Set default product table.
     */
    setInfoProdTbl() {
        if(Object.keys(products)) {
            let count = 0;
            let totalProduct = 0;
            for (const key in products) {
                if (Object.hasOwnProperty.call(products, key)) {
                    if(products[key]) {
                        totalProduct += products[key].length
                        count++
                    }
                }
            }
            this.totalDistinction.html(count)
            this.productChecked.html(totalProduct)
        }
    }

    /**
    * Init validate
    */
    initValidate() {
        const rules = {
            'is_confirm': {
                required: true
            }
        }
        const messages = {
            is_confirm: {
                required: errorMessageRequired
            }
        }

        new clsValidation('#payment_form', { rules: rules, messages: messages })
    }

    /**
     * Init variables of class.
     */
    initVariable() {
        this.totalDistinction = $('#total_distinction')
        this.productChecked = $('#product-checked')
    }
}
new CommonPayment()
