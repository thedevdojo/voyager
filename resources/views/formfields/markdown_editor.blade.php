<textarea class="form-control easymde" name="{{ $row->field }}" id="markdown{{ $row->field }}">{{ old($row->field, $dataTypeContent->{$row->field} ?? '') }}</textarea>
