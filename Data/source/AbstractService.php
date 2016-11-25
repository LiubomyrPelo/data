<?php

namespace Data;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

abstract class AbstractService implements AbstractServiceInterface
{
    protected $table;

    private $dbAdapterServiceName;

    public function __construct(ServiceLocatorInterface $serviceLocator, AbstractTableInterface $table)
    {
        $dbAdapter = null;

        if ($this->dbAdapterServiceName) {
            if (
                $serviceLocator->has(
                    $this->dbAdapterServiceName
                )
            ) {
                $dbAdapter = $serviceLocator->get(
                    $this->dbAdapterServiceName
                );
            }
        } else {
            $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        }

        $tableGateway = new TableGateway(
            $table->getName(),
            $dbAdapter,
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

    protected function setDbAdapterServiceName($value)
    {
        $this->dbAdapterServiceName = (string) $value;
    }

    public function save($data)
    {
        $this->table->getEntity()->exchangeArray($data);
        $this->table->save();
    }
}
