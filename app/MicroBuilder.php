<?php

namespace App;

use Phalcon\DiInterface;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Config;

class MicroBuilder
{
    /**
     * @var Phalcon\DiInterface
     */
    protected $di;

    /**
     * @var Phalcon\Mvc\Micro
     */
    protected $app;

    /**
     * @var Phalcon\Config
     */
    protected $config;

    /**
     * @param  DiInterface $di
     * @return MicroBuilder
     */
    public function __construct(DiInterface $di = null)
    {
        $this->di = $di;

        if ($this->di === null) {
            $this->di = new FactoryDefault();
        }

        $this->app = new Micro();
        $this->defineConfigEnvironment();
    }

    protected function defineConfigEnvironment()
    {
        $production = require __DIR__ . '/../config/production.php';
        $this->config = new Config($production);

        if (getenv('APPLICATION_ENV') != 'production') {
            $development = require __DIR__ . '/../config/development.php';
            $this->config->merge(new Config($development));
        }

        $this->di->set('config', $this->config);
    }

    public function registerServices()
    {
        $services = require __DIR__ . '/../config/services.php';

        foreach($services as $serviceName => $callback) {
            $this->di->set($serviceName, $callback($this->di));
        }
    }

    public function registerConnections()
    {
        $connections = $this->config->connections;

        foreach ($connections as $connectionName => $connection) {
            $connectionAdapter = $this->createLazyLoadingConnection($connection);
            $this->di->setShared($connectionName, $connectionAdapter);
        }
    }

    private function createLazyLoadingConnection($connection)
    {
        return $connectionAdapter = function () use ($connection) {
            return new $connection->adapter([
                'host'     => $connection->host,
                'username' => $connection->username,
                'password' => $connection->password,
                'dbname'   => $connection->dbname
            ]);
        };
    }

    public function registerNotFoundHandler()
    {
        $app = $this->app;
        $app->notFound(function () use ($app) {
            $app->response->setStatusCode(404, "Not Found")->sendHeaders();
            echo 'this page was not found!';
        });
    }

    public function registerExceptionHandler()
    {
        $this->error(function(){
            
        });
    }

    public function getMicro()
    {

    }
}
