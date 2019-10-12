<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Category;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Page;
use TCG\Voyager\Models\Translation;

class TranslationsTableSeeder extends Seeder
{

    protected $appLocale;

    protected $transLocales = ['pt_br'];

    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $this->appLocale = App::getLocale();
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
        foreach (__('voyager::seeders.categories') as $key => $category) {
            $cat = Category::where('slug', $category['slug'])->firstOrFail();

            foreach ($this->transLocales as $locale) {
                $this->trans($locale, $this->arr(['categories', 'slug'], $cat->id), __("voyager::seeders.categories")[$key]['slug']);
                $this->trans($locale, $this->arr(['categories', 'name'], $cat->id), __("voyager::seeders.categories")[$key]['slug']);
            }
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
        $transFields = ['post', 'page', 'user', 'category', 'menu', 'role'];

        foreach ($transFields as $transField) {
            $dtp = DataType::where($_fld, __("voyager::seeders.data_types.{$transField}.singular"))->firstOrFail();

            foreach ($this->transLocales as $locale) {
                $this->trans($locale, $this->arr($_tpl, $dtp->id), __("voyager::seeders.data_types.{$transField}.singular"));
            }
        }


        // Adding translations for 'display_name_plural'
        //
        $_fld = 'display_name_plural';
        $_tpl = ['data_types', $_fld];
        $transFields = ['post', 'page', 'user', 'category', 'menu', 'role'];

        foreach ($transFields as $transField) {
            $dtp = DataType::where($_fld, __("voyager::seeders.data_types.{$transField}.plural"))->firstOrFail();

            foreach ($this->transLocales as $locale) {
                $this->trans($locale, $this->arr($_tpl, $dtp->id), __("voyager::seeders.data_types.{$transField}.plural"));
            }
        }
    }

    /**
     * Auto generate Pages Translations.
     *
     * @return void
     */
    private function pagesTranslations()
    {
        foreach (__('voyager::seeders.pages') as $key => $page) {
            $page = Page::where('slug', $page['slug'])->firstOrFail();

            foreach ($this->transLocales as $locale) {
                $_arr = $this->arr(['pages', 'title'], $page->id);
                $this->trans($locale, $_arr, __("voyager::seeders.pages")[$key]['title']);

                $_arr = $this->arr(['pages', 'slug'], $page->id);
                $this->trans($locale, $_arr, __("voyager::seeders.pages")[$key]['slug']);

                $_arr = $this->arr(['pages', 'body'], $page->id);
                $this->trans($locale, $_arr, __("voyager::seeders.pages")[$key]['body']);
            }
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
        $transFields = ['dashboard', 'media', 'posts', 'users', 'categories', 'pages', 'roles', 'tools', 'menu_builder', 'database', 'settings'];

        foreach ($transFields as $transField) {
            $_item = $this->findMenuItem(__("voyager::seeders.menu_items.{$transField}"));
            foreach ($this->transLocales as $locale) {
                $this->trans($locale, $this->arr($_tpl, $_item->id), __("voyager::seeders.menu_items.{$transField}"));
            }
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
