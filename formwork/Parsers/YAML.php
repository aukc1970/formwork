<?php

namespace Formwork\Parsers;

use Formwork\Core\Formwork;
use Formwork\Utils\FileSystem;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class YAML
{
    /**
     * Whether to use PHP yaml extension to emit, parse, both or none of the operations
     *
     * @var string
     */
    protected static $PHPYAMLmode;

    /**
     * Parse a YAML string
     *
     * @param string $input
     *
     * @return array
     */
    public static function parse($input)
    {
        if (function_exists('yaml_parse') && static::PHPYAMLmode('parse')) {
            if (!preg_match('/^---\n/', $input)) {
                $input = "---\n" . $input;
            }
            return (array) yaml_parse($input);
        }
        return (array) SymfonyYaml::parse($input);
    }

    /**
     * Parse a YAML file
     *
     * @param string $file
     *
     * @return array
     */
    public static function parseFile($file)
    {
        return static::parse(FileSystem::read($file));
    }

    /**
     * Encode data to YAML format
     *
     * @param array $data
     *
     * @return string
     */
    public static function encode(array $data)
    {
        $data = (array) $data;
        if (empty($data)) {
            return '';
        }
        if (function_exists('yaml_emit') && static::PHPYAMLmode('emit')) {
            return preg_replace('/^---[\n ]|\n\.{3}$/', '', yaml_emit($data));
        }
        return SymfonyYaml::dump($data);
    }

    /**
     * Check if PHPHYAMLmode option matches a pattern
     *
     * @param string $pattern
     */
    protected static function PHPYAMLmode($pattern)
    {
        if (is_null(static::$PHPYAMLmode)) {
            $option = Formwork::instance()->option('parsers.use_php_yaml');
            if ($option) {
                switch (strtolower($option)) {
                    case 'all':
                        static::$PHPYAMLmode = 'all';
                        break;
                    case 'emit':
                        static::$PHPYAMLmode = 'emit';
                        break;
                    case 'parse':
                        static::$PHPYAMLmode = 'parse';
                        break;
                    case 'none':
                    default:
                        static::$PHPYAMLmode = false;
                        break;
                }
            }
        }
        return static::$PHPYAMLmode === $pattern || static::$PHPYAMLmode === 'all';
    }
}
