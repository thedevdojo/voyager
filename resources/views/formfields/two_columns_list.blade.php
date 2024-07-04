<br>
@php 
    $currentSimpleList = isset($dataTypeContent->{$row->field}) ? $dataTypeContent->{$row->field} : '{}';
    $currentSimpleList = json_decode($currentSimpleList, true);
@endphp
<div>
    <div id="simpleList-{{ $row->field }}">
        <input type="hidden" id="simple-list-{{ $row->field }}" name="{{ $row->field }}" data-name="{{ $row->display_name }}">
        <div id="simpleListContainer-{{ $row->field }}">
            <table class="table table-bordered simple-list-table">
                <thead>
                    <tr>
                        <th>{{ __('voyager::generic.element') }}</th>
                        <th>{{ __('voyager::generic.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($currentSimpleList) && (count($currentSimpleList)))
                        @foreach($currentSimpleList as $value)
                            <tr>
                                <td contenteditable="true">{{ $value }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm simple-list-move-up-{{ $row->field }}">&uarr;</button>
                                    <button type="button" class="btn btn-warning btn-sm simple-list-move-down-{{ $row->field }}">&darr;</button>
                                    <button type="button" class="btn btn-danger btn-sm simple-list-delete-row-{{ $row->field }}">{{ __('voyager::generic.delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td contenteditable="true"></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm simple-list-move-up-{{ $row->field }}" disabled>&uarr;</button>
                                <button type="button" class="btn btn-warning btn-sm simple-list-move-down-{{ $row->field }}" disabled>&darr;</button>
                                <button type="button" class="btn btn-danger btn-sm simple-list-delete-row-{{ $row->field }}">{{ __('voyager::generic.delete') }}</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-success" onclick="simpleListAddRow{{ $row->field }}()">{{ __('voyager::generic.add_element') }}</button>
    </div>
</div>

<script>
    // Function to add a new row
    function simpleListAddRow{{ $row->field }}() {
        const table = document.querySelector('#simpleListContainer-{{ $row->field }} .simple-list-table tbody');
        const newRow = table.insertRow();
        newRow.innerHTML = `
            <td contenteditable="true"></td>
            <td>
                <button type="button" class="btn btn-warning btn-sm simple-list-move-up-{{ $row->field }}">&uarr;</button>
                <button type="button" class="btn btn-warning btn-sm simple-list-move-down-{{ $row->field }}">&darr;</button>
                <button type="button" class="btn btn-danger btn-sm simple-list-delete-row-{{ $row->field }}">{{ __('voyager::generic.delete') }}</button>
            </td>
        `;
        attachCellEventListeners{{ $row->field }}(newRow.cells[0]); // Attach event listener to the new value cell
        updateJson{{ $row->field }}(); // Update JSON immediately after adding a row
        updateMoveButtons{{ $row->field }}(); // Update move buttons state
    }

    // Function to update JSON data
    function updateJson{{ $row->field }}() {
        const json = {};
        const table = document.querySelector('#simpleListContainer-{{ $row->field }} .simple-list-table tbody');

        // Iterate through rows to construct JSON object
        table.querySelectorAll('tr').forEach((row, index) => {
            const value = row.cells[0].innerText.trim();
            if (value !== '') {
                json[index] = value;
            }
        });

        // Update hidden input value with JSON string
        document.getElementById('simple-list-{{ $row->field }}').value = JSON.stringify(json, null, 2);
    }

    // Function to attach event listeners to editable cells
    function attachCellEventListeners{{ $row->field }}(cell) {
        cell.addEventListener('input', updateJson{{ $row->field }});
    }

    // Function to move a row up
    function moveRowUp{{ $row->field }}(row) {
        const prevRow = row.previousElementSibling;
        if (prevRow) {
            row.parentNode.insertBefore(row, prevRow);
            updateJson{{ $row->field }}();
            updateMoveButtons{{ $row->field }}();
        }
    }

    // Function to move a row down
    function moveRowDown{{ $row->field }}(row) {
        const nextRow = row.nextElementSibling;
        if (nextRow) {
            row.parentNode.insertBefore(nextRow, row);
            updateJson{{ $row->field }}();
            updateMoveButtons{{ $row->field }}();
        }
    }

    // Function to update the state of move buttons
    function updateMoveButtons{{ $row->field }}() {
        const rows = document.querySelectorAll('#simpleListContainer-{{ $row->field }} .simple-list-table tbody tr');
        rows.forEach((row, index) => {
            const moveUpButton = row.querySelector('.simple-list-move-up-{{ $row->field }}');
            const moveDownButton = row.querySelector('.simple-list-move-down-{{ $row->field }}');
            moveUpButton.disabled = index === 0;
            moveDownButton.disabled = index === rows.length - 1;
        });
    }

    // Event listener for dynamically added delete buttons and move buttons
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('simple-list-delete-row-{{ $row->field }}')) {
            const row = event.target.closest('tr');
            row.parentNode.removeChild(row);
            updateJson{{ $row->field }}();
            updateMoveButtons{{ $row->field }}();
        } else if (event.target.classList.contains('simple-list-move-up-{{ $row->field }}')) {
            moveRowUp{{ $row->field }}(event.target.closest('tr'));
        } else if (event.target.classList.contains('simple-list-move-down-{{ $row->field }}')) {
            moveRowDown{{ $row->field }}(event.target.closest('tr'));
        }
    });

    // Attach initial event listeners to existing cells and update move buttons
    document.querySelectorAll('#simpleListContainer-{{ $row->field }} .simple-list-table tbody tr').forEach(row => {
        attachCellEventListeners{{ $row->field }}(row.cells[0]);
    });
    updateMoveButtons{{ $row->field }}();

    updateJson{{ $row->field }}();
</script>