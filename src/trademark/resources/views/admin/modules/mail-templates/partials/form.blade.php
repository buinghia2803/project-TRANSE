@if(count(CommonHelper::langOption()) > 0)
    @include('admin.components.forms.radio', [
        'name' => 'lang',
        'value' => request()->lang ?? $mailTemplate->lang ?? App::getLocale(),
        'label' => __('labels.language'),
        'required' => false,
        'options' => CommonHelper::langOption(),
        'inline' => true
    ])
@endif

@include('admin.components.forms.tag', [
    'name' => 'cc',
    'value' => (!empty($mailTemplate->cc)) ? explode(",", $mailTemplate->cc) : [],
    'label' => __('labels.mail_template_cc'),
    'required' => false,
    'select2' => true,
    'options' => (!empty($mailTemplate->cc)) ? explode(",", $mailTemplate->cc) : [],
    'isMultiple' =>  true
])

@include('admin.components.forms.tag', [
    'name' => 'bcc',
    'value' => (!empty($mailTemplate->bcc)) ? explode(",", $mailTemplate->bcc) : [],
    'label' => __('labels.mail_template_bcc'),
    'required' => false,
    'select2' => true,
    'options' => (!empty($mailTemplate->bcc)) ? explode(",", $mailTemplate->bcc) : [],
    'isMultiple' =>  true
])

@include('admin.components.forms.text', [
    'type' => 'text',
    'name' => 'subject',
    'value' => $mailTemplate->subject ?? '',
    'label' => __('labels.mail_template_subject'),
    'placeholder' => __('labels.mail_template_subject'),
    'required' => true,
])

@include('admin.components.forms.editor', [
    'name' => 'content',
    'value' => $mailTemplate->content ?? null,
    'label' => __('labels.mail_template_content'),
    'placeholder' => __('labels.mail_template_content'),
    'required' => true,
])

@if(!empty($mailTemplateType['note']))
    <div class="form-group row">
        <label class="col-12 col-lg-3 col-xl-2 col-form-label"></label>
        <div class="col-12 col-lg-9 col-xl-10">
            @foreach($mailTemplateType['note'] ?? [] as $note)
                @php
                    $note['param'] = (!empty($note['param'])) ? '{{' . $note['param'] . '}}' : '';
                @endphp
                <p class="mb-0"><strong>{{ $note['param'] }}</strong>: {{ __($note['label'] ?? '') }}</p>
            @endforeach
        </div>
    </div>
@endif

@include('admin.components.forms.file', [
    'name' => 'attachment',
    'value' => $mailTemplate->attachment ?? null,
    'label' => __('labels.mail_template_attachment'),
    'placeholder' => __('labels.mail_template_attachment'),
    'required' => false,
    'buttonText' => __('labels.upload'),
])
