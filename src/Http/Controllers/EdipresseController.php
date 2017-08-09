<?php

namespace TCG\Voyager\Http\Controllers;

use TCG\Voyager\Http\Controllers\Controller as VoyagerController;

abstract class EdipresseController extends VoyagerController
{

    public function insertUpdateData($request, $slug, $rows, $data)
    {
        $multi_select = [];

        /*
         * Prepare Translations and Transform data
         */

        foreach ($rows as $row) {
            $options = json_decode($row->details);

            $content = $this->getContentBasedOnType($request, $slug, $row);

            /*
             * merge ex_images and upload images
             */
            if ($row->type == 'multiple_images' && !is_null($content)) {
                if (isset($data->{$row->field})) {
                    $ex_files = json_decode($data->{$row->field}, true);
                    if (!is_null($ex_files)) {
                        $content = json_encode(array_merge($ex_files, json_decode($content)));
                    }
                }
            }

            if (is_null($content)) {

                // If the image upload is null and it has a current image keep the current image
                if ($row->field == 'image' && is_null($request->input($row->field)) && isset($data->{$row->field})) {
                    $content = $data->{$row->field};
                }

                if ($row->field == 'password') {
                    $content = $data->{$row->field};
                }
            }

            if ($row->type == 'select_multiple' && property_exists($options, 'relationship')) {
                // Only if select_multiple is working with a relationship
                $multi_select[] = ['row' => $row->field, 'content' => $content];
            } else {
                $data->{$row->field} = $content;
            }
        }

        $data->fill($request->all());

        //$data->save();

        foreach ($multi_select as $sync_data) {
            $data->{$sync_data['row']}()->sync($sync_data['content']);
        }

        return $data;
    }
}
