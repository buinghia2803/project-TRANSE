var titleReason = reasonNo ? messagesTitle : '';
var titleReasonBranch = '';
var isChangeReason = false;
if (reasonNo && reasonNo.reason_branch_number > 0) {
    titleReasonBranch = '枝番は理由' + reasonNo.reason_branch_number  + 'で決定しますか。';
} else {
    titleReasonBranch = '枝番は枝番なしで決定しますか。';
}
class clsCreateReason {
    constructor() {
        $('body').off('change', 'select[name=m_nation_id]')
        const self = this
        this.initVariable()
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.init()

        if (reasonNo) {
            if(+$('.change_reason').val() === 0) {
                $('.open_modal_add_reason').attr('disabled', false)
            }
            $('.change_reason_branch').html('')
            for (let i = 0; i <= reasonNo.reason_number; i++) {
                if (i === 0) {
                    $('.change_reason_branch').append(`
                     <option value="${i}" ${reasonNo.reason_branch_number == i ?  'selected' : ''}>枝番なし</option>
                    `)
                } else {
                    $('.change_reason_branch').append(`
                    <option value="${i}" ${reasonNo.reason_branch_number == i ?  'selected' : ''}>理由 ${i}</option>
                `)
                }
            }
        }
    }


    // initial when load
    init() {
        const self = this
        this.clickFilePDF()
        this.changeNumberReason()
        this.changeNumberReasonBranch()
        this.openModalAddReason()
        this.openModalAddReasonBranch()
        this.addRowReason()
        this.addReferenceNumber()
        this.changeReferenceNumber()
        this.removeRow()
        this.createRowCode()
        this.removeRowKey()
        this.validateDate()
        this.validateReferenceNumber()
        this.validateCodeName()
        this.submit()
    }

    clickFilePDF() {
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    }

    changeNumberReason(reasonNo = null) {
        self = this
        if (reasonNo) {
            $(this).val(reasonNo.reason_number)
        }
        this.changeReason.on('change', function () {
            self.validateBranchReason()
            $(this).closest('#form').find('input[name="type_change"]').val(1)

            if (+$(this).val() !== 0) {
                $('.open_modal_add_reason').attr('disabled', false)
                titleReason = messagesTitle
            } else if (+$(this).val() === 0) {
                $('.open_modal_add_reason').attr('disabled', true)
            }
            $('.change_reason_branch').html('')

            for (let i = 0; i <= $(this).val(); i++) {
                if (i === 0) {
                    $('.change_reason_branch').append(`
                     <option value="${i}">枝番なし</option>
                    `)
                } else {
                    $('.change_reason_branch').append(`
                    <option value="${i}">理由 ${i}</option>
                `)
                }
            }

            self.isCheckSubmitWithLaw()
        })
    }

    changeNumberReasonBranch() {
        this.changeReasonBranch.on('change', function () {
            $(this).closest('#form').find('input[name="type_change"]').val(1)
            if (+$(this).val() !== 0) {
                $('.open_modal_add_reason_branch').prop('disabled', false)
                titleReasonBranch = '枝番は理由' + $(this).val() + 'で決定しますか。'
            } else if (+$(this).val() === 0) {
                titleReasonBranch = '枝番は枝番なしで決定しますか。'
            }

            self.isCheckSubmitWithLaw();
        })
    }

    openModalAddReason() {
        self = this
        let reasonNumber = $('.change_reason').val();

        self.openModalReason.on('click', function () {
            const selfOpenModal = this
            $.confirm({
                title: '',
                content: titleReason,
                buttons: {
                    cancel: {
                        text: CANCEL,
                        btnClass: 'btn-default',
                        action: function () {
                            $(selfOpenModal).closest('#form').find('.change_reason').val(reasonNumber)
                            self.isCheckSubmitWithLaw();
                        }
                    },
                    ok: {
                        text: OK,
                        btnClass: 'btn-blue',
                        action: function () {
                            $(selfOpenModal).closest('#form').find('input[name="type_change"]').val(1)
                            $(selfOpenModal).closest('#form').find('.change_reason_branch').val(0)

                            $('.reasons').html('')
                            $('.m_law_regulation').html('')

                            let countReason = $('.change_reason').val()
                            for (let i = 0; i < countReason; i++) {
                                $('.reasons').append(`<hr />
                                <dl class="w08em clearfix">
                                    <dt>理由</dt>
                                    <dd>
                                    <span class="reason_name_label">理由 ${i + 1}</span>
                                    <input type="hidden" name="reason_name[${i}]" value="理由 ${i + 1}">
                                    </dd>
                                    <dt>　法令</dt>
                                                <dd class="parent_law_regulation">
                                        <select name="m_laws_regulation_id[${i}]" data-i="${i}" class="m_law_regulation">
                                        </select>
                                    </dd>
                                </dl>`)
                            }
                            self.appendOptionMlawRegulation()

                            isChangeReason = true;
                            self.isCheckSubmitWithLaw();
                        }
                    }
                }
            });
        })
    }

