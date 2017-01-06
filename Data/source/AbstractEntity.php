<?php

namespace Data;

abstract class AbstractEntity implements AbstractEntityInterface
{
    public $methods = [];

    public function __invoke()
    {
        if (empty($this->methods)) {
            $this->constructGetSet();
        }
    }

    public function exchange($entity)
    {
        $this->exchangeArray(
            $entity->getArrayCopy()
        );
    }

    public function exchangeArray($data, $aliases = null, $prefix = null, $postfix = null)
    {
        $this->__invoke();

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

            $this->$key = $this->get($data[$_key]);
        }
    }

    public function additionsArray($data)
    {
        foreach ($this->getArrayCopy() as $key => $value) {
            if ($this->$key == null) {
                $this->$key = $this->get($data[$key]);
            }
        }
    }

    public function updateArray($data)
    {
        foreach ($this->getArrayCopy() as $key => $value) {
            if ($this->get($data[$key]) != null) {
                $this->$key = $this->get($data[$key]);
            }
        }
    }

    public function getArrayCopy()
    {
        $result = get_object_vars($this);
        unset($result['methods']);

        return $result;
    }

    public function get(&$val, $default = null)
    {
        if (isset($val)) {
            return $val;
        }

        return $default;
    }

    public function constructGetSet()
    {
        foreach ($this->getArrayCopy() as $key => $item) {
            $self = $this;

            $getFunction = function () use ($self, $key) {
                return $self->{$key};
            };

            $setFunction = function ($value) use ($self, $key) {
                $self->{$key} = $value;
                return $self;
            };

            $name = ucfirst($key);

            $snakeCase = strpos($name, '_');

            if ($snakeCase) {
                $name = ucwords($name, '_');
                $name = str_replace('_', '', $name);
            }

            $getName = 'get' . $name;
            $setName = 'set' . $name;

            $this->methods[$getName] = \Closure::bind($getFunction, $this, get_class());
            $this->methods[$setName] = \Closure::bind($setFunction, $this, get_class());
        }

        return $this;
    }

    public function __call($method, $args)
    {
        if (is_callable($this->methods[$method])) {
            return call_user_func_array($this->methods[$method], $args);
        }
    }

    public function getArrayProperties()
    {
        $result = get_object_vars($this);
        unset($result['methods']);

        return array_keys($result);
    }
}
