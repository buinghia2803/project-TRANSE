@php
    $btnModel = new \App\Models\NoticeDetailBtn;
@endphp

@if($admin->role == ROLE_OFFICE_MANAGER)
    <div class="button-group">
        @foreach($noticeDetailBtns as $noticeDetailBtn)
            @if(empty($noticeDetailBtn->is_hidden_btn) || $noticeDetailBtn->is_hidden_btn != true)
            @switch($noticeDetailBtn->btn_type)
                @case($btnModel::BTN_CREATE_HTML)
                    <input type="button" value="{{ $noticeDetailBtn->getBtnText() }}" class="btn_a mb-1"
                        data-type_btn="create_html"
                        data-route="{{ route('admin.notice-detail-btns.create-html', $noticeDetailBtn->id) }}"
                        @if($isCancel == true) disabled @endif
                    />
                @break

                @case($btnModel::BTN_XML_UPLOAD)
                    <input type="button" value="{{ $noticeDetailBtn->getBtnText() }}" class="btn_a mb-1"
                        data-type_btn="upload_xml"
                        data-route="{{ route('admin.notice-detail-btns.upload-xml', $noticeDetailBtn->id) }}"
                        @if($isCancel == true) disabled @endif
                    />
                    <form action="{{ route('admin.notice-detail-btns.upload-xml', $noticeDetailBtn->id) }}" method="POST" enctype="multipart/form-data" style="position: absolute;">
                        @csrf
                        <input type="file" value="" name="xml_file[]" accept="text/xml" multiple hidden />
                    </form>
                @break

                @case($btnModel::BTN_FIX)
                    <input type="button" value="{{ $noticeDetailBtn->getBtnText() }}" class="btn_a mb-1"
                    onclick="window.location = '{{ $noticeDetailBtn->url ?? '' }}'"
                    @if($isCancel == true) disabled @endif
                    />
                @break

                @case($btnModel::BTN_SEND_MAIL_REMIND)
                    <input type="button" value="{{ $noticeDetailBtn->getBtnText() }}" class="btn_a mb-1"
                        @if($isCancel == true) disabled @endif
                    />
                @break

                @case($btnModel::BTN_CONTACT_CUSTOMER)
                    <input type="button" value="{{ $noticeDetailBtn->getBtnText() }}" data-notice_detail_btns="{{ $noticeDetailBtn->id }}" class="btn_b mb-1 btn_contact_customer" data-date_click="{{ !empty($noticeDetailBtn->date_click) ? 'true' : 'false' }}"
                        @if($isCancel == true || !empty($noticeDetailBtn->date_click)) disabled @endif
                        data-type_btn="contact_customer"
                        data-route="{{ route('admin.notice-detail-btns.contact_customer', $noticeDetailBtn->id) }}"
                    />
                @break

                @case($btnModel::BTN_CONTACT_RESPONSE_PERSON)
                    <input type="button" value="{{ $noticeDetailBtn->getBtnText() }}" class="btn_b mb-1"
                        @if($isCancel == true) disabled @endif
                    />
                @break

                @case($btnModel::BTN_PDF_UPLOAD)
                    <input type="button" value="{{ $noticeDetailBtn->getBtnText() }}" class="btn_a mb-1"
                        data-type_btn="upload_pdf"
                        data-route="{{ route('admin.notice-detail-btns.upload-pdf', $noticeDetailBtn->id) }}"
                        @if($isCancel == true) disabled @endif
                    />
                    <form id="upload_pdf_{{ $noticeDetailBtn->id }}" action="{{ route('admin.notice-detail-btns.upload-pdf', $noticeDetailBtn->id) }}" method="POST" enctype="multipart/form-data" style="position: absolute;">
                        @csrf
                        <input type="file" value="" name="pdf_file[]" multiple hidden />
                    </form>
                @break
            @endswitch
            @endif
        @endforeach
    </div>

    <div class="show-error"></div>
@endif

<div class="xml-preview mt-2">
    @foreach($noticeDetailBtns->where('btn_type', $btnModel::BTN_XML_UPLOAD) as $noticeDetailBtn)
        @php
            $files = [];
            $trademarkDocuments = $noticeDetailBtn->trademarkDocuments;
            foreach ($trademarkDocuments as $item) {
                $item->url = \App\Helpers\FileHelper::getImage($item->url);
                $files[] = $item;
            }
        @endphp

        @if(count($files) > 0)
            <p class="mb-0">XML:</p>
            @foreach($files as $item)
                <p class="mb-1">
                    <a href="{{ asset($item->url) }}" target="_blank">{{ $item->name }}</a>
                </p>
            @endforeach
        @endif
    @endforeach
</div>

<div class="pdf-preview mt-2">
    @foreach($noticeDetailBtns->where('btn_type', $btnModel::BTN_PDF_UPLOAD) as $noticeDetailBtn)
        @php
            $files = [];
            $trademarkDocuments = $noticeDetailBtn->trademarkDocuments;
            foreach ($trademarkDocuments as $item) {
                $item->url = \App\Helpers\FileHelper::getImage($item->url);
                $files[] = $item;
            }
        @endphp

        @if(count($files) > 0)
            <p class="mb-0">PDF:</p>
            @foreach($files as $item)
                <p class="mb-1">
                    <a href="{{ asset($item->url) }}" target="_blank">{{ $item->name }}</a>
                </p>
            @endforeach
        @endif
    @endforeach
</div>