    appendOptionMlawRegulation() {
        for (let i = 0; i < mLawsRegulation.length; i++) {
            $('.m_law_regulation').append(`<option value="${mLawsRegulation[i].id}">${mLawsRegulation[i].name}</option>`)
        }
    }

    getOptionMlawRegulation() {
        let option = '';

        for (let i = 0; i < mLawsRegulation.length; i++) {
            option += `<option value="${mLawsRegulation[i].id}">${mLawsRegulation[i].name}</option>`;
        }

        return option;
    }

    addRowReason() {
        let countReason = $('.change_reason').val()
        let count = 0;
        $('body').on('click', '.add_row_reason', function () {
            let dataI = $(this).data('i')
            $(this).before(`
                        <dt class="title_add_row_reason_${count}">　引例番号</dt>
                        <dd class="append_reference_number input_row_reason_${count}">
                                <input type="text" name="reference_number[${dataI}][]" class="reference_number"/>
                                <a class="delete note delete_row delete_row_${count}" href="javascript:;" data-delete-key="${count}" data-delete-count="${count}">×削除</a>
                        </dd><bt/>`)
            count++;
            const countRowReferenceNumber = $(this).closest('.clearfix').find('.reference_number').length;
            if (countRowReferenceNumber > 49) {
                $(this).css('display', 'none')
            }
        })
    }

    removeRow() {
        $('body').on('click', '.delete_row', function (e) {
            let deleteKey = $(this).data('delete-key')
            let count = $(this).data('delete-count')
            const countRowReferenceNumber = $(this).closest('.clearfix').find('.reference_number').length;
            if (countRowReferenceNumber <= 50) {
                $(this).closest('.clearfix').find('.add_row_reason').css('display', 'block')
            }
            $(this).closest('.append_reference_number').remove()
            $('.title_add_row_reason_' + count).remove()
            $('.input_row_reason_' + count).remove()
            $('.delete_row_' + count).remove()
        })
    }

    openModalAddReasonBranch() {
        self = this
        let reasonBranchNumber = $('.change_reason_branch').val();

        self.openModalReasonBranch.on('click', function () {
            const selfOpenModal = this
            $.alert({
                title: '',
                content: titleReasonBranch,
                buttons: {
                    cancel: {
                        text: CANCEL,
                        btnClass: 'btn-default',
                        action: function () {
                            $(selfOpenModal).closest('#form').find('.change_reason_branch').val(reasonBranchNumber)
                            self.isCheckSubmitWithLaw();
                        }
                    },
                    ok: {
                        text: OK,
                        btnClass: 'btn-blue',
                        action: function () {
                            $(selfOpenModal).closest('#form').find('input[name="type_change"]').val(1)
                            let changeReasonBranch = $('.change_reason_branch').val()

                            $.each($('input[name^=reason_name]'), function () {
                                let value = $(this).val();

                                if (value.indexOf('-1') != -1) {
                                    let valueReplace = value.replaceAll('-1', '');
                                    $(this).val(valueReplace);
                                    $(this).parent().find('.reason_name_label').text(valueReplace);
                                }

                                if (value.indexOf('-2') != -1) {
                                    $(this).closest('dl').prev().remove();
                                    $(this).closest('dl').remove();
                                }
                            });
                            $('input[name^=reason_id]').remove();

                            if (changeReasonBranch != 0) {
                                let key = Math.floor(Math.random() * (10000000 - 10000 + 1) + 10000);
                                let optionMlawRegulation = self.getOptionMlawRegulation();
                                let appendReasonHtml = `
                                    <hr/>
                                    <dl class="w08em clearfix">
                                        <dt>理由</dt>
                                        <dd>
                                            <span class="reason_name_label">理由 ${changeReasonBranch}-2</span>
                                            <input type="hidden" name="reason_name[${key}]" value="理由 ${changeReasonBranch}-2">
                                        </dd>
                                        <dt>　法令</dt>
                                        <dd class="parent_law_regulation">
                                            <select name="m_laws_regulation_id[${key}]" data-i="${key}" class="m_law_regulation">
                                                ${optionMlawRegulation}
                                            </select>
                                        </dd>
                                    </dl>
                                `;

                                let reasonFirstBox = $('.reasons').find('dl').eq(changeReasonBranch - 1);
                                let firstReasonLabel = `理由 ${changeReasonBranch}-1`;

                                reasonFirstBox.find('[name^=reason_name]').parent().find('.reason_name_label').text(firstReasonLabel);
                                reasonFirstBox.find('[name^=reason_name]').val(firstReasonLabel);

                                reasonFirstBox.after(`${appendReasonHtml}`);
                            }

                            isChangeReason = true;
                            self.isCheckSubmitWithLaw();
                        }
                    }
                }
            });
        })
    }
    addReferenceNumber() {
        $('body').on('change', '.m_law_regulation', function () {
            let dataI = $(this).data('i');
            $(this).closest('#form').find('input[name="type_change"]').val(1)
            if (+$(this).val() === 4 || +$(this).val() === 5) {
                $(this).closest('.clearfix').find('.append_reference_number').remove()
                $(this).closest('.clearfix').find('.add_row_reason').remove()
                $(this).closest('.clearfix').find('.title_reference_number').remove()
                $(this).closest('.parent_law_regulation').after(`
            <dt class="title_reference_number">　引例番号</dt>
            <dd class="append_reference_number"><input type="text" name="reference_number[${dataI}][]" class="reference_number"/></dd>
            <a href="javascript:;" class="add_row_reason add_row_reason_${dataI}" data-i="${dataI}">引例追加 +</a><br /></dd>`)
            } else {
                $(this).closest('.clearfix').find('.append_reference_number').remove()
                $(this).closest('.clearfix').find('.add_row_reason').remove()
                $(this).closest('.clearfix').find('.title_reference_number').remove()
            }

        })
    }

