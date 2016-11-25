<?php

namespace Data\Db\Adapter;

use Zend\Db\Adapter\AdapterServiceFactory as ZendAdapterServiceFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;

class AdapterServiceFactory extends ZendAdapterServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $adapter = new Adapter($config['db']['adapters']['default']);

        return $adapter;
    }
}
