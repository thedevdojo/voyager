<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class DataRow extends Model
{
    protected $table = 'data_rows';

    protected $guarded = [];

    public $timestamps = false;

    public function addRelationshipRow($dataType, $requestData)
    {
        $model = app($dataType->model_name);

        $options = json_decode($requestData['details']);

        if (!method_exists($model, $options->relationship->method)) {
            throw new \Exception("Please provide the Eloquent relationship method name", 1);
        }

        $relatedModel = $model->{$options->relationship->method}()->getRelated();

        $field = $relatedModel->getTable() . "_" . $relatedModel->getKeyName();

        $dataRow = $dataType->rows()->firstOrNew(['field' => $field, 'details' => $requestData['details'], 'type' => $requestData['type']]);

        if (isset($requestData['display_name'])) $dataRow->display_name = $requestData['display_name'];

        foreach (['browse', 'read', 'edit', 'add', 'delete'] as $check) {
            $dataRow->{$check} = isset($requestData["{$check}"]);
        }

        return $dataRow->save();
    }
}
