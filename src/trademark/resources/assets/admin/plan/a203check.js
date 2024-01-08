class clsA203Check {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        $('#closeModal').click(function () {
            window.parent.closeModal('#open-modal-iframe')
            window.parent.$('body').removeClass('fixed-body')
        })
        $('input[type=radio]').click(function () {
            if ($(this).is(":checked")) {
                $('body').find(`.${$(this).data('white')}`).css("background-color", "#dfdfdf")
                $('body').find(`.${$(this).data('white')}-revol`).css("background-color", "#ceddeb")
                $('body').find(`.${$(this).prop("classList")[0]}`).each(function(idx, item) {
                    $(item).css("background-color", "white")
                })
            }
        })

        $('.choose_plan_detail').on('change', function () {
            const selectedInput = $(this)
            const planId = $(this).data('plan_id')
            const planDetailId = $(this).data('plan_detail_id')
            if(planId == planIdFirst) {
                const planDetailsSelected = $(`td[data-plan_detail_id=${planDetailId}][data-role_add=${ROLE_MANAGER}],td[data-plan_detail_id=${planDetailId}][data-role_add=${ROLE_SUPERVISOR}]`)
                planDetailsSelected.each(function (key, item) {
                    const planDetailsNeedUpdate = $(item).closest('tr').find(`td[data-child_plan_id][data-child_plan_id!=${planId}]`)
                    const leaveStatus = $(item).data('leave_status')
                    planDetailsNeedUpdate.each(function (_, element) {
                        const leaveStatusOtherWithEle = $(element).data('leave_status_other').find(el => el.plan_product_detail_id == planDetailId).value
                        let text = '';
                        switch (+leaveStatusOtherWithEle) {
                            case LEAVE_STATUS_4:
                                if(leaveStatus == LEAVE_STATUS_6) {
                                    text = LEAVE_STATUS_TYPES[LEAVE_STATUS_6]
                                }else if(leaveStatus == LEAVE_STATUS_7) {
                                    text = LEAVE_STATUS_TYPES[LEAVE_STATUS_7]
                                }else if(leaveStatus == LEAVE_STATUS_3) {
                                    text = LEAVE_STATUS_TYPES[LEAVE_STATUS_3]
                                }
                                break;
                            case LEAVE_STATUS_5:
                                text = LEAVE_STATUS_TYPES[leaveStatusOtherWithEle]
                                break;
                            case LEAVE_STATUS_3:
                                if (leaveStatus == LEAVE_STATUS_3) {
                                    text = LEAVE_STATUS_TYPES[LEAVE_STATUS_6] + LEAVE_STATUS_TYPES[LEAVE_STATUS_3]
                                } else {
                                    text = LEAVE_STATUS_TYPES[leaveStatus] + LEAVE_STATUS_TYPES[LEAVE_STATUS_3]
                                }
                                break;
                        }

                        $(element).text(text)
                    })
                })
            } else {
                const checkedData = $(`td[data-plan_detail_id][data-plan_detail_id=${planDetailId}]`)
                for (const item of checkedData) {
                    if ($(item).text() == LEAVE_STATUS_TYPES[LEAVE_STATUS_5]) {
                        $.confirm({
                            title: '',
                            content: messsagesErrorChooseNG,
                            buttons: {
                                ok: {
                                    text: labelClose,
                                    action: function action() {
                                        selectedInput.prop('checked', false)
                                        $('body').find(`.${selectedInput.data('white')}`).css("background-color", "#dfdfdf")
                                        $('body').find(`.${selectedInput.data('white')}-revol`).css("background-color", "#ceddeb")
                                    }
                                }
                            }
                        });
                        break
                    }
                }
            }
        })
    }
}

new clsA203Check()
