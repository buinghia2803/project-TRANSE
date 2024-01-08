<div id="modal-203-check" class="modal fade" role="dialog">
    <div class="modal-dialog" style="min-width: 60%;min-height: 60%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                <div class="content loaded">
                    <iframe
                        src="{{ route('admin.refusal.response-plan.modal-a203check', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null])}}"
                        style="width: 100%; height: 60vh;"
                        frameborder="0"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
