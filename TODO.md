This Todo list does **not** contain big improvements.  
It only contains small things that came to our minds which we couldn't implement immediately and would be forgotten otherwise.  
Instead, they are listed here:

- Add :disabled theming to buttons/inputs
- Filter available plugins by type
- PurgeCSS strips out a lot classes. Check everything and add it to the whitelist if necessary
- Media manager should not upload all files simultaneously

## Nice to have
- Validate layouts when saving a BREAD for formfields that don't have a field or double-assigned fields and validation-rules that don't have a rule **This is actually important**
- Add dark boxshadow variant to tailwind
- When saving a BREAD check if all needed routes exist (route caching)

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
- Translatable: Use `Ctrl` + `up/right` to select the next locale, `Ctrl` + `down/left` to select the previous locale
- Settings: Use `VoyagerSettings::settings()` with key `null` to get all settings, key `something` to get a whole group (first) or a setting with that name and no group, or key `group.name` to get a settings with that group and key.
- Backing-up a BREAD always uses the current stored state. So when backing-up after changing something (without saving first), the changes will NOT be included in the backup

## Checklist
The following things need to be double checked

- Browse relationships: ~BelongsTo~, ~BelongsToMany~, ~HasOne~, ~HasMany~
