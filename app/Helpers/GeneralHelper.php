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

     /**
      * Replace all symbols with key of data
      *
      * @param string $subject observed string.
      * @param array $data the strings replacement.
      * @param string $pattern regex to replace symbol.
      * @return string result
      */
      public static function replaceAllSymbols($subject, $data, $pattern = '/\$(%s)\$/')
      {
          $patterns = [];
          $replacements = [];

          foreach ($data as $key => $value) {
            if (is_string($value)) {
                $patterns[] = sprintf($pattern, $key);
                $replacements[] = $value;
            }
          }

          return preg_replace($patterns, $replacements, $subject);
      }

        /**
         * Create random strings with specified length.
         *
         * @param string $prefix prefix word.
         * @param int $digit digit of number.
         * @return string $result
         *
         */
        public static function generateRandomString($prefix, $digit = 5)
        {
            $rand = Str::random($digit);
            if (empty($prefix) && Str::length($prefix) > 3) {
                return $rand;
            }

            return Str::upper($prefix . '-' . $rand);
        }
}
