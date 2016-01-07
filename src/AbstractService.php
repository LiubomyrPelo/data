<?php

namespace Data;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

abstract class AbstractService implements AbstractServiceInterface
{
    protected $table;

    public function __construct(ServiceLocatorInterface $serviceLocator, AbstractTableInterface $table)
    {
        $tableGateway = new TableGateway(
            $table->getName(),
            $serviceLocator->get('Zend\Db\Adapter\Adapter'),
            null,
            $table->getResultSet()
        );

        $table->setGateway($tableGateway);
        $table->setServiceLocator($serviceLocator);

        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function save($data)
    {
        $this->table->getEntity()->exchangeArray($data);
        $this->table->save();
    }
}
