class registrationNotify {
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
        this.init()
        this.redirect()
        this.submit()
    }
    /**
     * Init validate
     */
    initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        this.rules = {}
        this.messages = {}

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    /**
     * Init
     */
    init() {
        let self = this
        let date;
        if (matchingResultDate) {
            date = new Date(matchingResultDate);
            date.setDate(date.getDate() + 20);
            $("#datepicker").prop("defaultValue", this.convert(date));
        }

        $("#datepicker").datepicker({
            minDate: new Date(),
            dateFormat: "yy年mm月dd日",
            showMonthAfterYear: true,
            yearSuffix: "年",
            dayNamesMin: ["日", "月", "火", "水", "木", "金", "土"],
            monthNames: ["1月", "２月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        });

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

            if (changeDatepickerChoose < changeNow) {
                $("#datepicker").closest('.change_datepicker').append('<dt class="notice-scrollable"></dt><dd class="error red notice-scrollable">' + Common_E038 + '</dd>')
            }
        })
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

    convert(date) {
        var date = new Date(date),
            mnth = ("0" + (date.getMonth() + 1)).slice(-2),
            day = ("0" + date.getDate()).slice(-2);

        return [date.getFullYear() + "年" + mnth + "月" + day + "日"];
    }

    redirect(){
        $('#redirect_a700shutsugannin01').click(function () {
            $.alert({
                title: '',
                content,
                buttons: {
                    OK: {
                        text: OK,
                        btnClass: 'btn-blue',
                        action: function(){
                            window.location.href = routeRedirectA700shutsugannin01
                        }
                    },
                    cancel: {
                        text: cancel,
                    },
                }
            });
        })
    }

    submit(){
        $('body').on('click', 'input[type=submit],button[type=submit]', function() {
            $('#datepicker').change();
        });
    }
}

new registrationNotify()
