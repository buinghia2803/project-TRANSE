@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        <h2>{{ __('labels.refusal_plans.u203b02paid.title') }}</h2>
        <form>
            <p>{{ __('labels.refusal_plans.u203b02paid.sub_title') }}</p>
            <p>
                <a href="{{ route('user.refusal.materials.index', [
                        'id' => $sessionData['comparison_trademark_result_id'],
                        'trademark_plan_id' => $sessionData['trademark_plan_id']
                    ]
                ) }}">
                    {{ __('labels.refusal_plans.u203b02paid.text_link_1') }}<br>
                    {{ __('labels.refusal_plans.u203b02paid.text_link_2') }}
                </a>
            </p>
        </form>
    </div>
 @endsection