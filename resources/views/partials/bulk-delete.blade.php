<a class="btn btn-danger" id="bulk_delete_btn"><i class="voyager-trash"></i> <span>{{ __('voyager.generic.bulk_delete') }}</span></a>

{{-- Bulk delete modal --}}
<div class="modal modal-danger fade" tabindex="-1" id="bulk_delete_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <i class="voyager-trash"></i> {{ __('voyager.generic.are_you_sure_delete') }} <span id="bulk_delete_count"></span> <span id="bulk_delete_display_name"></span>?
                </h4>
            </div>
            <div class="modal-body" id="bulk_delete_modal_body">
            </div>
            <div class="modal-footer">
                <form action="{{ route('voyager.'.$dataType->slug.'.index') }}/0" id="bulk_delete_form" method="POST">
                    {{ method_field("DELETE") }}
                    {{ csrf_field() }}
                    <input type="hidden" name="ids" id="bulk_delete_input" value="">
                    <input type="submit" class="btn btn-danger pull-right delete-confirm"
                             value="{{ __('voyager.generic.bulk_delete_confirm') }} {{ strtolower($dataType->display_name_plural) }}">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">
                    {{ __('voyager.generic.cancel') }}
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
window.onload = function () {
    // Bulk delete selectors
    var $bulkDeleteBtn = $('#bulk_delete_btn');
    var $bulkDeleteModal = $('#bulk_delete_modal');
    var $bulkDeleteCount = $('#bulk_delete_count');
    var $bulkDeleteDisplayName = $('#bulk_delete_display_name');
    var $bulkDeleteInput = $('#bulk_delete_input');
    // Reposition modal to prevent z-index issues
    $bulkDeleteModal.appendTo('body');
    // Bulk delete listener
    $bulkDeleteBtn.click(function () {
        var ids = [];
        var $checkedBoxes = $('#dataTable input[type=checkbox]:checked');
        var count = $checkedBoxes.length;
        if (count) {
            // Reset input value
            $bulkDeleteInput.val('');
            // Deletion info
            var displayName = count > 1 ? '{{ $dataType->display_name_plural }}' : '{{ $dataType->display_name_singular }}';
            displayName = displayName.toLowerCase();
            $bulkDeleteCount.html(count);
            $bulkDeleteDisplayName.html(displayName);
            // Gather IDs
            $.each($checkedBoxes, function () {
                var value = $(this).val();
                ids.push(value);
            })
            // Set input value
            $bulkDeleteInput.val(ids);
            // Show modal
            $bulkDeleteModal.modal('show');
        } else {
            // No row selected
            toastr.warning('{{ __('voyager.generic.bulk_delete_nothing') }}');
        }
    });
}
</script>