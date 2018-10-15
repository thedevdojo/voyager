# Missing required parameter

**Symptom:** You get an error page saying

```text
Missing required parameters for [Route...]
```

**Cause:** There are two possible causes:  
1. You dont have a primary-key for your table  
2. You have a primary-key but it's **not** called `id`

**Solution:** As there are two causes, there are also two solutions: 1. Simply create a field `id` for the table  
2. Tell your model about your primary-key: `protected $primaryKey = 'your_primary_key';`

Please consider following [Eloquents model conventions](https://laravel.com/docs/eloquent#eloquent-model-conventions)

