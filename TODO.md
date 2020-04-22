This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Change variables to use kebab-case everywhere
- Make sure all input with class `voyager-input` use `w-full` (where applicable)
- Source out all colors from styles to `colors.scss`
- Add :disabled theming to buttons/inputs
- Filter available plugins by type
- PurgeCSS strips out a lot classes. Check everything and add it to the whitelist if necessary
- Replace Vuex with a simple global storage

## Nice to have
- Validate (hash) AJAX data and check in controller
- Validate layouts when saving a BREAD for formfields that don't have a field or double-assigned fields
- Replace `vue-draggable` with `vue-slicksort` (much smaller, much cooler)
- Add dark boxshadow variant to tailwind
- When saving a BREAD check if all needed routes exist (route caching)

## Styling
- ~Align tailwinds color palette to something nicer~
- ~Add more shades (`bg-gray-50`, `bg-gray-950`)~
- Closed collapsible should not have vertical padding on the cards content div when its closed

## Bugs
- Pagination: When there are only 3 pages it shows as 1,2...3

## Documentation
- Relationship methods NEED TO define the return-type. Otherwise they won't be recognized by the BREAD builder
- Scopes need to start with `scope` (ex. `scopeCurrentUser()`)
- Accessors need to be named `getFieldAttribute` (ex. `getFullNameAttribute`)
- Computed properties need to implement an accessor AND mutator when used for adding or editing
- Browse filters can be cleared by double-clicking the input
- Browse searching on translatable formfields searches in the currently selected locale
- BREAD menu-badge only shows non-soft-deleted entries
- Translatable: Use `getTranslated($column, $locale, $fallback, $default)` to get a translated value (which is not the default locale)
- Translatable: Use `setTranslated($column, $value, $locale)` to set a translated value (which is not the default locale)

## Checklist
The following things need to be double checked
