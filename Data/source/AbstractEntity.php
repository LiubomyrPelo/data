<?php

namespace Data;

abstract class AbstractEntity implements AbstractEntityInterface
{
    public function exchangeArray($data, $aliases = null, $prefix = null, $postfix = null)
    {
        foreach ($this->getArrayCopy() as $key => $value) {
            $_key = $key;

            if (is_array($aliases)) {
                $_key = array_key_exists($key, $aliases) ? $aliases[$key] : $key;
            }

            if ($prefix) {
                $_key = $prefix . $_key;
            }

            if ($postfix) {
                $_key .= $postfix;
            }

            $this->$key = $this->_get($data[$_key]);
        }
    }

    public function additionsArray($data)
    {
        foreach ($this->getArrayCopy() as $key => $value) {
            if ($this->$key == null) {
                $this->$key = $this->_get($data[$key]);
            }
        }
    }

    public function updateArray($data)
    {
        foreach ($this->getArrayCopy() as $key => $value) {
            if ($this->_get($data[$key]) != null) {
                $this->$key = $this->_get($data[$key]);
            }
        }
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function _get(&$val, $default = null)
    {
        if (isset($val)) {
            return $val;
        }

        return $default;
    }

    public function get($value)
    {
        return $this->$value;
    }
}
