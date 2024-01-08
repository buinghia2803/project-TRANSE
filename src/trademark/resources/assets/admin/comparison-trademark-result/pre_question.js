class preQuestion {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
    * Do load all action and element.
    */
    doLoad() {
        this.initValidate()
        this.clickFilePDF()
        this.datepickerUserResponseDeadline()
        this.dataQuestion()
        this.isValidDate()
        this.clickAppend()
        this.deleteQuestion()
        this.checkboxQuestion()
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

    clickFilePDF() {
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    }

    datepickerUserResponseDeadline() {
        $('#datepicker').datepicker({
            minDate: new Date(),
            maxDate: new Date(responseDeadline),
            dateFormat: 'yy年mm月dd日',
            showMonthAfterYear: true,
            yearSuffix: '年',
            dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
            monthNames: ['1月','２月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
        });

        let date;

        if (userResponsedeadline) {
            date = new Date(userResponsedeadline);
            date.setDate(date.getDate());
        } else {
            date = new Date();

            if (planCorrespondence.type == TYPE_SIMPLE) {
                date.setDate(date.getDate() + 12);
            } else if(planCorrespondence.type == TYPE_SELECT) {
                date.setDate(date.getDate() + 7);
            } else if(planCorrespondence.type == TYPE_PACK) {
                date.setDate(date.getDate() + 12);
            }
        }

        function convert(date) {
            var date = new Date(date),
            mnth = ("0" + (date.getMonth() + 1)).slice(-2),
            day = ("0" + date.getDate()).slice(-2);

            return [date.getFullYear() + '年' + mnth + '月' + day + '日'];
        }

        $("#datepicker").prop("defaultValue", convert(date));
    }

    isValidDate() {
        $('body').on('change', '#datepicker', function(e) {
            $('body').find('.notice-scrollable').remove();

            var dateString = $(this).val()
            var regEx = /^\d{4}年\d{2}月\d{2}日$/;

            if(!dateString.match(regEx)) {
                $('.js-scrollable').append('<dd class="error mb15 notice-scrollable">間違ったフォーマット</dd>')
                e.stopPropagation();
                e.preventDefault();

                return
            };

            const changeNow = formatDate(new Date())
            const changeDatepickerChoose = formatDate(new Date(replaceDate($(this).val(), 'ja')));
            const changeResponsedeadline = formatDate(new Date(responseDeadline));

            if (changeDatepickerChoose < changeNow || changeDatepickerChoose > changeResponsedeadline) {
                $("#datepicker").closest('.change_datepicker').append('<dt class="notice-scrollable"></dt><dd class="error red notice-scrollable">' + Common_E038 + '</dd>')
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

    dataQuestion() {
        $('body').on('change keyup', 'textarea.data-question', function() {
            if (!$('input[name=question_status]').is(':checked')) {
                let value = $(this).val();
                $(this).parent().find('.error').remove();

                if(value.length == 0) {
                    $(this).after('<div class="error">'+ Common_E001 +'</div>');
                } else if (value.length > 500) {
                    $(this).after('<div class="error">'+ Common_E024 +'</div>');
                }
            }
        });
    }

    clickAppend() {
        $('body').on('click', '.click_append', function(e) {
            $('.checkbox_question').find('.error-checkbox').remove()
            $('.error-table').remove()
            e.preventDefault();

            let lengthAppend = $('body').find('.append_html').find('.pre_question').length;
            if (lengthAppend == 9) {
                $(this).remove();
            }

            $('input[name=question_status]').prop('checked', false)
            $(".append_html").find("tbody").append(`
                <tr class="pre_question">
                    <td class="center">
                        <span class="No">${lengthAppend + 1}</span>.<br />
                        <input type="hidden" name="data[${lengthAppend}][id]">
                        <input type="button" value="削除" class="small btn_d delete_question"/>
                    </td>
                    <td><textarea class="middle_b data-question w-100" name="data[${lengthAppend}][question]"></textarea></td>
                </tr>
            `);
        })
    }

    deleteQuestion() {
        $('body').on('click', '.delete_question', function () {
            const _valID = $(this).closest('.pre_question').find('input[type=hidden]').val()
            if (_valID != '' && _valID != undefined) {
                loadAjaxPost(routeDeleteQuestion, {
                    id: _valID,
                }, {
                    beforeSend: function(){},
                    success:function(result){},
                    error: function (error) {}
                }, 'loading');
            }
            $(this).closest('.pre_question').remove()
            $('body').find('.append_html').find('.No').each(function (idx) {
                $(this).text(idx + 1)
            })

            const lengthAppend = $('.append_html').find('.pre_question').length
            if (lengthAppend == 9) {
                $('.append_html').after(`<p class="eol"><a class="click_append" href="javascript:void(0)">+ 質問の追加</a></p>`)
            }
        })
    }

    checkboxQuestion () {
        $('input[name=question_status]').change(function () {
            $('.checkbox_question').find('.error').remove()
            let flagError = false
            $('.append_html').find('.pre_question').each(function (idx, item) {
                const valTextarea = $(item).find('textarea').val();
                if (valTextarea.length) {
                    flagError = true
                }
            })
            if (flagError) {
                $(this).prop('checked', false)
                $.alert({
                    title: '',
                    content: question_A202_E001,
                    buttons: {
                        cancel: {
                            text: close,
                        },
                    }
                });
            }
            if ($(this).is(':checked')) {
                $('.append_html').find('.error').remove();
            }
        })
    }

    submit(){
        $('body').on('click', 'input[type=submit],button[type=submit]', function(e) {
            $('textarea.data-question').change();

            const lengthQuestion = $('.append_html').find('.pre_question').length
            const checkboxQuestion = $('input[name=question_status]').is(':checked')
            $('.checkbox_question').find('.error').remove()
            if ($(this).attr('name') == 'submitRedirect') {
                if (lengthQuestion == 0 && !checkboxQuestion) {
                    $('.checkbox_question').append('<span class="error"><dt></dt><dd class="error">'+ Common_E025 +'</dd></span>')
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
new preQuestion()
