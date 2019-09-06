<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class DataRow extends Model
{
    use Translatable;

    protected $table = 'data_rows';

    protected $guarded = [];

    public $timestamps = false;

    protected $translatable = ['display_name'];

    public function rowBefore()
    {
        $previous = self::where('data_type_id', '=', $this->data_type_id)->where('order', '=', ($this->order - 1))->first();
        if (isset($previous->id)) {
            return $previous->field;
        }

        return '__first__';
    }

    public function relationshipField()
    {
        return @$this->details->column;
    }

    /**
     * Check if this field is the current filter.
     *
     * @return bool True if this is the current filter, false otherwise
     */
    public function isCurrentSortField($orderBy)
    {
        return $orderBy == $this->field;
    }

    /**
     * Build the URL to sort data type by this field.
     *
     * @return string Built URL
     */
    public function sortByUrl($orderBy, $sortOrder)
    {
        $params = [];
        $isDesc = $sortOrder != 'asc';
        if ($this->isCurrentSortField($orderBy) && $isDesc) {
            $params['sort_order'] = 'asc';
        } else {
            $params['sort_order'] = 'desc';
        }
        $params['order_by'] = $this->field;

        return url()->current().'?'.http_build_query(array_merge(\Request::all(), $params));
    }

    public function setDetailsAttribute($value)
    {
        $this->attributes['details'] = json_encode($value);
    }

    public function getDetailsAttribute($value)
    {
        return json_decode(!empty($value) ? $value : '{}');
    }
}
