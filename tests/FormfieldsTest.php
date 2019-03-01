<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\Category;
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

    public function testFormfieldHidden()
    {
        $this->createBreadForFormfield('text', 'hidden', json_encode([
            'default' => 'Default Text'
        ]));

        $this->visitRoute('voyager.categories.create')
             ->see('Default Text')
             ->type('New Text', 'hidden')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('New Text')
             ->click(__('voyager::generic.edit'))
             ->seeRouteIs('voyager.categories.edit', ['id' => 1])
             ->type('Edited Text', 'hidden')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('Edited Text');
    }

    public function testFormfieldPassword()
    {
        $this->createBreadForFormfield('text', 'password');

        $t = $this->visitRoute('voyager.categories.create')
        ->type('newpassword', 'password')
        ->press(__('voyager::generic.save'))
        ->seeRouteIs('voyager.categories.index');
        $this->assertTrue(Hash::check('newpassword', Category::first()->password));

        $t->click(__('voyager::generic.edit'))
        ->seeRouteIs('voyager.categories.edit', ['id' => 1])
        ->press(__('voyager::generic.save'))
        ->seeRouteIs('voyager.categories.index');
        $this->assertTrue(Hash::check('newpassword', Category::first()->password));
    }

    public function testFormfieldNumber()
    {
        $this->createBreadForFormfield('integer', 'number', json_encode([
            'default' => 1
        ]));

        $this->visitRoute('voyager.categories.create')
             ->see('1')
             ->type('2', 'number')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('2')
             ->click(__('voyager::generic.edit'))
             ->seeRouteIs('voyager.categories.edit', ['id' => 1])
             ->type('3', 'number')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('3');
    }

    public function testFormfieldCheckbox()
    {
        $this->createBreadForFormfield('boolean', 'checkbox', json_encode([
            'on' => 'Active',
            'off' => 'Inactive',
        ]));

        $this->visitRoute('voyager.categories.create')
             ->see('Inactive')
             ->check('checkbox')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('Active')
             ->click(__('voyager::generic.edit'))
             ->seeRouteIs('voyager.categories.edit', ['id' => 1])
             ->uncheck('checkbox')
             ->press(__('voyager::generic.save'))
             ->seeRouteIs('voyager.categories.index')
             ->see('Inactive');
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
