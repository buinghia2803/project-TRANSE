<h3> {{ __('labels.refusal_plans.u203.content_30') }}</h3>
@foreach ($trademarkPlan->plans as $keyPlan => $plan)
    @foreach ($plan->planReasons as $planReason)
        @foreach ($planReason->reasons as $key => $reason)
            <p> {{ __('labels.refusal_plans.u203.content_31') }}{{ $reason->reason_name }} {{ __('labels.refusal_plans.u203.content_32') }}<br />
                {{ !empty($reason->mLawsRegulation->id) ? $mLawRegulationContentDefault[$reason->mLawsRegulation->id] : '' }}
            </p>
        @endforeach
    @endforeach
@endforeach