    changeReferenceNumber() {
        $('body').on('change', '.reference_number', function () {
            $(this).closest('#form').find('input[name="type_change"]').val(1)
        })
    }

    createRowCode() {
        let count = 0;
        $('body').on('click', '.create_row_code', function () {

            let productKey = $(this).data('product-key')
            let dataProductId = $(this).data('product-id')
            let productType = $(this).data('product-type')
            $(this).before(`
                <div class="abc">
                    <input type="text" value="" class="em06 code_name input_code_${count}" name="code_name[${productKey}][]" data-count="${count}">
                     <a class="delete delete_row_code delete_row_code_${count}" data-delete-count="${count}" href="javascript:;" >×<br/></a>
                      <input type="hidden" name="product_id[${productKey}]]" value="${dataProductId}">
                     <input type="hidden" name="product_type[${productKey}][]" value="${productType}"
                 </div>
            `)
            count++;
            const countRowReferenceNumber = $(this).closest('.parent_code').find('.code_name').length;
            if (countRowReferenceNumber > 49) {
                $(this).css('display', 'none')
            }
        })
    }

    removeRowKey() {
        $('body').on('click', '.delete_row_code', function (e) {
            let deleteKey = $(this).data('delete-key')
            let count = $(this).data('delete-count')
            $(this).closest('div.abc').remove();
            const countRowReferenceNumber = $(this).closest('.parent_code').find('.code_name').length;
            if (countRowReferenceNumber <= 50) {
                $('.create_row_code').css('display', 'block')
            }
        })
    }

    initVariable() {
        this.changeReason = $('.change_reason');
        this.changeReasonBranch = $('.change_reason_branch');
        this.openModalReason = $('.open_modal_add_reason');
        this.openModalReasonBranch = $('.open_modal_add_reason_branch')
        this.titleReason = ''
    }
    validateReferenceNumber() {
        let regex = /^[a-zA-Z0-9]+$/;
        $('body').on('change keyup', 'input.reference_number', function () {
            let count = $(this).data('count')
            let value = $(this).val();
            $(this).closest('.append_reference_number').find('.error').remove();
            if (!value.length) {
                $(this).closest('.append_reference_number').append('<div class="error">' + errorMessageRequired + '</div>');
            } else if (!regex.test(value)) {
                $(this).closest('.append_reference_number').append('<div class="error">' + errorMessageFullWidth + '</div>');
            } else if (value.length > 255) {
                $(this).closest('.append_reference_number').append('<div class="error">' + errorMessageFullWidth + '</div>');
            }

        })
    }
    validateCodeName() {
        $('body').on('change keyup', 'input.code_name', function () {
            let count = $(this).data('count')
            let value = $(this).val();
            $(this).closest('div.abc').find('.error').remove();
            if (!value.length) {
                $(this).closest('div.abc').append('<div class="error">' + errorMessageRequired + '</div>');
            } else {
                if(!isValidProdCode(value)) {
                    $(this).closest('div.abc').append('<div class="error">' + errorCodeIsNotValid + '</div>');
                }
            }
        })
    }

