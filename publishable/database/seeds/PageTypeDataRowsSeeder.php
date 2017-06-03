<?php

use TCG\Voyager\Models\DataType;

class PageTypeDataRowsSeeder extends DataRowsTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Add attributes to PageType
         */

        $page_type_data_row = DataType::where('slug', 'page_types')->firstOrFail();

        // ID
        $data_row = $this->dataRow($page_type_data_row, 'id');
        if (!$data_row->exists) {
            $data_row->fill([
                'type'         => 'number',
                'display_name' => 'ID',
                'required'     => 1,
                'browse'       => 1,
                'read'         => 1,
                'edit'         => 0,
                'add'          => 0,
                'delete'       => 0,
                'details'      => '',
            ])->save();
        }

        // Name
        $data_row = $this->dataRow($page_type_data_row, 'name');
        if (!$data_row->exists) {
            $data_row->fill([
                'type'         => 'text',
                'display_name' => 'name',
                'required'     => 1,
                'browse'       => 1,
                'read'         => 1,
                'edit'         => 1,
                'add'          => 1,
                'delete'       => 1,
                'details'      => '',
            ])->save();
        }

        // Controller
        $data_row = $this->dataRow($page_type_data_row, 'controller');
        if (!$data_row->exists) {
            $data_row->fill([
                'type'         => 'text',
                'display_name' => 'controller',
                'required'     => 1,
                'browse'       => 1,
                'read'         => 1,
                'edit'         => 1,
                'add'          => 1,
                'delete'       => 1,
                'details'      => '',
            ])->save();
        }

        // Action
        $data_row = $this->dataRow($page_type_data_row, 'action');
        if (!$data_row->exists) {
            $data_row->fill([
                'type'         => 'text',
                'display_name' => 'action',
                'required'     => 1,
                'browse'       => 1,
                'read'         => 1,
                'edit'         => 1,
                'add'          => 1,
                'delete'       => 1,
                'details'      => '',
            ])->save();
        }

        /*
         * Add page_type_id to Page
         */
        $page_data_row = DataType::where('slug', 'pages')->firstOrFail();
        $data_row = $this->dataRow($page_data_row, 'page_type_id');
        if (!$data_row->exists) {
            $data_row->fill([
                'type'         => 'select_dropdown',
                'display_name' => 'page_type_id',
                'required'     => 0,
                'browse'       => 1,
                'read'         => 1,
                'edit'         => 1,
                'add'          => 1,
                'delete'       => 1,
                'details'      => '{"relationship":{"key":"id","label":"name","page_slug":"admin/page_types"}}',
            ])->save();
        }
    }
}
