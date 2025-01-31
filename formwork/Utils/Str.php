<?php

namespace Formwork\Utils;

class Str
{
    /**
     * Return whether $haystack string starts with $needle
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    /**
     * Return whether $haystack string ends with $needle
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Escape HTML tags from a given string
     *
     * @param string $string
     *
     * @return string
     */
    public static function escape($string)
    {
        return htmlspecialchars($string, ENT_COMPAT | ENT_SUBSTITUTE, 'utf-8', false);
    }

    /**
     * Remove HTML tags and entities from a given string
     *
     * @param string $string
     *
     * @return string
     */
    public static function removeHTML($string)
    {
        return html_entity_decode(strip_tags($string), ENT_QUOTES | ENT_HTML5, 'utf-8');
    }

    /**
     * Remove $needle from $haystack if it is at the beginning, otherwise return $haystack
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return string
     */
    public static function removeStart($haystack, $needle)
    {
        return static::startsWith($haystack, $needle) ? substr($haystack, strlen($needle)) : $haystack;
    }

    /**
     * Remove $needle from $haystack if it is at the end, otherwise return $haystack
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return string
     */
    public static function removeEnd($haystack, $needle)
    {
        return static::endsWith($haystack, $needle) ? substr($haystack, 0, -strlen($needle)) : $haystack;
    }
}
