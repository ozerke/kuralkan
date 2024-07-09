<?php

namespace App\Utils;

class SoapUtils
{
    public static function parseRow($payload, $mapFn = null, $isSingleEntity = false)
    {
        if (!$payload) return null;

        if (!property_exists($payload, 'ROW')) {
            return null;
        }

        $collection = collect($payload)['ROW'];

        if ($isSingleEntity) {
            $item = json_decode(json_encode($collection), true, flags: JSON_OBJECT_AS_ARRAY);
            $collection = collect($item);

            return $mapFn($collection);
        }

        return collect($collection)->map(function ($item) use ($mapFn) {
            $item = json_decode(json_encode($item), true);

            if ($mapFn) {
                return $mapFn($item);
            }

            return $item;
        });
    }
}
