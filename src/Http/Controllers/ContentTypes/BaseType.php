<?php

namespace TCG\Voyager\Http\Controllers\ContentTypes;

use Illuminate\Http\Request;

abstract class BaseType
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var
     */
    protected $slug;

    /**
     * @var
     */
    protected $row;

    /**
     * @var
     */
    protected $options;
    /**
     * @var instance of model
     */
    protected $data;

    /**
     * Password constructor.
     *
     * @param Request $request
     * @param $slug
     * @param $row
     */
    public function __construct(Request $request, $slug, $row, $options, $data)
    {
        $this->request = $request;
        $this->slug = $slug;
        $this->row = $row;
        $this->options = $options;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    abstract public function handle();
}