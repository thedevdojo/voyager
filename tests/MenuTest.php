<?php

namespace TCG\Voyager\Tests;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Menu;

class MenuTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
    }

    public function testCanRenameMenu()
    {
        $menu = Menu::where('name', '=', 'admin')->first();
        $this->visitRoute('voyager.menus.edit', ['id' => $menu->id])
             ->seeInField('name', $menu->name)
             ->type('new_admin', 'name')
             ->seeInElement('button', __('voyager::generic.save'))
             ->press(__('voyager::generic.save'))
             ->seePageIs(route('voyager.menus.index'))
             ->seeInDatabase('menus', [
                'id'   => $menu->id,
                'name' => 'new_admin',
             ]);
    }

    public function testCanDeleteMenu()
    {
        $menu = Menu::where('name', '=', 'admin')->first();
        $this->delete(route('voyager.menus.destroy', [
            'menu' => $menu->id,
        ]))->notSeeInDatabase('menus', [
            'name' => $menu->name
        ]);
    }
}
