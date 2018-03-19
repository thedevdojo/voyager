<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class DataTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $dataType = $this->dataType('slug', 'users');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'users',
                'display_name_singular' => __('voyager::seeders.data_types.user.singular'),
                'display_name_plural'   => __('voyager::seeders.data_types.user.plural'),
                'icon'                  => 'voyager-person',
                'model_name'            => 'TCG\\Voyager\\Models\\User',
                'policy_name'           => 'TCG\\Voyager\\Policies\\UserPolicy',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataType = $this->dataType('slug', 'menus');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'menus',
                'display_name_singular' => __('voyager::seeders.data_types.menu.singular'),
                'display_name_plural'   => __('voyager::seeders.data_types.menu.plural'),
                'icon'                  => 'voyager-list',
                'model_name'            => 'TCG\\Voyager\\Models\\Menu',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataType = $this->dataType('slug', 'roles');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'roles',
                'display_name_singular' => __('voyager::seeders.data_types.role.singular'),
                'display_name_plural'   => __('voyager::seeders.data_types.role.plural'),
                'icon'                  => 'voyager-lock',
                'model_name'            => 'TCG\\Voyager\\Models\\Role',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }
    }

    /**
     * [dataType description].
     *
     * @param [type] $field [description]
     * @param [type] $for   [description]
     *
     * @return [type] [description]
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }
}
