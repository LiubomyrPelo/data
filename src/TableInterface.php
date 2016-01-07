<?php

namespace Data;

use Zend\Db\TableGateway\TableGatewayInterface;

interface TableInterface
{
    public function __construct();
    public function setGateway(TableGatewayInterface $tableGateway);
}
