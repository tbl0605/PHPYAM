<?php
/**
 * This file should be present only once per PHP server!
 * It should be placed apart and added to the include path of the PHP server hosting this file,
 * so every web application could share this file and use it by doing:
 * <code>require_once 'server-conf.php';</code>
 *
 * There are at least 2 ways to achieve that:
 * <ul>
 * <li>redefine the include path at application level, inside of each "index.php"
 * <code>ini_set('include_path', '/path/to/this/file');</code>
 * <li>redefine the include path at server level, inside of the "php.ini" configuration file (it's the better option)
 * <code>include_path="C:\path\to\this\file;.;C:\xampp\php\PEAR"</code>
 * <ul>
 *
 * Main purpose of this file is to give a unique environment name (stored in constant YAM_ENVIRONNEMENT)
 * among all internal PHP servers.
 * Each web application can then host separate configuration files, one configuration file per existing PHP server,
 * and load the appropriate configuration file based on the environment name (stored in constant YAM_ENVIRONNEMENT).
 *
 * Other constants here simply help to customize the web application renderings.
 */
if (! defined('EOL')) {
    define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
}

/**
 * PHP Script start time.
 * It's a good place to put this here,
 * because this file should be included at first place of every "index.php" file.
 *
 * @var float $YAM_START_TIME
 */
define('YAM_START_TIME', microtime(true));

/**
 * Should we use the minified version of the css or javascript files?
 *
 * @var string $YAM_MIN
 */
define('YAM_MIN', '');
/* define('YAM_MIN', '.min'); */

/**
 * Environment name (e.g. development, production, ...).
 * This constant value will be used to retrieve the correct configuration file.
 * The configuration file should be located in the "confs/" directory of every
 * web application and named $YAM_ENVIRONNEMENT + ".php"
 *
 * @var string $YAM_ENVIRONNEMENT
 */
define('YAM_ENVIRONNEMENT', 'development');
