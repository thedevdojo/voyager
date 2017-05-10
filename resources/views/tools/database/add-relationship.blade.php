<div class="modal modal-info fade" tabindex="-1" id="additional_column" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-data"></i>Many to Many</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('voyager.database.bread.add_relationship', $dataType->id) }}" method="POST" role="form">
                  <!-- CSRF TOKEN -->
                  {{ csrf_field() }}

                  <input type="hidden" value="{{ $dataType->id }}" name="data_type_id">

                  <div class="form-group">
                    <label for="display_name" class="form-control-label">Display Name:</label>
                    <input type="text" class="form-control" name="display_name" id="display_name">
                  </div>
                  <div class="form-group">
                    <label for="optional_details" class="form-control-label">Optional Details:</label>
                    <div class="alert alert-danger validation-error">
                        Invalid JSON
                    </div>
                    <textarea id="details" class="resizable-editor" data-editor="json" name="details"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="input_type" class="form-control-label">Input Type:</label><br>
                    <select name="type">
                        @foreach (Voyager::formFields() as $formField)
                            <option
                              value="{{ $formField->getCodename() }}"
                              @if($formField->getCodename() == "select_multiple") selected @endif>
                                {{ $formField->getName() }}
                            </option>
                        @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-control-label">Visibility:</label><br>
                    <input type="checkbox"
                           id="browse"
                           name="browse">
                    <label for="browse">Browse</label>
                    <input type="checkbox"
                           id="read"
                           name="read">
                    <label for="read">Read</label>
                    <input type="checkbox"
                           id="edit"
                           name="edit">
                    <label for="edit">Edit</label>
                    <input type="checkbox"
                           id="add"
                           name="add">
                        <label for="add">Add</label>
                    <input type="checkbox"
                           id="delete"
                           name="delete">
                            <label for="delete">Delete</label>
                  </div>
                  <div class="modal-footer">
                      <button type="submit" class="btn btn-outline pull-right">Save</button>
                  </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->