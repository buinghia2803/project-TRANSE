class Supervisor {
    constructor() {
        const self = this
        self.doLoad();
    }

    /**
    * Do load all action and element.
    */
    doLoad() {
        this.init()
        this.isConfirmBlock()
        this.initValidate()
        this.defaultDisableQuestionEdit()
        this.clickAppend()
        this.changeClickAppend()
        this.clickQuestionStatus()
        this.changeQuestionEdit()
        this.validateQuestion()
        this.isValidDate()
        this.checkQuestion()
        this.deleteRowQuestion()
        this.copyAllQuestionToEdit()
        this.copyAllQuestionToDecision()
        this.copyAllQuestionEditToDecision()
        this.copyQuestionToEdit()
        this.copyQuestionToDecision()
        this.copyEditToDecision()
        this.submit()
    }

    /**
     * Init validate
     */
     initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        this.rules = {
            content: {
                required: false,
                maxlength: 500,
            }
        }
        this.messages = {
            content: {
                required: Common_E001,
                maxlength: Common_E046,
            }
        }

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    validateQuestion() {
        $('body').on('change', '.input_question_decision', function() {
            if (!$('input[name=question_status]').is(':checked')) {
                let value = $(this).val();
                $(this).parent().find('.error').remove();

                if(value.length == 0) {
                    $(this).after('<div class="error">'+ Common_E001 +'</div>');
                }
            }
        });
        $('body').on('change', '.checkQuestion', function() {
            if (!$('input[name=question_status]').is(':checked')) {
                let value = $(this).is(':checked');
                $(this).parent().find('.error').remove();

                if(!value) {
                    $(this).closest('.center').append('<div class="error error-checkQuestion">'+ Common_E025 +'</div>');
                }
            }
        });
    }

    init() {
        if ($('.checkQuestion:checked').length == $('.checkQuestion').length) {
            $('#checkAllQuestion').prop('checked', true)
        } else {
            $('#checkAllQuestion').prop('checked', false)
        }

        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })

        $('#datepicker').datepicker({
            minDate: new Date(),
            maxDate: new Date(comparisonTrademarkResultResponseDeadline),
            dateFormat: 'yy年mm月dd日',
            showMonthAfterYear: true,
            yearSuffix: '年',
            dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
            monthNames: ['1月','２月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
        });

        let date;

        if (userResponseDeadline) {
            date = new Date(userResponseDeadline);
        } else {
            date = new Date();
            date.setDate(date.getDate() + 2);
        }

        $("#datepicker").prop("defaultValue", this.convert(date));
    }

    convert(date) {
        var date = new Date(date),
        mnth = ("0" + (date.getMonth() + 1)).slice(-2),
        day = ("0" + date.getDate()).slice(-2);

        return [date.getFullYear() + '年' + mnth + '月' + day + '日'];
    }

    replaceDate(date, nation) {
        if (nation == 'ja') {
            date = date.replace('年', '-')
            date = date.replace('月', '-')
            date = date.replace('日', '')

            return date
        } else if (nation == 'en') {
            date = date.replace('/', '-')

            return date
        }
    }

    formatDate(date) {
        // Get year, month, and day part from the date
        var year = date.toLocaleString("default", { year: "numeric" });
        var month = date.toLocaleString("default", { month: "2-digit" });
        var day = date.toLocaleString("default", { day: "2-digit" });

        // Generate yyyy-mm-dd date string
        return year + month + day;
    }


    isValidDate() {
        let self = this
        $('body').on('change', '#datepicker', function(e) {
            $('body').find('.notice-scrollable').remove();

            var dateString = $(this).val()
            var regEx = /^\d{4}年\d{2}月\d{2}日$/;

            if(!dateString.match(regEx)) {
                $('.js-scrollable').append('<dt class="notice-scrollable"></dt><dd class="error mb15 notice-scrollable">間違ったフォーマット</dd>')
                e.stopPropagation();
                e.preventDefault();

                return
            };

            const changeNow = self.formatDate(new Date())
            const changeDatepickerChoose = self.formatDate(new Date(self.replaceDate($(this).val(), 'ja')));
            const changeResponsedeadline = self.formatDate(new Date(comparisonTrademarkResultResponseDeadline));

            if (changeDatepickerChoose < changeNow || changeDatepickerChoose > changeResponsedeadline) {
                $("#datepicker").closest('.js-scrollable').append('<dt class="notice-scrollable"></dt><dd class="error red notice-scrollable">' + Common_E038 + '</dd>')
            }
        })
    }

    defaultDisableQuestionEdit() {
        $('#form').find('.checkQuestion').each(function (idx, item) {
            const element = item.closest('.tr_reason_question_detail')
            $(element).find('textarea.question_edit').attr('disabled', true).addClass('disabled');
        })
    }

    changeQuestionEdit () {
        $('body').on('change', '.question_edit', function () {
            const questionEdit = $(this).val();
            $(this).parent().find('.error').remove();

            if (questionEdit.length > 500) {
                $(this).after('<div class="error">'+ Common_E024 +'</div>');
            }

            const questionEditHidden = $(this).closest('.tr_reason_question_detail').find('.question_edit_hidden')
                questionEditHidden.val(questionEdit)
                questionEditHidden.attr('value', questionEdit)
        })
    }

    copyAllQuestionToEdit() {
        $('body').on('click', '#copyAllQuestionToFix', function() {
            $('.item_question').each(function(idx, item) {
                const question = item.outerText
                const element = $(item).closest('.tr_reason_question_detail').find('textarea.question_edit');
                if (!$(item).closest('.tr_reason_question_detail').find('.checkQuestion').is(":checked")) {
                    element.attr('disabled', false).removeClass('disabled');
                    element.val(question)
                    element.attr('value', question)

                    const questionEditHidden = $(item).closest('.tr_reason_question_detail').find('.question_edit_hidden')
                    questionEditHidden.val(question)
                    questionEditHidden.attr('value', question)
                }
            })
        })
    }

    copyAllQuestionToDecision() {
        $('body').on('click', '#copyAllQuestionToDecision', function() {
            $('.input_question_decision').parent().find('.error').remove();
            $('.item_question').each(function(idx, item) {
                if (!$(item).closest('.tr_reason_question_detail').find('.checkQuestion').is(":checked")) {
                    const question = item.outerText
                    const element = $(item).closest('.tr_reason_question_detail');
                    element.find('.question_decision').text(question)
                    element.find('input.input_question_decision').val(question)
                }
            })
        })
    }

    copyAllQuestionEditToDecision() {
        $('body').on('click', '#copyAllQuestionEditToDecision', function() {
            $('.input_question_decision').parent().find('.error').remove();
            $('.question_edit').each(function(idx, item) {
                const question = $(item).val()
                const element = $(item).closest('.tr_reason_question_detail');
                if (question.length <= 500) {
                    if (!element.find('.checkQuestion').is(":checked")) {
                        element.find('.question_decision').text(question)
                        element.find('input.input_question_decision').val(question)
                    }
                }
            })
        })
    }

    copyQuestionToEdit() {
        $('body').on('click', '.copyQuestionToEdit', function() {
            const question = $(this).closest('.td_item_question').find('.item_question')[0].outerText
            const element = $(this).closest('.tr_reason_question_detail').find('textarea.question_edit');
                element.attr('disabled', false).removeClass('disabled');
                element.val(question)
                element.attr('value', question)

            const questionEditHidden = $(this).closest('.tr_reason_question_detail').find('.question_edit_hidden')
                questionEditHidden.val(question)
                questionEditHidden.attr('value', question)
        })
    }

    copyQuestionToDecision() {
        $('body').on('click', '.copyQuestionToDecision', function() {
            const question = $(this).closest('.td_item_question').find('.item_question')[0].outerText
            const element = $(this).closest('.tr_reason_question_detail')
                element.find('.input_question_decision').parent().find('.error').remove();
                element.find('span.question_decision').text(question);
                element.find('input.input_question_decision').val(question);
        })
    }

    checkQuestion() {
        const self = this
        $('body').on('click', '#checkAllQuestion', function() {
            $('.error-checkQuestion').remove()
            if ($(this).is(':checked')) {
                $('.checkQuestion').prop('checked', true)
            } else {
                $('.checkQuestion').prop('checked', false)
            }
            self.isConfirmBlock();
        })
        $('body').on('click', '.checkQuestion', function() {
            if ($('.checkQuestion:checked').length == $('.checkQuestion').length) {
                $('#checkAllQuestion').prop('checked', true)
            } else {
                $('#checkAllQuestion').prop('checked', false)
            }
            self.block(this);
        })
    }

    isConfirmBlock() {
        const self = this
        $('#form').find('.checkQuestion').each(function (idx, item) {
            self.block(item);
        })
    }

    block(checkbox) {
        const element = checkbox.closest('.tr_reason_question_detail')
        if ($(checkbox).is(':checked')) {
            if (!checkIsConfirm) {
                $(element).find('input, textarea, select, button[type=button], input[type=button]').prop('disabled', true).addClass('disabled')
                $(element).find('input[type=checkbox]').prop('disabled', false).removeClass('disabled')
            }
            $(element).find('input[type=hidden]').prop('disabled', false);
        } else {
            if (!checkIsConfirm) {
                $(element).find('input, textarea, select, button[type=button], input[type=button]').prop('disabled', false).removeClass('disabled')
                $(element).find('input[type=checkbox]').prop('disabled', false).removeClass('disabled')
            }
            $(element).find('input[type=hidden]').prop('disabled', false);
        }
    }

    copyEditToDecision() {
        $('body').on('click', '.copyEditToDecision', function() {
            const element = $(this).closest('.tr_reason_question_detail')
            const error = element.find('textarea.question_edit').closest('td').find('.error').remove()
            const val = element.find('textarea.question_edit').val()
            if(val.length == 0) {
                element.find('textarea.question_edit').after('<div class="error">'+ Common_E001 +'</div>');
                return
            } else if (val.length > 500) {
                element.find('textarea.question_edit').after('<div class="error">'+ Common_E024 +'</div>');
                return
            }
            element.find('.input_question_decision').parent().find('.error').remove();
            element.find('.question_decision').text(val)
            element.find('input.input_question_decision').val(val)
        })
    }

    /**
     * Delete question detail.
     */
    deleteRowQuestion() {
        $('body').on('click', '.delete_question_detail', function(e) {
            const btnDelete = this
            const id = $(this).data('question-detail-id')
            const routeDelete = routeDeleteQuestion.replace('%5Bid%5D', id)
            if (id != '' && id != undefined) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'DELETE',
                    url: routeDelete,
                    data: [],
                    beforeSend: function () {
                        loadingBox('open')
                    },
                    success: function (result) {
                        loadingBox('close')
                    },
                    error: function (error) {
                        loadingBox('close')
                    }
                })
            }
            $(btnDelete).closest('tr.tr_reason_question_detail').remove()
            $('body').find('.append_html').find('.No').each(function (idx) {
                $(this).text(idx + 1)
            })

            const lengthAppend = $('.append_html').find('.tr_reason_question_detail').length
            if (lengthAppend == 9) {
                $('.append_html').after(`<p class="eol"><a class="click_append" href="javascript:void(0)">+ 質問の追加</a></p>`)
            }
        })
    }

    clickAppend() {
        $('body').on('click', '.click_append', function(e) {
            e.preventDefault();

            let lengthAppend = $('body').find('.append_html').find('.tr_reason_question_detail').length;
            if (lengthAppend >= 9) {
                $(this).remove();
            }

            $(this).is(':checked')
            $("#checkAllQuestion").prop("checked", false);
            $('.checkbox_question').find('.error').remove();

            $(".append_html").find("tbody").append(`
                <tr class="tr_reason_question_detail">
                    <td class="center">
                        <span class="No">${lengthAppend + 1}</span>.<br />
                        <button type="button" data-question-detail-id="" class="small btn_d delete_question_detail" >削除</button>
                    </td>
                    <td class="td_item_question">
                        <span class="item_question" style="white-space: pre-line;"></span><br />
                        <input type="hidden" name="data[${lengthAppend}][id]">
                    </td>
                    <td>
                        <textarea name="data[${lengthAppend}][question_edit]" class="middle_b question_edit w-100"></textarea>
                        <input type="hidden" name="data[${lengthAppend}][question_edit_hidden]" class="question_edit_hidden">
                    </td>
                    <td class="center">
                        <input type="button" value="決定" class="btn_b copyEditToDecision" />
                    </td>
                    <td class="td_question_decision">
                        <span class="question_decision" style="white-space: pre-line;"></span>
                        <input type="hidden" name="data[${lengthAppend}][question_decision]" class="input_question_decision" value="">
                    </td>
                    <td class="center">
                        <input type="checkbox" name="data[${lengthAppend}][is_confirm]" value="1" class="checkQuestion"/>
                        確認＆ロック
                    </td>
                </tr>
            `);
        })
    }

    changeClickAppend() {
        let lengthAppend = $('body').find('.append_html').find('.tr_reason_question_detail').length;
        if (lengthAppend >= 9) {
            $('.click_append').remove();
        }
    }

    clickQuestionStatus() {
        $('input[name=question_status]').on('change', function () {
            $('.checkbox_question').find('.error').remove()
            if ($(this).is(':checked')) {
                $('input[name=submit]').prop('disabled', true).addClass('disabled')
                $('input[name=submit_no_question]').prop('disabled', false).removeClass('disabled')
                $('.append_html').find('.error').remove();
            } else {
                $('input[name=submit]').prop('disabled', false).removeClass('disabled')
                $('input[name=submit_no_question]').prop('disabled', true).addClass('disabled')
            }
        }).change()
    }

    submit(){
        $('body').on('click', 'input[type=submit],button[type=submit]', function(e) {
            $('#datepicker').change();

            $('.checkbox_question').find('.error').remove()
            if ($(this).attr('name') == 'submit') {
                $('.input_question_decision').change();
                $('.checkQuestion').change();

                const lengthQuestion = $('.append_html').find('.tr_reason_question_detail').length
                const checkboxQuestion = $('input[name=question_status]').is(':checked')
                if (lengthQuestion == 0 && !checkboxQuestion) {
                    $('.checkbox_question').append('<span class="error"><dt></dt><dd class="error">'+ question_A202_E001 +'</dd></span>')
                }
            }

            let form = $('#form');
            form.valid();
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {
                form.submit();
            } else {
                let firstError = hasError.first();
                scrollToElement(firstError, -100);
                return false;
            }
        });
    }
}

new Supervisor()
