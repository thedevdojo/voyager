<a class="btn btn-danger" id="bulk_delete_btn"><i class="voyager-trash"></i> Bulk delete</a>

{{-- Bulk delete modal --}}
<div class="modal modal-danger fade" tabindex="-1" id="bulk_delete_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-trash"></i> Are you sure you want to delete
                    these {{ strtolower($dataType->display_name_plural) }}?</h4>
            </div>
            <div class="modal-body" id="bulk_delete_modal_body">
            </div>
            <div class="modal-footer">
                <form action="{{ route('voyager.'.$dataType->slug.'.index') }}/0" id="bulk_delete_form" method="POST">
                    {{ method_field("DELETE") }}
                    {{ csrf_field() }}
                    <input type="hidden" name="ids" id="bulk_delete_input" value="">
                    <input type="submit" class="btn btn-danger pull-right delete-confirm"
                             value="Yes, delete these {{ strtolower($dataType->display_name_plural) }}">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
window.onload = function () {
    // Bulk delete selectors
    var $bulkDeleteBtn = $('#bulk_delete_btn');
    var $bulkDeleteModal = $('#bulk_delete_modal');
    var $bulkDeleteModalBody = $('#bulk_delete_modal_body');
    var $bulkDeleteInput = $('#bulk_delete_input');
    // Reposition modal to prevent z-index issues
    $bulkDeleteModal.appendTo('body');
    // Bulk delete listener
    $bulkDeleteBtn.click(function () {
        var ids = [];
        var $checkedBoxes = $('#dataTable input[type=checkbox]:checked');
        if ($checkedBoxes.length) {
            // Reset input value
            $bulkDeleteInput.val('');
            // Gather IDs and build modal's message
            var displayName = $checkedBoxes.length > 1 ? '{{ $dataType->display_name_plural }}' : '{{ $dataType->display_name_singular }}';
            var html = '<div>You\'re about to delete ' + $checkedBoxes.length + ' ' + displayName;
            $.each($checkedBoxes, function () {
                var value = $(this).val();
                ids.push(value);
            })
            html += '</div>';
            $bulkDeleteModalBody.html(html);
            // Set input value
            $bulkDeleteInput.val(ids);
            // Show modal
            $bulkDeleteModal.modal('show');
        } else {
            // No row selected
            toastr.warning('You haven\'t selected anything to delete');
        }
    });
}
</script>