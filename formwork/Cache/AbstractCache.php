<?php

namespace Formwork\Cache;

abstract class AbstractCache
{
    /**
     * Return cached resource
     *
     * @param string $key
     */
    abstract public function fetch($key);

    /**
     * Save data to cache
     *
     * @param string $key
     * @param mixed  $value
     */
    abstract public function save($key, $value);

    /**
     * Delete cached resource
     *
     * @param string $key
     */
    abstract public function delete($key);

    /**
     * Clear cache
     */
    abstract public function clear();

    /**
     * Return whether a resource is cached
     *
     * @param string $key
     *
     * @return bool
     */
    abstract public function has($key);

    /**
     * Fetch multiple data from cache
     *
     * @param array $keys
     *
     * @return array
     */
    public function fetchMultiple(array $keys)
    {
        $result = array();
        foreach ($keys as $key) {
            $result[] = $this->fetch($key);
        }
        return $result;
    }

    /**
     * Save multiple data to cache
     *
     * @param array $keysAndValues
     */
    public function saveMultiple(array $keysAndValues)
    {
        foreach ($keysAndValues as $key => $value) {
            $this->save($key, $value);
        }
    }

    /**
     * Delete multiple cached resources
     *
     * @param array $keys
     */
    public function deleteMultiple(array $keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    /**
     * Return whether multiple resources are cached
     *
     * @param array $keys
     *
     * @return bool
     */
    public function hasMultiple(array $keys)
    {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }
}
