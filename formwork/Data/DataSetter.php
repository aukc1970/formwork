<?php

namespace Formwork\Data;

class DataSetter extends DataGetter
{
    /**
     * Set a data value by key
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
