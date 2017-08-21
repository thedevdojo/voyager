<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Translatable;

class DataType extends Model
{
    use Translatable;

    protected $translatable = ['display_name_singular', 'display_name_plural'];

    protected $table = 'data_types';

    protected $fillable = [
        'name',
        'slug',
        'display_name_singular',
        'display_name_plural',
        'icon',
        'model_name',
        'controller',
        'description',
        'generate_permissions',
        'server_side',
    ];

    public function rows()
    {
        return $this->hasMany(Voyager::modelClass('DataRow'))->orderBy('order');
    }

    public function browseRows()
    {
        return $this->rows()->where('browse', 1);
    }

    public function readRows()
    {
        return $this->rows()->where('read', 1);
    }

    public function editRows()
    {
        return $this->rows()->where('edit', 1);
    }

    public function addRows()
    {
        return $this->rows()->where('add', 1);
    }

    public function deleteRows()
    {
        return $this->rows()->where('delete', 1);
    }

    public function setGeneratePermissionsAttribute($value)
    {
        $this->attributes['generate_permissions'] = $value ? 1 : 0;
    }

    public function setServerSideAttribute($value)
    {
        $this->attributes['server_side'] = $value ? 1 : 0;
    }

    public function updateDataType($requestData, $throw = false)
    {
        try {
            DB::beginTransaction();

            // Prepare data
            foreach (['generate_permissions', 'server_side'] as $field) {
                if (!isset($requestData[$field])) {
                    $requestData[$field] = 0;
                }
            }

            if ($this->fill($requestData)->save()) {
                $fields = $this->fields(array_get($requestData, 'name'));
                
                //dd($requestData);
                $requestData = $this->getRelationships($requestData, $fields);
                //dd($relationships);
                //dd($requestData);

                foreach ($fields as $field) {
                    $dataRow = $this->rows()->firstOrNew(['field' => $field]);

                    foreach (['browse', 'read', 'edit', 'add', 'delete'] as $check) {
                        $dataRow->{$check} = isset($requestData["field_{$check}_{$field}"]);
                    }

                    $dataRow->required = $requestData['field_required_'.$field];
                    $dataRow->field = $requestData['field_'.$field];
                    $dataRow->type = $requestData['field_input_type_'.$field];
                    $dataRow->details = $requestData['field_details_'.$field];
                    $dataRow->display_name = $requestData['field_display_name_'.$field];
                    $dataRow->order = intval($requestData['field_order_'.$field]);

                    if (!$dataRow->save()) {
                        throw new \Exception(__('voyager.database.field_safe_failed', ['field' => $field]));
                    }
                }

                // Clean data_rows that don't have an associated field
                // TODO: need a way to identify deleted and renamed fields.
                //   maybe warn the user and let him decide to either rename or delete?
                $this->rows()->whereNotIn('field', $fields)->delete();

                // It seems everything was fine. Let's check if we need to generate permissions
                if ($this->generate_permissions) {
                    Voyager::model('Permission')->generateFor($this->name);
                }

                DB::commit();

                return true;
            }
        } catch (\Exception $e) {
            DB::rollBack();

            if ($throw) {
                throw $e;
            }
        }

        return false;
    }

    public function fields($name = null)
    {
        if (is_null($name)) {
            $name = $this->name;
        }

        $fields = SchemaManager::listTableColumnNames($name);

        if ($extraFields = $this->extraFields()) {
            foreach ($extraFields as $field) {
                $fields[] = $field['Field'];
            }
        }

        return $fields;
    }

    public function getRelationships($requestData, &$fields){
        if(isset($requestData['relationship_field']) && count($requestData['relationship_field']) > 0){
            //dd(count($requestData['relationship_display_name']));
            foreach($requestData['relationship_field'] as $index => $relationship){
                    $relationshipField = $requestData['relationship_field'][$index];
                    
                    // Add the relationship field to the array of fields
                    array_push($fields, $relationshipField);
                    
                    // Build the relationship details
                    $relationshipDetails = [
                        'model' => $requestData['relationship_model'][$index],
                        'table' => $requestData['relationship_table'][$index],
                        'type' => $requestData['relationship_type'][$index],
                        'key' => $requestData['relationship_key'][$index],
                        'label' => $requestData['relationship_label'][$index]
                    ];

                    // Build the relationship field data and store it back in the request
                    $requestData['field_required_'.$relationshipField] = 0;
                    $requestData['field_'.$relationshipField] = $relationshipField;
                    $requestData['field_input_type_'.$relationshipField] = 'relationship';
                    $requestData['field_details_'.$relationshipField] = $relationshipDetails;
                    $requestData['field_display_name_'.$relationshipField] = $requestData['relationship_display_name'][$index];
                    $requestData['field_details_'.$relationshipField] = json_encode($relationshipDetails);

                    // !! WORK ON ORDER BELOW !!
                    //$requestData['field_order_'.$relationshipField] = json_encode($relationshipDetails);

                    $requestData['field_browse_'.$relationshipField] = $requestData['relationship_browse'][$index];
                    $requestData['field_read_'.$relationshipField] = $requestData['relationship_read'][$index];
                    $requestData['field_edit_'.$relationshipField] = $requestData['relationship_edit'][$index];
                    $requestData['field_add_'.$relationshipField] = $requestData['relationship_add'][$index];
                    $requestData['field_delete_'.$relationshipField] = $requestData['relationship_delete'][$index];
            }

            // Unset all the 'relationship_' fields
            foreach($requestData as $key => $value){
                if(substr( $key, 0, 13 ) == "relationship_"){
                    unset($requestData[$key]);
                }
            }
        }
        return $requestData;
    }

    public function fieldOptions()
    {
        $table = $this->name;

        // Get ordered BREAD fields
        $orderedFields = $this->rows()->pluck('field')->toArray();

        $_fieldOptions = SchemaManager::describeTable($table)->toArray();

        $fieldOptions = [];
        $f_size = count($orderedFields);
        for ($i = 0; $i < $f_size; $i++) {
            $fieldOptions[$orderedFields[$i]] = $_fieldOptions[$orderedFields[$i]];
        }
        $fieldOptions = collect($fieldOptions);

        if ($extraFields = $this->extraFields()) {
            foreach ($extraFields as $field) {
                $fieldOptions[] = (object) $field;
            }
        }

        return $fieldOptions;
    }

    public function extraFields()
    {
        if (empty(trim($this->model_name))) {
            return [];
        }

        $model = app($this->model_name);
        if (method_exists($model, 'adminFields')) {
            return $model->adminFields();
        }
    }
}
