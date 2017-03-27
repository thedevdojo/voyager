<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Page;
use TCG\Voyager\Models\Translation;

class TranslationsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $this->pagesTranslations();
        $this->menusTranslations();
    }

    /**
     * Auto generate Pages Translations.
     *
     * @return void
     */
    private function pagesTranslations()
    {
        $page = Page::where('slug', 'hello-world')->firstOrFail();
        if ($page->exists) {
            $_arr = $this->_arr('pages', 'title', $page->id);
            $this->_trans('pt', $_arr, 'Olá Mundo');
            /**
             * For configuring more languages use it like this example.
             *
             * ```
             *   $this->_trans('es', $_arr, 'hola-mundo');
             *   $this->_trans('de', $_arr, 'hallo-welt');
             * ```
             */
            $_arr = $this->_arr('pages', 'slug', $page->id);
            $this->_trans('pt', $_arr, 'ola-mundo');

            $_arr = $this->_arr('pages', 'body', $page->id);
            $this->_trans('pt', $_arr, '<p>Olá Mundo. Scallywag grog swab Cat o\'nine tails scuttle rigging hardtack cable nipper Yellow Jack. Handsomely spirits knave lad killick landlubber or just lubber deadlights chantey pinnace crack Jennys tea cup. Provost long clothes black spot Yellow Jack bilged on her anchor league lateen sail case shot lee tackle.</p>'
                                        ."\r\n".'<p>Ballast spirits fluke topmast me quarterdeck schooner landlubber or just lubber gabion belaying pin. Pinnace stern galleon starboard warp carouser to go on account dance the hempen jig jolly boat measured fer yer chains. Man-of-war fire in the hole nipperkin handsomely doubloon barkadeer Brethren of the Coast gibbet driver squiffy.</p>');
        }
    }

    /**
     * Auto generate Menus Translations.
     *
     * @return void
     */
    private function menusTranslations()
    {
        $menuItem = $this->_menuItem('Dashboard');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Painel de Controle');
        }

        $menuItem = $this->_menuItem('Media');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Media');
        }

        $menuItem = $this->_menuItem('Posts');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Publicações');
        }

        $menuItem = $this->_menuItem('Users');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Utilizadores');
        }

        $menuItem = $this->_menuItem('Categories');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Categorias');
        }

        $menuItem = $this->_menuItem('Pages');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Páginas');
        }

        $menuItem = $this->_menuItem('Roles');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Funções');
        }

        $menuItem = $this->_menuItem('Tools');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Ferramentas');
        }

        $menuItem = $this->_menuItem('Menu Builder');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Menus');
        }

        $menuItem = $this->_menuItem('Database');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Base de dados');
        }

        $menuItem = $this->_menuItem('Settings');
        if ($menuItem->exists) {
            $_arr = $this->_arr('menu_items', 'title', $menuItem->id);
            $this->_trans('pt', $_arr, 'Configurações');
        }
    }

    protected function _menuItem($title)
    {
        return MenuItem::where('title', $title)->firstOrFail();
    }

    protected function _arr($table, $key, $id)
    {
        return [
            'table_name'  => $table,
            'column_name' => $key,
            'foreign_key' => $id,
        ];
    }

    protected function _trans($lang, $keys, $value)
    {
        $_t = Translation::firstOrNew(array_merge($keys, [
            'locale' => $lang,
        ]));

        if (!$_t->exists) {
            $_t->fill(array_merge(
                $keys,
                ['value' => $value]
            ))->save();
        }
    }
}
