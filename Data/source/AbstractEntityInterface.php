<?php

namespace Data;

interface AbstractEntityInterface
{
    public function exchange($entity);
    public function exchangeArray($data, $aliases = null, $prefix = null, $postfix = null);
    public function additionsArray($data);
    public function updateArray($data);
    public function getArrayCopy();
    public function get(&$val, $default = null);
    public function constructGetSet();
    public function __call($method, $args);
    public function getArrayProperties();
}
