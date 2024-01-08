@extends('admin.layouts.app')
@section('main-content')
<style>
    .disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
    }
    input[type="button"].btn_b:hover {
        background: #359ce0;
        border: 1px solid #999999;
        color: #ffffff;
    }
    input[type="button"].btn_a:hover {
        background: #cccccc;
        color: #000000;
    }
</style>
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        <form id="form" action="{{ route('admin.refusal-request-review-create') }}" autocomplete="off" method="POST">
            @csrf
            <input type="hidden" name="trademark_id" value="{{ $id }}" />
            <input type="hidden" name="maching_result_id" value="{{ $_GET['maching_result_id'] }}" />
            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable,
            ])

            <table class="normal_a eol">
                <caption>
                    {{ __('labels.maching_results.caption') }}
                </caption>
                <tr>
                    <th>{{ __('labels.maching_results.tr_1') }}</th>
                    <td colspan="3">
                        {{ \CommonHelper::formatTime($machingResult->pi_dd_date ?? '', 'Y/m/d')}}
                        <input type="button" value="{{ __('labels.maching_results.btn_1') }}" class="btn_b disabled"/>
                        <input type="button" value="{{ __('labels.maching_results.btn_2') }}" class="btn_a disabled"/>
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.maching_results.tr_2') }}</th>
                    <td colspan="3">{{ $dateComparisonTrademarkResults }}</td>
                </tr>
            </table>

            <h3>{{ __('labels.maching_results.h3') }}</h3>
            @if ($textRed)
                <p class="red" style="margin-bottom: 1em">{{ __('labels.maching_results.text_red') }}</p>
            @endif

            <dl class="w10em eol clearfix js-scrollable">
                <dt>{{ __('labels.maching_results.deadline') }}</dt>
                <dd class="change_datepicker">
                    <input type="text" name="user_response_deadline" id="datepicker"/>
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li><input type="button" value="{{ __('labels.maching_results.back') }}" class="btn_a" onclick="window.location = '{{ route('admin.home') }}'"></li>
                <li><input type="submit" value="{{ __('labels.maching_results.submit') }}" class="btn_c" /></li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        function disableInput() {
            const form = $('form');
            form.find('input, button, textarea, select').prop('disabled', true)
            form.find('input, button, textarea, select').addClass('disabled')
            $('[data-dismiss]').prop('disabled', false).removeClass('disabled');
        }
    </script>
    <script>
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const wrong_format = '{{ __('messages.general.wrong_format') }}';
        const Common_E039 = '{{ __('messages.general.Common_E039') }}';
        const userResponsedeadline = '{{ (!empty($dateUserResponseDeadLine)) ? $dateUserResponseDeadLine->format('Y-m-d') : '' }}';
        const dateComparisonTrademarkResults = @JSON($dateComparisonTrademarkResults);
        const pack = @JSON($pack);
        const packA = @JSON($packA);
        const packB = @JSON($packB);
        const packC = @JSON($packC);
        const checkSubmit = @JSON($checkSubmit);

        if (pack == 3) {
            $("#datepicker").prop('disabled', true).attr("placeholder", "年/ 月/ 日");
        }

        if (pack != 3) {
            let date;
            if (userResponsedeadline) {
                date = new Date(userResponsedeadline);
                $("#datepicker").prop("defaultValue", convert(date));
            }
            $('body').on('click change keyup', '#datepicker', function () {
                $("body").find('.error-date').remove()

                const changeNow = formatDate(new Date())
                const changeDatepickerChoose = formatDate(new Date(replaceDate($(this).val(), 'ja')));
                const changeDateComparisonTrademarkResults = formatDate(new Date(dateComparisonTrademarkResults));

                if (changeDatepickerChoose < changeNow || changeDatepickerChoose > changeDateComparisonTrademarkResults) {
                    if (checkSubmit == true) {
                        $("body").find('.error-date').remove()
                    } else if (!$('body').find('#datepicker-error').length) {
                          $("#datepicker").closest('.change_datepicker').after('<span class="error-date"><dt></dt><dd class="error red">' + Common_E039 + '</dd></span>')
                    }
                } else {
                    $("body").find('.error-date').remove()
                }
            })
            $('#datepicker').change()
            $('#datepicker').datepicker({
                dateFormat: 'yy年mm月dd日',
                showMonthAfterYear: true,
                yearSuffix: '年',
                minDate: new Date(),
                maxDate: new Date(dateComparisonTrademarkResults),
                dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
                monthNames: ['1月','２月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
                onSelect: function(dateText) {
                    if (pack == packC) {
                        return false;
                    }

                    const dateChooseReplace = replaceDate(dateText, 'ja')
                    const dateComparisonTrademarkResultsReplace = replaceDate(dateComparisonTrademarkResults, 'en')

                    var dateChooseReplaceReal = new Date(dateChooseReplace);
                    var dateComparisonTrademarkResultsReplaceReal = new Date(dateComparisonTrademarkResultsReplace);
                    var now = new Date()

                    $("#datepicker").closest('.change_datepicker').find('.error').remove()
                    if (formatDate(dateChooseReplaceReal) >= formatDate(now) && formatDate(dateChooseReplaceReal) <= formatDate(dateComparisonTrademarkResultsReplaceReal)){
                        $("#datepicker-error").remove()
                        $("body").find('.error-date').remove()
                        return true;
                    } else {
                        $("#datepicker").closest('.change_datepicker').after('<span class="error-date"><dt></dt><dd class="error red">' + Common_E039 + '</dd></span>')
                        $("body").find('.error-date').remove()
                    }
                }
            });
        }
        validation('#form', {
            'user_response_deadline': {
                required: () => {
                    let require = false;
                    if (pack == packA || pack == packB) {
                        require = true;
                    }
                    return require;
                },
                isValidDateJapan: () => {
                    let require = true;
                    if (pack == packC) {
                        require = false;
                    }
                    return require;
                }
            },
        }, {
            'user_response_deadline': {
                required: Common_E001,
                isValidDateJapan: wrong_format,
            },
        });

        function formatDate(date) {
            // Get year, month, and day part from the date
            var year = date.toLocaleString("default", { year: "numeric" });
            var month = date.toLocaleString("default", { month: "2-digit" });
            var day = date.toLocaleString("default", { day: "2-digit" });

            // Generate yyyy-mm-dd date string
            return year + month + day;
        }

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

        function convert(date) {
            var date = new Date(date),
            mnth = ("0" + (date.getMonth() + 1)).slice(-2),
            day = ("0" + date.getDate()).slice(-2);

            return [date.getFullYear() + '年' + mnth + '月' + day + '日'];
        }
    </script>
    @if($checkSubmit)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_OFFICE_MANAGER] ])
@endsection
