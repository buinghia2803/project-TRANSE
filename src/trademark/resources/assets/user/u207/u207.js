class clsU207 {
    constructor() {
        const self = this
        // this.initVariable()
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.init();
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.formSubmit();

        openAllFileAttach(trademarkDocument);
    }

    //==================================================================
    // Form Submit
    //==================================================================
    formSubmit() {
        $('body').on('click', 'input[type=submit]', function (e) {
            e.preventDefault();
            let form = $('#form');
            form.find('input[name=submit_type]').val($(this).data('submit'));
            form.submit();
        });

    }
}
new clsU207()
