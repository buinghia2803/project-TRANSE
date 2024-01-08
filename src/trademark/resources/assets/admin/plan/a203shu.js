class clsEditPlanSupervisor {
    selectReasons = {};
    newReason = {}

    newDistrict = {}
    countPlan = $('body').find('.parent_table_plan').length;

    constructor() {
        const self = this
        self.initVariable()
        self.doLoad()
    }

    doLoad() {
        this.init()
        if(self.countPlan >= 5) {
            $('body').find('.add_reciprocal_countermeasures').css('display', 'none')
        }

        for (const indexPlan in plans) {
            if(indexPlan != 0) {
                $('.plan_reasons_' + indexPlan).multipleSelect({
                    selectAllText: checkAll,
                    allSelected: checkAll,
                    selectAll: false
                });
            } else {
                $('.plan_reasons_' + indexPlan).multipleSelect({
                    selectAllText: checkAll,
                    allSelected: checkAll,
                });
            }
            //show checked and checkall
            let countChecked = $('.parent_plan_'+indexPlan).find('.check_disabled:checked')
            if(countChecked.length === plans[indexPlan].plan_details.length) {
                $('.parent_plan_'+indexPlan).find('.check_disalbed_all').prop('checked', true)
            }
            $.each(plans[indexPlan].plan_details, function (indexPlanDetail, itemPlanDetail) {
                if (itemPlanDetail.distincts_is_add.length > 0 && itemPlanDetail.isDistinctSettmentEdit.length > 0) {
                    let itemDistinctSettementIds = []
                    if(!Array.isArray(self.newDistrict[indexPlan])) {
                        self.newDistrict[indexPlan] = [];
                    }
                    if (!Array.isArray(self.newDistrict[indexPlan][indexPlanDetail])) {
                        self.newDistrict[indexPlan][indexPlanDetail] = []
                    }
                    $.each(itemPlanDetail.isDistinctSettmentEdit, function (index, item) {
                        self.newDistrict[indexPlan][indexPlanDetail].push(item.id)
                    })
                }
                $('.is_decision_'+indexPlan+'_'+indexPlanDetail).val(itemPlanDetail.is_decision)
            })
        }
        $('input[name="is_distinct_settlement_edit[]"]').val(JSON.stringify(self.newDistrict))

        $.each($('.parent_plan'), function (index, itemPlan) {
            let planDetail = $(itemPlan).find('.row_plan_detail')
            let checkAllDisabled =  $(itemPlan).find('.check_disalbed_all')
            if(checkAllDisabled[0].checked === true) {
                $(itemPlan).find('.copy_all_value_to_edit_col, .copy_all_value_to_confirm_col, .copy_all_value_edit_to_confirm_col').addClass('disabled')
                $(itemPlan).find('.copy_all_value_to_edit_col, .copy_all_value_to_confirm_col, .copy_all_value_edit_to_confirm_col').attr('disabled', true)
            } else {
                $(itemPlan).find('.copy_all_value_to_edit_col, .copy_all_value_to_confirm_col, .copy_all_value_edit_to_confirm_col').removeClass('disabled')
                $(itemPlan).find('.copy_all_value_to_edit_col, .copy_all_value_to_confirm_col, .copy_all_value_edit_to_confirm_col').attr('disabled', false)
            }
            $.each(planDetail, function (indexPlanDetail, item) {
                let checkDisabled = $(item).find('.check_disabled')
                if(checkDisabled[0].checked) {
                    $(item).find('.copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .type_plan_description, .possibility_resolution_edit, .plan_detail_distinct, .copy_value_edit_to_confirm_col, .is_leave_all_edit, .plan_detail_distinct button, .delete_file_info_description, .add_file_info, .file_info_description, .delete_plan_detail_backend, div.is_distinct_settlement_edit_'+index+'_'+indexPlanDetail+' button').addClass('disabled')
                    $(item).find('.copy_value_to_edit_col, .copy_value_to_confirm_col, .copy_value_edit_to_confirm_col, .type_plan_name, .is_leave_all_edit, .possibility_resolution_edit option:not(:selected), .type_plan_doc, .delete_file_info_description, .delete_plan_backend, .delete_plan_detail_backend, .add_file_info, .delete_plan_detail_backend, div.is_distinct_settlement_edit_'+index+'_'+indexPlanDetail+' button').attr('disabled', true)
                    $(item).find('.type_plan_name, .type_plan_description, .possibility_resolution_edit, .plan_detail_distinct, .add_file_info, .file_info_description').prop('readonly', true)
                    $(item).find('.check_disabled').prop('checked', true)
                } else {
                    $(item).find('.copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .type_plan_description, .possibility_resolution_edit, .plan_detail_distinct, .copy_value_edit_to_confirm_col, .is_leave_all_edit, .plan_detail_distinct button, .delete_file_info_description, .add_file_info, .file_info_description, .delete_plan_detail_backend, div.is_distinct_settlement_edit_'+index+'_'+indexPlanDetail+' button').removeClass('disabled')
                    $(item).find('.copy_value_to_edit_col, .copy_value_to_confirm_col, .copy_value_edit_to_confirm_col, .type_plan_name, .is_leave_all_edit, .possibility_resolution_edit option:not(:selected), .type_plan_doc, .delete_file_info_description, .delete_plan_backend, .delete_plan_detail_backend, .add_file_info, .delete_plan_detail_backend, div.is_distinct_settlement_edit_'+index+'_'+indexPlanDetail+' button').attr('disabled', false)
                    $(item).find('.type_plan_name, .type_plan_description, .possibility_resolution_edit, .plan_detail_distinct, .add_file_info').prop('readonly', false)
                    $(item).find('.check_disabled').prop('checked', false)
                }
            })
            self.newReason[index] = $(this).find('.plan_reasons').val();
        })
        self.onchangeSelectItem()

        $('input[name="plan_reason[]"]').val(JSON.stringify(self.newReason))
    }

    // initial when load
    init() {
        const self = this
        self.selectReason()
        self.selectAllReason()
        self.selectDistrict()
        self.selectAllDistrict()
        self.funcAddReciprocalCountermeasures()
        self.deletePlan()
        self.deletePlanBackend()
        self.addPlanDetail()
        self.deletePlanDetail()
        self.deletePlanDetailBackend()
        self.addFileInfo()
        self.removeFileInfoDescription()
        self.changeTypePlanName()
        self.validateTypePlanName()
        self.changeTypePlanDoc()
        self.checkedIsleaveAll()
        self.deleteTypePlanDescription()
        self.createPlanReason()
        self.validateTypePlanDescription()
        self.validateFilePlanDocDescription()
        // self.validateReasonPlanDetail()
        self.validatePlanComment()
        self.copyValueToConfirmCol()
        self.copyValueToEditCol()
        self.copyAllValueToConfirmCol()
        self.copyAllValueToEditCol()
        self.copyValueEditToConfirmCol()
        self.copyAllValueEditToConfirmCol()
        self.checkDisabled()
        self.checkAllDisabled()
        // self.validateCheckDisabled()
        self.onSubmit()
    }


    onchangeSelectItem() {
        let reasonIds = []
        let newReason = {};

        $.each($('.parent_plan'), function (index, item) {
            let selectItemChecked = $(this).find('input[name=selectItem]:checked');

            $.each(selectItemChecked, function () {
                let value = parseInt($(this).val());
                reasonIds.push(value);
            })
        })
        $.each($('.parent_plan'), function (index, item) {
            let selectItem = $(this).find('input[name=selectItem]');
            let selectItemChecked = $(this).find('input[name=selectItem]:checked');
            if (index != 0) {
                if (selectItemChecked.length > 0) {
                    $.each(selectItem, function () {
                        if (!$(this).prop('checked')) {
                            $(this).prop('disabled', true);
                            $(this).closest('label').addClass('disabled');
                        } else {
                            $(this).prop('disabled', false);
                            $(this).closest('label').removeClass('disabled');
                        }
                    })
                } else {
                    $.each(selectItem, function () {
                        let selectValue = parseInt($(this).val());
                        $(this).prop('disabled', false);
                        $(this).closest('label').removeClass('disabled');
                    })
                }
            }
        });
    }

    selectReason() {
        const self = this
        let newReason = {}
        if (plans.length > 0) {
            $.each(plans, function (indexPlan, itemPlan) {
                if (!Array.isArray(self.selectReasons[indexPlan])) {
                    self.selectReasons[indexPlan] = []
                }
                if (!Array.isArray(self.newReason[indexPlan])) {
                    self.newReason[indexPlan] = []
                }
                $.each(itemPlan.reason, function (indexReason, reason) {
                    self.selectReasons[indexPlan].push(reason.id)
                    self.newReason[indexPlan].push(reason.id)
                })
            })
        }
        $('body').on('click', 'input[name="selectItem"]', function () {
            self.onchangeSelectItem();

            const selfSelectItem = this
            let keyPlanPresent = $(this).closest('.parent_plan').data('key-plan')
            if (!Array.isArray(self.selectReasons[keyPlanPresent])) {
                self.selectReasons[keyPlanPresent] = []
            }
            if (!Array.isArray(self.newReason[keyPlanPresent])) {
                self.newReason[keyPlanPresent] = []
            }
            const index = $(this).closest('p.parent_reasons').find('select.multi').data('index')
            if ($(this)[0].checked === true) {
                self.newReason[keyPlanPresent].push($(this).val())
                self.selectReasons[keyPlanPresent].push($(this).val())
            } else {
                self.selectReasons[keyPlanPresent].pop()
                $.each(self.newReason[keyPlanPresent], function (indexDistinct, item) {
                    if(+$(selfSelectItem).val() === +item) {
                        self.newReason[keyPlanPresent].splice(indexDistinct, 1)
                    }
                })

            }
            $.each(self.newReason, function (index, item) {
                if(item.length > 0) {
                    $(selfSelectItem).closest('.parent_plan_'+index).find('.error').remove()
                }
            })
            $('input[name="plan_reason[]"]').val(JSON.stringify(self.newReason))
        })
    }

    selectAllReason() {
        const self = this
        let newReason = {}
        if (plans.length > 0) {
            $.each(plans, function (indexPlan, itemPlan) {
                if (!Array.isArray(self.selectReasons[indexPlan])) {
                    self.selectReasons[indexPlan] = []
                }
                if (!Array.isArray(self.newReason[indexPlan])) {
                    self.newReason[indexPlan] = []
                }
                $.each(itemPlan.reason, function (indexReason, reason) {
                    self.selectReasons[indexPlan].push(reason.id)
                    self.newReason[indexPlan].push(reason.id)
                })
            })
        }
        $('body').on('click', 'input[name="selectAll"]', function (e) {
            self.onchangeSelectItem();
            const selfSelectItem = this
            $(this).closest('ul').find('input[name=selectItem]:disabled').prop('checked', false);

            let tagReason = $(this).closest('.plan_reasons').find('input[name="selectItem"]')
            $(this).closest('ul').find('input[name=selectItem]:disabled').prop('checked', false);
            let keyPlanPresent = $(this).closest('.parent_plan').data('key-plan')
            let keyPlan = $(this).closest('#form').find('.parent_plan')

            if (!Array.isArray(self.selectReasons[keyPlanPresent])) {
                self.selectReasons[keyPlanPresent] = []
            }
            self.newReason[keyPlanPresent] = []

            if($(this)[0].checked === true) {
                $.each(tagReason, function (index, item) {
                    if ($(item)[0].checked) {
                        self.newReason[keyPlanPresent].push($(item).val())
                    }
                })
            } else {
                self.newReason[keyPlanPresent] = []
                self.selectReasons[keyPlanPresent] = []
            }
            $.each(self.newReason, function (index, item) {
                if(item.length > 0) {
                    $(selfSelectItem).closest('.parent_plan_'+index).find('.error').remove()
                }
            })
            $('input[name="plan_reason[]"]').val(JSON.stringify(self.newReason))
        })
    }

    selectDistrict() {
        const self = this
        let newDistrict = {}
        $('body').on('change', 'input[name="selectItemDistinct"]', function () {
            const selfSelectItemDistinct = this
            self.countPlan = $('body').find('.parent_table_plan').length
            const keyPlan = $(this).closest('.parent_is_distinct_settlement_edit').find('select.multi').data('key-plan')
            const keyPlanDetail = $(this).closest('.parent_is_distinct_settlement_edit').find('select.multi').data('key-plan-detail')
            if(!Array.isArray(self.newDistrict[keyPlan])) {
                self.newDistrict[keyPlan] = [];
            }
            if (!Array.isArray(self.newDistrict[keyPlan][keyPlanDetail])) {
                self.newDistrict[keyPlan][keyPlanDetail] = []
            }
            if ($(this)[0].checked) {
                self.newDistrict[keyPlan][keyPlanDetail].push($(this).val())
            } else {
                $.each(self.newDistrict[keyPlan][keyPlanDetail], function (indexDistinct, item) {
                    if(+$(selfSelectItemDistinct).val() === +item) {
                        self.newDistrict[keyPlan][keyPlanDetail].splice(indexDistinct, 1)
                    }
                })
            }
            $('input[name="is_distinct_settlement_edit[]"]').val(JSON.stringify(self.newDistrict))
        })
    }

    selectAllDistrict() {
        const self = this
        let newDistrict = {}
        $('body').on('click', 'input[name="selectAllDistinct"]', function () {
            const selfSelectAllItemDistinct = this
            self.countPlan = $('body').find('.parent_table_plan').length
            const keyPlan = $(this).closest('.parent_is_distinct_settlement_edit').find('select.multi').data('key-plan')
            const keyPlanDetail = $(this).closest('.parent_is_distinct_settlement_edit').find('select.multi').data('key-plan-detail')
            let tagDistrict = $(this).closest('.multi').find('input[name="selectItemDistinct"]')
            if(!Array.isArray(self.newDistrict[keyPlan])) {
                self.newDistrict[keyPlan] = [];
            }
            if (!Array.isArray(self.newDistrict[keyPlan][keyPlanDetail])) {
                self.newDistrict[keyPlan][keyPlanDetail] = []
            }
            self.newDistrict[keyPlan][keyPlanDetail] = []
            if($(this)[0].checked === true) {
                $.each(tagDistrict, function (index, item) {
                    if ($(item)[0].checked) {
                        self.newDistrict[keyPlan][keyPlanDetail].push($(item).val())
                    }
                })
            } else {
                self.newDistrict[keyPlan][keyPlanDetail] = []
            }
            $('input[name="is_distinct_settlement_edit[]"]').val(JSON.stringify(self.newDistrict))

        })
    }

    changeTypePlanName() {
        $('body').on('change', '.type_plan_name', function () {
            let self = this
            let typePlanId = $(self).val()
            let dataId = $(this).data('id')
            let dataKey = $(this).data('key')
            let dataKeyDetail = $(this).data('key-detail')
            let dataTypePlanIdEdit = $(this).closest('.row_plan_detail').find('.type_plan_id_edit').val(typePlanId)
            $(self).closest('.row_plan_detail').find('.info_file').html(' ')
            $(self).closest('.info_type_plan').find('.type_plan_description').html(' ')
            $(self).closest('.info_type_plan').find('.type_plan_description').val(' ')
            $.each(mTypePlans, function (index, item) {
                if (+typePlanId === item.id) {
                    $(self).closest('.info_type_plan').find('.type_plan_description').html(item.description)
                    $(self).closest('.info_type_plan').find('.type_plan_description').val(item.description)
                    $(self).closest('.info_type_plan').find('.type_plan_content').html(item.content)
                    $(self).closest('.info_type_plan').find('.type_plan_content').val(item.content)
                }
            })

            $.each(mTypePlanDocs, function (index, item) {
                if (+typePlanId === item.m_type_plan_id) {
                    if (+typePlanId !== 8) {
                        $(self).closest('.row_plan_detail').find('.info_file').append(`
                         <div class="infor-file-item">
                            <input type="hidden" name="plan_detail_doc_id[${dataKey}][${dataKeyDetail}][]" value="0">
                            <span class="file_info_name white-space-pre-line">${item.name}</span>
                            <input type="hidden" name="type_plan_doc_id_edit[${dataKey}][${dataKeyDetail}][]" value="${item.id}">
                            <textarea class="wide file_info_description doc_requirement_des_edit" name="doc_requirement_des_edit[${dataKey}][${dataKeyDetail}][]" style="width: 500px;">${item.description}</textarea>
                            <input type="button" value="${delete2}" class="btn_a small delete_file_info_description"><br/>
                         </div>
                        `)
                        $(self).closest('.row_plan_detail').find('.info_file').find('.add_file_info').remove()
                    }
                }
            })
            if (+typePlanId === 8) {
                $(self).closest('.row_plan_detail').find('.info_file').append(`
                          <div class="infor-file-item">
                            <input type="hidden" name="plan_detail_doc_id[${dataKey}][${dataKeyDetail}][]"  value="0">
                            <select class="mb10 type_plan_doc" name="type_plan_doc_id_edit[${dataKey}][${dataKeyDetail}][]" data-key="${dataKey}" data-key-detail="${dataKeyDetail}">
                            </select>
                            <textarea class="wide file_info_description doc_requirement_des_edit" name="doc_requirement_des_edit[${dataKey}][${dataKeyDetail}][]" style="500px"></textarea>
                                                        <input type="button" value="${delete2}" class="btn_a small delete_file_info_description">
                            <br/>
                          </div>
                         <a href="javascript:;" class="add_file_info" data-key="${dataKey}" data-key-detail="${dataKeyDetail}">+ ${add4}</a>
                        `)
            }

            $.each(mTypePlanDocs, function (index, item) {
                //value 8: その他自由記述
                if (item.m_type_plan_id === 8) {
                    $(self).closest('.row_plan_detail').find('.info_file').find('.type_plan_doc').append(`
                            <option value="${item.id}">${item.name}</option>
                        `)
                }
            })
        })
    }

    changeTypePlanDoc() {
        const self = this
        $('body').on('change', '.type_plan_doc', function () {
            const selfTypePlanDoc = this
            let valueClick = $(this).val()
            $(selfTypePlanDoc).closest('.infor-file-item').find('.type_plan_doc_id_edit_value').val(valueClick)
            //value 11: 不要
            if (+valueClick === 11) {
                $(selfTypePlanDoc).closest('.infor-file-item').find('.file_info_description').attr('readonly', true)
                $(selfTypePlanDoc).closest('.infor-file-item').find('.file_info_description').addClass('disabled')
            } else {
                $(selfTypePlanDoc).closest('.infor-file-item').find('.file_info_description').attr('readonly', false)
                $(selfTypePlanDoc).closest('.infor-file-item').find('.file_info_description').removeClass('disabled')
            }
            $.each(mTypePlanDocs, function (index, item) {
                $(selfTypePlanDoc).closest('.infor-file-item').find('.file_info_description').html(' ')
                $(selfTypePlanDoc).closest('.infor-file-item').find('.file_info_description').val()
                if (+valueClick === item.id) {
                    $(selfTypePlanDoc).closest('.infor-file-item').find('.file_info_description').html(item.description)
                    $(selfTypePlanDoc).closest('.infor-file-item').find('.file_info_description').val(item.description)
                }
            })
        })
    }


    deleteTypePlanDescription() {
        $('body').on('click', '.delete_type_plan_description', function () {
            $(this).closest('.info_type_plan').find('.type_plan_description').html(' ')
            $(this).closest('.info_type_plan').find('.type_plan_description').val('')
            $(this).closest('.info_type_plan').find('.type_plan_content').html(' ')
            $(this).closest('.info_type_plan').find('.type_plan_content').val('')
        })
    }

    AppendOption(options, selectClass, count) {
        const self = this
        self.countPlan = $('body').find('.parent_table_plan').length;

        $(selectClass + '_' + self.countPlan + '_' + count).html('')
        for (let i = 0; i < options.length; i++) {
            if (selectClass === '.plan_reasons') {
                $(selectClass + '_' + count).append(`
                    <option value="${options[i].id}" class="reason reason_${i}">${options[i].reason_name}</option>
                `)
                $.each(self.newReason, function (indexSelectReason, itemSelectReason) {
                    let checkExist = jQuery.inArray(String(options[i].id), itemSelectReason) >= 0 ? true : false;
                })

                $.each(self.newReason, function (indexSelectReason, itemSelectReason) {
                    if (jQuery.inArray(String(options[i].id), itemSelectReason) === itemSelectReason.length - 1) {
                        $($('input[name="selectAll"]')[self.countPlan]).prop('disabled', true)
                    }
                })
            } else if (selectClass === '.type_plan_name') {
                $(selectClass + '_' + (self.countPlan - 1) + '_' + (count)).append(`
                    <option value="${options[i].id}">${options[i].name}</option>
                `)
            }
        }
    }

    getOptionHTML(options) {
        let optionHTML = '';

        for (let i = 0; i < options.length; i++) {
            optionHTML += `<option value="${options[i].id}">${options[i].name}</option>`;
        }

        return optionHTML;
    }

    addPlanDetail() {
        self = this
        $('body').on('click', '.add_plan_detail', function () {
            self.countPlan = $('body').find('.parent_table_plan').length;
            let count = $(this).closest('.parent_table_plan').find('.row_plan_detail').last().data('index');
            count = count + 1;
            let keyPlan = $(this).closest('.parent_table_plan').data('key-plan')
            let dataCount = $(this).data('count')
            let html = `
                    <tr class="row_plan_detail row_plan_detail_${count}" data-index="${count}">
                        <th>
                        ${draftPolicy}<br>(${count + 1})<br/>
                        <input type="hidden" name="plan_detail_id[${keyPlan}][${count}]" value="0" />
                        <input type="button" value="削除" class="small btn_d delete_plan_detail" data-delete-id="${count}">
                        </th>
                        <td class="bg_sky"><br><br>
                        </td>
                        <td class="bg_sky">

                        </td>
                        <td class="center bg_sky"></td>`;

            if(dataCount === 0) {
                html +=  `<td class="center bg_sky">
                                </td>
                                <td class="center bg_sky" style="">
                                    <select multiple="multiple" class="multi" style="width: 8em; display: none;"></select>
                                </td>`
            }

            html += `</td>
                        <td class="center bg_sky"></td>
                        <td class="center bg_sky">
                        </td>
                        <td class="info_type_plan">
                            <select class="mb10 type_plan_name type_plan_id_edit type_plan_name_${self.countPlan - 1}_${count}"
                              data-key="${keyPlan}" data-key-detail="${count}"
                                style="width: 350px;">
                                  <option value="0">${defaultSelect}</option>
                                  ${self.getOptionHTML(mTypePlans)}
                            </select>
                            <div class="parent_type_plan_description">
                                 <textarea class="wide plan_description_edit type_plan_description" name="plan_description_edit[${keyPlan}][${count}]" style=""></textarea><br>
                                 <textarea hidden class="wide plan_content_edit type_plan_description" name="plan_content_edit[${keyPlan}][${count}]" style=""></textarea><br>
                            </div>
                         <input type="hidden" name="type_plan_id_edit[${keyPlan}][${count}]" data-id="${count}" class="type_plan_id_edit"/>
                       </td>
                        <td class="info_file parent_type_plan_doc_edit">
                            <input type="hidden" name="plan_detail_doc_id[${keyPlan}][${count}][]" value="0"
                        </td>
                        <td class="center">
                            <select name="possibility_resolution[${keyPlan}][${count}]" class="possibility_resolution_edit">
                                <option value="1">◎</option>
                                <option value="2">○</option>
                                <option value="3">△</option>
                                <option value="4">×</option>
                            </select>
                        </td>`
            if(dataCount === 0) {
                html +=  `  <td class="center distincts_is_add_edit ">
                                       </td>
                                       <td class="center parent_is_distinct_settlement_edit ">
                                            <span class="is_distinct_settlement_edit"></span>
                                          <input type="hidden" name="is_distinct_settlement_edit[]">
                                       </td>`
            }

            html += `<td class="center ">
                                <input type="checkbox"
                                       class="is_leave_all_edit"
                                       name="is_leave_all_edit[${keyPlan}][]"
                                       />
                                       </td>
                            <td class="center">
                                <input type="button" value="決定" class="btn_b copy_value_edit_to_confirm_col" data-key-plan-detail="${count}">
                            </td>
                                <td class="bg_blue2 parent_type_plan_detail_confirm">
                                    <div style="width: 350px;">
                                        <span class="confirm_type_plan_name confirm_type_plan_name_0"></span>
                                        <br>
                                        <br>
                                        <span class="confirm_type_plan_description mt10" style="white-space: pre-line"></span>
                                    </div>
                                </td>
                                <td class="bg_blue2 parent_type_plan_detail_doc_confirm">
                                     <div style="width: 350px;" class="infor-file-item_confirm">
                                        <span class="confirm_type_plan_doc_name white-space-pre-line w-100"></span>
                                            <br>
                                            <br>
                                            <span class="confirm_doc_requirement_des" style="white-space: pre-line"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="center bg_blue2 parent_possibility_resolution_confirm">
                                     <span class="confirm_possibility_resolution"></span>
                                </td>`
            if(dataCount === 0) {
                html +=  ` <td class="center bg_blue2 confirm_distincts_is_add"></td>
                                    <td class="center bg_blue2 confirm_is_distinct_settlement"></td>`
            }

            html += `<td class="center bg_blue2 confirm_is_leave_all"></td>
                            <td class="center bg_blue2" nowrap="">
                                <input type="checkbox" class="check_disabled">
                                <input type="hidden" name="is_confirm[${keyPlan}][${count}]" class="is_confirm_value">確認＆ロック
                                <input type="hidden" name="is_decision[${keyPlan}][${count}]" class="is_decision">
                                <input type="hidden" name="type_create[${keyPlan}][${count}]" value="1">
                            </td>
                        </tr>`
            $(this).closest('.parent_table_plan').find('.row_plan_detail').last().after(html)
            let checkDisabledChecked = $(this).closest('.parent_table_plan').find('.check_disabled:checked')
            let checkDisabled = $(this).closest('.parent_table_plan').find('.check_disabled')
            if(checkDisabledChecked.length < checkDisabled.length) {
                $(this).closest('.parent_table_plan').find('.check_disalbed_all').prop('checked', false)
            }
        })
    }

    deletePlanDetail() {
        $('body').on('click', '.delete_plan_detail', function () {
            const selfDelete = this
            let dataDeleteId = $(selfDelete).data('delete-id')
            let dataPlanDetailKey = $(selfDelete).data('plan-detail-key')
            let count = $(this).closest('.parent_table_plan').find('.row_plan_detail').length;
            let dataCount = $(this).data('count')
            $.confirm({
                title: '',
                content: deletePlanDetailTitle,
                buttons: {
                    cancel: {
                        text: CANCEL,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    },
                    ok: {
                        text: OK,
                        btnClass: 'btn-blue',
                        action: function () {
                            let buttonAdd = $(selfDelete).closest('.parent_table_plan').find('.add_plan_detail')
                            let countTab = $(selfDelete).closest('.parent_table_plan').find('.row_plan_detail');
                            if(count <= 1) {
                                $(selfDelete).closest('.parent_plan_'+ dataCount).remove()
                            } else {
                                $(selfDelete).closest('.row_plan_detail_' + dataDeleteId).remove()
                            }
                            self.countPlan = $('body').find('.parent_table_plan').length;
                            if(self.countPlan >= 5) {
                                $('body').find('.add_reciprocal_countermeasures').css('display', 'none')
                            } else {
                                $('body').find('.add_reciprocal_countermeasures').css('display', 'block')
                            }
                            for (dataCount; dataCount < self.countPlan; dataCount++) {
                                $(selfDelete).closest('#form').find('.add_reciprocal_countermeasures').before(`<div></div>`)
                            }

                        }
                    }
                }
            });
        })
    }

    deletePlanDetailBackend() {
        $('body').on('click', '.delete_plan_detail_backend', function () {
            const selfDelete = this
            let planDetailId = $(this).data('plan-detail-id')
            $.confirm({
                title: '',
                content: deletePlanDetailTitle,
                buttons: {
                    cancel: {
                        text: CANCEL,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    },
                    ok: {
                        text: OK,
                        btnClass: 'btn-blue',
                        action: function () {
                            loadAjaxPost(
                                deletePlanDetailURL,
                                {
                                    plan_detail_id: planDetailId,
                                }, {
                                    beforeSend: function () {
                                    },
                                    success: function (result) {
                                        selfDelete.closest('.row_plan_detail').remove();
                                    },
                                    error: function (error) {
                                    }
                                }, 'loading');
                        }
                    }
                }
            });
        })
    }

    addFileInfo() {
        const self = this
        let count = 1;
        $('body').on('click', '.add_file_info', function () {
            const selfAddFileInfo = this
            let dataKey = $(this).data('key')
            let dataKeyDetail = $(this).data('key-detail')
            const countInfoFileItem = $('body').find('.info_file').find('.infor-file-item').length
            $(this).before(`<div class="infor-file-item mt-2">
                                <input type="hidden" name="plan_detail_doc_id[${dataKey}][${dataKeyDetail}][]" value="0">
                                <select class="mb10 type_plan_doc type_plan_doc_${countInfoFileItem}" name="type_plan_doc_id_edit[${dataKey}][${dataKeyDetail}][]"></select>
                                <textarea class="wide file_info_description doc_requirement_des_edit" name="doc_requirement_des_edit[${dataKey}][${dataKeyDetail}][]"></textarea><br>
                                <input type="button" value="クリア" class="btn_a small delete_file_info_description"><br/>
                            </div>`)
            $(selfAddFileInfo).closest('.row_plan_detail').find('.infor-file-item').find('.type_plan_doc_' + countInfoFileItem).html('')
            $.each(mTypePlanDocs, function (index, item) {
                //value 8: その他自由記述
                if (item.m_type_plan_id === 8) {
                    $(selfAddFileInfo).closest('.row_plan_detail').find('.infor-file-item').find('.type_plan_doc_' + countInfoFileItem).append(`
                            <option value="${item.id}">${item.name}</option>
                    `)
                }
            })
        })
    }

    removeFileInfoDescription() {
        const self = this
        $('body').on('click', '.delete_file_info_description', function () {
            $(this).closest('.infor-file-item').find('.file_info_description').html(' ')
            $(this).closest('.infor-file-item').find('.file_info_description').val('')
            self.AppendOption(reasons, '.plan_reasons')
        })
    }

    funcAddReciprocalCountermeasures() {
        const self = this
        $('body').on('click', '.add_reciprocal_countermeasures', function () {
            self.countPlan = $('body').find('.parent_table_plan').length;
            let count = $('body').find('.parent_table_plan').find('.row_plan_detail').length;
            $(this).before(`
            <div class="parent_table_plan parent_plan parent_plan_${self.countPlan}" data-key-plan="${self.countPlan}">
                    <h3>・対応策-${ self.countPlan+1}</h3>
                    <input type="hidden" name="plan_id[]" value="0">

                  <p style="" class="parent_reasons">
                    <select multiple="multiple"
                     class="plan_reasons plan_reasons_${self.countPlan}"
                     style="width: 12em; display: none;"
                      name="" data-index="${self.countPlan}">
                    </select>
                    への対応</p>
                    <input type="hidden" name="plan_reason[]" >
                                       <table class="normal_b mb10 table_plan table_plan_${self.countPlan}">
                        <tbody>
                        <tr>
                            <th></th>
                            <th colspan="5" class="bg_sky">原案</th>
                            <th colspan="5">修正</th>
                            <th colspan="5" class="bg_blue2">決定</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th style="width:16%;" class="bg_sky">方針案</th>
                            <th style="width:16%;" class="bg_sky">必要資料</th>
                            <th class="bg_sky">解消<br>可能性</th>
                            <th class="bg_sky">商品名<br>全て残す</th>
                            <th class="bg_sky">
                                <input type="submit" value="一括修正" class="btn_a mb05 disabled" disabled><br>
                                <input type="submit" value="一括決定" class="btn_b disabled" disabled></th>
                            <th style="width:16%;">修正：<br>方針案</th>
                            <th style="min-width:16%;">修正：<br>必要資料</th>
                            <th>修正：<br>解消<br>可能性</th>
                            <th>修正：<br>商品名<br>全て残す</th>
                            <th>修正：<br/><input type="button" value="一括決定" class="btn_b copy_all_value_edit_to_confirm_col"/></th>
                            <th style="width:16%;" class="bg_blue2">決定：<br>方針案</th>
                            <th style="width:16%;" class="bg_blue2">決定：<br>必要資料</th>
                            <th class="bg_blue2">決定：<br>解消<br>可能性</th>
                            <th class="bg_blue2">決定：<br>商品名<br>全て残す</th>
                            <th class="bg_blue2">一括確認＆ロック <input type="checkbox" class="check_disalbed_all"></th>
                        </tr>
                        <tr class="row_plan_detail row_plan_detail_0" data-index="0">
                            <th>
                              方針案<br/>
                                    (1)
                                    <input type="hidden" name="plan_detail_id[${self.countPlan}][]" value="0">
                                    <br/>
                                    <input type="button" value="${delete1}" class="small btn_d delete_plan_detail" data-delete-id="${count}" data-count="${self.countPlan}">
                            </th>
                           <td class="bg_sky">
                               <input type="hidden" value="5" class="type_plan_id">
                                <br>
                                <br>
                               <input type="hidden" class="plan_description" value="">
                            </td>
                            <td class="bg_sky">
                                            <span class="type_plan_doc_name"></span><br>
                                            <br>
                                            <span class="type_plan_doc_requirement_des"></span>
                                </td>
                                <td class="center bg_sky">
                                    <input type="hidden" class="possibility_resolution" value="2">
                                </td>
                                <td class="center bg_sky">

                                </td>
                                <td class="center bg_sky">
                                    <input type="submit" value="修正" class="btn_a mb05 disabled" disabled><br>
                                    <input type="submit" value="決定" class="btn_b; disabled" disabled><br>
                                </td>
                                <td class="info_type_plan">
                                    <select class="mb10 type_plan_name type_plan_name_${self.countPlan}_0"

                                      style="width: 350px;"
                                      data-key="${self.countPlan}" data-key-detail="0">
                                        <option value="0">選択してください</option>
                                    </select>
                                    <textarea class="wide plan_description_edit type_plan_description" name="plan_description_edit[${self.countPlan}][0]"></textarea>
                                    <textarea class="wide plan_content_edit type_plan_description" name="plan_content_edit[${self.countPlan}][0]"></textarea>
                                    <br>
                                </td>
                                <input type="hidden" name="type_plan_id_edit[${self.countPlan}][0]" class="type_plan_id_edit"/>
                                <td class="info_file parent_type_plan_doc_edit">
                                     <input type="hidden" name="plan_detail_doc_id[${self.countPlan}][0][]" value="0"
                                </td>
                                <td class="center">
                                    <select name="possibility_resolution_edit[${self.countPlan}][0]" class="possibility_resolution_edit">
                                      <option value="1">◎</option>
                                      <option value="2" selected="">○</option>
                                      <option value="3">△</option>
                                      <option value="4">×</option>
                                    </select>
                                </td>
                                <td class="center">
                                    <input type="checkbox" class="is_leave_all_edit">
                                    <input type="hidden" name="is_leave_all_edit[${self.countPlan}][0]" class="is_leave_all_edit_value is_leave_all_edit_value_0_1" value="">
                                </td>
                                <td class="center">
                                    <input type="button" value="決定" class="btn_b copy_value_edit_to_confirm_col" data-key-plan-detail="0">
                                </td>
                                <td class="bg_blue2 parent_type_plan_detail_confirm">
                                    <div style="width: 350px;">
                                        <span class="confirm_type_plan_name confirm_type_plan_name_0"></span>
                                        <br>
                                        <br>
                                        <span class="confirm_type_plan_description mt10" style="white-space: pre-line"></span>
                                    </div>
                                </td>
                                <td class="bg_blue2 parent_type_plan_detail_doc_confirm">
                                     <div style="width: 350px;" class="infor-file-item_confirm">
                                        <span class="confirm_type_plan_doc_name white-space-pre-line w-100"></span>
                                            <br>
                                            <br>
                                            <span class="confirm_doc_requirement_des" style="white-space: pre-line"></span>
                                    </div>
                                </td>
                                <td class="center bg_blue2 parent_possibility_resolution_confirm">
                                     <span class="confirm_possibility_resolution"></span>
                                </td>
                                <td class="center bg_blue2 confirm_is_leave_all"></td>
                                <td class="center bg_blue2" nowrap="">
                                    <input type="checkbox" class="check_disabled">確認＆ロック
                                    <input type="hidden" name="is_confirm[${self.countPlan}][0]" class="is_confirm_value">
                                    <input type="hidden" name="is_decision[${self.countPlan}][0]" class="is_decision">
                                    <input type="hidden" name="type_create[${self.countPlan}][0]" value="1">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p><a href="javascript:;" class="add_plan_detail add_plan_detail_0" data-count="${self.countPlan}">+ 方針案の追加</a></p>
                    <p><input type="button" class="btn_a delete_plan"  data-count="${self.countPlan}" value="この対応策を削除"></p><hr>
                </div>`)
            self.AppendOption(reasons, '.plan_reasons', self.countPlan)
            self.AppendOption(mTypePlans, '.type_plan_name', 0)
            $(".plan_reasons_" + (self.countPlan - 1)).multipleSelect({
                selectAllText: checkAll,
                allSelected: checkAll,
                selectAll: false
            });
            if (!Array.isArray(self.newReason[self.countPlan-1])) {
                self.newReason[self.countPlan-1] = []
            }
            if ($('#form').find('.table_plan').length >= 5) {
                $(this).css('display', 'none')
            }
        })
    }

    deletePlan() {
        $('body').on('click', '.delete_plan', function () {
            const selfDeletePlan = this
            let dataCount = $(this).data('count')
            $.confirm({
                title: '',
                content: deletePlanDetailTitle,
                buttons: {
                    cancel: {
                        text: CANCEL,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    },
                    ok: {
                        text: OK,
                        btnClass: 'btn-blue',
                        action: function () {
                            $('body').find('.add_reciprocal_countermeasures').css('display', 'block')
                            $(selfDeletePlan).closest('.parent_plan_'+dataCount).remove()
                        }
                    }
                }
            });
        })
    }

    deletePlanBackend() {
        $('body').on('click', '.delete_plan_backend', function () {
            let dataCount = $(this).data('count')
            let planId = $(this).data('plan-id')
            $.confirm({
                title: '',
                content: deletePlanDetailTitle,
                buttons: {
                    cancel: {
                        text: CANCEL,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    },
                    ok: {
                        text: OK,
                        btnClass: 'btn-blue',
                        action: function () {
                            loadAjaxPost(
                                deletePlanURL,
                                {
                                    plan_id: planId,
                                }, {
                                    beforeSend: function () {
                                    },
                                    success: function (result) {
                                        location.reload();
                                    },
                                    error: function (error) {
                                    }
                                }, 'loading');
                        }
                    }
                }
            });
        })
    }

    createPlanReason() {
        const self = this
        $('body').on('click', '.create_plan_reason', function () {
            let arr = [];
            let PlanIds = $('body').find('input[name="plan_id[]"]');
            $.each(PlanIds, function (index, item) {
                arr.push(item.value)
            })
            loadAjaxPost(
                createPlanReasonURL,
                {
                    comparison_trademark_result_id: id,
                    plan_reason: JSON.parse($('input[name="plan_reason[]"]').val()),
                    plan_id: arr,
                    submit: 'create_reason'
                }, {
                    beforeSend: function () {
                    },
                    success: function (result) {
                    },
                    error: function (error) {
                    }
                }, 'loading');
        })
    }

    validateTypePlanDescription() {
        $('body').on('change, keyup', '.plan_description_edit', function () {
            let value = $(this).val()
            $(this).closest('.info_type_plan').find('.error').remove();
            if (value.length > 1000) {
                $(this).after('<div class="error">' + errorCommonE026 + '</div>');
            }
        })
    }

    validateTypePlanName() {
        $('body').on('change', '.type_plan_name', function () {
            let value = $(this).val()
            $(this).closest('.info_type_plan').find('.error').remove();
            if (+value !== 0) {
                $(this).closest('.row_plan_detail').find('.parent_type_plan_name').find('.error').remove();
            }
        })
    }

    validateFilePlanDocDescription() {
        const self = this
        $('body').on('change, keyup', '.doc_requirement_des_edit', function () {
            let dataKeyPlanDetailDoc = $(this).data('key-plan-detail-doc')
            let value = $(this).val();
            $(this).closest('.info_file').find('.parent_doc_requirement_des_edit_' + dataKeyPlanDetailDoc).find('.error').remove();
            if (value.length > 1000) {
                $(this).closest('.info_file').find('.parent_doc_requirement_des_edit_' + (+dataKeyPlanDetailDoc)).find('.doc_requirement_des_edit_' + (+dataKeyPlanDetailDoc)).after('<div class="error">' + errorCommonE026 + '</div>');
            }
        })
    }

    copyValueToEditCol() {
        self = this
        $('body').on('click', '.copy_value_to_edit_col', function () {
            self.countPlan = $('body').find('.parent_table_plan').length;
            let count = $(this).closest('.parent_table_plan').find('.row_plan_detail').length;
            let KeyPlan = $(this).data('key-plan')
            let keyPlanDetail = $(this).data('key-plan-detail')
            $(this).closest('.row_plan_detail').find('.type_plan_id_edit').val($(this).closest('.row_plan_detail').find('.type_plan_id').val())
            $(this).closest('.row_plan_detail').find('.plan_description_edit').val($(this).closest('.row_plan_detail').find('.type_plan_detail_description').val())
            $(this).closest('.row_plan_detail').find('.plan_content_edit').val($(this).closest('.row_plan_detail').find('.type_plan_detail_content').val())
            $(this).closest('.row_plan_detail').find('.possibility_resolution_edit').val($(this).closest('.row_plan_detail').find('.possibility_resolution').val())
            let typePlanDocID = $(this).closest('.row_plan_detail').find('.type_plan_doc_id')
            let typePlanDocName = $(this).closest('.row_plan_detail').find('.type_plan_doc_name')
            $(this).closest('.row_plan_detail').find('.parent_type_plan_doc_edit').html('')
            $.each(typePlanDocID, function (index, item) {
                let selfTypePlanDoc = this
                let valueItem = parseInt($(item).val())
                let typePlanDocDes = $(this).closest('.row_plan_detail').find('.type_plan_doc_requirement_des_'+index)[0].innerText
                if (valueItem >= 6) {
                    if(valueItem === 11) {
                        $(this).closest('.row_plan_detail').find('.parent_type_plan_doc_edit').append(`
                        <div class="infor-file-item infor-file-item_${index}">
                            <input type="hidden" name="plan_detail_doc_id[{{$keyPlan}}][{{$keyPlanDetail}}][]" value="{{$planDetailDoc->id}}">
                            <select class="mb10 type_plan_doc type_plan_doc_id_edit type_plan_doc_id_edit_${index}" style="width: 350px;">
                            </select>
                             <input type="hidden" class="mb10 type_plan_doc_id_edit_value type_plan_doc_id_edit_value_${index}"
                                    name="type_plan_doc_id_edit[${KeyPlan}][${keyPlanDetail}][]" value="${valueItem}"/>

                            <textarea class="wide file_info_description doc_requirement_des_edit disabled" name="doc_requirement_des_edit[${KeyPlan}][${keyPlanDetail}][]" readonly style="width: 500px">${typePlanDocDes}</textarea>
                            <input type="button" value="${delete2}" class="btn_a small delete_file_info_description mb-2"><br/>
                        </div>`)
                    } else {
                        $(this).closest('.row_plan_detail').find('.parent_type_plan_doc_edit').append(`
                        <div class="infor-file-item infor-file-item_${index}">
                            <input type="hidden" name="plan_detail_doc_id[{{$keyPlan}}][{{$keyPlanDetail}}][]" value="{{$planDetailDoc->id}}">
                            <select class="mb10 type_plan_doc type_plan_doc_id_edit type_plan_doc_id_edit_${index}" style="width: 350px;">
                            </select>
                             <input type="hidden" class="mb10 type_plan_doc_id_edit_value type_plan_doc_id_edit_value_${index}"
                                    name="type_plan_doc_id_edit[${KeyPlan}][${keyPlanDetail}][]" value="${valueItem}"/>

                            <textarea class="wide file_info_description doc_requirement_des_edit" name="doc_requirement_des_edit[${KeyPlan}][${keyPlanDetail}][]" style="width: 500px">${typePlanDocDes}</textarea>
                            <input type="button" value="${delete2}" class="btn_a small delete_file_info_description mb-2"><br/>
                        </div>`)
                    }
                    $.each(mTypePlanDocs, function (indexTypePlanDoc, item) {
                        if(+item.id >= 6) {
                            if(+item.id === valueItem) {
                                $(selfTypePlanDoc).closest('.row_plan_detail').find('.type_plan_doc_id_edit_'+index).append(`
                                <option value="${item.id}" selected>${item.name}</option>`)
                            } else {
                                $(selfTypePlanDoc).closest('.row_plan_detail').find('.type_plan_doc_id_edit_'+index).append(`
                                <option value="${item.id}">${item.name}</option>`)
                            }
                        }
                    })
                    $(this).closest('.row_plan_detail').find('.infor-file-item_'+(typePlanDocID.length -1)).after(`<div class="parent_add_file_info mt-3">
                                                            <a href="javascript:;" class="add_file_info" data-key="${KeyPlan}" data-key-detail="${keyPlanDetail}">+ ${add4}</a>
                                                    </div>`)
                    $.each(mTypePlanDocs, function (index, item) {
                        $(this).closest('.row_plan_detail').find('.type_plan_doc_id_edit').append(`
                    <option value="${item.id}">${item.name}</option>
                `)
                    })
                } else {
                    $(this).closest('.row_plan_detail').find('.parent_type_plan_doc_edit').append(`
                     <div class="infor-file-item infor-file-item_${index}">
                        <span class="file_info_name white-space-pre-line type_plan_doc_name_edit">${typePlanDocName[index].innerText}</span><br/>
                        <input type="hidden" class="type_plan_doc_id_edit" value="${$(item).val()}"
                               name="type_plan_doc_id_edit[${KeyPlan}][${keyPlanDetail}][]">
                        <textarea class="wide file_info_description doc_requirement_des_edit" name="doc_requirement_des_edit[${KeyPlan}][${keyPlanDetail}][]" style="width: 500px">${typePlanDocDes}</textarea>
                    </div>`)
                }
            })
            if ($(this).closest('.row_plan_detail').find('.is_leave_all')[0].innerText !== '') {
                $(this).closest('.row_plan_detail').find('.is_leave_all_edit').val(1)
                $(this).closest('.row_plan_detail').find('.is_leave_all_edit_value').val(1)
                $(this).closest('.row_plan_detail').find('.is_leave_all_edit').prop('checked', true)
            }
            if(KeyPlan === 0) {
                $(this).closest('.row_plan_detail').find('.distincts_is_add_edit').html($(this).closest('.row_plan_detail').find('.distincts_is_add')[0].innerText)
                let distinctSettement = [];
                $.each($(this).closest('.row_plan_detail').find('.distinct_settement'), function (index, item) {
                    distinctSettement.push(+$(item).val())
                })
                if(!Array.isArray(self.newDistrict[KeyPlan])) {
                    self.newDistrict[KeyPlan] = [];
                }
                if (!Array.isArray(self.newDistrict[KeyPlan][keyPlanDetail])) {
                    self.newDistrict[KeyPlan][keyPlanDetail] = []
                }
                if (self.newDistrict[KeyPlan][keyPlanDetail].length > 0) {
                    self.newDistrict[KeyPlan][keyPlanDetail] = []
                }
                $(this).closest('.row_plan_detail').find('.plan_detail_distinct_' + keyPlanDetail).html('')
                $.each($(this).closest('.row_plan_detail').find('.distinct_is_add'), function (index, item) {
                    let valueItem = JSON.parse($(item).val())

                    if ($.inArray(valueItem, distinctSettement) >= 0) {
                        self.newDistrict[KeyPlan][keyPlanDetail].push(valueItem)
                        $(this).closest('.row_plan_detail').find('.is_distinct_settlement_edit_' + KeyPlan + '_' +keyPlanDetail).append(`<option value="${valueItem}" class="distinct_${valueItem}" selected> 第${valueItem}類</option>`)
                    } else {
                        $(this).closest('.row_plan_detail').find('.is_distinct_settlement_edit_' + KeyPlan + '_' +keyPlanDetail).append(`<option value="${valueItem}" class="distinct_${valueItem}" > 第${valueItem}類</option>`)
                    }
                })
                $('.is_distinct_settlement_edit_' + KeyPlan + '_' +keyPlanDetail).multipleSelect({
                    selectAllText: checkAll,
                    allSelected: checkAll,
                    name: 'Distinct',
                });

                $('input[name="is_distinct_settlement_edit[]"]').val(JSON.stringify(self.newDistrict))
            }

        })
    }

    copyAllValueToEditCol() {
        const self = this
        $('body').on('click', '.copy_all_value_to_edit_col', function () {
            self.countPlan = $('body').find('.parent_table_plan').length;
            let count = $(this).closest('.parent_table_plan').find('.row_plan_detail').length;
            let KeyPlan = $(this).data('key-plan')
            let typePlanDetails = $(this).closest('.table_plan_' + KeyPlan).find('.type_plan_id')
            let keyPlanDetail = $(this).data('key-plan-detail')
            const selfCopyAll = this
            $.each(typePlanDetails, function (indexTypePlanDetails, item) {
                $(this).closest('.row_plan_detail').find('.type_plan_id_edit').val($(this).closest('.row_plan_detail').find('.type_plan_id').val())
                $(this).closest('.row_plan_detail').find('.plan_description_edit').val($(this).closest('.row_plan_detail').find('.type_plan_detail_description').val())
                $(this).closest('.row_plan_detail').find('.possibility_resolution_edit').val($(this).closest('.row_plan_detail').find('.possibility_resolution').val())
                let typePlanDocID = $(this).closest('.row_plan_detail').find('.type_plan_doc_id')
                let typePlanDocName = $(this).closest('.row_plan_detail').find('.type_plan_doc_name')
                $(this).closest('.row_plan_detail').find('.parent_type_plan_doc_edit').html('')
                $.each(typePlanDocID, function (index, item) {
                    let selfTypePlanDoc = this
                    let valueItem = parseInt($(item).val())
                    let typePlanDocDes = $(this).closest('.row_plan_detail').find('.type_plan_doc_requirement_des_'+index)[0].innerText
                    if (valueItem >= 6) {
                        if(valueItem === 11) {
                            $(this).closest('.row_plan_detail').find('.parent_type_plan_doc_edit').append(`
                        <div class="infor-file-item infor-file-item_${index}">
                            <input type="hidden" name="plan_detail_doc_id[{{$keyPlan}}][{{$keyPlanDetail}}][]" value="{{$planDetailDoc->id}}">
                            <select class="mb10 type_plan_doc type_plan_doc_id_edit type_plan_doc_id_edit_${index}" style="width: 350px;">
                            </select>
                             <input type="hidden" class="mb10 type_plan_doc_id_edit_value type_plan_doc_id_edit_value_${index}"
                                    name="type_plan_doc_id_edit[${KeyPlan}][${indexTypePlanDetails}][]" value="${valueItem}"/>

                            <textarea class="wide file_info_description doc_requirement_des_edit disabled" name="doc_requirement_des_edit[${KeyPlan}][${indexTypePlanDetails}][]" readonly style="width: 500px">${typePlanDocDes}</textarea>
                            <input type="button" value="${delete2}" class="btn_a small delete_file_info_description mb-2"><br/>
                        </div>`)
                        } else {
                            $(this).closest('.row_plan_detail').find('.parent_type_plan_doc_edit').append(`
                        <div class="infor-file-item infor-file-item_${index}">
                            <input type="hidden" name="plan_detail_doc_id[{{$keyPlan}}][{{$keyPlanDetail}}][]" value="{{$planDetailDoc->id}}">
                            <select class="mb10 type_plan_doc type_plan_doc_id_edit type_plan_doc_id_edit_${index}" style="width: 350px;">
                            </select>
                             <input type="hidden" class="mb10 type_plan_doc_id_edit_value type_plan_doc_id_edit_value_${index}"
                                    name="type_plan_doc_id_edit[${KeyPlan}][${indexTypePlanDetails}][]" value="${valueItem}"/>

                            <textarea class="wide file_info_description doc_requirement_des_edit" name="doc_requirement_des_edit[${KeyPlan}][${indexTypePlanDetails}][]" style="width: 500px">${typePlanDocDes}</textarea>
                            <input type="button" value="${delete2}" class="btn_a small delete_file_info_description mb-2"><br/>
                        </div>`)
                        }

                        $(this).closest('.row_plan_detail').find('.infor-file-item_'+(typePlanDocID.length -1)).after(`<div class="parent_add_file_info mt-3">
                                                            <a href="javascript:;" class="add_file_info" data-key="${KeyPlan}" data-key-detail="${keyPlanDetail}">+ ${add4}</a>
                                                    </div>`)
                        $.each(mTypePlanDocs, function (indexTypePlanDoc, item) {
                            if(+item.id >= 6) {
                                if(+item.id === valueItem) {
                                    $(selfTypePlanDoc).closest('.row_plan_detail').find('.type_plan_doc_id_edit_'+index).append(`
                                <option value="${item.id}" selected>${item.name}</option>`)
                                } else {
                                    $(selfTypePlanDoc).closest('.row_plan_detail').find('.type_plan_doc_id_edit_'+index).append(`
                                <option value="${item.id}">${item.name}</option>`)
                                }
                            }
                        })
                    } else {
                        $(this).closest('.row_plan_detail').find('.parent_type_plan_doc_edit').append(`
                                        <div class="infor-file-item infor-file-item_${index}">
                                            <span class="type_plan_doc_name_edit file_info_name white-space-pre-line">${typePlanDocName[index].innerText}</span><br/>
                                                <input type="hidden" class="type_plan_doc_id_edit" value="${$(item).val()}"
                                                       name="type_plan_doc_id_edit[${KeyPlan}][${indexTypePlanDetails}][]">
                                            <textarea class="wide file_info_description doc_requirement_des_edit" name="doc_requirement_des_edit[${KeyPlan}][${indexTypePlanDetails}][]" style="width: 500px">${typePlanDocDes}</textarea>
                                        </div>`)
                    }
                })

                if ($(this).closest('.row_plan_detail').find('.is_leave_all')[0].innerText !== '') {
                    $(this).closest('.row_plan_detail').find('.is_leave_all_edit').val(1)
                    $(this).closest('.row_plan_detail').find('.is_leave_all_edit').prop('checked', true)
                }
                if(KeyPlan === 0) {
                    $(this).closest('.row_plan_detail').find('.distincts_is_add_edit').html($(this).closest('.row_plan_detail').find('.distincts_is_add')[0].innerText)

                    let distinctSettement = [];
                    $.each($(this).closest('.row_plan_detail').find('.distinct_settement'), function (index, item) {
                        distinctSettement.push(+$(item).val())
                    })
                    if(!Array.isArray(self.newDistrict[KeyPlan])) {
                        self.newDistrict[KeyPlan] = [];
                    }
                    if (!Array.isArray(self.newDistrict[KeyPlan][indexTypePlanDetails])) {
                        self.newDistrict[KeyPlan][indexTypePlanDetails] = []
                    }
                    if (self.newDistrict[KeyPlan][indexTypePlanDetails].length > 0) {
                        self.newDistrict[KeyPlan][indexTypePlanDetails] = []
                    }
                    $(this).closest('.row_plan_detail').find('.plan_detail_distinct_' + indexTypePlanDetails).html('')
                    $.each($(this).closest('.row_plan_detail').find('.distinct_is_add'), function (index, item) {
                        let valueItem = JSON.parse($(item).val())

                        if ($.inArray(valueItem, distinctSettement) >= 0) {
                            self.newDistrict[KeyPlan][indexTypePlanDetails].push(valueItem)
                            $(this).closest('.row_plan_detail').find('.is_distinct_settlement_edit_' + KeyPlan + '_' +indexTypePlanDetails).append(`<option value="${valueItem}" class="distinct_${valueItem}" selected> 第${valueItem}類</option>`)
                        } else {
                            $(this).closest('.row_plan_detail').find('.is_distinct_settlement_edit_' + KeyPlan + '_' +indexTypePlanDetails).append(`<option value="${valueItem}" class="distinct_${valueItem}" > 第${valueItem}類</option>`)
                        }
                    })
                    $('.is_distinct_settlement_edit_' + KeyPlan + '_' +indexTypePlanDetails).multipleSelect({
                        selectAllText: checkAll,
                        allSelected: checkAll,
                        name: 'Distinct',
                    });
                }

            })
        })
    }


    copyValueToConfirmCol() {
        $('body').on('click', '.copy_value_to_confirm_col', function () {
            const selfCopyValue = this
            let keyPlan = $(this).data('key-plan')
            $(selfCopyValue).closest('.row_plan_detail').find('.confirm_type_plan_name').html($(selfCopyValue).closest('.row_plan_detail').find('.type_plan_detail_name')[0].innerHTML)
            $(selfCopyValue).closest('.row_plan_detail').find('.confirm_type_plan_description').html($(selfCopyValue).closest('.row_plan_detail').find('.type_plan_detail_description').val())
            let typePlanDocName = $(this).closest('.row_plan_detail').find('.type_plan_doc_name')
            $(selfCopyValue).closest('.row_plan_detail').find('.confirm_type_plan_doc_name').html(' ')
            $(selfCopyValue).closest('.row_plan_detail').find('.confirm_doc_requirement_des').html(' ')
            $(selfCopyValue).closest('.row_plan_detail').find('.parent_type_plan_detail_doc_confirm').html(' ')
            $(selfCopyValue).closest('.row_plan_detail').find('.parent_type_plan_name').find('.error').remove()
            $.each(typePlanDocName, function (index, item) {
                $(selfCopyValue).closest('.row_plan_detail').find('.parent_type_plan_detail_doc_confirm').append(`
                                   <div class="infor-file-item_confirm" style="width: 350px;">
                                     <div class="confirm_type_plan_doc_name white-space-pre-line w-100">${item.innerHTML}</div><br/>
                                    <div class="mb-2 confirm_doc_requirement_des"><span style="white-space: pre-line">${$(this).closest('.row_plan_detail').find('.type_plan_doc_requirement_des_'+index)[0].innerHTML}</span></div>
                                   </div>
                                `)
            })
            $(this).closest('.row_plan_detail').find('.confirm_possibility_resolution').html($(this).closest('.row_plan_detail').find('.parent_possibility_resolution')[0].innerHTML)
            if(keyPlan === 0) {
                $(this).closest('.row_plan_detail').find('.confirm_distincts_is_add').html($(this).closest('.row_plan_detail').find('.distincts_is_add')[0].innerText)
                $(this).closest('.row_plan_detail').find('.confirm_is_distinct_settlement').html($(this).closest('.row_plan_detail').find('.distincts_is_distinct_ettement')[0].innerText)
            }
            $(this).closest('.row_plan_detail').find('.is_decision').val(1)
            $(this).closest('.row_plan_detail').find('.confirm_distincts_is_add').html($(this).closest('.row_plan_detail').find('.distincts_is_add')[0].innerText)
            $(this).closest('.row_plan_detail').find('.confirm_is_distinct_settlement').html($(this).closest('.row_plan_detail').find('.distincts_is_distinct_ettement')[0].innerText)
            $(this).closest('.row_plan_detail').find('.confirm_is_leave_all').html($(this).closest('.row_plan_detail').find('.is_leave_all')[0].innerText)
            let valueTypePlanId = $(this).closest('.row_plan_detail').find('.type_plan_id')
            let valueTypePlanDescription = $(this).closest('.row_plan_detail').find('.type_plan_detail_description')
            if  (+valueTypePlanId.val() !== 0 && valueTypePlanDescription.val().length !== 0) {
                $(this).closest('.parent_table_plan').find('.error-table').remove()
            }
        })
    }

    copyAllValueToConfirmCol() {
        $('body').on('click', '.copy_all_value_to_confirm_col', function () {
            const selfCopyValue = this
            let keyPlan = $(this).data('key-plan')
            let typePlanName = $(this).closest('.table_plan').find('.type_plan_detail_name')
            $.each(typePlanName, function (index, item) {
                $(item).closest('.row_plan_detail').find('.parent_type_plan_name').find('.error').remove()
                let checkDisabled = $(this).closest('.row_plan_detail').find('.check_disabled')
                if(!checkDisabled[0].checked) {
                    $(this).closest('.row_plan_detail').find('.confirm_type_plan_name').html(item.innerHTML)
                    $(this).closest('.row_plan_detail').find('.confirm_type_plan_description').html($(this).closest('.row_plan_detail').find('.type_plan_detail_description').val())
                    let typePlanDocName = $(this).closest('.row_plan_detail').find('.type_plan_doc_name')
                    $(this).closest('.row_plan_detail').find('.confirm_type_plan_doc_name').html(' ')
                    $(this).closest('.row_plan_detail').find('.confirm_doc_requirement_des').html(' ')
                    $(this).closest('.row_plan_detail').find('.parent_type_plan_detail_doc_confirm').html(' ')

                    $.each(typePlanDocName, function (index, item) {
                        $(item).closest('.row_plan_detail').find('.parent_type_plan_detail_doc_confirm').append(`
                                    <div class="infor-file-item_confirm" style="width: 350px;">
                                     <div class="confirm_type_plan_doc_name white-space-pre-line w-100">${item.innerHTML}</div><br/>
                                    <div class="mb-2 confirm_doc_requirement_des"><span style="white-space: pre-line">${$(this).closest('.row_plan_detail').find('.type_plan_doc_requirement_des_'+index)[0].innerHTML}</span></div>
                                   </div>`)
                    })
                    $(this).closest('.row_plan_detail').find('.confirm_possibility_resolution').html($(this).closest('.row_plan_detail').find('.parent_possibility_resolution')[0].innerHTML)
                    $(this).closest('.row_plan_detail').find('.is_decision').val(1)
                    if(keyPlan === 0) {
                        $(this).closest('.row_plan_detail').find('.confirm_distincts_is_add').html($(this).closest('.row_plan_detail').find('.distincts_is_add')[0].innerText)
                        $(this).closest('.row_plan_detail').find('.confirm_is_distinct_settlement').html($(this).closest('.row_plan_detail').find('.distincts_is_distinct_ettement')[0].innerText)
                    }
                    $(this).closest('.row_plan_detail').find('.confirm_is_leave_all').html($(this).closest('.row_plan_detail').find('.is_leave_all')[0].innerText)
                }
                let valueTypePlanId = $(this).closest('.row_plan_detail').find('.type_plan_id')
                let valueTypePlanDescription = $(this).closest('.row_plan_detail').find('.type_plan_detail_description')
                if  (+valueTypePlanId.val() !== 0 && valueTypePlanDescription.val().length !== 0) {
                    $(this).closest('.parent_table_plan').find('.error-table').remove()
                }
            })
        })
    }

    copyValueEditToConfirmCol() {
        $('body').on('click', '.copy_value_edit_to_confirm_col', function () {
            const selfCopyValue = this
            let valuePlanIdEdit = $(this).closest('.row_plan_detail').find('.type_plan_id_edit option:selected').val()
            let keyPlanDetail = $(this).data('key-plan-detail')
            let keyPlan = $(this).data('key-plan')
            if (+valuePlanIdEdit === 0) {
                $(selfCopyValue).closest('.row_plan_detail').find('.parent_type_plan_name').find('.error').remove()
                $(selfCopyValue).closest('.row_plan_detail').find('.type_plan_name').after('<div class="error">' + errorValidate + '</div>')
                return false
            }
            $.each(mTypePlans, function (index, item) {
                if (+valuePlanIdEdit === item.id) {
                    $(selfCopyValue).closest('.row_plan_detail').find('.confirm_type_plan_name').html(item.name)
                } else if(+valuePlanIdEdit === 0) {

                }
                $(selfCopyValue).closest('.row_plan_detail').find('.confirm_type_plan_description').html($(selfCopyValue).closest('.row_plan_detail').find('.plan_description_edit').val())
            })
            let docRequirementDesEdit = $(selfCopyValue).closest('.row_plan_detail').find('.infor-file-item')
            $(selfCopyValue).closest('.row_plan_detail').find('.parent_type_plan_detail_doc_confirm div').html(' ')
            $.each(docRequirementDesEdit, function (index, item) {
                if ($($(item).find('.file_info_name'))[0] !== undefined) {
                    $(selfCopyValue).closest('.row_plan_detail_'+keyPlanDetail).find('.parent_type_plan_detail_doc_confirm').append(`
                                                                  <div class="infor-file-item_cofirm">
                                                                   <div class="confirm_type_plan_doc_name white-space-pre-line w-100">${$($(item).find('.file_info_name'))[0].innerText}</div><br/>
                                                                    <div class="mb-2 confirm_doc_requirement_des"><span style="white-space: pre-line">${$(item).find('.file_info_description').val()}</span></div>
                                                                </div>`)
                } else if ($(item).find('.type_plan_doc')[0] !== undefined) {
                    let typePlanDoc = $(item).find('.type_plan_doc').val();
                    let textTypePlanDoc = '';
                    if (+typePlanDoc === 6) {
                        textTypePlanDoc = '資格を有することを証明する書面';
                    } else if (+typePlanDoc === 7) {
                        textTypePlanDoc = '新聞・雑誌の記事、チラシ・ホームページなどの印刷物';
                    } else if (+typePlanDoc === 8) {
                        textTypePlanDoc = '使用意思（フォーマット付き）';
                    } else if (+typePlanDoc === 9) {
                        textTypePlanDoc = '承諾書（フォーマット付き）';
                    } else if (+typePlanDoc === 10) {
                        textTypePlanDoc = '自由記述';
                    } else if (+typePlanDoc === 11) {
                        textTypePlanDoc = '不要';
                    }
                    $(selfCopyValue).closest('.row_plan_detail_'+keyPlanDetail).find('.parent_type_plan_detail_doc_confirm').append(`
                                                                <div class="infor-file-item-confirm"> <div class="confirm_type_plan_doc_name white-space-pre-line w-100">${textTypePlanDoc}</div></br>
                                                                    <div class="mb-2 confirm_doc_requirement_des"><span style="white-space: pre-line">${$(item).find('.file_info_description').val()}</span></div></div>`)
                }

            })
            let confirmPosibilityReso = $(selfCopyValue).closest('.row_plan_detail').find('.possibility_resolution_edit').val();
            if (+confirmPosibilityReso === 1) {
                $(selfCopyValue).closest('.row_plan_detail').find('.confirm_possibility_resolution').html('◎')
            } else if (+confirmPosibilityReso === 2) {
                $(selfCopyValue).closest('.row_plan_detail').find('.confirm_possibility_resolution').html('○')
            } else if (+confirmPosibilityReso === 3) {
                $(selfCopyValue).closest('.row_plan_detail').find('.confirm_possibility_resolution').html('△')
            } else if (+confirmPosibilityReso === 4) {
                $(selfCopyValue).closest('.row_plan_detail').find('.confirm_possibility_resolution').html('×')
            }
            if(keyPlan === 0) {
                $(selfCopyValue).closest('.row_plan_detail').find('.confirm_distincts_is_add').html($(this).closest('.row_plan_detail').find('.distincts_is_add_edit')[0].innerText)
                $.each(self.newDistrict, function (indexNewDistinct, itemNewDistinct) {
                    $.each(itemNewDistinct, function (index, item) {
                        if(keyPlanDetail === +index) {
                            $(selfCopyValue).closest('.parent_plan_'+indexNewDistinct).find('.row_plan_detail_'+index).find('.confirm_is_distinct_settlement').html(' ')
                            $.each(item, function (indexItem, it) {
                                $(selfCopyValue).closest('.parent_plan_'+indexNewDistinct).find('.row_plan_detail_'+index).find('.confirm_is_distinct_settlement').append(it)
                                if(+indexItem < +item.length -1) {
                                    $(selfCopyValue).closest('.row_plan_detail').find('.confirm_is_distinct_settlement').append(' ,')
                                }
                            })
                        }
                    })
                })
            }
            if ($($(this).closest('.row_plan_detail').find('.is_leave_all_edit'))[0].checked === true) {
                $(selfCopyValue).closest('.row_plan_detail').find('.confirm_is_leave_all').html('全て残す')
            } else {
                $(selfCopyValue).closest('.row_plan_detail').find('.confirm_is_leave_all').html('')
            }
            $(selfCopyValue).closest('.row_plan_detail').find('.is_decision').val(2)
            let valueTypePlanIdEdit = $(selfCopyValue).closest('.row_plan_detail').find('.type_plan_id_edit')
            let valueTypePlanDescriptionEdit = $(selfCopyValue).closest('.row_plan_detail').find('.plan_description_edit')
            if  (+valueTypePlanIdEdit.val() !== 0 && valueTypePlanDescriptionEdit.val().length !== 0) {
                $(selfCopyValue).closest('.parent_table_plan').find('.error-table').remove()
            }
        })
    }

    copyAllValueEditToConfirmCol() {
        $('body').on('click', '.copy_all_value_edit_to_confirm_col', function () {
            const selfCopyValue = this
            let keyPlan = $(this).data('key-plan')
            let planDetail = $(selfCopyValue).closest('.table_plan').find('.row_plan_detail')
            $.each(planDetail, function (indexPlanDetail, item) {
                const selfPlanDetail = this
                let valuePlanIdEdit = $(selfPlanDetail).closest('.row_plan_detail').find('.type_plan_id_edit option:selected').val()
                let rowPlanDetail = $(selfCopyValue).closest('.row_plan_detail_' + indexPlanDetail)
                if (!$(selfPlanDetail).find('.check_disabled')[0].checked) {
                    if (+valuePlanIdEdit === 0) {
                        $(selfPlanDetail).closest('.row_plan_detail').find('.parent_type_plan_name').find('.error').remove()
                        $(selfPlanDetail).closest('.row_plan_detail').find('.type_plan_name').after('<div class="error">' + errorValidate + '</div>')
                    }
                }
            })
            const countError = $(selfCopyValue).closest('.table_plan').find('.error').length
            if (countError > 0) {
                return false
            }
            $.each(planDetail, function (indexPlanDetail, item) {
                const selfPlanDetail = this
                let valuePlanIdEdit = $(selfPlanDetail).closest('.row_plan_detail').find('.type_plan_id_edit option:selected').val()
                let rowPlanDetail = $(selfCopyValue).closest('.row_plan_detail_' + indexPlanDetail)
                if (!$(selfPlanDetail).find('.check_disabled')[0].checked) {
                    $.each(mTypePlans, function (index, itemTypePlan) {
                        if (+valuePlanIdEdit === +itemTypePlan.id) {
                            $(selfPlanDetail).closest('.row_plan_detail').find('.confirm_type_plan_name').html(itemTypePlan.name)
                        }
                        $(selfPlanDetail).find('.confirm_type_plan_description').html($(selfPlanDetail).closest('.row_plan_detail').find('.plan_description_edit').val())
                    })
                    let docRequirementDesEdit = $(item).find('.infor-file-item')
                    $(item).find('.parent_type_plan_detail_doc_confirm div').html(' ')
                    $.each(docRequirementDesEdit, function (index, item) {
                        if ($(item).find('.file_info_name')[0] !== undefined) {
                            $(selfPlanDetail).closest('.row_plan_detail').find('.parent_type_plan_detail_doc_confirm').append(`
                                                      <div class="infor-file-item_cofirm">
                                                        <div class="confirm_type_plan_doc_name white-space-pre-line w-100">${$($(item).find('.file_info_name'))[0].innerText}</div></br>
                                                        <div class="mb-2 confirm_doc_requirement_des">
                                                            <span style="white-space: pre-line">${$(item).find('.file_info_description').val()}</span>
                                                        </div>
                                                      </div>`)
                        } else if ($(item).find('.type_plan_doc')[0] !== undefined) {
                            let typePlanDoc = $(item).find('.type_plan_doc').val();
                            let textTypePlanDoc = '';
                            if (+typePlanDoc === 6) {
                                textTypePlanDoc = '資格を有することを証明する書面';
                            } else if (+typePlanDoc === 7) {
                                textTypePlanDoc = '新聞・雑誌の記事、チラシ・ホームページなどの印刷物';
                            } else if (+typePlanDoc === 8) {
                                textTypePlanDoc = '使用意思（フォーマット付き）';
                            } else if (+typePlanDoc === 9) {
                                textTypePlanDoc = '承諾書（フォーマット付き）';
                            } else if (+typePlanDoc === 10) {
                                textTypePlanDoc = '自由記述';
                            } else if (+typePlanDoc === 11) {
                                textTypePlanDoc = '不要';
                            }
                            $(selfPlanDetail).closest('.row_plan_detail_'+indexPlanDetail).find('.parent_type_plan_detail_doc_confirm').append(`
                                                       <div class="infor-file-item_cofirm">
                                                            <div class="confirm_type_plan_doc_name white-space-pre-line w-100">${textTypePlanDoc}</div></br>
                                                            <div class="mb-2 confirm_doc_requirement_des">
                                                                <span style="white-space: pre-line">${$(item).find('.file_info_description').val()}</span>
                                                            </div>
                                                        </div>`)
                        }
                    })
                    let confirmPosibilityReso = $(this).closest('.row_plan_detail').find('.possibility_resolution_edit').val();
                    if (+confirmPosibilityReso === 1) {
                        $(this).closest('.row_plan_detail').find('.confirm_possibility_resolution').html('◎')
                    } else if (+confirmPosibilityReso === 2) {
                        $(this).closest('.row_plan_detail').find('.confirm_possibility_resolution').html('○')
                    } else if (+confirmPosibilityReso === 3) {
                        $(this).closest('.row_plan_detail').find('.confirm_possibility_resolution').html('△')
                    } else if (+confirmPosibilityReso === 4) {
                        $(this).closest('.row_plan_detail').find('.confirm_possibility_resolution').html('×')
                    }
                    if(keyPlan === 0) {
                        $(this).closest('.row_plan_detail').find('.confirm_distincts_is_add').html($(this).closest('.row_plan_detail').find('.distincts_is_add_edit')[0].innerText)
                        $.each(self.newDistrict, function (indexNewDistinct, itemNewDistinct) {
                            $.each(itemNewDistinct, function (index, item) {
                                $(selfCopyValue).closest('.parent_plan_'+indexNewDistinct).find('.row_plan_detail_'+index).find('.confirm_is_distinct_settlement').html('')
                                $.each(item, function (indexItem, it) {
                                    $(selfCopyValue).closest('.parent_plan_'+indexNewDistinct).find('.row_plan_detail_'+index).find('.confirm_is_distinct_settlement').append(it)
                                    if(+indexItem < +item.length -1) {
                                        $(selfCopyValue).closest('.parent_plan_'+indexNewDistinct).find('.row_plan_detail_'+index).find('.confirm_is_distinct_settlement').append(' , ')
                                    }
                                })
                            })
                        })
                    }

                    if ($($(this).closest('.row_plan_detail').find('.is_leave_all_edit'))[0].checked === true) {
                        $(this).closest('.row_plan_detail').find('.confirm_is_leave_all').html('全て残す')
                    } else {
                        $(this).closest('.row_plan_detail').find('.confirm_is_leave_all').html('')
                    }
                    $(this).closest('.row_plan_detail').find('.is_decision').val(2) //value is_decision is edit
                }
                let valueTypePlanIdEdit = $(this).closest('.row_plan_detail').find('.type_plan_id_edit')
                let valueTypePlanDescriptionEdit = $(this).closest('.row_plan_detail').find('.plan_description_edit')
                if  (+valueTypePlanIdEdit.val() !== 0 && valueTypePlanDescriptionEdit.val().length !== 0) {
                    $(this).closest('.parent_table_plan').find('.error-table').remove()
                }
            });

        })
    }
    checkedIsleaveAll() {
        $('body').on('click', '.is_leave_all_edit', function () {
            if($(this)[0].checked) {
                $(this).closest('.row_plan_detail').find('.is_leave_all_edit_value').val(1) //value is_leave_all_edit is true
            } else {
                $(this).closest('.row_plan_detail').find('.is_leave_all_edit_value').val(0) //value is_leave_all_edit is false
            }
        })
    }

    checkDisabled() {
        $('body').on('click', '.check_disabled', function () {
            let countCheckdisabled = $(this).closest('.table_plan').find('.check_disabled:checked').length
            let countCheckUndisabled = $(this).closest('.table_plan').find('.check_disabled').length
            let keyPlan = $(this).data('key-plan')
            let keyPlanDetail = $(this).data('key-plan-detail')
            if (countCheckdisabled === countCheckUndisabled) {
                $(this).closest('.table_plan').find('.check_disalbed_all').prop('checked', true)
            } else {
                $(this).closest('.table_plan').find('.check_disalbed_all').prop('checked', false)
            }
            $(this).closest('.parent_div_is_confirm').find('.error').remove()
            if ($(this)[0].checked === true) {
                $(this).closest('.row_plan_detail').find('.is_confirm_value').val(1)
                $(this).closest('.row_plan_detail').find('.copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .copy_value_edit_to_confirm_col, .is_leave_all_edit, .plan_detail_distinct button, .delete_file_info_description, .add_file_info, .delete_plan_detail_backend, .delete_plan_detail, div.is_distinct_settlement_edit_'+keyPlan+'_'+keyPlanDetail+' button').addClass('disabled')
                $(this).closest('.row_plan_detail').find('.copy_value_to_edit_col, .copy_value_to_confirm_col, .copy_value_edit_to_confirm_col, .type_plan_name, .is_leave_all_edit, .possibility_resolution_edit option:not(:selected), .type_plan_doc, .delete_file_info_description, .delete_plan_backend, .delete_plan_detail_backend, .add_file_info, .delete_plan_detail_backend, .delete_plan_detail, div.is_distinct_settlement_edit_'+keyPlan+'_'+keyPlanDetail+' button').attr('disabled', true)
                $(this).closest('.row_plan_detail').find('.type_plan_name, .type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .add_file_info').prop('readonly', true)
                $(this).closest('.row_plan_detail').find('.add_file_info').css('pointerEvents', 'none')
            } else {
                $(this).closest('.row_plan_detail').find('.is_confirm_value').val(0)
                $(this).closest('.row_plan_detail').find('.copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .copy_value_edit_to_confirm_col, .is_leave_all_edit,  .plan_detail_distinct button, .delete_file_info_description, .add_file_info, .delete_plan_detail_backend, .delete_plan_detail, div.is_distinct_settlement_edit_'+keyPlan+'_'+keyPlanDetail+' button').removeClass('disabled')
                $(this).closest('.row_plan_detail').find('.copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .copy_value_edit_to_confirm_col, .is_leave_all_edit, .possibility_resolution_edit option:not(:selected), .type_plan_doc, .delete_file_info_description, .delete_plan_backend, .delete_plan_detail_backend, .add_file_info, .delete_plan_detail_backend, .delete_plan_detail, div.is_distinct_settlement_edit_'+keyPlan+'_'+keyPlanDetail+' button').attr('disabled', false)
                $(this).closest('.row_plan_detail').find('.type_plan_name, .type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .add_file_info, .add_file_info').prop('readonly', false)
                $(this).closest('.row_plan_detail').find('.add_file_info').css('pointerEvents', 'visible')
            }
        })
    }

    checkAllDisabled() {
        $('body').on('click', '.check_disalbed_all', function () {
            let keyPlan = $(this).data('key-plan')
            let planDetail = $(this).closest('.table_plan').find('.row_plan_detail')
            if ($(this)[0].checked === true) {
                $(this).closest('.table_plan').find('.copy_all_value_to_edit_col, .copy_all_value_to_confirm_col, .copy_all_value_edit_to_confirm_col, .add_file_info').addClass('disabled')
                $(this).closest('.table_plan').find('.copy_all_value_to_edit_col, .copy_all_value_to_confirm_col, .copy_all_value_edit_to_confirm_col, .add_file_info').attr('disabled', true)
                $.each(planDetail, function (index, item) {
                    $(item).find('.parent_div_is_confirm').find('.error').remove()
                    $(item).find('.is_confirm_value').val(1)
                    $(item).find('.type_plan_name, .copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .copy_value_edit_to_confirm_col, .type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .is_leave_all_edit, .plan_detail_distinct button, .delete_file_info_description, .add_file_info, .delete_plan_detail_backend, .delete_plan_detail, div.is_distinct_settlement_edit_'+keyPlan+'_'+index+' button').addClass('disabled')
                    $(item).find('.type_plan_name, .copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .copy_value_edit_to_confirm_col, .is_leave_all_edit, .possibility_resolution_edit option:not(:selected), .delete_file_info_description, .delete_plan_backend, .delete_plan_detail_backend, .add_file_info, .delete_plan_detail_backend, .delete_plan_detail, div.is_distinct_settlement_edit_'+keyPlan+'_'+index+' button').attr('disabled', true)
                    $(item).find('.type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .add_file_info').prop('readonly', true)
                    $(item).find('.check_disabled').prop('checked', true)
                    $(item).find('.add_file_info').css('pointerEvents', 'none')
                })
            } else {
                $(this).closest('.table_plan').find('.copy_all_value_to_edit_col, .copy_all_value_to_confirm_col, .copy_all_value_edit_to_confirm_col, .add_file_info').removeClass('disabled')
                $(this).closest('.table_plan').find('.copy_all_value_to_edit_col, .copy_all_value_to_confirm_col, .copy_all_value_edit_to_confirm_col, .add_file_info').attr('disabled', false)
                $.each(planDetail, function (index, item) {
                    $(item).find('.is_confirm_value').val(0)
                    $(item).find('.type_plan_name, .copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .copy_value_edit_to_confirm_col, .type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .is_leave_all_edit, .plan_detail_distinct button, .delete_file_info_description, .add_file_info, .delete_plan_detail_backend, .delete_plan_detail, div.is_distinct_settlement_edit_'+keyPlan+'_'+index+' button').removeClass('disabled')
                    $(item).find('.type_plan_name, .copy_value_to_edit_col, .copy_value_to_confirm_col, .type_plan_name, .copy_value_edit_to_confirm_col, .is_leave_all_edit, .possibility_resolution_edit option:not(:selected), .delete_file_info_description, .delete_plan_backend, .delete_plan_detail_backend, .add_file_info, .delete_plan_detail_backend, .delete_plan_detail, div.is_distinct_settlement_edit_'+keyPlan+'_'+index+' button').attr('disabled', false)
                    $(item).find(' .type_plan_description, .possibility_resolution_edit, .doc_requirement_des_edit, .plan_detail_distinct, .add_file_info').prop('readonly', false)
                    $(item).find('.check_disabled').prop('checked', false)
                    $(item).find('.add_file_info').css('pointerEvents', 'visible')
                })
            }
        })
    }

    validatePlanComment() {
        $('body').on('change', '.plan_comment', function () {
            let value = $(this).val()
            $(this).closest('.parent_plan_comment').find('.error').remove();
            if (value.length > 1000) {
                $(this).after('<div class="error">' + errorCommonE026 + '</div>');
            }
        })
    }

    initVariable() {
        this.addReciprocalCountermeasures = $('body').find('.add_reciprocal_countermeasures');
        this.planId = $('body').find('input[name="plan_id[]"]').val()

        // common203Rule, common203Message is constant in file common
        this.rules = {...common203Rule};
        this.messages = {...common203Message};

        new clsValidation('#form', {rules: this.rules, messages: this.messages})
    }

    /**
     * If the value of the select is 8 | 11 and the value of the input is empty, then add a div with the
     * class "notice error-required" after the input.
     * If there is a div with the class "notice error-required" in the form, then return true.
     *
     */
    checkValidateRequireDoc() {
        let error = [];
        const typePlanDescriptionList = $('.type_plan_description');
        for (const item of typePlanDescriptionList) {
            const valSelect = $(item).closest('.info_type_plan').find('select.type_plan_name').val()
            if (valSelect == 8 && !$(item).val()) {
                if (!$(item).closest('.info_type_plan').find('.error-required').length) {
                    $(item).after(`
                        <div class="notice error">${errorMessageRequired}<div>
                    `)
                }
            }
        }

        const fileInfoDescriptions = $('.file_info_description');
        for (const item of fileInfoDescriptions) {
            const valSelectDoc = $(item).closest('.infor-file-item ').find('select.type_plan_doc').val()
            if (valSelectDoc && valSelectDoc != 11 && !$(item).val()) {
                if (!$(item).closest('.infor-file-item').find('.error-required').length) {
                    $(item).after(`
                        <div class="notice error">${errorMessageRequired}<div>
                    `)
                }
            }
        }

        return $('#form').find('.notice, .error').length ? true : false
    }

    onSubmit() {
        const _self = this
        $('body').on('click', '.submit, #submit, .create_plan_reason', function (e) {
            e.preventDefault();

            if ( $(this)[0].id === 'submit') {
                let currentReasonID = [];
                $.each(self.newReason, function (index, reason) {
                    if(reason && reason.length <= 0) {
                        if ($('body').find('.plan_reasons_'+index)[1]) {
                            let tagReason = $($('body').find('.plan_reasons_'+index)[1].closest('.parent_reasons'))
                            tagReason.closest('.parent_plan_'+index).find('.error').remove()
                            tagReason.after('<div class="error mb-2">' + errorCorrespondenceA203E003 + '</div>')
                        }
                    }

                    $.each(reason, function (index, item) {
                        currentReasonID.push(parseInt(item));
                    });
                })

                let currentReasonDuplicate = findDuplicates(currentReasonID);
                if (currentReasonDuplicate.length > 0) {
                    $.confirm({
                        title: '',
                        content: errorCorrespondenceA203E002,
                        buttons: {
                            cancel: {
                                text: OK2,
                                btnClass: 'btn-default',
                                action: function () {}
                            },
                        }
                    });
                    return false;
                }

                let reasonIDs = [];
                $.each(reasons, function (index, item) {
                    reasonIDs.push(item.id);
                });

                let reasonNotSelected = diffArray(currentReasonID, reasonIDs);
                if (reasonNotSelected.length > 0) {
                    $.confirm({
                        title: '',
                        content: errorCorrespondenceA203E004,
                        buttons: {
                            cancel: {
                                text: OK2,
                                btnClass: 'btn-default',
                                action: function () {}
                            },
                        }
                    });
                    return false;
                }
            }

            let form = $('#form');
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length > 0) {
                let firstError = hasError.first();
                scrollToElement(firstError, -100);
                return false;
            }

            const valid = _self.checkValidateRequireDoc()

            $('body').find('input.file_info_description').change()
            $('body').find('input.type_plan_description').change()
            if (valid) {
                return
            }
            $('#input_submit').click()
        });
    }
}

new clsEditPlanSupervisor()
