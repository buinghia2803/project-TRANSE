<div id="common-a205-shu" style="padding: 10px">
    <div>
        <h4>{{ __('labels.a205iken02window.content_title') }}</h4>

        <dl class="w10em clearfix">

            <dt></dt>
            <dd>
                @php
                    $isWrittenOpinionTrue = \App\Models\DocSubmission::IS_WRITTEN_OPINION_TRUE;
                @endphp
                @if($type && $type == ACTION)
                    <input type="checkbox" name="data-submission[is_written_opinion]" id="is_written_opinion"
                           value="{{ $isWrittenOpinionTrue }}"
                        {{ ($oldDataCommonShu02Window && $oldDataCommonShu02Window->is_written_opinion == $isWrittenOpinionTrue) ? 'checked' : ''}}/>
                    {{ __('labels.a205shu02_window.opinion_not_required') }}<br/><br/>
                @endif
                <div class="mb05">
                    <!-- if create or update-->
                    <div id="wp-description-written-opinion" class="{{ $type && $type == VIEW ? 'd-none' : '' }}">
                            <textarea class="middle_c custom_textarea" name="data-submission[description_written_opinion]"
                                      id="description_written_opinion">{{ old('description_written_opinion', $oldDataCommonShu02Window ? $oldDataCommonShu02Window->description_written_opinion : $planDetailDescription ?? '') }}</textarea>
                        <div class="show-error red"></div>
                    </div>
                    @if($type && $type == VIEW)
                        @if(isset($oldDataCommonShu02Window) && $oldDataCommonShu02Window->is_written_opinion == IS_NOT_WRITTEN_OPINION)
                            <div class="white-space-cus">{{ $oldDataCommonShu02Window ? $oldDataCommonShu02Window->description_written_opinion : '' }}</div>
                        @elseif(isset($oldDataCommonShu02Window) && $oldDataCommonShu02Window->is_written_opinion == IS_WRITTEN_OPINION)
                            <div class="white-space-cus">{{ __('labels.a205shu02_window.opinion_not_required') }}</div>
                        @endif
                    @endif
                </div>
            </dd>
            <dl class="w04em clearfix">
                @foreach ($dataProductCommonA205Shu02 as $mDistinctionId => $products)
                    @php
                        $dataDelete = $products->filter(function ($value, $key) {
                             return $value->plan_detail_product_deleted_at;
                        });

                        $dataAdd = $products->filter(function ($value, $key) {
                             return (!$value->plan_detail_product_deleted_at && (in_array($value->role_add, [\App\Models\PlanDetailProduct::ROLL_ADD_MANAGER, \App\Models\PlanDetailProduct::ROLL_ADD_SUPERVISOR])));
                        });
                    @endphp
                    @if ($dataDelete->count() > 0 && $dataAdd->count() > 0)
                        <dt>{{ __('labels.support_first_times.No') }}{{ $mDistinctionId }}{{ __('labels.support_first_times.kind') }}</dt>
                        @if($dataDelete->count() > 0)
                            <dd><span>{{ __('labels.agent.delete') }}</span>　{{ $dataDelete->implode('name', ' ,') }}</dd>
                        @endif
                        @if($dataAdd->count() > 0)
                            <dd><span>{{ __('labels.agent.addition') }}</span>　{{ $dataAdd->implode('name', ' ,') }}</dd>
                        @endif
                        @if($dataDelete->count() == 0 && $dataAdd->count() == 0)
                            <dd></dd>
                        @endif
                    @endif
                @endforeach
            </dl>
            <br/>
        </dl>

        @if($oldDataCommonShu02Window && $oldDataCommonShu02Window->docSubmissionAttachProperties->count() > 0
            || $type && $type == ACTION)
            <h4>{{ __('labels.a205iken02window.list_file') }}</h4>
        @endif
        <dl class="w10em clearfix" id="list-submission-property">
            @if($oldDataCommonShu02Window && $oldDataCommonShu02Window->docSubmissionAttachProperties->count() > 0)
                @php
                    $docSubmissionAttachProperties = $oldDataCommonShu02Window->docSubmissionAttachProperties->map(function ($item) {
                        $docSubmissionAttachments = $item->docSubmissionAttachments ?? collect([]);
                        $docSubmissionAttachments = $docSubmissionAttachments->sortBy('file_no');

                        $fileNo = $docSubmissionAttachments->first()->file_no;
                        $fileNo = (int) mb_convert_kana($fileNo, 'n');

                        $item->file_no = $fileNo ?? 9999;

                        return $item;
                    })->sortBy('file_no')->values();
                @endphp

                @foreach($docSubmissionAttachProperties as $k => $docSubmissionAttachProperty)
                    <div class="row-item-submission row-item-submission-old" data-key="{{ $k }}">
                        <dt>{{ __('labels.a205iken02window.name_submission') }}</dt>
                        <dd class="show-error-box">
                            @if($type && $type == ACTION)
                                <input type="text" class="em24 data-property-name"
                                       name="data-properties[{{ $k }}][name]"
                                       value="{{ $docSubmissionAttachProperty->name }}"/>
                                <input type="hidden" class="em24 data-property-id"
                                       name="data-properties[{{ $k }}][doc_submission_attach_property_id]"
                                       value="{{ $docSubmissionAttachProperty->id }}"/>
                                <input type="button" value="{{ __('labels.a205shu02_window.delete_row') }}"
                                       class="btn_d delete-row"
                                       data-doc-submission-attach-property-id="{{ $docSubmissionAttachProperty->id }}"/>
                                <div class="show-error red"></div>
                            @else
                                {{ $docSubmissionAttachProperty->name }}
                            @endif

                        </dd>

                        <dt>{{ __('labels.a205iken02window.content_file') }}</dt>
                        <dd class="bukken clearfix">
                            @if($type && $type == ACTION)
                                <input type="file" name="data-properties[{{ $k }}][attach_file][]"
                                       id="attach_file_{{ $k }}"
                                       class="attach_file attach_file_old"
                                       accept="image/png, image/gif, image/jpeg, image/bmp" multiple/><br/>
                                <div class="show-error-attach-file red"></div>
                                <div class="error-files-select-{{ $k }} red"></div>
                            @endif
                            <br/>
                            <div class="list-item-attach-file bqn-{{ $k }}">
                                @if($docSubmissionAttachProperty->docSubmissionAttachments)
                                    @php
                                        $docSubmissionAttachments = $docSubmissionAttachProperty->docSubmissionAttachments;
                                        $docSubmissionAttachments = $docSubmissionAttachments->map(function($item) {
                                            $item->file_no_covert = (int) mb_convert_kana($item->file_no, 'n');
                                            return $item;
                                        })->sortBy('id')->sortBy('file_no_covert');
                                    @endphp
                                    @foreach ($docSubmissionAttachments as $i => $docSubmissionAttachment)
                                        <div class="item-attach-file item-attach-file-old" data-key-attach="{{ $i }}">
                                            <input type="hidden"
                                                   name="data-properties[{{ $k }}][data-attach][{{ $i }}][doc_submission_attachment_id]"
                                                   value="{{ $docSubmissionAttachment->id }}"/>
                                            <img src="{{ asset($docSubmissionAttachment->attach_file) }}"/>
                                            @if($type && $type == ACTION)
                                                <div class="delete">
                                                    <span>{{ __('labels.a205shu02_window.order') }}</span>
                                                    <input type="text" class="em04 mb05 file_no"
                                                           name="data-properties[{{ $k }}][data-attach][{{ $i }}][file_no]"
                                                           value="{{ $docSubmissionAttachment->file_no }}"/><br/>
                                                    <div class="show-error-file-no"></div>
                                                    <input type="button" value="{{ __('labels.a203check.delete') }}"
                                                           class="btn_d delete-file"
                                                           data-id="{{ $docSubmissionAttachment->id }}"/>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                        </dd>
                    </div>
                @endforeach
            @endif
        </dl>
        @if($type && $type == ACTION)
            <p class="eol"><a href="#" id="add_row_doc_submission"
                              class="add_row_reason">{{ __('labels.a205shu02_window.add_row') }}</a></p>
        @endif

    </div>

    @include('admin.layouts.footer', ['class' => 'footer-custom'])
