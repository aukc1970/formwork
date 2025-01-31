<?php

namespace Formwork\Utils;

class Arr
{
    /**
     * Get data by key returning a default value if key is not present in a given array,
     * using dot notation to traverse if literal key is not found
     *
     * @param array      $array
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public static function get(array $array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        return $array;
    }

    /**
     * Return whether a key is present in a given array, using dot notation to traverse
     * if literal key is not found
     *
     * @param array  $array
     * @param string $key
     *
     * @return bool
     */
    public static function has(array $array, $key)
    {
        if (array_key_exists($key, $array)) {
            return true;
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }
            $array = $array[$segment];
        }
        return true;
    }

    /**
     * Recursively append elements from the second array that are missing in the first
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function appendMissing(array $array1, array $array2)
    {
        foreach ($array1 as $key => $value) {
            if (is_array($value) && array_key_exists($key, $array2) && is_array($array2[$key])) {
                $array1[$key] = static::appendMissing($array1[$key], $array2[$key]);
            }
        }
        $array1 += $array2;
        return $array1;
    }
}
