<?php

namespace TCG\Voyager\Traits;

use Illuminate\Support\Facades\DB;

trait Spatial
{
    /**
     * Get a new query builder for the model's table.
     * Manipulate in case we need to convert geometrical fields to text.
     *
     * @param bool $excludeDeleted
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery($excludeDeleted = true)
    {
        if (!empty($this->spatial)) {
            $raw = '';
            foreach ($this->spatial as $column) {
                $raw .= 'ST_AsText(' . $column . ') AS ' . $column . ', ';
            }
            $raw = substr($raw, 0, -2);

            return parent::newQuery($excludeDeleted)->addSelect('*', DB::raw($raw));
        }

        return parent::newQuery($excludeDeleted);
    }

    /**
     * Format and return array of (lat,lng) pairs of points fetched from the database.
     *
     * @return array $coords
     */
    public function getCoordinates()
    {
        $coords = [];

        if (!empty($this->spatial)) {
            foreach ($this->spatial as $column) {
                $clear = trim(preg_replace('/[a-zA-Z\(\)]/', '', $this->$column));
                if (!empty($clear)) {
                    foreach (explode(',', $clear) as $point) {
                        list($lat, $lng) = explode(' ', $point);
                        $coords[] = [
                            'lat' => $lat,
                            'lng' => $lng,
                        ];
                    }
                }
            }
        }

        return $coords;
    }
}
