class clsCommon {
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
        $('.close-alert').click(function () {
            $('body').removeClass('fixed-body')
        })
        this.init();
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        this.ready()
        this.openModalNotSubmit()
        this.submitOpenModal()
        this.submitRedirectPage()
        this.clickFilePdf()
        this.submit()
    }

    ready() {
        let date;
        if (responseDeadline) {
            date = new Date(responseDeadline);
            $("#datepicker").prop("defaultValue", convert(date));
        }

        $("#datepicker").datepicker({
            minDate: new Date(),
            maxDate: new Date(comparisonTrademarkResultResponseDeadline),
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

            const changeNow = formatDate(new Date())
            const changeDatepickerChoose = formatDate(new Date(replaceDate($(this).val(), 'ja')));
            const changeResponsedeadline = formatDate(new Date(comparisonTrademarkResultResponseDeadline));

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

        function convert(date) {
            var date = new Date(date),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);

            return [date.getFullYear() + "年" + mnth + "月" + day + "日"];
        }
    }

    openModalNotSubmit() {
        // Not submit open modal and add name page
        $('.open-modal').on('click', function (e) {
            const route = $(this).data('route')
            $('input[name=name_page]').val(route)
            if (!IS_SUBMIT || route == 'a202n_s') {
                $('body').addClass('fixed-body')
                const srcIframe = $(this).data('src-iframe')
                $('.src-iframe').prop('src', srcIframe)
                openModal('#open-modal-iframe');
            }
        })
    }

    submitOpenModal() {
        // Submit open modal
        const ROUTE_MODAL = $(`*[data-route="${OPEN_MODAL}"]`)
        const SRC_IFRAME = ROUTE_MODAL.data('src-iframe')
        if (OPEN_MODAL.length > 0) {
            $('body').addClass('fixed-body')
            $('.src-iframe').prop('src', SRC_IFRAME)
            openModal(`#open-modal-iframe`);
        }
    }

    submitRedirectPage() {
        // Submit redirect page
        $('.redirect_url').on('click', function (e) {
            const redirect = $(this).data('redirect')
            if (IS_SUBMIT) {
                $('input[name=link_redirect]').val(redirect)
            } else {
                window.location.href = redirect
            }
        })
    }

    clickFilePdf() {
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    }

    submit(){
        $('body').on('click', 'input[type=submit],button[type=submit]', function(e) {
            $('#datepicker').change();
        });
    }
}
new clsCommon()
