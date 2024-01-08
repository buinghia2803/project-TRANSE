<!-- contents inner -->
@if ($dataCommonA205Iken02)
    <div class="clearfix" style="text-align:left;padding: 10px">
        <div style="margin-bottom: 50px;">
            <h4>{!! __('labels.a205iken02window.content_title') !!}</h4>
            <dl class="w16em clearfix">
                <dt> </dt>
                <dd>
                    <div class="mb05" style="white-space:pre-line;">
                        @if(isset($dataCommonA205Iken02) && $dataCommonA205Iken02->is_written_opinion == IS_NOT_WRITTEN_OPINION)
                            <div class="white-space-cus">{{ $dataCommonA205Iken02->description_written_opinion ?? ''  }}</div>
                        @elseif(isset($dataCommonA205Iken02) && $dataCommonA205Iken02->is_written_opinion == IS_WRITTEN_OPINION)
                            <div class="white-space-cus">{{ __('labels.a205shu02_window.opinion_not_required') }}</div>
                        @endif
                    </div>
                </dd>
            </dl>

            <h4>{!! __('labels.a205iken02window.list_name_doc_submission') !!}</h4>
            <dl class="w16em eol clearfix">
                @if ($dataCommonA205Iken02->docSubmissionAttachProperties)
                    @foreach ($dataCommonA205Iken02->docSubmissionAttachProperties as $docSubmissionAttachPropertie)
                        <dt>{!! __('labels.a205iken02window.name_submission') !!}</dt>
                        <dd>{{ $docSubmissionAttachPropertie->name }}</dd>
                    @endforeach
                @endif
            </dl>

            <h4>{!! __('labels.a205iken02window.list_file') !!}</h4>
            <dl class="w16em clearfix">
                @if ($dataCommonA205Iken02->docSubmissionAttachProperties)
                    @foreach ($dataCommonA205Iken02->docSubmissionAttachProperties as $docSubmissionAttachPropertie)
                        <dt>{!! __('labels.a205iken02window.name_submission') !!}</dt>
                        <dd>{{ $docSubmissionAttachPropertie->name }}</dd>

                        <dt>{!! __('labels.a205iken02window.content_file') !!}</dt>
                        @if ($docSubmissionAttachPropertie->docSubmissionAttachments)
                            <dd class="bukken clearfix">
                                @php
                                    $docSubmissionAttachments = $docSubmissionAttachPropertie->docSubmissionAttachments;
                                    $docSubmissionAttachments = collect($docSubmissionAttachments)
                                        ->map(function ($item) {
                                            $item->file_no_covert = (int) mb_convert_kana($item->file_no, 'n');
                                            return $item;
                                        })
                                        ->sortBy('id')
                                        ->sortBy('file_no_covert');
                                @endphp
                                @foreach ($docSubmissionAttachments as $item)
                                    <img src="{{ asset($item->attach_file) }}" />
                                    <br>
                                @endforeach
                            </dd>
                        @endif
                    @endforeach
                @endif

            </dl>
            <!-- footer -->
            @include('admin.layouts.footer', ['class' => 'footer-custom'])
        </div>
    </div><!-- /contents inner -->
@endif
