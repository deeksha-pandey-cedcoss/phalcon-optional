<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;
use Phalcon\Config\Adapter\Php;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Escaper;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);
$container->set(
    'logger',
    function () {
        $adapter = new Stream(APP_PATH .'/logs/attack.log');
        $logger  = new Logger(
            'messages',
            [
                'main' => $adapter,
            ]
        );

        return $logger;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);
$container->set(
    'db',
    function () {
     return new Mysql($this['config']->db->toArray());
    }
);
$container->set(
    'escaper',
    function ()  {
        return new Escaper();
    }
);

$application = new Application($container);

$container->set(
    'config',
    function () {
        $fileName='../app/config/config.php';

        // $factory= new ConfigFactory();
        // return $config=$factory->newInstance('php', $fileName);


        $config = new Config([]);
        $array = new Php($fileName);
        return $config->merge($array);
    }
);

$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
    [
        'savePath' => '/tmp',
    ]
);
        $session->setAdapter($files);
        $session->start();
    }
);



// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}