</div>
<style>
    .item-attach-file {
        display: block;
    }

    .disabled {
        cursor: default;
        background-color: -internal-light-dark(rgba(239, 239, 239, 0.3), rgba(59, 59, 59, 0.3));
        color: -internal-light-dark(rgb(84, 84, 84), rgb(170, 170, 170));
        border-color: rgba(118, 118, 118, 0.3);
    }

    .white-space-cus {
        white-space: pre-line;
    }

    .list-item-attach-file {
        margin-bottom: 30px !important;
    }
    .delete-file {
        margin-bottom: 30px;
    }
    .custom_textarea {
        width: 100%!important;
        margin-right: 10px!important;
    }
    @media only screen and (max-width: 1430px) {
        .data-property-name {
            margin-bottom: 8px;
        }
    }
</style>
<script src="{{ asset('common/js/iframe-custom.js') }}"></script>
<script>
    const textDeleteRow = "{{ __('labels.a205shu02_window.delete_row') }}";
    const textNameSubmission = "{{ __('labels.a205iken02window.name_submission') }}";
    const contentFile = "{{ __('labels.a205iken02window.content_file') }}";
    const textOrder = "{{ __('labels.a205shu02_window.order') }}";
    const labelDelete = "{{ __('labels.delete') }}";
    const textTitleModal = "{{ __('labels.a205shu02_window.modal.title_1') }}";
    const textContentModal = "{{ __('labels.a205shu02_window.modal.content_1') }}";
    const textCancel = "{{ __('labels.a205shu02_window.modal.cancel') }}";
    const errorMessageMaxLength1000String = "{{ __('messages.general.Common_E055') }}";
    const errorMessageRequired = "{{ __('messages.general.Common_E001') }}";
    const errorMessageMaxLength255 = "{{ __('messages.general.Common_E029') }}";
    const errorMessageFormatFileNo = "{{ __('messages.general.correspondence_A205_E001') }}";
    const errorMessageMaxFileSize3MB = "{{ __('messages.general.Common_E028') }}";
    const Freerireki_E003 = "{{ __('messages.general.Freerireki_E003') }}";
    const Import_A000_E001 = "{{ __('messages.general.Import_A000_E001') }}"; //validate max 20 file
    const errorMesssageFormatFile = "{{ __('messages.general.Common_E045') }}";
    const routeDeleteDocSubmissionAttachProperty = "{{ route('admin.refusal.documents.a205-shu02-window.doc-submission-attach-property.delete') }}";
    const routeDeleteDocSubmissionAttach = "{{ route('admin.refusal.documents.a205-shu02-window.doc-submission-attachment.delete') }}";
</script>
@if($type != VIEW)
    <script type="text/JavaScript" src="{{ asset('admin_assets/doc-submissions/common/a205shu02window.js') }}"></script>
@endif
