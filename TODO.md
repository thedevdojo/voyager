This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Change variables to use kebab-case everywhere
- Rename language-strings like `voyager::bread.browse_name` and `voyager::generic.add_type` to be uniform (use `type`!)
- Remove csrf_token as data-prop from AJAX requests as it is sended globally as a header
- Tune Modal and Pagination components (Tailwind UI?)
- Make sure all input with class `voyager-input` use `w-full` (where applicable)
- Source out all colors from styles to `colors.scss`
- Add :disabled theming to buttons/inputs
- Filter available plugins by type

## Nice to have
- Validate (hash) AJAX data and check in controller
- Validate layouts when saving a BREAD for formfields that don't have a field or double-assigned fields

## Documentation
- Relationship methods NEED TO define the return-type. Otherwise they won't be recognized by the BREAD builder
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing

## Checklist
The following things need to be double checked

- Sorting. Try to sort by an accessor and by a relationship property. It should also work when set as default-sort.