<?php

namespace Formwork\Core;

use Formwork\Metadata\Metadata;
use Formwork\Utils\FileSystem;
use RuntimeException;

class Site extends AbstractPage
{
    /**
     * Array containing all loaded pages
     *
     * @var array
     */
    protected $storage = array();

    /**
     * Current page
     *
     * @var Page
     */
    protected $currentPage;

    /**
     * Array containing all available templates
     *
     * @var array
     */
    protected $templates = array();

    /**
     * Create a new Site instance
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->path = Formwork::instance()->option('content.path');
        $this->relativePath = DS;
        $this->route = '/';
        $this->data = array_merge($this->defaults(), $data);
        $this->loadTemplates();
    }

    /**
     * Return site default data
     *
     * @return array
     */
    public function defaults()
    {
        return array(
            'title'    => 'Formwork',
            'aliases'  => array(),
            'metadata' => array()
        );
    }

    /**
     * Get all available templates
     *
     * @return array
     */
    public function templates()
    {
        return array_map('strval', array_keys($this->templates));
    }

    /**
     * Return whether a template exists
     *
     * @param string $template
     *
     * @return bool
     */
    public function hasTemplate($template)
    {
        return array_key_exists($template, $this->templates);
    }

    /**
     * Return template filename
     *
     * @param string $name
     *
     * @return string
     */
    public function template($name)
    {
        if (!$this->hasTemplate($name)) {
            throw new RuntimeException('Invalid template ' . $name);
        }
        return $this->templates[$name];
    }

    /**
     * Return whether site has been modified since given time
     *
     * @param int $time
     *
     * @return bool
     */
    public function modifiedSince($time)
    {
        return FileSystem::directoryModifiedSince($this->path, $time);
    }

    /**
     * @inheritdoc
     */
    public function parent()
    {
        return null;
    }

    /**
     * Return a PageCollection containing site pages
     *
     * @return PageCollection
     */
    public function pages()
    {
        return $this->children();
    }

    /**
     * Return whether site has pages
     *
     * @return bool
     */
    public function hasPages()
    {
        return !$this->children()->isEmpty();
    }

    /**
     * Return alias of a given route
     *
     * @param string $route
     *
     * @return string|null
     */
    public function alias($route)
    {
        if ($this->has('aliases')) {
            $route = trim($route, '/');
            if (isset($this->data['aliases'][$route])) {
                return $this->data['aliases'][$route];
            }
        }
    }

    /**
     * Set and return site current page
     *
     * @param Page $page
     *
     * @return Page
     */
    public function setCurrentPage(Page $page)
    {
        return $this->currentPage = $page;
    }

    /**
     * Navigate to and return a page from its route, setting then the current page
     *
     * @param string $route
     *
     * @return Page
     */
    public function navigate($route)
    {
        return $this->currentPage = $this->findPage($route);
    }

    /**
     * Get site index page
     *
     * @return Page|null
     */
    public function indexPage()
    {
        return $this->findPage(Formwork::instance()->option('pages.index'));
    }

    /**
     * Return or render site error page
     *
     * @param bool $navigate Whether to navigate to the error page or not
     *
     * @return Page|null
     */
    public function errorPage($navigate = false)
    {
        $errorPage = $this->findPage(Formwork::instance()->option('pages.error'));
        if ($navigate) {
            $this->currentPage = $errorPage;
        }
        return $errorPage;
    }

    /**
     * Get site language
     *
     * @deprecated
     *
     * @return string|null
     */
    public function lang()
    {
        trigger_error(static::class . '::lang() is deprecated since Formwork 1.2.0, use ' . static::class . '::languages()->default() instead', E_USER_DEPRECATED);
        return $this->languages()->default() ?? ($this->data['lang'] ?? 'en');
    }

    /**
     * @inheritdoc
     */
    public function isSite()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isIndexPage()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isErrorPage()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isDeletable()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function metadata()
    {
        if (!is_null($this->metadata)) {
            return $this->metadata;
        }
        $defaults = array(
            'charset'     => Formwork::instance()->option('charset'),
            'author'      => $this->get('author'),
            'description' => $this->get('description'),
            'generator'   => Formwork::instance()->option('metadata.set_generator') ? 'Formwork' : null
        );
        return $this->metadata = new Metadata(array_filter(array_merge($defaults, $this->data['metadata'])));
    }

    /**
     * Find page from route
     *
     * @param string $route
     *
     * @return Page|null
     */
    public function findPage($route)
    {
        if ($route === '/') {
            return $this->indexPage();
        }

        $components = explode('/', trim($route, '/'));
        $path = $this->path;

        foreach ($components as $component) {
            $found = false;
            foreach (FileSystem::listDirectories($path) as $dir) {
                if (preg_replace(Page::NUM_REGEX, '', $dir) === $component) {
                    $path .= $dir . DS;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return null;
            }
        }

        $page = $this->retrievePage($path);

        return !$page->isEmpty() ? $page : null;
    }

    /**
     * Retrieve page from the storage creating a new one if not existing
     *
     * @param string $path
     *
     * @return Page
     */
    public function retrievePage($path)
    {
        if (isset($this->storage[$path])) {
            return $this->storage[$path];
        }
        return $this->storage[$path] = new Page($path);
    }

    /**
     * Load all available templates
     */
    protected function loadTemplates()
    {
        $templatesPath = Formwork::instance()->option('templates.path');
        $templates = array();
        foreach (FileSystem::listFiles($templatesPath) as $file) {
            $templates[FileSystem::name($file)] = $templatesPath . $file;
        }
        $this->templates = $templates;
    }

    public function __toString()
    {
        return 'site';
    }
}
