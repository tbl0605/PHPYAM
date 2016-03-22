<?php

/**
 * Configuration file for the "development" environment.
 *
 * For more info about constants please @see http://php.net/manual/en/function.define.php
 * If you want to know why we use "define" instead of "const" @see http://stackoverflow.com/q/2447791/1114320
 */

/**
 * Configuration for: error reporting.
 * Useful to show every little problem during development, but should only show hard errors in production!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Should we use the library log4php to do message logging?
 * By setting this constant to false, the developer will drop all dependencies to the library log4php
 * but he has then to fully implement it's own logging system if he needs one.
 * By setting this constant to false, be also aware that:
 * <ul>
 * <li>class \PHPYAM\libs\LoggerUtils will become useless
 * <li>router fatal errors (in the \PHPYAM\core\Core class) will be outputted to an error page, but there will be no additional logging
 * </ul>
 *
 * By default, USE_LOG4PHP is set to true.
 *
 * @var boolean
 */
define('USE_LOG4PHP', true);

if (USE_LOG4PHP) {
    \PHPYAM\libs\LoggerUtils::configure(__DIR__ . '/log4php-development.xml');
}

/**
 * URL to this application base directory.<br />
 * Note: the value of URL will always be ended by a "/".
 * Note 2: The environment variable REDIRECT_HTTP_YAM_REWRITEBASE
 * is defined in the htaccess file. If you don't use htaccess files,
 * you have to manually set the value of URL.<br />
 * For example: <code>define('URL', '/path/to/the/appbasedir/');</code>
 *
 * @var string
 * @see the htaccess file
 */
define('URL', getenv('REDIRECT_HTTP_YAM_REWRITEBASE'));

/**
 * URL to this application "public" directory.<br />
 * Note: the value of URL_PUB will always be ended by a "/".
 * Warning: be careful to update your htaccess file accordingly
 * (when using them) if you change the location of the "public" directory.
 *
 * @var string
 */
define('URL_PUB', URL . 'public-demo/');

/**
 * Full path of this application "public" directory.<br />
 * Note: the value of SYS_PUB will always be ended by a "/".
 *
 * @var string
 */
define('SYS_PUB', __DIR__ . '/../public-demo/');

/**
 * Full path of the "application" directory.
 * Necessary to the router to determinate the base directory for a resource
 * that has to be checked by {@link PHPYAM\core\interfaces\IRouter::isResource($pathName, $resourceName)}
 * or loaded by {@link PHPYAM\core\interfaces\IRouter::loadResource($pathName, $resourceName)}.<br />
 * Note: the value of SYS_APP will always be ended by a "/".
 *
 * @var string
 */
define('SYS_APP', __DIR__ . '/../application-demo/');

/**
 * Defines how PHPYAM should store the parameters extracted
 * from an URL.
 * Example of URL to decode:
 * localhost/myapp/controller1/action1/param1/param2/param3/param4/param5
 *
 * With URL_ASSOCIATIVE_PARAMS = true, we will retrieve following parameters:
 * <code>array("param1" => "param2", "param3" => "param4", "param5" => "")</code>
 *
 * With URL_ASSOCIATIVE_PARAMS = false, we will retrieve following parameters:
 * <code>array(0 => "param1", 1 => "param2", 2 => "param3", 3 => "param4", 4 => "param5")</code>
 *
 * By default, URL_ASSOCIATIVE_PARAMS is set to true.
 *
 * @var boolean
 * @see \PHPYAM\core\Router::splitUrl()
 */
define('URL_ASSOCIATIVE_PARAMS', true);

/**
 * Should we drop all ODBC connections when an exception occurred while
 * using PHPYAM?
 * Note: ODBC connections will never be dropped when a \PHPYAM\core\RouterException was thrown.
 * Such \PHPYAM\core\RouterException should only be thrown by the developer in case of legitimate errors
 * (the router throws \PHPYAM\core\RouterExceptions to signal invalid URLs for example).
 *
 * By default, DROP_ALL_ODBC_CONNECTIONS_ON_FATAL_ERROR is set to true.
 *
 * @var boolean
 * @see \PHPYAM\core\Router::cleanupOnFatalError()
 */
define('DROP_ALL_ODBC_CONNECTIONS_ON_FATAL_ERROR', true);

/**
 * Should we unset and destroy current PHP session when an exception occurred while
 * using PHPYAM?
 * Note: current PHP session will never be dropped when a \PHPYAM\core\RouterException was thrown.
 * Such \PHPYAM\core\RouterException should only be thrown by the developer in case of legitimate errors
 * (the router throws \PHPYAM\core\RouterExceptions to signal invalid URLs for example).
 *
 * By default, DROP_SESSION_ON_FATAL_ERROR is set to true.
 *
 * @var boolean
 * @see \PHPYAM\core\Router::cleanupOnFatalError()
 */
define('DROP_SESSION_ON_FATAL_ERROR', true);

/**
 * Name of the class used for user authentication.
 * This class must be located in the directory "{SYS_APP}/security".
 *
 * @var string
 */
define('SECURITY_POLICY', 'authentication');

/**
 * Name of the class used as default controller when user
 * connects to this web application, or when URL contains no controller name.
 *
 * @var string
 */
define('DEFAULT_CONTROLLER', 'home');

/**
 * Name of the method used as default action when user
 * connects to this web application, or when URL contains no action name.
 *
 * @var string
 */
define('DEFAULT_ACTION', 'index');

/**
 * Name of the class used as controller by the router
 * to display exceptions that were thrown while handling a web request.
 *
 * @var string
 */
define('ERROR_CONTROLLER', 'error');

/**
 * Name of the method used as action by the router
 * to display exceptions that were thrown while handling a non-Ajax web request.
 *
 * @var string
 */
define('ERROR_ACTION', 'index');

/**
 * Name of the method used as action by the router
 * to display exceptions that were thrown while handling an Ajax web request.
 *
 * @var string
 */
define('ERROR_AJAX_ACTION', 'indexAjax');

/**
 * Server-side encoding AND client-side encoding (web browser).
 * Anyway, developers should always use same encoding on both side...
 *
 * @var string
 * @see http://stackoverflow.com/questions/553463/jquery-ajax-character-encoding
 */
define('CLIENT_CHARSET', 'ISO-8859-1');

/**
 * Server-side language AND client-side language (web browser).
 * Anyway, developers should always use same language on both side...
 *
 * @var string
 */
define('CLIENT_LANGUAGE', 'en_GB');

/**
 * Title of all web pages (if used).
 *
 * @var string
 */
define('TITLE', 'DEMO application MVC');

/**
 * Configuration for: database.
 * This is the place where you define your database credentials, database type, etc...
 * Better put all those in a class with static members and properties...
 */
/*
 * TODO: AppConfig is just an example here how the developer can store global preferences and configurations!
 * You can name your class like you want, core libraries of PHPYAM have no dependencies to it...
 */
class AppConfig
{

    public static $locations = array(
        'east' => array(
            'ODBC_DSN' => 'serverdev1',
            'ODBC_USER' => 'userdev1',
            'ODBC_PWD' => 'password1',
            'ODBC_DESCRIPTION' => 'SERVER DEV 1'
        ),
        'west' => array(
            'ODBC_DSN' => 'serverdev2',
            'ODBC_USER' => 'userdev2',
            'ODBC_PWD' => 'password2',
            'ODBC_DESCRIPTION' => 'SERVER DEV 2'
        )
    );
}
