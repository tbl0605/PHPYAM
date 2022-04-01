<?php
namespace PHPYAM\demo\confs;

use PHPYAM\extra\Configuration;

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

\PHPYAM\extra\LoggerUtils::configure(__DIR__ . '/log4php-development.xml');

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
define('URL', php_sapi_name() === 'cli-server' ? '/' : getenv('REDIRECT_HTTP_YAM_REWRITEBASE'));

class AppConfig extends Configuration
{

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
     * <b>Creating this constant is optional</b>,
     * not creating it will be the same as defining URL_ASSOCIATIVE_PARAMS to true.
     *
     * @var boolean
     * @see \PHPYAM\core\Router::splitUrl()
     */
    //const URL_ASSOCIATIVE_PARAMS = true;

    /**
     * Should this router start (or resume) PHP sessions?
     * Note: in most case, this constant should be set to true,
     * except for (stateless) REST API applications.
     * Disabling PHP sessions will also disable the Intelliform anti-repost feature,
     * because this feature needs to store and retrieve data in $_SESSION to work correctly.
     *
     * <b>Creating this constant is optional</b>,
     * not creating it will be the same as defining CREATE_SESSION to true.
     *
     * @var boolean
     * @see \PHPYAM\core\Router::initRouter()
     * @see \PHPYAM\core\Router::cleanupOnFatalError()
     */
    //const CREATE_SESSION = true;

    /**
     * Should we drop all ODBC connections when an exception occurred while
     * using PHPYAM?
     * Note: ODBC connections will never be dropped when a \PHPYAM\core\RouterException was thrown.
     * Such \PHPYAM\core\RouterException should only be thrown by the developer in case of legitimate errors
     * (the router throws \PHPYAM\core\RouterExceptions to signal invalid URLs for example).
     *
     * <b>Creating this constant is optional</b>,
     * not creating it will be the same as defining DROP_ALL_ODBC_CONNECTIONS_ON_FATAL_ERROR to true.
     *
     * @var boolean
     * @see \PHPYAM\core\Router::cleanupOnFatalError()
     */
    //const DROP_ALL_ODBC_CONNECTIONS_ON_FATAL_ERROR = true;

    /**
     * Should we unset and destroy current PHP session when a "Throwable" occurred while
     * using PHPYAM?
     * Note: current PHP session will never be dropped when a \PHPYAM\core\RouterException was thrown.
     * Such \PHPYAM\core\RouterException should only be thrown by the developer in case of legitimate errors
     * (the router throws \PHPYAM\core\RouterExceptions to signal invalid URLs for example).
     * Note: setting DROP_SESSION_ON_FATAL_ERROR to true will have no effect if CREATE_SESSION is set to false.
     *
     * <b>Creating this constant is optional</b>,
     * not creating it will be the same as defining DROP_SESSION_ON_FATAL_ERROR to true.
     *
     * @var boolean
     * @see \PHPYAM\core\Router::cleanupOnFatalError()
     */
    //const DROP_SESSION_ON_FATAL_ERROR = true;

    /**
     * Let PHPYAM try to handle (or not) "internal PHP errors" like it does with any other kind of "throwable fatal error".
     * When this constant is undefined or set to false, the function defined by \set_exception_handler()
     * will be used to handle "internal PHP errors", i.e. all errors inheriting from base class \Error.
     * Note: defining a custom error handler through \set_exception_handler() in addition to PHPYAM's own handler is always
     * a good idea, to be sure to catch "internal PHP errors" that could have escaped the vigilance of PHPYAM.
     *
     * <b>Creating this constant is optional</b>,
     * not creating it will be the same as defining PHPYAM_CATCH_INTERNAL_PHP_ERRORS to false.
     *
     * @var boolean
     * @see \set_exception_handler()
     */
    //const PHPYAM_CATCH_INTERNAL_PHP_ERRORS = false;

    /**
     * Name of the class used for user authentication.
     *
     * @var string
     */
    const SECURITY_POLICY = 'authentication';

    /**
     * Name of the class used as default controller when user
     * connects to this web application, or when URL contains no controller name.
     *
     * @var string
     */
    const DEFAULT_CONTROLLER = 'home';

    /**
     * Name of the method used as default action when user
     * connects to this web application, or when URL contains no action name.
     *
     * @var string
     */
    const DEFAULT_ACTION = 'index';

    /**
     * Name of the class used as controller by the router
     * to display exceptions that were thrown while handling a web request.
     *
     * @var string
     */
    const ERROR_CONTROLLER = 'error';

    /**
     * Name of the method used as action by the router
     * to display exceptions that were thrown while handling a non-Ajax web request.
     *
     * @var string
     */
    const ERROR_ACTION = 'index';

    /**
     * Name of the method used as action by the router
     * to display exceptions that were thrown while handling an Ajax web request.
     *
     * @var string
     */
    const ERROR_AJAX_ACTION = 'indexAjax';

    /**
     * Server-side encoding AND client-side encoding (web browser).
     * Anyway, developers should always use same encoding on both side...
     *
     * @var string
     * @see http://stackoverflow.com/questions/553463/jquery-ajax-character-encoding
     */
    const CLIENT_CHARSET = 'ISO-8859-1';

    /**
     * Server-side language AND client-side language (web browser).
     * Anyway, developers should always use same language on both side...
     *
     * @var string
     */
    const CLIENT_LANGUAGE = 'en-GB';

    /**
     * Enable IntelliForm debug options.
     * When enabled, IntelliForm will throw exceptions on IntelliForm usage problems.
     *
     * <b>Creating this constant is optional</b>,
     * not creating it will be the same as defining ANTZ_DEBUG to false.
     *
     * @var boolean
     */
    const ANTZ_DEBUG = true;

    /*
     * Configuration for: database.
     * This is the place where you define your database credentials, database type, etc...
     * Better put all those in a class with static members and properties...
     */

    /**
     * URL to this application "public" directory.<br />
     * Note: the value of URL_PUB will always be ended by a "/".
     * Warning: be careful to update your htaccess file accordingly
     * (when using them) if you change the location of the "public" directory.
     *
     * @var string
     */
    const URL_PUB = URL . 'public/';

    /**
     * Full path of this application "public" directory.<br />
     * Note: the value of SYS_PUB will always be ended by a "/".
     *
     * @var string
     */
    const SYS_PUB = __DIR__ . '/../public/';

    /**
     * Title of all web pages (if used).
     *
     * @var string
     */
    const TITLE = 'DEMO application MVC';

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
