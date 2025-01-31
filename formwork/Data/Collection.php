<?php

namespace Formwork\Data;

use Countable;
use Iterator;

class Collection implements Countable, Iterator
{
    /**
     * Array containing collection items
     *
     * @var array
     */
    protected $items = array();

    /**
     * Create a new Collection instance
     *
     * @param array $items
     */
    public function __construct(array $items = array())
    {
        $this->items = $items;
    }

    /**
     * Rewind the iterator to the first element
     */
    public function rewind()
    {
        reset($this->items);
    }

    /**
     * Return the current element
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * Return the key of the current element
     *
     * @return int|string|null
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * Check if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return !is_null($this->key());
    }

    /**
     * Return the number of items
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Return an array containing collection items
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Return first collection item
     *
     * @return mixed|null
     */
    public function first()
    {
        return $this->items[0] ?? null;
    }

    /**
     * Return last collection item
     *
     * @return mixed|null
     */
    public function last()
    {
        return $this->items[$this->count() - 1] ?? null;
    }

    /**
     * Return whether collection is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }
}
