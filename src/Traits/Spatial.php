<?php

namespace TCG\Voyager\Traits;

use Illuminate\Support\Facades\DB;

trait Spatial
{
    /**
     * Get location as WKT from Geometry for given field.
     *
     * @param string $column
     *
     * @return string
     */
    public function getLocation($column)
    {
        $model = self::select(DB::Raw('ST_AsText('.$column.') AS '.$column))
            ->where('id', $this->id)
            ->first();

        return isset($model) ? $model->$column : '';
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
                $clear = trim(preg_replace('/[a-zA-Z\(\)]/', '', $this->getLocation($column)));
                if (!empty($clear)) {
                    foreach (explode(',', $clear) as $point) {
                        list($lng, $lat) = explode(' ', $point);
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
