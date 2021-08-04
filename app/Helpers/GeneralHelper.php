<?php

namespace App\Helper;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GeneralHelper {
    /**
     * Convert payload to camel case.
     * 
     * @param \Illuminate\Http\Request $json
     * @return array|null
     */
    public static function toCamelCase($json)
    {
        if (is_array($json)) {
            if (Arr::isAssoc($json)) {
                $result = [];
                foreach ($json as $key => $value) {
                    $result[Str::camel($key)] = GeneralHelper::toCamelCase($value);
                }
    
                return $result;
            } else {
                $result = [];
                foreach ($json as $obj) {
                    $result[] = GeneralHelper::toCamelCase($obj);
                }
                return $result;
            }
        }

        return $json;
    }

    /**
     * Convert payload to snake case.
     * 
     * @param \Illuminate\Http\Request $json
     * @return array|null
     */
    public static function toSnakeCase($json)
    {
        if (is_array($json)) {
            if (Arr::isAssoc($json)) {
                $result = [];
                foreach ($json as $key => $value) {
                    $result[Str::snake($key)] = GeneralHelper::toSnakeCase($value);
                }
    
                return $result;
            } else {
                $result = [];
                foreach ($json as $obj) {
                    $result[] = GeneralHelper::toSnakeCase($obj);
                }
                return $result;
            }
        }

        return $json;
    }
}