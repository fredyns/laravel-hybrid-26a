<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

/**
 * usage in model:
 *
 *  public function <field_name>($key = null, $default = null)
 *  {
 *      return JsonField::getField($this, '<field_name>', $key, $default);
 *  }
 */
class JsonField
{
    public static function ensureArray($mixed): array
    {
        if (empty($mixed)) {
            return [];
        }

        if (is_array($mixed)) {
            return $mixed;
        }

        return (array) json_decode($mixed, true);
    }

    public static function ensureJson($mixed, $flags = JSON_UNESCAPED_UNICODE): false|string|null
    {
        if (empty($mixed)) {
            return null;
        }

        if (is_string($mixed)) {
            return $mixed;
        }

        return json_encode($mixed, $flags);
    }

    protected static array $cache = [];

    public static function getField($obj, $field, $key = null, $default = null)
    {
        if (! is_object($obj)) {
            return $default;
        }

        $class = get_class($obj);
        $isCached = isset(self::$cache[$class][$field]);

        if (! $isCached) {
            self::$cache[$class][$field] = self::ensureArray($obj->{$field});
        }

        if (! $key) {
            return self::$cache[$class][$field];
        }

        return Arr::get(self::$cache[$class][$field], $key, $default);
    }
}
