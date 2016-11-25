<?php

namespace Data;

use Data\Table\ForeignKeyInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

interface AbstractTableInterface
{
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);
    public function getServiceLocator();
    public function getResultSet();
    public function addForeignKey(ForeignKeyInterface $foreignKey);
    public function disableForeignKeys();
    public function getName();
    public function getEntity();
    public function getSelect();
    public function fetch();
    public function fetchAll();
    public function get($value, $col = 'id');
    public function save($entity);
    public function delete($entity);
}
