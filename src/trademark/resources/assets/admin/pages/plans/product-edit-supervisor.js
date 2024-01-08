class PlanProductEditSupervisor {
    constructor() {
        this.initValidate();
        this.changePlanConfirm();
        this.showAllCode();
        this.clickEditCode();
        this.clickDeleteRow();
        this.clickAddProduct();
        this.clickAddDistinct();
        this.onChangeProductName();
        this.onChangeProductCode();
        this.validateDistinct()
        this.onClickSubmit();

        // Action Copy
        this.clickEditPlan();
        this.clickDecision();
        this.clickDecisionEdit();
        this.showHideStep3();
        this.showStep();
    }

    initValidate() {
        // common203Rule, common203Message is constant in file common
        this.rules = {
            'content': {
                maxlength: 1000,
            },
        }
        this.rules = {...common203Rule, ...this.rules};

        this.messages = {
            'content': {
                maxlength: errorMessageMaxLength1000,
            },
        }
        this.messages = {...common203Message, ...this.messages};

        new clsValidation('#form', {rules: this.rules, messages: this.messages})
    }

    uniqueID() {
        return Math.floor(Math.random() * (10000000 - 1 + 1) + 1);
    }

    changePlanConfirm() {
        $('body').on('change', '[data-plan_confirm]', function (e) {
            e.preventDefault();
            let planId = $(this).data('plan_confirm');

            if ($(this).prop('checked')) {
                $('[data-is_plan_confirm='+planId+']').addClass('none-event');
            } else {
                $('[data-is_plan_confirm='+planId+']').removeClass('none-event');
            }
        })
        $('[data-plan_confirm]').change();
    }

    showAllCode() {
        $('body').on('click', '.show_all_code', function (e) {
            e.preventDefault();

            $(this).closest('.code-block').find('.hidden').removeClass('hidden');
            $(this).remove();
        })
    }

    handleRow(row, action) {
        let inputDelete = $('input[name=delete_plan_detail_product_ids]');
        let inputDeleteValue = inputDelete.val();
        let deleteIds = inputDeleteValue.length > 0 ? inputDeleteValue.split(',') : [];

        let inputRestore = $('input[name=restore_plan_detail_product_ids]');
        let inputRestoreValue = inputRestore.val();
        let restoreIds = inputRestoreValue.length > 0 ? inputRestoreValue.split(',') : [];

        let planDetail = $('tr[data-row=' + row + ']').find('td[data-plan_detail_product_id]');
        $.each(planDetail, function (index, item) {
            let planDetailProductID = $(this).data('plan_detail_product_id');

            if (action == 'delete_row') {
                if (!inArray(planDetailProductID, deleteIds)) {
                    deleteIds.push(planDetailProductID);

                    restoreIds = $.grep(restoreIds, function(value) {
                        return value != planDetailProductID;
                    });
                }
            } else if (action == 'restore_row') {
                if (!inArray(planDetailProductID, restoreIds)) {
                    restoreIds.push(planDetailProductID)

                    deleteIds = $.grep(deleteIds, function(value) {
                        return value != planDetailProductID;
                    });
                }
            }
        });

        deleteIds = deleteIds.join(',');
        inputDelete.val(deleteIds);

        restoreIds = restoreIds.join(',');
        inputRestore.val(restoreIds);
    }

    clickDeleteRow() {
        self = $(this);

        $('body').on('click', '[data-delete_row]', function (e) {
            e.preventDefault();
            let trEl = $(this).closest('tr');
            let row = trEl.data('row');

            self.handleRow(row, 'delete_row');

            if (trEl.hasClass('is-manager')) {
                $('tr[data-row=' + row + ']').find('td').removeClass('bg_yellow').addClass('bg_gray');
                $(this).val(LABEL_RESTORE);
                $(this).removeAttr('data-delete_row');
                $(this).attr('data-restore_row', '');
            } else {
                $('tr[data-row=' + row + ']').remove();
            }

            self.showHideStep3();
        });

        $('body').on('click', '[data-restore_row]', function (e) {
            let trEl = $(this).closest('tr');
            let row = trEl.data('row');

            self.handleRow(row, 'restore_row');

            if (trEl.hasClass('is-manager')) {
                $('tr[data-row=' + row + ']').find('td').removeClass('bg_gray').addClass('bg_yellow');
                $(this).val(LABEL_DELETE_ALL);
                $(this).removeAttr('data-restore_row');
                $(this).attr('data-delete_row', '');
            }

            self.showHideStep3()
        });
    }

    clickEditCode() {
        $('body').on('click', '[data-edit_code]', function (e) {
            e.preventDefault();

            $(this).closest('.step_2').find('.show_all_code').click();
            $(this).closest('td').find('[data-input_product_code]').removeClass('hidden');

            $(this).closest('td').find('[data-type=3]').remove();
            $(this).closest('td').find('[data-type=4]').remove();

            $(this).remove();
        });
    }

    clickAddProduct() {
        self = this;

        $('body').on('click', '[data-add_product]', function (e) {
            e.preventDefault();

            let info = $(this).data('info');
            let rowID = self.uniqueID();
            let rowHTML = productHTML;

            rowHTML = rowHTML.replaceAll('{rowID}', rowID);
            rowHTML = rowHTML.replaceAll('{distinctionName}', info.distinction.name);
            rowHTML = rowHTML.replaceAll('{planDetailDistinctionID}', info.distinction.plan_detail_distinction);
            rowHTML = rowHTML.replaceAll('{distinctionID}', info.distinction.id);
            rowHTML = rowHTML.replaceAll('{index}', rowID);
            rowHTML = rowHTML.replaceAll('{productName}', info.distinction.product_name);

            $(this).closest('tr').after(rowHTML);

            $('[data-plan_confirm]').prop('checked', false).change();
            $('input[name=is_decision]').val(0);
            $('.step_3').addClass('hidden');
        });
    }

    clickAddDistinct() {
        self = this;

        $('body').on('click', '[data-add_distinct]', function (e) {
            e.preventDefault();

            let info = $(this).data('info');
            let rowID = self.uniqueID();
            let rowHTML = distinctHTML;

            rowHTML = rowHTML.replaceAll('{rowID}', rowID);
            rowHTML = rowHTML.replaceAll('{index}', rowID);
            rowHTML = rowHTML.replaceAll('{productName}', info.distinction.product_name);

            $(this).closest('tbody').append(rowHTML);

            // Scroll to new Distinct
            let firstEL = $('tr[data-row=' + rowID + ']').first();
            window.scroll({
                top: firstEL.offset().top - 100,
                behavior: 'smooth'
            });

            $('[data-plan_confirm]').prop('checked', false).change();
            $('input[name=is_decision]').val(0);
            $('.step_3').addClass('hidden');
        });
    }

    onChangeProductName() {
        $('body').on('change keyup', '[data-input_product_name]:visible', function (e) {
            e.preventDefault();
            let value = $(this).val();
            let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜－]+$/;

            $(this).parent().find('.error').remove();
            if (value.length == 0) {
                $(this).after(`<div class="error mt-0">${errorMessageRequired}</div>`);
            } else if (value.length > 200) {
                $(this).after(`<div class="error mt-0">${errorMessageMaxLength200}</div>`);
            } else if (!regex.test(value)) {
                $(this).after(`<div class="error mt-0">${errorMessageFormat}</div>`);
            }
        })
    }

    onChangeProductCode() {
        $('body').on('change keyup', '[data-input_product_code]:visible', function (e) {
            e.preventDefault();
            let el = $(this);

            let value = $(this).val();
            value = value.replaceAll('  ', ' ');
            $(this).val(value);

            $(this).parent().find('.error').remove();

            let valueArray = value.split(" ");
            $.each(valueArray, function (index, item) {
                if (item.length == 0) {
                    el.after(`<div class="error mt-0">${errorMessageRequired}</div>`);
                    return false;
                } else if (!isValidProdCode(item)) {
                    el.after(`<div class="error mt-0">${errorMessageFormatCode}</div>`);
                    return false;
                }
            });
        });
    }

    onClickSubmit() {
        self = this;
        $('body').on('click', 'button[type=submit], input[type=submit]', function (e) {
            const form = $('#form');
            let value = $(this).val();
            let name = $(this).attr('name');

            $('[data-input_product_name]:visible').change();
            $('[data-input_product_code]:visible').change();
            $('[data-select_distinct]:visible').change();
            $('#datepicker').change();

            if (name == SUBMIT && value != DRAFT) {
                let isDecision = $('input[name=is_decision]').val();
                if (isDecision == 0) {
                    $.alert({
                        title: '',
                        scrollToPreviousElement: false,
                        content: errorMessageNotDecision
                    });
                    self.scrollTo($('[data-decision]:first').closest('table'), 10);
                    return false;
                }

                let allConfirm = $('[data-plan_confirm]').length;
                let allConfirmChecked = $('[data-plan_confirm]:checked').length;
                if (allConfirm != allConfirmChecked) {
                    $.alert({
                        title: '',
                        scrollToPreviousElement: false,
                        content: errorMessageNotConfirm
                    });
                    self.scrollTo($('[data-plan_confirm]:first').closest('table'), 10);
                    return false;
                }
            }

            let has_error = form.find('.notice:visible,.error:visible,.error-validate:visible');
            if (has_error.length == 0 && form.valid() || name == DRAFT) {
                form.submit();
            } else {
                e.preventDefault();

                let firstError = has_error.first();
                self.scrollTo(firstError, 100)
            }
        });
    }

    scrollTo(element, padding = 10) {
        window.scroll({
            top: element.offset().top - padding,
            behavior: 'smooth'
        });
    }

    clickEditPlan() {
        self = this;

        $('body').on('click', '[data-edit_plan]', function (e) {
            e.preventDefault();

            let items = $(this).closest('table').find('tr.item').not('.item-add');
            $('input[name=is_edit_plan]').val(1);
            $('.step_2').removeClass('hidden');

            self.handleEditPlan(items);
        });
    }

    handleEditPlan(items) {
        $.each(items, function (index, item) {
            let step2 = $(item).find('[data-step_2]');

            $.each(step2, function (index, step2Item) {
                let el = $(step2Item);
                let step1Name = el.data('step_2');

                let step1Value = $(this).closest('tr').find('[data-step_1=' + step1Name + ']').val();

                switch (step1Name) {
                    case 'code_name_fix':
                        if ($(this).closest('.item').hasClass('is-user')) {
                            let codeData = $(this).closest('tr').find('[data-step_1=code_data]').val();
                            let prodID = $(this).closest('tr').find('[data-step_1=code_data]').data('id');
                            let step2Box = $(this).closest('.step_2');
                            let isChoice = $(this).closest('tr').data('is_choice');

                            codeData = JSON.parse(codeData);
                            let htmlAddCodeName = self.htmlAddCodeName(codeData, prodID);
                            step2Box.html(htmlAddCodeName);

                            if (isChoice == 0) {
                                step2Box.find('[data-edit_code]').remove();
                            }
                        } else {
                            $(this).val(step1Value).change();
                        }
                        break;
                    case 'product_name':
                    case 'rank':
                        $(this).val(step1Value).change();
                        $(this).closest('.step_2').find('span').html(step1Value);
                        break;
                    default:
                        $(this).val(step1Value).change();
                        break;
                }
            });
        });
    }

    htmlAddCodeName(codeData, prodID) {
        let codeHtml = ``;
        let codeFix = [];
        let codeEdit = [];
        $.each(codeData, function (index, item) {
            codeHtml += `
                <span data-type="${item.type}" class="${(index > 2 ? 'hidden' : '')}">
                    ${item.name}
                    ${codeData.length > 2 && index == 2 ? '<span class="show_all_code cursor-pointer">+</span>' : ''}
                </span>
            `;
            if (item.type == 1 || item.type == 2) {
                codeFix.push(item.name);
            } else {
                codeEdit.push(item.name);
            }
        });

        let htmlEdit = '';
        if (codeEdit.length > 0) {
            htmlEdit = `
                <input type="button" data-edit_code value="${LABEL_EDIT}" class="btn_a small">
                <textarea data-step_2="code_name" data-input_product_code name="update_products[${prodID}][code_name_edit]" class="wide w-100 hidden">${codeEdit.join(' ')}</textarea>
            `;
        }

        let html = `
            <div class="code-block">${codeHtml}</div>
            <div class="code-edit mt-1">
                <input data-step_2="code_name_fix" type="hidden" name="update_products[${prodID}][code_name_edit_fix]" value="${codeFix.join(' ')}">
                ${htmlEdit}
            </div>
        `;
        return html;
    }

    clickDecision() {
        self = this;

        $('body').on('click', '[data-decision]', function (e) {
            e.preventDefault();
            let el = $(this);

            let itemSupervisor = $(this).closest('table').find('tr.is-supervisor');
            if (itemSupervisor.length > 0) {
                $.confirm({
                    title: '',
                    content: MESSAGE_DELETE_IS_SUPERVISOR,
                    buttons: {
                        cancel: {
                            text: CANCEL,
                            action: function action() {}
                        },
                        confirm: {
                            text: OK,
                            btnClass: 'btn-blue',
                            action: function action() {
                                $.each(itemSupervisor, function () {
                                    $(this).find('[data-delete_row]').click();
                                })

                                self.handleDecision(el);
                                self.showHideStep3();
                            }
                        }
                    }
                });
            } else {
                self.handleDecision(el);
                self.showHideStep3();
            }
        });
    }

    showHideStep3() {
        let isDecision = $('input[name=is_decision]').val();

        if (isDecision == DECISION_DRAFT || isDecision == DECISION_EDIT) {
            $('.step_3').removeClass('hidden');
        }

        let items = $('#product_detail_table').find('tr.item');
        $.each(items, function (index, item) {
            $(this).find('.bg_gray').find('.step_3').addClass('hidden');
        });
    }

    handleDecision(element) {
        let items = $(element).closest('table').find('tr.item');
        $('input[name=is_decision]').val(DECISION_DRAFT);

        $.each(items, function (index, item) {
            let step3 = $(item).find('[data-step_3]');

            $.each(step3, function (index, step3Item) {
                let el = $(step3Item);
                let step1Name = el.data('step_3');

                let step1Value = $(this).closest('tr').find('[data-step_1=' + step1Name + ']').val();

                $(this).closest('tr').find('[data-finish=' + step1Name + ']').val(step1Value);

                switch (step1Name) {
                    case 'm_distinction_id':
                        step1Value = ALL_DISTINCTION[step1Value] ?? step1Value;
                        $(this).html(step1Value);
                        break;
                    case 'product_name':
                    case 'rank':
                    case 'code_name':
                        $(this).html(step1Value);
                        break;
                    default:
                        if (step1Value == '') {
                            $(this).html('-');
                        } else {
                            $(this).html(OPTION_LEAVE_STATUS[step1Value] ?? step1Value);
                        }
                        break;
                }
            });
        });
    }

    clickDecisionEdit() {
        self = this;

        $('body').on('click', '[data-decision_edit]', function (e) {
            e.preventDefault();

            let isEditPlan = $('input[name=is_edit_plan]').val();
            if (isEditPlan == 0) {
                return false;
            }

            $('[data-input_product_name]:visible').change();
            $('[data-input_product_code]:visible').change();

            let has_error = $(this).closest('table').find('.notice:visible,.error:visible');
            if (has_error.length > 0) {
                return false;
            }

            let items = $(this).closest('table').find('tr.item');
            $('input[name=is_decision]').val(DECISION_EDIT);

            $.each(items, function (index, item) {
                let step3 = $(item).find('[data-step_3]');

                $.each(step3, function (index, step3Item) {
                    let el = $(step3Item);
                    let step2Name = el.data('step_3');

                    let step2Value = $(this).closest('tr').find('[data-step_2=' + step2Name + ']').val();

                    $(this).closest('tr').find('[data-finish=' + step2Name + ']').val(step2Value);

                    switch (step2Name) {
                        case 'm_distinction_id':
                            step2Value = ALL_DISTINCTION[step2Value] ?? step2Value;
                            $(this).html(step2Value);
                            break;
                        case 'product_name':
                        case 'rank':
                            $(this).html(step2Value);
                            break;
                        case 'code_name':
                            let codeNameFix = $(this).closest('tr').find('[data-step_2=code_name_fix]').val();

                            let codeNameStr = '';
                            if (codeNameFix != undefined) {
                                codeNameStr += codeNameFix + ' ';
                            }

                            if (step2Value != undefined) {
                                codeNameStr += step2Value + ' ';
                            }

                            $(this).closest('tr').find('[data-finish=' + step2Name + ']').val(codeNameStr);
                            $(this).html(codeNameStr);
                            break;
                        default:
                            if (step2Value == '') {
                                $(this).html('-');
                            } else {
                                $(this).html(OPTION_LEAVE_STATUS[step2Value] ?? step2Value);
                            }
                            break;
                    }
                });
            });

            self.showHideStep3()
        });
    }

    validateDistinct() {
        $('body').on('change', '[data-select_distinct]', function (e) {
            e.preventDefault();
            let value = $(this).val();
            $(this).parent().find('.error').remove();
            if (value === '') {
                $(this).after(`<div class="error mt-0 mb-0">${errorMessageRequired}</div>`);
            }
        })
    }

    showStep() {
        self = this;

        let itemUser = $('tr.is-user');
        let itemManager = $('tr.is-manager');
        let itemSupervisor = $('tr.is-supervisor');

        itemSupervisor.find('.step_1').addClass('hidden');

        let isEditPlan = $('input[name=is_edit_plan]').val();
        if (isEditPlan == false) {
            itemUser.find('.step_2').addClass('hidden');
            itemManager.find('.step_2').addClass('hidden');

            self.handleEditPlan(itemSupervisor.not('.has-edit'));
        }

        let isDecision = $('input[name=is_decision]').val();
        if (isDecision == 0) {
            $('.step_3').addClass('hidden');
        }
    }
}

new PlanProductEditSupervisor;
