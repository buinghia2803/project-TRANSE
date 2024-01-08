@php
    if (!isset($trademark) && !empty($trademark_id)) {
        $trademark = \App\Models\Trademark::find($trademark_id);
    }

    $tableData = [];
    foreach ($table as $row) {
        $fields = $row['fields'] ?? [];
        $colspan = count($fields) > 0;

        $tableData[] = array_merge($row, [
            'colspan' => $colspan
        ]);
    }

@endphp

@if(count($tableData))
<div class="info">

    <table class="info_table">

        @forelse($tableData as $row)
            @switch($row['show_type'] ?? '')
                @case('image')
                    <tr>
                        <th nowrap>{{ $row['label'] ?? '' }}</th>
                        <td colspan="{{ $row['colspan'] == false ? '100%' : '' }}">
                            <img src="{{ $row['value'] ?? '' }}" height="80">
                            <a href="{{ $row['value'] ?? '' }}" target="_blank">
                                @if($row['image_url_label'] && $row['image_url'])
                                    {{ $row['image_url_label'] ?? '' }}
                                @endif
                            </a>
                        </td>
                    </tr>
                @break

                @case('input')
                    <tr>
                        <th nowrap>{{ $row['label'] ?? '' }}<span class="red">*</span></th>
                        <td colspan="{{ $row['colspan'] == false ? '100%' : '' }}">
                            <input type="text" name="{{ $row['input_name'] ?? '' }}" value="{{ $row['value'] ?? '' }}" nospace>
                        </td>
                    </tr>
                    @break
                @default
                    <tr>
                        <th>{{ $row['label'] ?? '' }}</th>
                        <td colspan="{{ $row['colspan'] == false ? '100%' : '' }}">

                            @if(!empty($row['link_value']))
                                <a href="{{ $row['link_value'] ?? '' }}" target="_blank">{!! $row['value'] ?? '' !!}</a>
                            @else
                                {!! $row['value'] ?? '' !!}
                            @endif

                            @if(!empty($row['links']))
                                @foreach($row['links'] as $link)
                                    <a href="{{ $link['url'] ?? '' }}" class="{{ $link['class'] ?? 'btn_a' }}" target="_blank">{{ $link['label'] ?? '' }}</a>
                                @endforeach
                            @endif

                            @if(!empty($row[SHOW_EDIT_REFERENCE_NUMBER]))
                                <input type="button" id="changeReferenceNumber" value="{{ __('labels.edit') }}" class="btn_a small">
                                <script>
                                    $('body').on('change keyup', '#form-change-reference-number .name', function () {
                                        $(this).closest('.form-group').find('.notice').remove();
                                    });
                                    $('body').on('click', '#changeReferenceNumber', function () {
                                        $.confirm({
                                            title: '',
                                            content: `
                                                <form action="" id="form-change-reference-number" class="formName">
                                                    <div class="form-group">
                                                        <span>{{ __('labels.common.change_reference_number.label') }}</label>
                                                        <input type="text" value="{{ $row['value'] ?? '' }}" class="name w-100 px-2 py-1 mt-2" required />
                                                    </div>
                                                </form>
                                            `,
                                            buttons: {
                                                cancel: {
                                                    text: '{{ __('labels.btn_cancel') }}',
                                                },
                                                save: {
                                                    text: '{{ __('labels.save') }}',
                                                    btnClass: 'btn-blue',
                                                    action: function () {
                                                        const nameRequired = '{{ __('messages.common.errors.Common_E001') }}';
                                                        const nameMaxLength = '{{ __('messages.common.errors.support_U011_E002') }}';
                                                        let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;
                                                        let name = this.$content.find('.name').val();

                                                        $('#form-change-reference-number .notice').remove();
                                                        if (name.length == 0) {
                                                            this.$content.find('.name').after(`<div class="notice">${nameRequired}</div>`);
                                                            return false;
                                                        } else if (!regex.test(name) || name.length > 20) {
                                                            this.$content.find('.name').after(`<div class="notice">${nameMaxLength}</div>`);
                                                            return false;
                                                        } else {
                                                            loadAjaxPost(
                                                                '{{ route('user.ajax.change-reference-number', $row['trademark_id'] ?? '') }}',
                                                                {reference_number: name},
                                                                {
                                                                    beforeSend: function () {},
                                                                    success: function (result) {
                                                                        $.confirm({
                                                                            title: '',
                                                                            content: result.message,
                                                                            type: 'blue',
                                                                            buttons: {
                                                                                close: {
                                                                                    text: '{{ __('labels.close') }}',
                                                                                    action: function () {
                                                                                        window.location.reload();
                                                                                    }
                                                                                }
                                                                            }
                                                                        });
                                                                    },
                                                                    error: function (error) {
                                                                        $.alert({
                                                                            title: '',
                                                                            content: error.responseJSON.message,
                                                                            type: 'red',
                                                                            buttons: {
                                                                                close: {
                                                                                    text: '{{ __('labels.close') }}',
                                                                                    action: function () {
                                                                                    }
                                                                                }
                                                                            }
                                                                        });
                                                                    }
                                                                }, 'loading');
                                                        }
                                                    }
                                                },
                                            },
                                        });
                                    })
                                </script>
                            @endif
                        </td>
                        @if(!empty($row['fields']))
                            @foreach($row['fields'] as $field)
                                <th nowrap>{{ $field['label'] ?? '' }}</th>
                                <td>{{ $field['value'] ?? '' }}</td>
                            @endforeach
                        @endif
                    </tr>
            @endswitch
        @empty
        @endforelse

    </table>
</div>
@endif
