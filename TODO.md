This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Change variables to use kebab-case everywhere
- Rename language-strings like `voyager::bread.browse_name` and `voyager::generic.add_type` to be uniform (use `type` or `name`?)
- DateTimePicker needs a formatting feature
- Order by relationship
- Remove csrf_token as data-prop from AJAX requests as it is sended globally as a header
- Actions for BREAD-Browse is an object but should be an array
- Edit-Add modal for relationships needs to be emptied when the modal closes or opens
- Remove components `v-button` and `v-input` (?)

## Nice to have
- Validate (hash) AJAX data and check in controller
- Validate layouts when saving a BREAD for formfields that don't have a field or double-assigned fields