    validateDate() {
        $('body').on('change', '.response_deadline', function () {
            let now = new Date();
            let dateNow = new Date(now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate())

            let responseDeadlineNow = new Date($(this).val())
            responseDeadlineNow = new Date(responseDeadlineNow.getFullYear() + '-' + (responseDeadlineNow.getMonth() + 1) + '-' + responseDeadlineNow.getDate())

            let responseDeadline2 = new Date(responseDeadline)
            responseDeadline2 = new Date(responseDeadline2.getFullYear() + '-' + (responseDeadline2.getMonth() + 1) + '-' + responseDeadline2.getDate())

            $(this).closest('.parent_response_deadline').find('.error').remove();
            if (responseDeadlineNow < dateNow) {
                $(this).after('<div class="error">' + errorCommonE38 + '</div>');
            } else if (responseDeadlineNow > responseDeadline2) {
                if (planCorrespondence.type == planCorrespondenceType2) {
                    $(this).after('<div class="error">' + messTime + '</div>');
                } else {
                    $(this).after('<div class="error">' + errorCommonE38 + '</div>');
                }
            }
        })
        $('.response_deadline').change();
    }

    validateBranchReason() {
        $('.change_reason').closest('.clearfix').find('.error').remove();
        if($('.change_reason').val() <= 0) {
            $('.change_reason').closest('.parent_change_reason').after('<div class="error">' + errorCommonE025 + '</div>');
        } else {
            $('.change_reason').closest('.clearfix').find('.error').remove();
        }
    }

    isCheckSubmitWithLaw() {
        let mLawRegulationBox = $('select.m_law_regulation');
        let changeReason = $('.change_reason').val();
        let changeReasonBranch = $('.change_reason_branch').val();

        let totalReason = parseInt(changeReason);
        if (changeReasonBranch != 0) {
            totalReason++;
        }

        let reasonSecond = null;
        $.each($('input[name^=reason_name]'), function () {
            let value = $(this).val();

            if (value.indexOf('-2') != -1) {
                let reason = value.replace('理由 ', '');
                reason = reason.replace('-2', '');
                reasonSecond = reason;
            }
        });

        $('.change_reason_branch').closest('.clearfix').next('.error').remove();
        if (totalReason != mLawRegulationBox.length
            || reasonSecond != null && reasonSecond != changeReasonBranch) {
            $('.change_reason_branch').closest('.clearfix').after('<div class="error">' + warningDontSubmit + '</div>');
        }
    }

    submit() {
        const _self = this
        $('body').on('click', 'input[type=submit],button[type=submit]', function (e) {
            let el = $(this);

            $('body').find('input.code_name').change()
            $('body').find('input.reference_number').change()
            $('body').find('input.response_deadline').change()

            _self.validateBranchReason();
            _self.isCheckSubmitWithLaw();

            let form = $('#form');
            form.valid();
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length > 0) {
                let firstError = hasError.first();
                scrollToElement(firstError, -100);
                return false;
            }

            // Check duplicate m_law_regulation
            let mLawRegulations = $('select.m_law_regulation').map(function() {
                return $(this).val();
            }).get();
            let uniqueArr = mLawRegulations.filter(function(value, index, self) {
                return self.indexOf(value) === index;
            });

            if (mLawRegulations.length != uniqueArr.length) {
                $.confirm({
                    title: '',
                    content: warningDuplicateLawRegulations,
                    buttons: {
                        cancel: {
                            text: CANCEL,
                            btnClass: 'btn-default',
                            action: function () {}
                        },
                        ok: {
                            text: OK,
                            btnClass: 'btn-blue',
                            action: function () {
                                if (reasonNo != null && isChangeReason == true) {
                                    $.confirm({
                                        title: '',
                                        content: warningChangeReason,
                                        buttons: {
                                            cancel: {
                                                text: CANCEL,
                                                btnClass: 'btn-default',
                                                action: function () {}
                                            },
                                            ok: {
                                                text: OK,
                                                btnClass: 'btn-blue',
                                                action: function () {
                                                    el.closest('form').submit();
                                                }
                                            }
                                        }
                                    });
                                } else {
                                    el.closest('form').submit();
                                }
                            }
                        }
                    }
                });

                return false;
            }

            if (reasonNo != null && isChangeReason == true) {
                $.confirm({
                    title: '',
                    content: warningChangeReason,
                    buttons: {
                        cancel: {
                            text: CANCEL,
                            btnClass: 'btn-default',
                            action: function () {}
                        },
                        ok: {
                            text: OK,
                            btnClass: 'btn-blue',
                            action: function () {
                                el.closest('form').submit();
                            }
                        }
                    }
                });

                return false;
            }
        });
    }
}

new clsCreateReason()
