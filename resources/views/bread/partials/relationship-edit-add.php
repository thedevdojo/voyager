<script id="realtionship-edit-add" type="text/x-handlebars-template">
<tr>
    <td>
        <select id="select2-{{id}}" class="form-control select2" style="width: 100%">
            <option value="Feature 1">Feature 1</option>
            <option value="Feature 2">Feature 2</option>
            <option value="Feature 3">Feature 3</option>
        </select>
    </td>
    <td class="active"><input type="text" class="form-control" placeholder="value"></td>
    <td class="success"><input type="text" class="form-control" placeholder="value_unit"></td>
    <td class="warning"><input type="text" class="form-control" placeholder="value_type"></td>
    <td class="danger">
        <a href="#" onclick="javascript: return false;" class="btn-save-bread-relationship">save</a>
        <a href="#" onclick="javascript: return false;" class="btn-remove-bread-relationship">delete</a>
    </td>
</tr>
</script>