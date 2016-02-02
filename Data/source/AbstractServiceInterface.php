<?php

namespace Data;

use Zend\ServiceManager\ServiceLocatorInterface;

interface AbstractServiceInterface
{
    public function __construct(ServiceLocatorInterface $serviceLocator, AbstractTableInterface $table);
    public function getTable();
    public function save($data);
}
