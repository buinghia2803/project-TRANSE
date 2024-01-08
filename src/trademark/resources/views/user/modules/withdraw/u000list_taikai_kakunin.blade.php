@extends('user.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <form>
            <h3>{{ __('labels.withdraw_confirm.title') }}</h3>
            <div class="js-scrollable eol">
            <table class="normal_b" id="list_table">
                <thead>
                    <tr>
                        <th>{{ __('labels.withdraw_confirm.trademark_number') }} 
                            <a href="javascript:void(0)" class="sort_field" data-type='desc' data-sort_field="trademark_number">▼</a>
                            <a href="javascript:void(0)" class="sort_field" data-type="asc" data-sort_field="trademark_number">▲</a>
                        </th>
                        <th>{{ __('labels.withdraw_confirm.reference_number') }}</th>
                        <th>{{ __('labels.withdraw_confirm.type_trademark') }}</th>
                        <th>{{ __('labels.withdraw_confirm.trademark_info') }}</th>
                        <th>{{ __('labels.withdraw_confirm.register_number') }} 
                            <a href="javascript:void(0)" class="sort_field" data-type='desc' data-sort_field="register_number">▼</a>
                            <a href="javascript:void(0)" class="sort_field" data-type="asc" data-sort_field="register_number">▲</a></th>
                        <th>{{ __('labels.withdraw_confirm.notice_detail_content') }}</th>
                        <th>{{ __('labels.withdraw_confirm.notice_detail_created_at') }} 
                            <a href="javascript:void(0)" class="sort_field" data-type='desc' data-sort_field="notice_created_at">▼</a>
                            <a href="javascript:void(0)" class="sort_field" data-type="asc" data-sort_field="notice_created_at">▲</a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @include('user.modules.withdraw.partials.taikai_kakunin_table', ['trademarks' => $trademarks])
                </tbody>
            </table>
            </div><!-- /scroll wrap -->

            <ul class="footerBtn clearfix">
                <li><button style="width: 129px; height: 38px; padding:0; text-align: center;font-size: 1.3em;" type="button" class="btn_a">{{ __('labels.withdraw_confirm.back') }}</button></li>
                <li><a style="width: 147px; height: 38px;padding:0; text-align: center;font-size: 1.3em;line-height: 38px;color: white;" href="{{ route('user.menu-new-apply') }}" class="btn_b">{{ __('labels.withdraw_confirm.next') }}</a></li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('script')
<script type="text/JavaScript">
    const routeSortAjax = '{{ route('user.withdraw.anken-list.sort') }}'
</script>
<script type="text/JavaScript" src="{{ asset('end-user/withdraw/u000list_taikai_kakunin.js') }}"></script>
@endsection
