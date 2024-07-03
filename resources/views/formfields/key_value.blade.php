<br>
@php 
    $currentKeyValue = isset($dataTypeContent->{$row->field}) ? $dataTypeContent->{$row->field} : '{}';
    $currentKeyValue = json_decode($currentKeyValue, true);
@endphp
<div>
    <div id="keyValue-{{ $row->field }}">
        <input type="hidden" id="key-value-{{ $row->field }}" name="{{ $row->field }}" data-name="{{ $row->display_name }}" @if($row->required == 1) required @endif>
        <div id="keyValueContainer-{{ $row->field }}">
            <table class="table table-bordered key-value-table">
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.key') }}</th>
                        <th>{{ __('voyager::database.field') }}</th>
                        <th>{{ __('voyager::generic.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($currentKeyValue) && (count($currentKeyValue)))
                        @foreach($currentKeyValue as $key => $value)
                            <tr>
                                <td contenteditable="true">{{ $key }}</td>
                                <td contenteditable="true">{{ $value }}</td>
                                <td><button type="button" class="btn btn-danger btn-sm key-value-delete-row-{{ $row->field }}">{{ __('voyager::generic.delete') }}</button></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td contenteditable="true"></td>
                            <td contenteditable="true"></td>
                            <td><button type="button" class="btn btn-danger btn-sm key-value-delete-row-{{ $row->field }}">{{ __('voyager::generic.delete') }}</button></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-success" onclick="keyValueAddRow{{ $row->field }}()">{{ __('voyager::generic.add') }}</button>
    </div>
</div>

<script>
    // Function to add a new row
    function keyValueAddRow{{ $row->field }}() {
        const table = document.querySelector('#keyValueContainer-{{ $row->field }} .key-value-table tbody');
        const newRow = table.insertRow();
        newRow.innerHTML = `
            <td contenteditable="true"></td>
            <td contenteditable="true"></td>
            <td><button type="button" class="btn btn-danger btn-sm key-value-delete-row-{{ $row->field }}">{{ __('voyager::generic.delete') }}</button></td>
        `;
        attachCellEventListeners{{ $row->field }}(newRow.cells[0]); // Attach event listener to the new key cell
        attachCellEventListeners{{ $row->field }}(newRow.cells[1]); // Attach event listener to the new value cell
        updateJson{{ $row->field }}(); // Update JSON immediately after adding a row
    }

    // Function to update JSON data
    function updateJson{{ $row->field }}() {
        const json = {};
        const table = document.querySelector('#keyValueContainer-{{ $row->field }} .key-value-table tbody');

        // Iterate through rows to construct JSON object
        table.querySelectorAll('tr').forEach(row => {
            const key = row.cells[0].innerText.trim();
            const value = row.cells[1].innerText.trim();
            if (key !== '' && value !== '') {
                json[key] = value;
            }
        });

        // Update hidden input value with JSON string
        document.getElementById('key-value-{{ $row->field }}').value = JSON.stringify(json, null, 2);
    }

    // Function to attach event listeners to editable cells
    function attachCellEventListeners{{ $row->field }}(cell) {
        cell.addEventListener('input', updateJson{{ $row->field }});
    }

    // Event listener for dynamically added delete buttons
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('key-value-delete-row-{{ $row->field }}')) {
            const row = event.target.closest('tr');
            row.parentNode.removeChild(row);
            updateJson{{ $row->field }}();
        }
    });

    // Attach initial event listeners to existing cells
    document.querySelectorAll('#keyValueContainer-{{ $row->field }} .key-value-table tbody tr td').forEach(cell => {
        attachCellEventListeners{{ $row->field }}(cell);
    });

    updateJson{{ $row->field }}();
</script>