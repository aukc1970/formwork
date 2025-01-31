<?php

namespace Formwork\Admin;

use Formwork\Languages\LanguageCodes;
use Formwork\Parsers\YAML;
use Formwork\Utils\FileSystem;
use LogicException;
use RuntimeException;

class Translation
{
    /**
     * Fallback language code
     *
     * @var string
     */
    protected const FALLBACK_LANGUAGE_CODE = 'en';

    /**
     * Array containing available languages
     *
     * @var array
     */
    protected static $availableLanguages = array();

    /**
     * Fallback translation instance
     *
     * @var Translation
     */
    protected static $fallbackTranslation;

    /**
     * Language code
     *
     * @var string
     */
    protected $code;

    /**
     * Array containing language strings
     *
     * @var array
     */
    protected $strings = array();

    /**
     * Create a new Translation istance
     *
     * @param string $code
     * @param array  $strings
     */
    public function __construct($code, array $strings)
    {
        $this->code = $code;
        $this->strings = $strings;
    }

    /**
     * Get all available languages
     *
     * @return array
     */
    public static function availableLanguages()
    {
        return static::$availableLanguages;
    }

    /**
     * Get language code
     *
     * @return string
     */
    public function code()
    {
        return $this->code;
    }

    /**
     * Return whether a language string is set
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->strings[$key]);
    }

    /**
     * Return a formatted language label
     *
     * @param string           $key
     * @param float|int|string ...$arguments
     *
     * @return string
     */
    public function get($key, ...$arguments)
    {
        if (!$this->has($key)) {
            if ($this->code !== self::FALLBACK_LANGUAGE_CODE) {
                return $this->fallbackTranslation()->get($key, ...$arguments);
            }
            throw new LogicException('Invalid language string "' . $key . '"');
        }

        if (!empty($arguments)) {
            return sprintf($this->strings[$key], ...$arguments);
        }

        return $this->strings[$key];
    }

    /**
     * Load administration panel language
     *
     * @param string $languageCode
     *
     * @return self
     */
    public static function load($languageCode)
    {
        if (empty(static::$availableLanguages)) {
            foreach (FileSystem::listFiles(TRANSLATIONS_PATH) as $file) {
                $code = FileSystem::name($file);
                static::$availableLanguages[$code] = LanguageCodes::codeToNativeName($code) . ' (' . $code . ')';
            }
        }

        $translationFile = TRANSLATIONS_PATH . $languageCode . '.yml';

        if (!(FileSystem::exists($translationFile) && FileSystem::isReadable($translationFile))) {
            throw new RuntimeException('Cannot load Admin language file ' . $translationFile);
        }

        $languageStrings = YAML::parseFile($translationFile);

        return new static($languageCode, $languageStrings);
    }

    /**
     * Return fallback translation instance
     *
     * @return Translation
     */
    protected function fallbackTranslation()
    {
        if (!is_null(static::$fallbackTranslation)) {
            return static::$fallbackTranslation;
        }
        return static::$fallbackTranslation = static::load(self::FALLBACK_LANGUAGE_CODE);
    }
}
