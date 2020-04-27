<?php
namespace PHPYAM\demo;

// Load the server config.
require_once 'server-conf.php';

// Load application classes.
require_once __DIR__ . '/vendor/autoload.php';

// Load application config depending on the server config (for database connections, ...).
require_once __DIR__ . '/confs/' . YAM_ENVIRONNEMENT . '.php';

/**
 * Customized router to launch the application demo.
 *
 * @package PHPYAM\demo
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2020 Thierry BLIND
 */
class DemoRouter extends \PHPYAM\core\Router
{

    /**
     * In this demo, you can try 2 different ways to load the PHPYAM MVC framework and all aplication resources,
     * like controller, model or security policy classes.<br>
     * For this purpose, all aplication resources were duplicated in 2 different directories.<br>
     * You can either let the \PHPYAM\core\Router class load the user MVC classes
     * (inside the application-demo-noautoloader directory),
     * or let an external autoloader (for example generated by Composer) handle class loading (inside the
     * application-demo-autoloader directory).<br>
     * By default, if you directly use the \PHPYAM\core\Router without extending it,
     * the router will do the MVC class loading stuff by himself, <b>all the MVC and security classes having
     * then to be defined in the global namespace</b>.
     *
     * To control the class naming & loading feature of PHPYAM, extend the class \PHPYAM\core\Router and
     * implement its \PHPYAM\core\Router::getResourceFileName($type, $resourceName) and
     * \PHPYAM\core\Router::getClassName($type, $resourceName) methods depending on your needs.
     *
     * @var bool try the autoloader demo version when true, the non-autoloader demo version otherwise
     */
    public $useApplicationAutoLoader = true;

    public function getResourceFileName($type, $resourceName)
    {
        if ($this->useApplicationAutoLoader) {
            // Resource will be autoloaded when necessary, so no need to load it manually
            // and no need to know its location!
            return null;
        }
        // Return file location:
        return __DIR__ . DIRECTORY_SEPARATOR . 'application-demo-noautoloader' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . strtolower($resourceName) . '.php';
    }

    public function getClassName($type, $resourceName)
    {
        if ($this->useApplicationAutoLoader) {
            return '\\PHPYAM\\demo\\application\\' . $type . '\\' . strtolower($resourceName);
        }
        // In this demo, all classes are in global scope:
        return '\\' . $resourceName;
    }
}

// Start the application.
new DemoRouter();
