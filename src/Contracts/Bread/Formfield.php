<?php

namespace TCG\Voyager\Contracts\Bread;

abstract class Formfield implements \JsonSerializable
{
    public $translatable = false;
    public $column;

    /**
     * Get the name of the formfield.
     *
     * @return string|array
     */
    abstract public function name();

    /**
     * Get the type of the formfield.
     *
     * @return string
     */
    abstract public function type(): string;

    /**
     * Get the (custom) options for a list.
     *
     * @return array
     */
    abstract public function listOptions(): array;

    /**
     * Get the (custom) options for a view.
     *
     * @return array
     */
    abstract public function viewOptions(): array;

    /**
     * Get the data for browsing.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function browse($input)
    {
        return $input;
    }

    /**
     * Get the data for reading.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function read($input)
    {
        return $input;
    }

    /**
     * Get the data for editing.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function edit($input)
    {
        return $input;
    }

    /**
     * Get the data for updating (after editing).
     *
     * @param mixed $input
     * @param mixed $old
     *
     * @return mixed
     */
    public function update($input, $old)
    {
        return $input;
    }

    /**
     * Get the data for adding (eg. default values).
     *
     * @return mixed
     */
    public function add()
    {
        return '';
    }

    /**
     * Get the data for storing (after adding).
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function store($input)
    {
        return $input;
    }

    /**
     * Gets if the formfield can be translated.
     *
     * @return bool
     */
    public function canBeTranslated()
    {
        return true;
    }

    /**
     * If this formfield can be used as a setting.
     *
     * @return bool
     */
    public function canBeUsedAsSetting()
    {
        return true;
    }

    /**
     * If this formfield can be used in a list.
     *
     * @return bool
     */
    public function canBeUsedInList()
    {
        return true;
    }

    /**
     * If this formfield can be used in a view.
     *
     * @return bool
     */
    public function canBeUsedInView()
    {
        return true;
    }

    /**
     * If array data should be passed to this formfield when browsing
     * This is especially useful for media-pickers and other formfields that don't just show text.
     *
     * @return bool
     */
    public function browseDataAsArray()
    {
        return false;
    }

    /**
     * If formfield accepts normal table columns.
     *
     * @return bool
     */
    public function allowColumns()
    {
        return true;
    }

    /**
     * If formfield accepts computed properties.
     *
     * @return bool
     */
    public function allowComputed()
    {
        return true;
    }

    /**
     * If formfield accepts relationships (method name)
     * This is only useful for relationship-formfields.
     *
     * @return bool
     */
    public function allowRelationships()
    {
        return false;
    }

    /**
     * If formfield accepts relationship-columns.
     *
     * @return bool
     */
    public function allowRelationshipColumns()
    {
        return true;
    }

    /**
     * If formfield accepts relationship-pivot columns.
     *
     * @return bool
     */
    public function allowRelationshipPivots()
    {
        return true;
    }

    public function jsonSerialize()
    {
        // Formfield will be used in BREAD builder. We need list/view options and some other things
        if (!$this->column) {
            return [
                'name'                     => $this->name(),
                'type'                     => $this->type(),
                'canBeTranslated'          => $this->canBeTranslated(),
                'listOptions'              => (object) $this->listOptions(),
                'viewOptions'              => (object) $this->viewOptions(),
                'asSetting'                => $this->canBeUsedAsSetting(),
                'inList'                   => $this->canBeUsedInList(),
                'inView'                   => $this->canBeUsedInView(),
                'browseArray'              => $this->browseDataAsArray(),
                'allowColumns'             => $this->allowColumns(),
                'allowComputed'            => $this->allowComputed(),
                'allowRelationships'       => $this->allowRelationships(),
                'allowRelationshipColumns' => $this->allowRelationshipColumns(),
                'allowPivot'               => $this->allowRelationshipPivots(),

            ];
        }

        // BREAD was already stored by the BREAD builder. We don't need the above things at this point
        return array_merge([
            'type'            => $this->type(),
        ], (array) $this);
    }
}
