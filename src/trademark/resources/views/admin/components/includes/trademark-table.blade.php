@if(!empty($table['user']))
    @php
        $admin = auth()->user();
        $user = $table['user']['info'] ?? [];
        $userDetailLink = $table['user']['user_detail_link'] ?? '#';
        $QaLink = $table['user']['qa_link'] ?? '#';
    @endphp
    <h5 class="membertitle">
        {{ __('labels.common.trademark_table.user.title') }}
        <a href="{{ $userDetailLink }}" target="_blank">{{ __('labels.common.trademark_table.user.user_detail_link') }}</a>
    </h5>
    <ul class="memberinfo">
        <li>{{ $user->info_member_id ?? '' }}</li>
        <li>{{ $user->info_name ?? '' }}</li>
        <li>
            @if($admin->role != ROLE_SUPERVISOR)
                <a class="btn_b" href="{{ $QaLink }}">{{ __('labels.common.trademark_table.user.qa_link') }}</a>
            @endif
        </li>
    </ul>
@endif

@php
    if (!isset($trademark) && !empty($trademark_id)) {
        $trademark = \App\Models\Trademark::find($trademark_id);
    }

    $tableData = [];
    foreach ($table['data'] as $row) {
        $fields = $row['fields'] ?? [];
        $colspan = count($fields) > 0;

        $tableData[] = array_merge($row, [
            'colspan' => $colspan
        ]);
    }
@endphp
<div class="info mb20">
    <table class="info_table">
        <caption>{{ __('labels.common.trademark_table.title') }}</caption>
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

<ul class="btn_left eol">
    @if(!empty($table['sft']))
        <li><a href="{{ __($table['sft']['url'] ?? '') }}" target="_blank" class="btn_a mrg-10">{{ __($table['sft']['label'] ?? '') }}</a></li>
    @endif
    @if(!empty($table['precheck']))
        <li><a href="javascript:;" class="btn_a" id="precheck-detail">{{ __($table['precheck']['label'] ?? '') }}</a></li>
        <div id="precheck-modal" class="modal fade" role="dialog">
            <div class="modal-dialog" style="min-width: 85%;">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                        <div class="content"></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $('body').on('click', '#precheck-detail', function (e) {
                e.preventDefault();

                //Ajax Code
                    let url = '{{ __($table['precheck']['url'] ?? '') }}';
                    let precheckModal = $('#precheck-modal');
                    loadAjaxPost(url, {
                        id: '{{ __($table['precheck']['trademark_id'] ?? '') }}',
                        data: []
                    }, {
                        beforeSend: function(){},
                        success:function(result){
                            $('.content').addClass('loaded');
                            $('.content').html(result);
                            openModal('#precheck-modal');
                        },
                        error: function (error) {}
                    }, 'loading');
            });
        </script>
    @endif
    @if(!empty($table['history']))
        <li><a href="{{ __($table['history']['url'] ?? '') }}" target="_blank" class="btn_a mrg-10">{{ __($table['history']['label'] ?? '') }}</a></li>
    @endif
</ul>

