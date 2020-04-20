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
- Swap div's with class alert to use alert component instead
- PurgeCSS strips out a lot classes. Check everything and add it to the whitelist if necessary

## Nice to have
- Validate (hash) AJAX data and check in controller
- Validate layouts when saving a BREAD for formfields that don't have a field or double-assigned fields
- Replace `vue-draggable` with `vue-slicksort` (much smaller, much cooler)
- Add dark boxshadow variant to tailwind
- When saving a BREAD check if all needed routes exist (route caching)

## Styling
- Align tailwinds color palette to something nicer
- Add more shades (`bg-gray-50`, `bg-gray-950`)
- Closed collapsible should not have vertical padding on the cards content div when its closed

## Bugs
- Pagination: When there are only 3 pages it shows as 1,2...3

## Documentation
- Relationship methods NEED TO define the return-type. Otherwise they won't be recognized by the BREAD builder
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing
- Browse filters can be cleared by double-clicking the input

## Checklist
The following things need to be double checked

- Sorting. Try to sort by an accessor and by a relationship property. It should also work when set as default-sort.