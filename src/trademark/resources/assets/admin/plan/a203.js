class clsCreatePlan {
    selectReasons = {};
    countPlan = $('body').find('.parent_table_plan').length;
    newReason = {}

    newDistrict = {}

    constructor() {
        const self = this
        self.initVariable()
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    doLoad() {
        this.init()
        if(self.countPlan >= 5) {
            $('body').find('.add_reciprocal_countermeasures').css('display', 'none')
        }
        if (plans.length > 0) {
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

                $.each(plans[indexPlan].plan_details, function (indexPlanDetail, itemPlanDetail) {
                    if (itemPlanDetail.distincts_is_add.length > 0 && itemPlanDetail.distinctsIsDistinctSettement.length > 0) {
                        let itemDistinctSettementIds = []
                        $.each(itemPlanDetail.distincts_is_add, function (indexDistinctIsAdd, distincts_is_distinct_settement) {
                            itemDistinctSettementIds.push(distincts_is_distinct_settement.id)
                        })
                        if(!Array.isArray(self.newDistrict[indexPlan])) {
                            self.newDistrict[indexPlan] = [];
                        }
                        if (!Array.isArray(self.newDistrict[indexPlan][indexPlanDetail])) {
                            self.newDistrict[indexPlan][indexPlanDetail] = []
                        }
                        $.each(itemPlanDetail.distinctsIsDistinctSettement, function (index, item) {
                            self.newDistrict[indexPlan][indexPlanDetail].push(item.id)
                        })
                    }
                })
            }
            $.each($('.parent_plan'), function (index, item) {
                self.newReason[index] = $(this).find('.plan_reasons').val();
            })
        } else {
            self.newReason[0] = [];
        }
        $('input[name="is_distinct_settlements[]"]').val(JSON.stringify(self.newDistrict))
        $('input[name="plan_reason[]"]').val(JSON.stringify(self.newReason))
        self.onchangeSelectItem()
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
        self.changeTypePlanDoc()
        self.deleteTypePlanDescription()
        self.validateTypePlanDescription()
        self.validateFilePlanDocDescription()
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
            const keyPlan = $(this).closest('.parent_is_distinct_settlement').find('select.multi').data('index')
            const keyPlanDetail = $(this).closest('.parent_is_distinct_settlement').find('select.multi').data('key-plan-detail')
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
            $('input[name="is_distinct_settlements[]"]').val(JSON.stringify(self.newDistrict))
        })
    }


    selectAllDistrict() {
        const self = this
        let newDistrict = {}
        $('body').on('click', 'input[name="selectAllDistinct"]', function () {
            const selfSelectAllItemDistinct = this
            self.countPlan = $('body').find('.parent_table_plan').length
            const keyPlan = $(this).closest('.parent_is_distinct_settlement').find('select.multi').data('index')
            const keyPlanDetail = $(this).closest('.parent_is_distinct_settlement').find('select.multi').data('key-plan-detail')
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
            $('input[name="is_distinct_settlements[]"]').val(JSON.stringify(self.newDistrict))

        })
    }


    changeTypePlanName() {
        $('body').on('change', '.type_plan_name', function () {
            let self = this
            let typePlanId = $(self).val()
            let dataId = $(this).data('id')
            let dataKey = $(this).data('key')
            let dataKeyDetail = $(this).data('key-detail')
            $(self).closest('.info_type_plan').find('.type_plan_description').html(' ')
            $(self).closest('.info_type_plan').find('.type_plan_description').val(' ')
            $(self).closest('.row_plan_detail').find('.info_file').html(' ')
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
                            <span class="file_info_name white-space-pre-line">${item.name}</span>
                            <input type="hidden" name="plan_detail_doc_id[${dataKey}][${dataKeyDetail}][]" value="0">
                            <input type="hidden" name="type_plan_doc_id[${dataKey}][${dataKeyDetail}][]" value="${item.id}">
                            <textarea class="wide file_info_description" name="doc_requirement_des[${dataKey}][${dataKeyDetail}][]">${item.description}</textarea>
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
                            <input type="hidden" name="plan_detail_doc_id[${dataKey}][${dataKeyDetail}][]" value="0">
                            <select class="mb10 type_plan_doc" name="type_plan_doc_id[${dataKey}][${dataKeyDetail}][]" data-key="${dataKey}" data-key-detail="${dataKeyDetail}">
                            </select>
                            <textarea class="wide file_info_description" name="doc_requirement_des[${dataKey}][${dataKeyDetail}][]"></textarea>
                            <input type="button" value="${delete2}" class="btn_a small delete_file_info_description">
                            <br/>
                        </div>
                        <div class="parent_add_file_info mt-3">
                             <a href="javascript:;" class="add_file_info mt-1" data-key="${dataKey}" data-key-detail="${dataKeyDetail}">+ ${add4}</a>
                        </div>
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

    AppendOption(options, selectClass, count, keyPlan = null) {
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
                    // if (checkExist === true) {
                    //     $('.reason_' + i).prop('disabled', true)
                    // }
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
            const selfAddPlanDetail = this
            self.countPlan = $('body').find('.parent_table_plan').length;
            let count = $(this).closest('.parent_table_plan').find('.row_plan_detail').last().data('index');
            count = count + 1;
            let dataCount = $(this).data('count')

            let html =`
                     <tr class="row_plan_detail row_plan_detail_${count}" data-index="${count}">
                        <th class="th_plan_detail_${count}">
                            <span class="title_plan_detail_${count}">${draftPolicy}<br>(${count + 1})</span> <br>
                            <input type="hidden" name="plan_detail_id[${dataCount}][${count}]" value="0">
                            <input type="button" value="${delete1}" class="small btn_d delete_plan_detail" data-delete-id="${count}" data-count="${dataCount}">
                        </th>
                        <td class="info_type_plan">
                            <select class="w-75 type_plan_name type_plan_name_${self.countPlan - 1}_${count}" name="type_plan_id[${dataCount}][${count}]" data-id="${count}" data-key="${dataCount}" data-key-detail="${count}">
                                  <option value="0">${defaultSelect}</option>
                                  ${self.getOptionHTML(mTypePlans)}
                            </select>
                            <textarea class="wide type_plan_description mt10" name="plan_description[${dataCount}][${count}]"></textarea><br>
                            <textarea hidden type="text" class="type_plan_content" name="plan_content[${dataCount}][${count}]"></textarea>
                            <input type="button" value="${delete2}" class="btn_a small delete_type_plan_description">
                            <br>
                            <br>
                        </td>
                        <td class="center bg_sky">
                            <select name="possibility_resolution[${dataCount}][${count}]">
                                 <option value="1">◎</option>
                                 <option value="2">○</option>
                                 <option value="3">△</option>
                                 <option value="4">×</option>
                            </select>
                        </td>
                        <td class="info_file">
                            <span class="file_info_name white-space-pre-line">${uses}</span>
                            <input type="hidden" name="type_plan_doc_id[${dataCount}][${count}][]" value="0">
                            <textarea class="wide file_info_description" name="doc_requirement_des[${dataCount}][${count}][]"></textarea><br>
                            <input type="button" value="${delete2}" class="btn_a small delete_file_info_description">
                            <input type="hidden" name="plan_detail_doc_id[${dataCount}][${count}][]" value="0">
                        </td>`

                       if(dataCount === 0) {
                            html +=  `<td class="center">
                                </td>
                                <td class="center" style="">
                                    <select multiple="multiple" class="multi" style="width: 8em; display: none;"></select>
                                </td>`
                       }
                        html += `<td class="center">
                                        <input type="checkbox" name="is_leave_all[${dataCount}_${count}]">
                                        <input type="hidden" name="type_create[${dataCount}][${count}]" value="1">
                                    </td>
                                </tr>`
            $(this).closest('.parent_table_plan').find('.row_plan_detail').last().after(html)
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
        $('body').on('click', '.add_file_info', function () {
            const selfAddFileInfo = this
            const countInfoFileItem = $('body').find('.info_file').find('.infor-file-item').length
            let dataKey = $(this).data('key')
            let dataKeyDetail = $(this).data('key-detail')
            $(this).closest('.parent_add_file_info').before(`<div class="infor-file-item mt-3">
                                <input type="hidden" name="plan_detail_doc_id[${dataKey}][${dataKeyDetail}][]" value="0">
                                <select class="mb10 type_plan_doc type_plan_doc_${countInfoFileItem}" name="type_plan_doc_id[${dataKey}][${dataKeyDetail}][]"></select>
                                <textarea class="wide file_info_description" name="doc_requirement_des[${dataKey}][${dataKeyDetail}][]"></textarea><br>
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
            let count = $('body').find('.parent_table_plan').length;
            let countDetail = $(this).closest('.parent_table_plan').find('.row_plan_detail').length;
            self.countPlan = $('body').find('.parent_table_plan').length;
            $(this).before(`
            <div class="parent_plan parent_plan_${self.countPlan}" data-key-plan="${self.countPlan}">
            <h3 class="name_plan_${self.countPlan}">・${countermeasures}-${self.countPlan + 1}</h3>
                <p class="description_plan_${self.countPlan}">${desription1}</p>
                <input type="hidden" name="plan_id[]" value="0">

                <p class="parent_reasons mb-0">
                    <select multiple="multiple"
                     class="plan_reasons plan_reasons_${self.countPlan}"
                     style="width: 12em; display: none;"
                      name="" data-index="${self.countPlan}">
                    </select>
                への対応</p>
                <input type="hidden" name="plan_reason[]">
                <ul class="clearfix mb10 mt-3">
                    <li><button type="submit" name="submit" value="save" class="btn_b create_plan_reason">保存</button></li>
                </ul>
                <div class="parent_table_plan">
                 <table class="normal_b mb10 table_plan table_plan_${self.countPlan}">
                    <tbody class="tbody tbody_${self.countPlan}">
                    <tr>
                        <th></th>
                        <th class="em40">${draftPolicy}</th>
                        <th>${handle}<br>${ability}</th>
                        <th class="em24">${planDetailDoc}</th>
                        <th>${product}<br>${serviceName}<br>${backAll}</th>
                    </tr>
                    <tr class="row_plan_detail row_plan_detail_0" data-index="0">
                        <th>
                            ${draftPolicy}<br>(1) <br>
                            <input type="hidden" name="plan_detail_id[${self.countPlan}][0]" value="0">
                            <input type="button" value="${delete1}" class="small btn_d delete_plan_detail" data-delete-id="${countDetail}" data-count="${self.countPlan}">
                        </th>
                        <td class="info_type_plan">
                            <select class="mb10 type_plan_name type_plan_name_${self.countPlan}_0 w-75"  name="type_plan_id[${self.countPlan}][]" data-id="${self.countPlan}" data-key="${self.countPlan}" data-key-detail="0">
                              <option  value="0">${defaultSelect}</option>
                            </select>
                            <textarea class="wide type_plan_description" name="plan_description[${self.countPlan}][]" name="plan_description[][]"></textarea><br>
                            <textarea hidden type="text" class="type_plan_content" name="plan_content[${self.countPlan}][]"></textarea>
                            <br>
                            <input type="button" value="${delete2}" class="btn_a small delete_type_plan_description">
                            <br>
                            <br>
                        </td>
                        <td class="center">
                              <select name="possibility_resolution[${self.countPlan}][]">
                                 <option value="1">◎</option>
                                 <option value="2">○</option>
                                 <option value="3">△</option>
                                 <option value="4">×</option>
                            </select>
                        </td>
                        <td class="info_file">
                            <span class="file_info_name white-space-pre-line">${uses}</span>
                            <input type="hidden" name="type_plan_doc_id[${self.countPlan}][][]" value="0">
                            <textarea class="wide file_info_description" name="doc_requirement_des[${self.countPlan}][][]"></textarea><br>
                            <input type="button" value="クリア" class="btn_a small delete_file_info_description " aria-invalid="false">
                            <input type="hidden" name="plan_detail_doc_id[${self.countPlan}][][]" value="0">
                        </td>
                        <td class="center">
                          <input type="checkbox" name="is_leave_all[${self.countPlan}_0]">
                        <input type="hidden" name="type_create[${self.countPlan}][0]"  value="1"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                    <p class="eol"><a href="javascript:;" class="add_plan_detail" data-count="${count}" >+ 方針案の追加</a></p>
                </div>

                <p><input type="button" class="btn_a delete_plan"  data-count="${count}" value="この対応策を削除"></p><hr>
            </div>`)
            self.AppendOption(reasons, '.plan_reasons', count, self.countPlan)
            self.AppendOption(mTypePlans, '.type_plan_name', 0)
            $(".plan_reasons_" + count).multipleSelect({
                selectAllText: checkAll,
                allSelected: checkAll,
                selectAll: false,
            });
            self.newReason[self.countPlan-1] = []
            if ($('#form').find('.table_plan').length >= 5) {
                $(this).css('display', 'none')
            } else {
                $(this).css('display', 'block')
            }
            count++;
        })
    }

    deletePlan() {
        $('body').on('click', '.delete_plan', function () {
            const selfDelete = this
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
                            $(selfDelete).closest('.parent_plan').remove()
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

    validateTypePlanDescription() {
        $('body').on('change, keyup', '.type_plan_description', function () {
            let value = $(this).val()
            $(this).closest('.info_type_plan').find('.error').remove();
            if (value.length > 1000) {
                $(this).after('<div class="error">' + errorCommonE026 + '</div>');
            }
        })
    }

    validateFilePlanDocDescription() {
        $('body').on('change, keyup', '.file_info_description', function () {
            let value = $(this).val()
            $(this).closest('.info_file').find('.error').remove();
            if (value.length > 1000) {
                $(this).after('<div class="error">' + errorCommonE026 + '</div>');
            }
        })
    }

    checkValidateRequireTypePlanName() {
        const typePlan = $('.type_plan_name');
        for (const item of typePlan) {
            let value = $(item).closest('.info_type_plan').find('select.type_plan_name').val()
            $(item).closest('.info_type_plan').find('.error').remove();
            if (+value === 0) {
                $(item).closest('.info_type_plan').find('select.type_plan_name').after('<div class="error">' + erorrespondenceA203E001 + '</div>')
            }
        }
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
                        <div class="notice error-required">${errorMessageRequired}<div>
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
                        <div class="notice error-required">${errorMessageRequired}<div>
                    `)
                }
            }
        }

        return $('#form').find('.notice, .error').length ? true : false
    }

    onSubmit() {
        const self = this
        $('body').on('click', '.submit, #submit', function (event) {

            const _self = this
            if ($(this)[0].id === 'submit') {
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
                self.checkValidateRequireTypePlanName()

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

            const countError = $('body').find('.error').length
            if (countError > 0) {
                document.querySelector('.error').parentNode.scrollIntoView({
                    behavior: 'smooth',
                });
                return false
            }

            let typePlanName = $($('body').find('.type_plan_name'))
            for (let keyTypePlanName = 0; keyTypePlanName < typePlanName.length; keyTypePlanName++) {
                let valueTypePlanName = +$(typePlanName[keyTypePlanName]).val()
                if ($(_self)[0].id === 'submit' && valueTypePlanName === 0) {
                    $.confirm({
                        title: '',
                        content: titleConfirmNoTypePlan,
                        buttons: {
                            cancel: {
                                text: BACK,
                                btnClass: 'btn-default',
                                action: function () {
                                }
                            },
                            ok: {
                                text: OK2,
                                btnClass: 'btn-blue',
                                action: function (e) {
                                    $('#input_submit').click()
                                }
                            }
                        }
                    });
                    return false;
                }
            }

            $('body').find('textarea.file_info_description').change()
            $('body').find('textarea.type_plan_description').change()
            $('body').find('.plan_comment').change()
            const valid = self.checkValidateRequireDoc()

            if (valid) {
                return
            }
            if (countError > 0) {
                document.querySelector('.error').parentNode.scrollIntoView({
                    behavior: 'smooth',
                });
                return false
            } else {
                $('#input_submit').click()
            }
        });
    }
}

new clsCreatePlan()
