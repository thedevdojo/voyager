<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Database\Eloquent\Relations\HasManySelfElementsBy;

class MenuItem extends Model
{
    protected $table = 'menu_items';
    protected $fillable = ['menu_id', 'title', 'url', 'target', 'icon_class', 'parent_id', 'color', 'order'];

    /**
     * Create self relation to get sub elements for menus.
     *
     * @return mixed
     */
    public function subitems()
    {
        return $this->hasManySelfElementsBy('menu_id', 'parent_id', 'id')
            ->orderBy('order', 'asc');
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $column
     * @param  string  $foreignIdentifier
     * @param  string  $localIdentifier
     * @return \TCG\Voyager\Database\Eloquent\Relations\HasManySelfElementsBy
     */
    public function hasManySelfElementsBy($column, $foreignIdentifier = null, $localIdentifier = null)
    {
        $foreignIdentifier = $foreignIdentifier ?: $this->getForeignKey();

        $localIdentifier = $localIdentifier ?: $this->getKeyName();

        return new HasManySelfElementsBy($this->newQuery(), $this, $this->getTable().'.'.$column, $column, $foreignIdentifier, $localIdentifier);
    }

    /**
     * Check if this is a sub menu or not.
     *
     * @return bool
     */
    public function isSubMenu()
    {
        if (!isset($this->subitems)) {
            $this->load('subitems');
        }

        return count($this->subitems) > 0;
    }
}