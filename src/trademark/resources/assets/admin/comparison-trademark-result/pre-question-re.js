class preQuestionRe {
    constructor() {
        const self = this
        this.initValidate()
        this.hasError = false
        this.typeView = $('.type_view').val()
        this.numberQues = 0
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
    * Do load all action and element.
    */
     doLoad() {
        this.clickFilePDF()
        this.datepickerUserResponseDeadline()
        this.isValidDate()
        this.validateQuestion()
        this.preSubmit()
        this.addNewQuestion()
        this.deleteQuestion()
        this.deleteQuestionOldData()
        this.hideShowButtonAddNewQues()
        this.showModalForSeki()
        this.clickOpenFiles()
        this.showHideAddQuestionBtn()
    }

    showModalForSeki() {
        if ([ ROLE_SUPERVISOR, ROLE_MANAGER ].includes(adminRole) && type && (request == viewConst)) {
            this.disableInput()
            $('.btn_delete_question').prop('disabled', true).css('cursor', 'not-allowed')
            $('#question_status').prop('disabled', true).css('cursor', 'not-allowed')
            $('.btn_delete_question_old_data').prop('disabled', true).css('cursor', 'not-allowed')
            $('#add_new_question').hide()
            $('input[type=submit]').css('cursor', 'not-allowed')
        }
    }

    disableInput() {
        const form = $('form');
        form.find('input, textarea, select').prop('disabled', true);
        form.find('#closeModal').prop('disabled', false);
    }

    /**
     * Hide show button add
     */
    hideShowButtonAddNewQues() {
        const self = this
        self.numberQues = $('.tr_question').length
        if(self.numberQues == 9) {
            $('#add_new_question').hide()
        } else {
            $('#add_new_question').show()
        }
    }

    //click open pdf
    clickFilePDF() {
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    }

    /**
     * Handling event open file with click.
     */
    clickOpenFiles() {
        const self = this
        $('.click_open_file').click(function (e) {
            e.preventDefault();
            if (checkIsConfirm == true) {
                return false;
            }
            const a = $(this).closest('.data-files').find('.file_data')

            for (const object of a) {
                let url = $(object).attr('href')
                const newTab = window.open(url)
                const error = self.checkPopupBlocker(newTab)
                if (error) {
                    break;
                }
            }
        })
    }

    /**
     * Check pop-up blocker
     * @param {*} popupWindow
     * @returns
     */
    checkPopupBlocker(popupWindow) {
        let error = false
        const self = this
        if (popupWindow) {
            popupWindow.onload = function () {
                error = self.isPopupBlocked(popupWindow);
            };
        } else {
            self.displayError();
            error = true
        }

        return error;
    }

    /**
     *  Check pop-up block is enable
     * @param {*} popupWindow
     * @returns
     */
    isPopupBlocked(popupWindow) {
        if ((popupWindow.innerHeight > 0) == false) {
            this.displayError();
            return true
        }

        return false
    }

    /**
     * Show error
     */
    displayError() {
        $.confirm({
            title: '',
            content: messageBlockerPopupIsEnabled,
            buttons: {
                ok: {
                    text: 'OK',
                    btnClass: 'btn-blue',
                    action: function () { }
                }
            }
        });
    }

    //submit form
    preSubmit() {
        const self = this
        $('body').on('click', 'input[type=submit]', function() {
            //validate when submit
            if($(this).hasClass('submitSaveToEndUser')) {
                let flug = true;

                //if not row
                if($('.tr_question').not('.tr_question_hide').length <= 0) {
                    flug = false;
                } else {
                    $('.tr_question').not('.tr_question_hide').each(function (key, item) {
                        if($(item).find('textarea').val() == '') {
                            flug = false;
                        }
                    });
                }

                if(!flug) {
                    $.confirm({
                        title: '',
                        content: questionA202_E002,
                        buttons: {
                            cancel: {
                                text: closeModalText,
                                btnClass: 'btn-default',
                                action: function () {
                                }
                            },
                        }
                    });
                    return flug
                }
            }

            if($(this).hasClass('saveComplateQuestion')) {
                $('.code-submit').val(saveComplateQuestion)
            } else if($(this).hasClass('submitSaveToEndUser')) {
                $('.code-submit').val(saveToEndUser)
            } else {
                //save draft
                $('.code-submit').val(saveDraft)
            }
            if(!self.hasError && $('#form').valid()) {
                $('#form').submit()
            }
        });
    }

    datepickerUserResponseDeadline() {
        $('#datepicker').datepicker({
            minDate: new Date(),
            maxDate: new Date(userResponsedeadline),
            dateFormat: 'yy年mm月dd日',
            showMonthAfterYear: true,
            yearSuffix: '年',
            dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
            monthNames: ['1月','２月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
        });

        let date;
        date = new Date();
        date.setDate(date.getDate() + 2);

        if (userResponsedeadline) {
            date = new Date(userResponsedeadline);
        }

        $("#datepicker").prop("defaultValue", this.convert(date));
    }

    isValidDate() {
        $('body').on('change', '#datepicker', function(e) {
            $('body').find('.notice-scrollable').remove();

            var dateString = $(this).val()
            var regEx = /^\d{4}年\d{2}月\d{2}日$/;

            if(!dateString.match(regEx)) {
                $('.b-user_response_deadline').append('<div class="error mb15 notice-scrollable">間違ったフォーマット</div>')
                e.stopPropagation();
                e.preventDefault();

                return
            };
            const changeNow = formatDate(new Date())
            const changeDatepickerChoose = formatDate(new Date(replaceDate($(this).val(), 'ja')));
            const changeResponsedeadline = formatDate(new Date(userResponsedeadline));
            if (changeDatepickerChoose < changeNow || changeDatepickerChoose > changeResponsedeadline) {
                $("#datepicker").closest('.change_datepicker').append('<dt class="notice-scrollable"></dt><dd class="error red notice-scrollable">'+ commonE039 + '</dd>')
            }
        })

        function replaceDate(date, nation) {
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
        function formatDate(date) {
            // Get year, month, and day part from the date
            var year = date.toLocaleString("default", { year: "numeric" });
            var month = date.toLocaleString("default", { month: "2-digit" });
            var day = date.toLocaleString("default", { day: "2-digit" });
            // Generate yyyy-mm-dd date string
            return year + month + day;
        }
    }

    validateQuestion() {
        const self = this
        $('body').on('change focusout', '.input_question', function() {
            $(this).closest('td').find('.error').remove()
            let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;
            const value = $(this).val();
            if(!value) {
                self.hasError = true
                $(this).closest('td').append(`<div id="question-error" class="error">${Common_E001}</div>`)
            } else if (value.length > 500) {
                self.hasError = true
                $(this).closest('td').append(`<div id="question-error" class="error">${Common_E046}</div>`)
            } else {
                self.hasError = false
            }
        })
    }

    deleteQuestion() {
        $('body').on('click', '.btn_delete_question', function() {
            $(this).closest('tr.tr_question').remove()
            $('.tr_question').not('.tr_question_hide').each(function(index, item) {
                $(item).find('.index-data').text(index + 1 + lengthReasonQuestionDetailsOld)
            });
            let rowLength = $('.tr_question').length
            if(rowLength == 10) {
                $('#add_new_question').hide()
            } else {
                $('#add_new_question').show()
            }
        })
    }

    //delete old
    deleteQuestionOldData() {
        const self = this
        $('body').on('click', '.btn_delete_question_old_data', function() {
            $(this).closest('tr.tr_question').addClass('tr_question_hide').hide()
            $(this).closest('tr.tr_question').find('.delete_status_old_data').prop('checked', true)

            $('.tr_question').not('.tr_question_hide').each(function(index, item) {
                $(item).find('.index-data').text(index + 1 + lengthReasonQuestionDetailsOld)
            });
            let rowLength = $('.tr_question').length
            self.showHideAddQuestionBtn()
        })
    }

    showHideAddQuestionBtn() {
        let rowLength = $('.tr_question').not('.tr_question_hide').length
        if(rowLength >= 10) {
            $('#add_new_question').hide()
        } else {
            $('#add_new_question').show()
        }
    }

    addNewQuestion() {
        const self = this
        $('body').on('click', '#add_new_question', function () {
            self.numberQues = $('.tr_question').length
            let indexData = $('.tr_question').not('.tr_question_hide').length + 1 + lengthReasonQuestionDetailsOld
            const count = $('#createQuestionTbl tbody tr').length
            if (!checkIsConfirm) {
                $('#createQuestionTbl tbody').append(`
                    <tr class="tr_question">
                        <td class="center"><span class="index-data">${indexData}</span>.<br><input type="button" value="削除" class="small btn_d btn_delete_question"></td>
                        <td><textarea name="data[${count}][question]" class="middle_b input_question"></textarea></td>
                    </tr>
                `)
            }
            self.showHideAddQuestionBtn()
        })
    }

    /**
     * Init validate
     */
    initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        const rules = {
            'content': {
                maxlength: 500,
                isFullwidth: false,
            }
        }
        const messages = {
            'content': {
                maxlength: Common_E046,
                isFullwidth: QA_U000_E001,
            }
        }

        new clsValidation('#form', { rules: rules, messages: messages })
    }

    convert(date) {
        var date = new Date(date),
        mnth = ("0" + (date.getMonth() + 1)).slice(-2),
        day = ("0" + date.getDate()).slice(-2);

        return [date.getFullYear() + '年' + mnth + '月' + day + '日'];
    }
}

new preQuestionRe();
