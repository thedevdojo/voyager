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
     * @return mixed
     */
    abstract public function browse($input);

    /**
     * Get the data for reading.
     *
     * @param mixed $input
     * @return mixed
     */
    abstract public function read($input);

    /**
     * Get the data for editing.
     *
     * @param mixed $input
     * @return mixed
     */
    abstract public function edit($input);

    /**
     * Get the data for updating (after editing).
     *
     * @param mixed $input
     * @param mixed $old
     * @return mixed
     */
    abstract public function update($input, $old);

    /**
     * Get the data for adding (eg. default values).
     *
     * @return mixed
     */
    abstract public function add();

    /**
     * Get the data for storing (after adding).
     *
     * @param mixed $input
     * @return mixed
     */
    abstract public function store($input);

    /**
     * Gets if the formfield can be translated.
     *
     * @return bool
     */
    public function canBeTranslated()
    {
        return true;
    }

    public function jsonSerialize()
    {
        // Formfield will be used in BREAD manager. We need list/view options and some other things
        if (!$this->column) {
            return [
                'name'            => $this->name(),
                'type'            => $this->type(),
                'canbetranslated' => $this->canBeTranslated(),
                'listOptions'     => (object) $this->listOptions(),
                'viewOptions'     => (object) $this->viewOptions(),
            ];
        }

        // BREAD was already stored by the BREAD manager. We don't need the above things at this point
        return array_merge([
            'type'            => $this->type(),
        ], (array) $this);
    }
}
