<h3>{{ __('labels.support_first_times.mailing_regis_cert.title') }}</h3>

<p class="eol">
    <input type="checkbox" id="is_mailing_regis_cert" name="is_mailing_regis_cert" {{ !empty($appTrademark) && $appTrademark->is_mailing_regis_cert == 1 ? 'checked': '' }} value="1" />
    {{ __('labels.support_first_times.mailing_regis_cert.note_1') }} <br />
    <span class="note">{{ __('labels.u031b.separate_fee') }}（{{ \CommonHelper::formatPrice(($mailRegisterCert->base_price +  $setting->value * $mailRegisterCert->base_price / 100)) ?? 0 }}{{ __('labels.u031b.currency') }}）{{ __('labels.u031b.will_occur') }}。<br />
    {{ __('labels.u031b.note_certificate') }}</span>
</p>
