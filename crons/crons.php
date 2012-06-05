<?php
    // Define path to application directory
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

    // Define application environment
    defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
    )));

    require_once APPLICATION_PATH .
        '/../library/Doctrine/Common/ClassLoader.php';

    require_once APPLICATION_PATH .
        '/../library/Symfony/Component/Di/sfServiceContainerAutoloader.php';

    include "Zend/Loader/Autoloader.php";

    sfServiceContainerAutoloader::register();
    $autoloader = Zend_Loader_Autoloader::getInstance();

    $fmmAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
    $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Bisna');

    $fmmAutoloader = new \Doctrine\Common\ClassLoader('App');
    $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'App');

    $fmmAutoloader = new \Doctrine\Common\ClassLoader('Boilerplate');
    $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Boilerplate');

    $fmmAutoloader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL\Migrations');
    $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Doctrine\DBAL\Migrations');

    // Creating application
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );

    // Bootstrapping resources
    $application->bootstrap();
    
    $streamManager = new \App\Cron\Twitter\StreamManager();
    $streamManager->loadStreams();
?>
