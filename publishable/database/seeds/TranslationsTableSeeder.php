<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
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
        $this->dataTypesTranslations();
        $this->pagesTranslations();
        $this->menusTranslations();
    }

    /**
     * Auto generate DataTypes Translations.
     *
     * @return void
     */
    private function dataTypesTranslations()
    {
        // Adding translations for 'display_name_singular'
        //
        $_fld = 'display_name_singular';
        $_tpl = ['data_types', $_fld];
        $dtp = DataType::where($_fld, 'Post')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Post');
        }
        $dtp = DataType::where($_fld, 'Page')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Página');
        }
        $dtp = DataType::where($_fld, 'User')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Utilizador');
        }
        $dtp = DataType::where($_fld, 'Category')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Categoria');
        }
        $dtp = DataType::where($_fld, 'Menu')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Menu');
        }
        $dtp = DataType::where($_fld, 'Role')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Função');
        }

        // Adding translations for 'display_name_plural'
        //
        $_fld = 'display_name_plural';
        $_tpl = ['data_types', $_fld];
        $dtp = DataType::where($_fld, 'Posts')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Posts');
        }
        $dtp = DataType::where($_fld, 'Pages')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Páginas');
        }
        $dtp = DataType::where($_fld, 'Users')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Utilizadores');
        }
        $dtp = DataType::where($_fld, 'Categories')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Categorias');
        }
        $dtp = DataType::where($_fld, 'Menus')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Menus');
        }
        $dtp = DataType::where($_fld, 'Roles')->firstOrFail();
        if ($dtp->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $dtp->id), 'Funções');
        }
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
            $_arr = $this->_arr(['pages', 'title'], $page->id);
            $this->_trans('pt', $_arr, 'Olá Mundo');
            /**
             * For configuring additional languages use it e.g.
             *
             * ```
             *   $this->_trans('es', $_arr, 'hola-mundo');
             *   $this->_trans('de', $_arr, 'hallo-welt');
             * ```
             */
            $_arr = $this->_arr(['pages', 'slug'], $page->id);
            $this->_trans('pt', $_arr, 'ola-mundo');

            $_arr = $this->_arr(['pages', 'body'], $page->id);
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
        $_tpl = ['menu_items', 'title'];
        $mItem = $this->_menuItem('Dashboard');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Painel de Controle');
        }

        $mItem = $this->_menuItem('Media');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Media');
        }

        $mItem = $this->_menuItem('Posts');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Publicações');
        }

        $mItem = $this->_menuItem('Users');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Utilizadores');
        }

        $mItem = $this->_menuItem('Categories');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Categorias');
        }

        $mItem = $this->_menuItem('Pages');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Páginas');
        }

        $mItem = $this->_menuItem('Roles');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Funções');
        }

        $mItem = $this->_menuItem('Tools');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Ferramentas');
        }

        $mItem = $this->_menuItem('Menu Builder');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Menus');
        }

        $mItem = $this->_menuItem('Database');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Base de dados');
        }

        $mItem = $this->_menuItem('Settings');
        if ($mItem->exists) {
            $this->_trans('pt', $this->_arr($_tpl, $mItem->id), 'Configurações');
        }
    }

    protected function _menuItem($title)
    {
        return MenuItem::where('title', $title)->firstOrFail();
    }

    protected function _arr($par, $id)
    {
        return [
            'table_name'  => $par[0],
            'column_name' => $par[1],
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
