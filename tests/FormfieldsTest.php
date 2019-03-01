<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Permission;

class FormfieldsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function testFormfieldText()
    {
        $this->createBreadForFormfield('text', 'text', json_encode([
            'default' => 'Default Text'
        ]));

        $this->visitRoute('voyager.categories.create')
             ->see('Default Text')
             ->type('New Text', 'text')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('New Text')
             ->click(__('voyager::generic.edit'))
             ->seeRouteIs('voyager.categories.edit', ['id' => 1])
             ->type('Edited Text', 'text')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('Edited Text');
    }

    public function testFormfieldTextbox()
    {
        $this->createBreadForFormfield('text', 'text_area', json_encode([
            'default' => 'Default Text'
        ]));

        $this->visitRoute('voyager.categories.create')
             ->see('Default Text')
             ->type('New Text', 'text_area')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('New Text')
             ->click(__('voyager::generic.edit'))
             ->seeRouteIs('voyager.categories.edit', ['id' => 1])
             ->type('Edited Text', 'text_area')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('Edited Text');
    }

    private function createBreadForFormfield($type, $name, $options = '')
    {
        Schema::dropIfExists('categories');
        Schema::create('categories', function ($table) use ($type, $name) {
            $table->bigIncrements('id');
            $table->{$type}($name);
            $table->timestamps();
        });

        // Delete old BREAD
        $this->delete(route('voyager.bread.delete', ['id' => DataType::where('name', 'categories')->first()->id]));

        // Create BREAD
        $this->visitRoute('voyager.bread.create', ['table' => 'categories'])
             ->select($name, 'field_input_type_'.$name)
             ->type($options, 'field_details_'.$name)
             ->type('TCG\\Voyager\\Models\\Category', 'model_name')
             ->press(__('voyager::generic.submit'))
             ->seeRouteIs('voyager.bread.index');

        // Attach permissions to role
        Auth::user()->role->permissions()->syncWithoutDetaching(Permission::all()->pluck('id'));
    }
}
