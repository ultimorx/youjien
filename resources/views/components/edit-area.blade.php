<div class="modal" tabindex="-1" role="dialog" data-backdrop="static" id="edit-area">
    <div class="modal-dialog {{ $size ?? '' }}">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">{{ $title ?? '' }}</div>
                <div class="text-right">
                    <div class="text-right">
                        <button type="button" class="btn btn-primary btn-save">登録</button>
                        <button type="button" class="btn btn-light btn-close" data-dismiss="modal">閉じる</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form accept-charset="utf-8" id="edit-form">
                    <div class="alert alert-danger" v-if="validation_error">
                        <ul class="list-unstyled">
                            <li v-for="message in validation_error">@{{message}}</li>
                        </ul>
                    </div>
                    {{ $slot }}
                </form>
            </div>
            <div class="modal-footer">
                <div class="text-right">
                    <button type="button" class="btn btn-primary btn-save">登録</button>
                    <button type="button" class="btn btn-light btn-close" data-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
</div>
