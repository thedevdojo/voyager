# Coordinates

## Optional Details

Set these in the Edit BREAD interface

### showLatLngInput / showAutocompleteInput

Set either of these to `false` to not include that input. Both default to `true`.

```json
{
  "showAutocompleteInput": false,
  "showLatLngInput": false
}
```

### onChange

```json
{
  "onChange": "myFunction"
}
```

Defines an onChange callback so that you can perform subsequent actions (such as using the Autocomplete Place address to populate another field) after the user changes any of the inputs in this formfield.

onChange callback is debounced at 300ms.

First parameter is `eventType` ("mapDragged", "latLngChanged", or "placeChanged"). Second parameter is `data` object containing `lat`, `lng`, and `place` properties.

```javascript
function myFunction(eventType, data) {
  console.log('eventType', eventType);
  console.log('data.lat', data.lat);
  console.log('data.lng', data.lng);
  console.log('data.place', data.place);
}
```
