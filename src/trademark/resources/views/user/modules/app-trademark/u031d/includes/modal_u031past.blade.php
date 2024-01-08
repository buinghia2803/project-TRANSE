<div id="u031pass-modal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="min-width: 60%;min-height: 60%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                <div class="content loaded">
                    <iframe src="{{route('user.apply-trademark.show-pass', [
                                 'id' => $id ?? 0,
                                 'from_page' => U031B
                                 ])}}" style="width: 100%; height: 80vh;" frameborder="0"></iframe></div>
            </div>
        </div>
    </div>
</div>
