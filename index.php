<?php
// Load the server config.
require_once 'server-conf.php';

// Load application classes.
require_once __DIR__ . '/vendor/autoload.php';

// Load application config depending on the server config (for database connections, ...).
require_once __DIR__ . '/confs/' . YAM_ENVIRONNEMENT . '.php';

// Start the application.
new \PHPYAM\core\Router();
