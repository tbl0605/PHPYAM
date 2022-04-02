<?php
namespace PHPYAM\demo;

use PHPYAM\demo\confs\AppConfig;

// Immediately turn output buffering on (DemoRouter will later also start a new output buffer),
// so the \PHPYAM\extra\LoggerUtils instance can gracefully handle (or discard) early error messages.
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    // Use compression when possible.
    if (! ob_start('ob_gzhandler')) {
        ob_start();
    }
} else {
    ob_start();
}

// Load the server config.
require_once 'server-conf.php';

// Load application classes.
require_once __DIR__ . '/../vendor/autoload.php';

// Load application config depending on the server config (for database connections, ...).
require_once __DIR__ . '/confs/' . YAM_ENVIRONNEMENT . '.php';

/*
 * !!! You do not need this code part on a real web server !!!
 * Following hack is only necessary to make this demo work with a php built-in web server through
 * php -S localhost:8000 index.php
 * because php built-in web servers are not able to create $_GET['route'] by themselves,
 * like Apache would do using the .htaccess file provided with this demo.
 */
if (php_sapi_name() === 'cli-server') {
    $path = pathinfo($_SERVER['SCRIPT_FILENAME']);
    $isCurrentFileName = $path['basename'] === basename(__FILE__);
    // Does $_SERVER['SCRIPT_FILENAME'] contain "/index.php"?
    if ($path['dirname'] === __DIR__ && $isCurrentFileName) {
        // Translate /path/to/controller/method to /index.php?route=path/to/controller/method
        $_GET['route'] = ltrim($_SERVER['REQUEST_URI'], '/');
    } else {
        if (strpos($path['dirname'], __DIR__) !== 0 ||
        // $_SERVER['SCRIPT_FILENAME'] contains "index.php" when the request URI
        // doesn't match with a file on local filesystem.
        ($path['dirname'] === '.' && $isCurrentFileName)) {
            // Access forbidden.
            http_response_code(403);
            exit();
        }
        if ($path['extension'] === 'js') {
            header('Content-Type: text/javascript');
            readfile($_SERVER['SCRIPT_FILENAME']);
            exit();
        }
        if ($path['extension'] === 'css') {
            header('Content-Type: text/css');
            readfile($_SERVER['SCRIPT_FILENAME']);
            exit();
        }
        // We don't support any other kind of files for this demo.
        http_response_code(403);
        exit();
    }
}

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

    public function getResourceFileName($type, $resourceName)
    {
        if (YAM_DEMO_USE_AUTOLOADER) {
            // Resource will be autoloaded when necessary, so no need to load it manually
            // and no need to know its location!
            return null;
        }
        // Return file location:
        return __DIR__ . DIRECTORY_SEPARATOR . YAM_DEMO_APP_DIR . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . strtolower((string) $resourceName) . '.php';
    }

    public function getClassName($type, $resourceName)
    {
        if (YAM_DEMO_USE_AUTOLOADER) {
            return '\\PHPYAM\\demo\\application\\' . $type . '\\' . strtolower((string) $resourceName);
        }
        // In the non-autoloader demo, all classes are in global scope:
        return '\\' . $resourceName;
    }
}

// Start the application.
new DemoRouter(new AppConfig());
