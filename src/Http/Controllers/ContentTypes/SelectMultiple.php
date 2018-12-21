<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

class SelectMultiple extends BaseType
{
    public function handle()
    {

        $content = $this->request->input($this->row->field, []);

        if (true === empty($content)) {
            return $this->prepareResponse([]);
        }

        // Check if we need to parse the editablePivotFields to update fields in the corresponding pivot table

        if (isset($this->options->relationship) && !empty($this->options->relationship->editablePivotFields)) {
            $pivotContent = [];
            // Read all values for fields in pivot tables from the request
            foreach ($this->options->relationship->editablePivotFields as $pivotField) {
                if (!isset($pivotContent[$pivotField])) {
                    $pivotContent[$pivotField] = [];
                }
                $pivotContent[$pivotField] = $this->request->input('pivot_'.$pivotField);
            }
            // Create a new content array for updating pivot table
            $newContent = [];
            foreach ($content as $contentIndex => $contentValue) {
                $newContent[$contentValue] = [];
                foreach ($pivotContent as $pivotContentKey => $value) {
                    $newContent[$contentValue][$pivotContentKey] = $value[$contentIndex];
                }
            }
            $content = $newContent;
        }

        return $this->prepareResponse($content);
    }

     /*
     * prepare response for saving data from request into database (before return)
     * if Model has casts and cast is json or array, we don't need json_encode
     */
    protected function prepareResponse($response)
    {

        $casts = $this->data->getCasts();
        if ($casts[$this->row->field]) {
            $cast = $casts[$this->row->field];
            if (in_array($cast, ['json', 'array']) !== false)
                return $response;
        }

        return json_encode($response);
    }
}
