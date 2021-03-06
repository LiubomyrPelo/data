<?php

namespace Data;

use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Data\Table\ForeignKeyInterface;

abstract class AbstractTable implements AbstractTableInterface
{
    protected $tableGateway;
    protected $serviceLocator;
    protected $name;
    protected $entity;
    protected $select;
    protected $foreignKeys = [];
    protected $allowForeignKeys = true;

    protected function setGateway(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->select = new Select();
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getResultSet()
    {
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype($this->entity);

        return $resultSet;
    }

    public function addForeignKey(ForeignKeyInterface $foreignKey)
    {
        $this->foreignKeys[] = $foreignKey;
        return $this;
    }

    public function disableForeignKeys()
    {
        $this->allowForeignKeys = false;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getSelect()
    {
        return $this->select;
    }

    public function fetch($meta = null)
    {
        $this->select->from($this->name);

        $dbSelect = new DbSelect(
            $this->select,
            $this->tableGateway->getAdapter(),
            $this->getResultSet()
        );

        $paginator = new Paginator($dbSelect);

        if ($meta) {
            if (
                isset($meta['loadAllToFirstPage'])
                ||
                isset($meta['all'])
            ) {
                $paginator->setItemCountPerPage(
                    $paginator->getTotalItemCount()
                );
            }
        }

        return $this->constructForeignKeys($paginator);
    }

    public function constructForeignKeys(Paginator $paginator)
    {
        if (!$this->allowForeignKeys) {
            return $paginator;
        }

        foreach ($this->foreignKeys as $key) {

            foreach ($paginator->getCurrentItems() as $item) {

                if ($this->serviceLocator->has('model\\' . $key->table)) {
                    try {
                        $table = $this->serviceLocator
                            ->get('model\\' . $key->table)
                            ->getTable();

                        $table->getSelect()->reset('where');

                        $object = $table->get(
                            $item->{$key->name},
                            $key->col,
                            true
                        );
                    } catch (Exception $e) {
                        throw new \Exception('Can\'t find col ' . $key->col . ' in model ' . $key->table);
                    }
                } else {
                    throw new \Exception('Can\'t find \'model\' ' . $key->table . ' service');
                }

                $object = $object
                    ? (
                        $object->count() == 1
                        ? current($object->getArrayCopy())
                        : $object->getArrayCopy()
                    )
                    : null;

                $item->{$key->key ? $key->key : $key->table} = $object;

                $functionName = $key->table;
                $snakeCase = strpos($functionName, '_');
                if ($snakeCase) {
                    $functionName = ucwords($functionName, '_');
                    $functionName = str_replace('_', '', $functionName);
                } else {
                    $functionName = ucfirst($functionName);
                }
                $functionName = 'get' . $functionName;

                $getFunction = function () use ($item, $key) {
                    return $item->{$key->key ? $key->key : $key->table};
                };

                $item->methods[$functionName] = \Closure::bind($getFunction, $item, get_class($item));
            }
        }

        return $paginator;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function get($value, $col = 'id', $fetch = false)
    {
        if ($fetch) {
            $this->select->where([$col => $value]);
            return $this->fetch()->getCurrentItems();
        }

        return $this->tableGateway
            ->select([$col => $value])
            ->current();
    }

    public function save($entity = null, $_id = 'id', $insert = false)
    {
        if (!$entity) {
            $entity = $this->entity;
        }

        $data = !is_array($entity) ? (array) $entity : $entity;

        if (isset($data['methods'])) {
            unset($data['methods']);
        }

        $id = is_object($entity) ? $entity->{$_id} : $entity{$_id};

        if (
            $id == 0
            ||
            $insert
        ) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->get($id, $_id)) {
                $this->tableGateway->update(
                    $data,
                    [$_id => $id]
                );
            }
            return $id;
        }
    }

    public function delete($entity, $id = 'id')
    {
        $this->tableGateway->delete([
            $id => $entity->{$id},
        ]);
    }
}
