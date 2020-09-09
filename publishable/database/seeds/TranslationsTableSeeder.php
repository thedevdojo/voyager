<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Category;
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
        $this->categoriesTranslations();
        $this->pagesTranslations();
        $this->menusTranslations();
    }

    /**
     * Auto generate Categories Translations.
     *
     * @return void
     */
    private function categoriesTranslations()
    {
        // Adding translations for 'categories'
        //
        $cat = Category::where('slug', 'category-1')->firstOrFail();
        if ($cat->exists) {
            $this->trans('pt', $this->arr(['categories', 'slug'], $cat->id), 'categoria-1');
            $this->trans('pt', $this->arr(['categories', 'name'], $cat->id), 'Categoria 1');
            $this->trans('fr', $this->arr(['categories', 'slug'], $cat->id), 'categorie-1');
            $this->trans('fr', $this->arr(['categories', 'name'], $cat->id), 'Catégorie 1');
        }
        $cat = Category::where('slug', 'category-2')->firstOrFail();
        if ($cat->exists) {
            $this->trans('pt', $this->arr(['categories', 'slug'], $cat->id), 'categoria-2');
            $this->trans('pt', $this->arr(['categories', 'name'], $cat->id), 'Categoria 2');
            $this->trans('fr', $this->arr(['categories', 'slug'], $cat->id), 'categorie-2');
            $this->trans('fr', $this->arr(['categories', 'name'], $cat->id), 'Catégorie 2');
        }
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
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.post.singular'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Post');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Article');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.page.singular'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Página');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Page');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.user.singular'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Utilizador');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Utilisateur');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.category.singular'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Categoria');
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Catégorie');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.menu.singular'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Menu');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Menu');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.role.singular'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Função');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Rôle');
        }

        // Adding translations for 'display_name_plural'
        //
        $_fld = 'display_name_plural';
        $_tpl = ['data_types', $_fld];
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.post.plural'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Posts');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Articles');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.page.plural'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Páginas');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Pages');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.user.plural'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Utilizadores');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Utilisateurs');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.category.plural'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Categorias');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Catégories');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.menu.plural'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Menus');
            $this->trans('fr', $this->arr($_tpl, $dtp->id), 'Menus');
        }
        $dtp = DataType::where($_fld, __('voyager::seeders.data_types.role.plural'))->firstOrFail();
        if ($dtp->exists) {
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Funções');
            $this->trans('pt', $this->arr($_tpl, $dtp->id), 'Rôles');
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
            $_arr = $this->arr(['pages', 'title'], $page->id);
            $this->trans('pt', $_arr, 'Olá Mundo');
            $this->trans('fr', $_arr, 'Bonjour le monde');

            $_arr = $this->arr(['pages', 'slug'], $page->id);
            $this->trans('pt', $_arr, 'ola-mundo');
            $this->trans('fr', $_arr, 'bonjour-le-monde');

            $_arr = $this->arr(['pages', 'body'], $page->id);
            $this->trans('pt', $_arr, '<p>Olá Mundo. Scallywag grog swab Cat o\'nine tails scuttle rigging hardtack cable nipper Yellow Jack. Handsomely spirits knave lad killick landlubber or just lubber deadlights chantey pinnace crack Jennys tea cup. Provost long clothes black spot Yellow Jack bilged on her anchor league lateen sail case shot lee tackle.</p>'
                ."\r\n".'<p>Ballast spirits fluke topmast me quarterdeck schooner landlubber or just lubber gabion belaying pin. Pinnace stern galleon starboard warp carouser to go on account dance the hempen jig jolly boat measured fer yer chains. Man-of-war fire in the hole nipperkin handsomely doubloon barkadeer Brethren of the Coast gibbet driver squiffy.</p>');
            $this->trans('fr', $_arr, '<p>Bonjour le monde. Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolorem inventore necessitatibus esse odio cupiditate nam aperiam, laborum ea exercitationem facere autem fugit corporis et debitis nostrum consequuntur, dicta unde ut?</p>'
                ."\r\n".'<p>Obcaecati officiis amet repudiandae animi. Provident sed aliquam, harum aperiam nesciunt perspiciatis corporis, praesentium libero autem tempora nihil accusamus. Nihil, voluptatibus quasi.Consectetur, perferendis similique repellendus sed veniam, quisquam nostrum ab voluptate accusantium libero facilis corporis excepturi sunt ad, nobis deleniti minima sequi voluptatum.</p>');
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
        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.dashboard'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Painel de Controle');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Tableau de bord');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.media'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Media');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Média');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.posts'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Publicações');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Publications');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.users'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Utilizadores');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Utilisateurs');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.categories'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Categorias');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Catégories');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.pages'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Páginas');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Pages');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.roles'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Funções');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Rôles');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.tools'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Ferramentas');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Outils');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.menu_builder'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Menus');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Menus');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.database'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Base de dados');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'DB');
        }

        $_item = $this->findMenuItem(__('voyager::seeders.menu_items.settings'));
        if ($_item->exists) {
            $this->trans('pt', $this->arr($_tpl, $_item->id), 'Configurações');
            $this->trans('fr', $this->arr($_tpl, $_item->id), 'Règlages');
        }
    }

    private function findMenuItem($title)
    {
        return MenuItem::where('title', $title)->firstOrFail();
    }

    private function arr($par, $id)
    {
        return [
            'table_name'  => $par[0],
            'column_name' => $par[1],
            'foreign_key' => $id,
        ];
    }

    private function trans($lang, $keys, $value)
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
