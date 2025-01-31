<?php

namespace Formwork\Core;

use Formwork\Utils\Arr;
use Formwork\Utils\FileSystem;
use Formwork\Utils\HTTPRequest;
use Formwork\Utils\Uri;
use LogicException;

abstract class AbstractPage
{
    /**
     * Page path
     *
     * @var string
     */
    protected $path;

    /**
     * Page path relative to content path
     *
     * @var string
     */
    protected $relativePath;

    /**
     * Page route
     *
     * @var string
     */
    protected $route;

    /**
     * Page data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Page metadata
     *
     * @var Metadata
     */
    protected $metadata;

    /**
     * PageCollection containing page parents
     *
     * @var PageCollection
     */
    protected $parents;

    /**
     * PageCollection containing page children
     *
     * @var PageCollection
     */
    protected $children;

    /**
     * PageCollection containing page descendants
     *
     * @var PageCollection
     */
    protected $descendants;

    /**
     * Return a URI relative to page
     *
     * @param string      $path
     * @param bool|string $includeLanguage
     *
     * @return string
     */
    public function uri($path = '', $includeLanguage = true)
    {
        $base = HTTPRequest::root();
        if ($includeLanguage) {
            $language = is_string($includeLanguage) ? $includeLanguage : Formwork::instance()->site()->languages()->current();

            $default = Formwork::instance()->site()->languages()->default();
            $preferred = Formwork::instance()->site()->languages()->preferred();

            if ((!is_null($language) && $language !== $default) || (!is_null($preferred) && $preferred !== $default)) {
                $base .= $language . '/';
            }
        }
        return $base . ltrim($this->route, '/') . ltrim($path, '/');
    }

    /**
     * Get page absolute URI
     *
     * @return string
     */
    public function absoluteUri()
    {
        return Uri::resolveRelativeUri($this->uri());
    }

    /**
     * Get page last modified time
     *
     * @return int
     */
    public function lastModifiedTime()
    {
        return FileSystem::lastModifiedTime($this->path);
    }

    /**
     * Return page date optionally in a given format
     *
     * @param string $format
     *
     * @return string
     */
    public function date($format = null)
    {
        if (is_null($format)) {
            $format = Formwork::instance()->option('date.format');
        }
        return date($format, $this->lastModifiedTime());
    }

    /**
     * Get parent page
     *
     * @return Page|Site
     */
    public function parent()
    {
        $parentPath = dirname($this->path) . DS;
        if (FileSystem::isDirectory($parentPath) && $parentPath !== Formwork::instance()->option('content.path')) {
            return Formwork::instance()->site()->retrievePage($parentPath);
        }
        // If no parent was found returns the site as first level pages' parent
        return Formwork::instance()->site();
    }

    /**
     * Return a PageCollection containing page parents
     *
     * @return PageCollection
     */
    public function parents()
    {
        if (!is_null($this->parents)) {
            return $this->parents;
        }
        $parentPages = array();
        $page = $this;
        while (($parent = $page->parent()) !== null) {
            $parentPages[] = $parent;
            $page = $parent;
        }
        return $this->parents = new PageCollection(array_reverse($parentPages));
    }

    /**
     * Return whether page has parents
     *
     * @return bool
     */
    public function hasParents()
    {
        return !$this->parents()->isEmpty();
    }

    /**
     * Return a PageCollection containing page children
     *
     * @return PageCollection
     */
    public function children()
    {
        if (!is_null($this->children)) {
            return $this->children;
        }
        return $this->children = PageCollection::fromPath($this->path);
    }

    /**
     * Return whether page has children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return !$this->children()->isEmpty();
    }

    /**
     * Return a PageCollection containing page descendants
     *
     * @return PageCollection
     */
    public function descendants()
    {
        if (!is_null($this->descendants)) {
            return $this->descendants;
        }
        return $this->descendants = PageCollection::fromPath($this->path, true);
    }

    /**
     * Return whether page has descendants
     *
     * @return bool
     */
    public function hasDescendants()
    {
        foreach ($this->children() as $child) {
            if ($child->hasChildren()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return page level
     *
     * @return int
     */
    public function level()
    {
        return $this->parents()->count();
    }

    /**
     * Return whether current page is Site
     *
     * @return bool
     */
    abstract public function isSite();

    /**
     * Return whether current page is index page
     *
     * @return bool
     */
    abstract public function isIndexPage();

    /**
     * Return whether current page is error page
     *
     * @return bool
     */
    abstract public function isErrorPage();

    /**
     * Return whether current page is deletable
     *
     * @return bool
     */
    abstract public function isDeletable();

    /**
     * Return page metadata
     *
     * @var Metadata
     */
    abstract public function metadata();

    /**
     * Get page data by key
     *
     * @param string $key
     * @param mixed  $default Default value if key is not set
     */
    public function get($key, $default = null)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        if (Arr::has($this->data, $key)) {
            return Arr::get($this->data, $key, $default);
        }
        if (method_exists($this, $key)) {
            return $this->$key();
        }
        return $default;
    }

    /**
     * Return whether page data has a key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return property_exists($this, $key) || Arr::has($this->data, $key);
    }

    /**
     * Set page data
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __call($name, $arguments)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }
        throw new LogicException('Invalid method ' . static::class . '::' . $name);
    }
}
