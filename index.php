<?php
// Load the server config.
require_once 'server-conf.php';

// Load application classes.
require_once __DIR__ . '/vendor/autoload.php';

// Load application config depending on the server config (for database connections, ...).
require_once __DIR__ . '/confs/' . YAM_ENVIRONNEMENT . '.php';

class Router extends \PHPYAM\core\Router
{

    public $useProjectAutoLoader = true;

    public function getResourceFileName($type, $resourceName)
    {
        if (! $this->useProjectAutoLoader) {
            return parent::getResourceFileName($type, $resourceName);
        }
        return null;
    }

    public function getClassName($type, $resourceName)
    {
        if (! $this->useProjectAutoLoader) {
            return parent::getClassName($type, $resourceName);
        }
        return '\\PHPYAM\\demo\\application\\' . $type . '\\' . strtolower($resourceName);
    }
}

// Start the application.
new Router();
