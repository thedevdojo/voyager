<?php

class PageTypeDataTypeSeeder extends DataTypesTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_type = $this->dataType('slug', 'page_types');
        if (!$data_type->exists) {
            $data_type->fill([
                'name'                  => 'page_types',
                'display_name_singular' => 'Page type',
                'display_name_plural'   => 'Page types',
                'icon'                  => 'voyager-window-list',
                'model_name'            => 'TCG\\Voyager\\Models\\PageType',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }
    }
}
