<?php

namespace TCG\Voyager\Traits;

use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

trait BreadSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDataType();
        $this->createInputFields();
        $this->createMenuItem();
        $this->generatePermissions();
    }

    /**
     * Create a new data-type for the current bread
     *
     * @return void
     */
    public function createDataType()
    {
        $dataType = $this->dataType('slug', $this->bread()['name']);
        if (!$dataType->exists) {
            $dataType->fill($this->bread())->save();
        }
    }

    /**
     * Create all the input fields specified in the
     * bread() method
     *
     * @return [type] [description]
     */
    public function createInputFields()
    {
        $productDataType = DataType::where('slug', $this->bread()['name'])->firstOrFail();

        collect($this->inputFields())->each(function ($field, $key) use ($productDataType) {
            $dataRow = $this->dataRow($productDataType, $key);
            if (!$dataRow->exists) {
                $dataRow->fill($field)->save();
            }
        });

    }

    /**
     * Create the new menu entry using the configuration
     * specified in the menuEntry() method. IF set to null
     * then no menu entry is going to be created
     *
     * @return [type] [description]
     */
    public function createMenuItem()
    {
        if (empty($this->menuEntry())) {
            return;
        }
        $menuEntry = collect($this->menuEntry());

        if (empty($menuEntry->menu_id)) {
            $menu = Menu::where('name', $menuEntry->get('role'))->firstOrFail();
            $menuEntry = $menuEntry->put('menu_id', $menu->id);
        }

        $menuItem = MenuItem::firstOrNew($menuEntry->only(['menu_id', 'title', 'url', 'route'])->toArray());
        if (!$menuItem->exists) {
            $menuItem->fill($menuEntry->only(['target', 'icon_class', 'color', 'parent_id', 'order'])->toArray())->save();
        }
    }

    /**
     * Generates admin permissions to the current
     * bread
     *
     * @return void
     */
    public function generatePermissions()
    {
        Permission::generateFor($this->bread()['name']);
    }

    /**
     * Find or create a new data-type
     *
     * @param  string $field Field name
     * @param  string $for   Bread name
     *
     * @return DataType::class
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }

    /**
     * Find or create a new data-row
     *
     * @param  string $type  Type name
     * @param  string $field Field name
     *
     * @return DataType::class
     */
    protected function dataRow($type, $field)
    {
        return DataRow::firstOrNew([
                'data_type_id' => $type->id,
                'field'        => $field,
            ]);
    }
}
