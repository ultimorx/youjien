<div class="modal" tabindex="-1" role="dialog" data-backdrop="static" id="popup-area">
    <div class="modal-dialog {{ $size ?? '' }}">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">{{ $title ?? '' }}</div>
                <div class="text-right">
                    <div class="text-right">
                        <button type="button" class="btn btn-light btn-close" data-dismiss="modal">閉じる</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button type="button" class="btn btn-light btn-close" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
</div>
