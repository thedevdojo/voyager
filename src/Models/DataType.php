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
        return $this->hasMany(Voyager::modelClass('DataRow'));
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

            if ($this->fill($requestData)->save()) {
                $fields = $this->fields(array_get($requestData, 'name'));

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

                    if (!$dataRow->save()) {
                        throw new \Exception('Failed to save field '.$field.", we're rolling back!");
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

    public function fieldOptions()
    {
        $table = $this->name;

        $fieldOptions = SchemaManager::describeTable($table);

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
