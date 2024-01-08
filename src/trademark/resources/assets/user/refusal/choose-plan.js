const DATA_CHECKED = 1
const BANK_TRANSFER = 2
const TRUE = 1;
const IS_REGISTER = 1;
class clsChoosePlan {
    planDetails= [];
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
        this.initValidation();
    }

    //==================================================================
    // Init Validation Form
    //==================================================================
    initValidation() {
        plans.map((plan) => {
            localRules[`is_choice[${plan.id}]`] = {
                required: true,
            }
            localMessages[`is_choice[${plan.id}]`] = {
                required: messageError,
            }
        })
        if (typeof flag == 'undefined' && +$('#total').text().replaceAll(',', '') > 0) {
            this.rules = { ...paymentRule, ...localRules }
            this.messages = { ...paymentMessage, ...localMessages }
        } else {
            this.rules = { ...localRules }
            this.messages = { ...localMessages }
        }

        new clsValidation('#form', { rules: this.rules, messages: this.messages }, true);
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.hide();
        this.changePaymentTye();
        this.formSubmit();
        this.getDataDefault();
        this.clearChecked();
        this.checkLeaveStatus();
        this.hiddenActualFeeAndTax();
        this.submitDraft();
        this.checkCondition();
        this.regex();
        this.redirectToU201BCancel()
        // this.ajaxGetInfoPayment();
        openAllFileAttach(trademarkDocument);
        this.changeIsChoice();
        this.getWorstResult();
    }
    //==================================================================
    // Regex remove ,
    //==================================================================
    regex() {
        let str = $('#product_regex').text().trim();
        let regex = /,$/;
        let result = str.replace(regex, "");
        $('#product_regex').text(result)
    }

    //==================================================================
    // Check Condition Screen
    //==================================================================
    checkCondition() {
        if (trademarkPlan.is_register == IS_REGISTER) {
            const form = $('#form').not('#form-logout');
            form.find('input, textarea, select , button ,a').not('.not-disabled').addClass('disabled');
            form.find('input, textarea, select , button ,a').not('.not-disabled').css('pointer-events', 'none');
            $('#btn_content').removeClass('disabled');
            $('#btn_content').css('pointer-events', '');
        }
    }

    //==================================================================
    // Handling change event of nation input.
    //==================================================================
    hiddenActualFeeAndTax() {
        $('#m_nation_id').on('change', function () {
            let valueNation = $('#m_nation_id').find("option:selected").val();
            if (valueNation != 1) {
                $('#hidden_commission_tax').hide();
            } else {
                $('#hidden_commission_tax').show();
            }
        })
    }

    //==================================================================
    // Form Submit
    //==================================================================
    formSubmit() {
        $('#contents').on('click', 'input[type=submit]', function (e) {
            e.preventDefault();
            let form = $('#form');
            form.find('input[name=submit_type]').val($(this).data('submit'));
            form.submit();
        });

    }

    //==================================================================
    // Form Submit Draft
    //==================================================================
    submitDraft() {
        $('#save_draft').on('click', function (e) {
            e.preventDefault();
            let form = $('#form');
            form.find('input[name=submit_type]').val($(this).data('submit'));
            form.unbind("submit").submit();
            form.submit();
        })
    }

    redirectToU201BCancel() {
        $('.redirectToU201bCancel').on('click', function (e) {
            e.preventDefault();
            window.location.href = routeU201bCancel;
        })
    }

    //==================================================================
    // Clear Input Checked
    //==================================================================
    clearChecked() {
        let seft = this;
        $('#clear_checked').on('click', function () {
            $('table.planCorrespondenceTbl').find('input[type=radio]:checked').prop('checked', false)
            $('table.planCorrespondenceTblProduct').find('input[type=radio]:checked').prop('checked', false)
            dataCost.number_distinct = 0;
            seft.ajaxGetInfoPayment();
            // seft.changeIsChoice();
        })
    }

    //==================================================================
    // Hidden Default
    //==================================================================
    hide() {
        $('#hidden_cost_bank_transfer').hide()
        $('table.planCorrespondenceTbl:first').find('th.hidden_m_distinct,td.hidden_m_distinct').each(function (key, item) {
            $(item).removeClass('d-none')
        })
        let valueNation = $('#m_nation_id').find("option:selected").val();
        if (valueNation != 1) {
            $('#hidden_commission_tax').hide();
        } else {
            $('#hidden_commission_tax').show();
        }
        if (+$('#total').text().replaceAll(',', '') == 0) {
            $('.hidden_common').hide();
        } else {
            $('.hidden_common').show();
        }
    }

    //==================================================================
    // Event Change Payment Type
    //==================================================================
    changePaymentTye() {
        let seft = this;
        $('input[name=payment_type]').on('change', function () {
            if ($(this).val() == BANK_TRANSFER) {
                $('#hidden_cost_bank_transfer').show()
            } else {
                $('#hidden_cost_bank_transfer').hide()
            }

            seft.ajaxGetInfoPayment();
        })
    }

    //==================================================================
    // Check Leave Status
    //==================================================================
    checkLeaveStatus() {
        const planDetailChecked = [];
        var planDetailIds = [];
        plans.map((plan) => {
            plan.plan_details.map((planDetail) => {
                planDetail.plan_detail_products.map((planDetailProduct) => {
                    let planDetailId = planDetail.id
                    planDetailIds.push(planDetailId)
                    const leaveStatus = planDetailProduct.leave_status
                    const object = {}
                    object[planDetailId] = leaveStatus
                    planDetailChecked.push(object)
                })

            })

        })
        const uniquePlanDetailIds = planDetailIds.filter((x, i, a) => a.indexOf(x) == i)
        uniquePlanDetailIds.map((planDetailId) => {
            const numberDistinct = $(`.number_distinct_${planDetailId}`).text()
            if (numberDistinct == 0) {
                $(`.m_distinct_name_${planDetailId}`).text('なし')
            }
        })

        $('table.planCorrespondenceTbl').find('input[type=radio]').on('change', function () {
            const seft = this;
            planDetailChecked.forEach(function (item) {
                Object.keys(item).forEach(function (key) {
                    if ($(seft).val() == key && item[key] == 5) {
                        $.confirm({
                            title: '',
                            content: messagePopup,
                            buttons: {
                                ok: {
                                    text: 'OK',
                                    btnClass: 'btn-blue',
                                    action: function () {
                                        $(seft).prop('checked', '')
                                    }
                                }
                            }
                        });
                    }
                });
            });
        })
    }

    // ==================================================================
    // Event Change Register Before Deadline
    // ==================================================================
    changeIsChoice() {
        const self = this;
        $('body').on('change', 'input.radio_is_choice', function () {
            if(!$(this).closest('table.planCorrespondenceTbl').data('key')){
                const numberDistinct = $(this).closest("tr").find(".number_distinct_" + $(this).val())[0].outerText;
                if ($.isNumeric(numberDistinct)) {
                    dataCost.number_distinct = numberDistinct;
                } else {
                    dataCost.number_distinct = 0;
                }
                dataCost.plan_detail_id = $(this).val()
                self.ajaxGetInfoPayment();
            }
            self.getWorstResult();
        })
        $('input.radio_is_choice:checked').change();

        $('body').on('change', 'input.choose_plan_detail', function () {
            let distincionAddOn  = $(this).data('distincion_add_on');
            let planDetailID = $(this).data('plan_detail_id');

            if (distincionAddOn != undefined) {
                dataCost.number_distinct = distincionAddOn;
                dataCost.plan_detail_id = planDetailID;
                self.ajaxGetInfoPayment();
            }
        })
        $('input.choose_plan_detail:checked').change();
    }

    /**
     * Get Worst Result of plan detail was choice
     */
    getWorstResult() {
        const possibilityResolution = []
        $('input.radio_is_choice:checked').each(function () {
            possibilityResolution.push($(this).data('possibility_resolution'));
        })
        const worstResult = Math.max(...possibilityResolution);
        $('#worst_result').text(revolutionTypes[worstResult])
    }

    getDataDefault() {
        plans.forEach(element => {
            this.planDetails = [...this.planDetails, ...element.plan_details]
        });

        let seft = this;
        if (typeof flag != 'undefined' && flag == 'u203c') {
            // this.changeIsChoice();
        } else {
            this.changeIsChoice();
        }
        if ($('table.planCorrespondenceTbl:first').find('input[name=plan_detail_id]').is(':checked')) {
            seft.changeIsChoice();
        }
    }
    //==================================================================
    // Get Data To Form
    //==================================================================
    getData(dataCost) {
        let data = {
            cost_additional: dataCost.cost_additional,
            period_registration: dataCost.period_registration,
            number_distinct: dataCost.number_distinct,
            plan_detail_id: dataCost.plan_detail_id,
            cost_bank_transfer: $('input[name=payment_type]:checked').val() == BANK_TRANSFER ? dataCost.cost_bank_transfer : 0,
        }

        return data
    }

    //==================================================================
    // Call Ajax Get Data Info Payment
    //==================================================================
    ajaxGetInfoPayment() {
        let data = this.getData(dataCost);
        let seft = this;
        loadAjaxPost(routeAjax, data, {
            async: false,
            beforeSend: function(){},
            success:function(res){
                if (res) {
                    if (res.total == 0) {
                        $('.hidden_common').hide();
                    } else {
                        $('.hidden_common').show();
                    }
                    seft.showInfo(res);
                }
            },
            error: function (error) {}
        }, 'loading');
    }

    //==================================================================
    // Show Data Cart
    //==================================================================
    showInfo(res) {
        $('.cost_additional').text(this.numberFormat(res.pof_distinction))
        $('.number_distinct_add').text(this.numberFormat(res.number_distinct))
        $('.number_plan_detail_prods').text(this.numberFormat(res.number_plan_detail_product))
        $('.cost_add_prod_name').text(this.numberFormat(res.cost_add_prod_name))
        $('#prod_add').text(this.numberFormat(res.prod_add))
        $('#total').text(this.numberFormat(res.total))
        $('#sub_total').text(this.numberFormat(res.sub_total))
        $('#cost_bank_transfer').text(this.numberFormat(res.cost_bank_transfer))
        $('#commission').text(this.numberFormat(res.commission))
        $('#tax').text(this.numberFormat(res.tax))
        $('#percent_tax').text(res.percent_tax)
        $('#patent_cost').text(this.numberFormat(res.patent_cost))
        $('#cost_prod_add').text(this.numberFormat(res.cost_prod_add))
        $('#total_amount').text(this.numberFormat(res.total_amount))
        // Value Input Hidden
        $('#total_input').val(res.total);
        $('#cost_bank_transfer-input').val(res.cost_bank_transfer);
        $('#subtotal-input').val(res.sub_total);
        $('#commission-input').val(res.commission);
        $('#tax-input').val(res.tax);
        $('#cost_one_distintion-input').val(res.patent_cost);
        $('#total_amount-input').val(res.total_amount);
        $('#cost_prod_add-input').val(res.cost_prod_add);
    }

    //==================================================================
    // Format Number
    //==================================================================
    numberFormat(val) {
        return new Intl.NumberFormat('en-us').format(Math.floor(val))
    }
}
new clsChoosePlan()
