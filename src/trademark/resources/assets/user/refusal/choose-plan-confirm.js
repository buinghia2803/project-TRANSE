const DATA_CHECKED = 1
const BANK_TRANSFER = 2
const TRUE = 1;
class clsChoosePlanConfirm {
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
        this.redirectToCommonPayment()
        this.redirectToAnkenTop()
        this.redirectToQuote()
        this.reloadPage()
        this.getWorstResult()
    }

    /**
     * Fetch newest
     */
    reloadPage() {
        $('.goto_u203c').on('click', function () {
            $('#input_redirect').val(U203C)
            $('#input_redirect').attr('value', U203C)
            $('#choose_plan__confirm').submit()
        })
    }

    /**
     * Redirect to common payment
     */
    redirectToCommonPayment() {
        $('.redirect_common_payment').click(function () {
            $('#input_redirect').val(COMMON_PAYMENT)
            $('#input_redirect').attr('value', COMMON_PAYMENT)
            $('#choose_plan__confirm').submit()
        })
    }

    /**
     * Redirect to ankentop
     */
    redirectToAnkenTop() {
        $('#redirect_top').click(function () {
            $('#input_redirect').val(U000ANKEN_TOP)
            $('#input_redirect').attr('value', U000ANKEN_TOP)
            $('#choose_plan__confirm').submit()
        })
    }

    /**
     * Redirect to quote
     */
    redirectToQuote() {
        $('#redirect_quote').click(function () {
            $('#input_redirect').val(QUOTE)
            $('#input_redirect').attr('value', QUOTE)
            $('#choose_plan__confirm').submit()
        })
    }

    /**
     * Get Worst Result of plan detail was choice
     */
    getWorstResult() {
        const possibilityResolution = []
        $('td.td_revolution').each(function () {
            possibilityResolution.push($(this).data('possibility_resolution'));
        })
        const worstResult = Math.max(...possibilityResolution);
        $('#worst_result').text(revolutionTypes[worstResult])
    }
}

new clsChoosePlanConfirm()