<?php

namespace Data;

interface AbstractEntityInterface
{
    public function exchangeArray($data, $aliases = null, $prefix = null, $postfix = null);
    public function additionsArray($data);
    public function updateArray($data);
    public function getArrayCopy();
    public function _get(&$val, $default = null);
    public function get($value);
}
