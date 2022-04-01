<?php
namespace PHPYAM\core;

use PHPYAM\core\interfaces\IKeepRouterException;
use PHPYAM\core\interfaces\IRouter;
use PHPYAM\libs\Assert;
use PHPYAM\libs\BufferUtils;
use PHPYAM\libs\IntelliForm;
use PHPYAM\libs\RouterException;
use PHPYAM\libs\Store;

/**
 * Class used to translate the URL of a web request into a call to a class and a method
 * (also known as controller and action) that will do all the application logic.
 * Web applications using this "router" class should comply with the MVC paradigm
 * (Model-View-Controller).
 *
 * This class is the main part of every MVC-based web application.
 * Its main features are to:
 * <ul>
 * <li>configure a full logging system (based on the log4php library)
 * <li>do user authentication
 * <li>do ajax controls
 * <li>have data output buffering support
 * <li>give client&server encoding support
 * <li>do URL forwarding
 * <li>translate the URL of the web request into a call to a controller + action + parameters
 * </ul>
 * Done with minimal code complexity for maximum flexibility and extensibility.
 *
 * There are some router options available to change its behavior.
 * Those options are coded as configuration keys and are defined in the conf/*.php files.
 *
 * @package PHPYAM\core
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2022 Thierry BLIND
 */
abstract class Router implements IRouter
{

    /**
     *
     * @var string The class treating the web request, also known as "controller"
     */
    private $urlController = '';

    /**
     *
     * @var string The method attached to the controller, also known as "action"
     */
    private $urlAction = '';

    /**
     *
     * @var array Parameters to pass to the control + action
     */
    private $urlParameters = array();

    /**
     *
     * @var \PHPYAM\core\interfaces\IAuthentication Authentication
     */
    private $authentication = null;

    /**
     * Define additional headers that will be sent to the client.
     */
    protected function initAdditionalHeaders()
    {
    }

    /**
     * \PHPYAM\core\Router initialization
     */
    private function initRouter()
    {
        $createSession = Store::get('CREATE_SESSION', true);
        $clientCharset = Store::getRequired('CLIENT_CHARSET');
        $clientLanguage = Store::getRequired('CLIENT_LANGUAGE');

        if ($createSession) {
            // We set a session name based on the "baseurl"
            // to not mix the session data of different web applications
            // on a same webserver domain.
            session_name('SESS' . md5($_SERVER['HTTP_HOST'] . URL));
            // Must be run asap by the router!
            if (session_id() === '') {
                session_start();
            }
        }

        // We set the language used for the PHPYAM messages.
        // Can be overridden later again, for example
        // in the object constructor of $this->authentication.
        putenv('LC_ALL=' . $clientLanguage);
        $locale = setlocale(LC_ALL, $clientLanguage);
        bindtextdomain('PHPYAM', __DIR__ . '/../locales');
        bind_textdomain_codeset('PHPYAM', $clientCharset);

        // Useful for Ajax requests (JQuery).
        header('Content-Type: text/html; charset=' . $clientCharset);

        $this->initAdditionalHeaders();

        // The Ajax directive 'contentType: "application/x-www-form-urlencoded;charset=" . Store::getRequired("CLIENT_CHARSET")'
        // is ignored by mostly every web browser (and should therefore not be used).
        // They will *always* send Ajax requests encoded with the UTF-8 charset.
        // We must therefore re-encode the received query data ($_REQUEST, $_POST, $_GET, ...) using the server&client charset:
        if ($clientCharset !== 'UTF-8' && self::isAjaxCall()) {
            $conversionFunction = function (&$paramValue, $paramKey, $paramEncodings) {
                if (is_string($paramValue)) {
                    $paramValue = mb_convert_encoding($paramValue, $paramEncodings['to'], $paramEncodings['from']);
                }
            };
            if (is_array($GLOBALS['_' . $_SERVER['REQUEST_METHOD']])) {
                array_walk_recursive($GLOBALS['_' . $_SERVER['REQUEST_METHOD']], $conversionFunction, array(
                    'from' => 'UTF-8',
                    'to' => $clientCharset
                ));
                $_REQUEST = array_replace($_REQUEST, $GLOBALS['_' . $_SERVER['REQUEST_METHOD']]);
            }
        }

        if ($createSession) {
            // Prevent accidental submitting by refresh or back-button.
            // Use after session_start() and before any output to the browser (it uses header redirection).
            IntelliForm::antiRepost($_SERVER['REQUEST_URI']);

            // Clear expired form data.
            IntelliForm::purge();
        }

        // Create array with URL parts in $url.
        $this->splitUrl();

        $authenticationClassName = $this->loadResource('security', Store::getRequired('SECURITY_POLICY'), true);
        $this->authentication = new $authenticationClassName();

        // We check that the header() statements have been taken into account,
        // which is only possible if the HTTP headers have not been sent yet.
        Assert::isFalse(headers_sent(), Core::gettext('HTTP headers have already been sent.'));

        // We check that all non-critical resources have been successfully set (or loaded) once the security policy was set.
        Assert::isTrue($locale !== false, Core::gettext("The locale '%s' could not be set."), $clientLanguage);

        ob_start();
    }

    /**
     * Called when router ends without errors.
     */
    private function endRouter()
    {
        // We "flush" all buffers to output remaining data.
        BufferUtils::closeOutputBuffers(0, true);
    }

    /**
     * Called when router ends with errors.
     * All current buffers are cleared without being displayed.
     * Instead, an error page is displayed (or returned if it
     * is an Ajax request), with the full list of errors.
     * In addition, the code 500 (Internal Server Error) is returned
     * to the client in the case of an Ajax request.
     *
     * @param array $msgs
     *            list of error messages to be displayed
     */
    private function endRouterOnError(array $msgs)
    {
        // http_response_code() is called before ob_end_clean()
        // to ensure that the code 500 will always be sent to the client
        // (even if ob_end_clean() subsequently fails).
        http_response_code(500);

        // We empty all buffers.
        BufferUtils::closeOutputBuffers(0, false);
        try {
            // We start a new buffer to avoid leaking error informations
            // when the error handler itself has problems.
            ob_start();
            if ($this->isAjaxCall()) {
                // We send a HTML error message that can be retrieved client-side
                // by the Ajax error manager.
                $this->call(Store::getRequired('ERROR_CONTROLLER'), Store::getRequired('ERROR_AJAX_ACTION'), $msgs);
            } else {
                $this->call(Store::getRequired('ERROR_CONTROLLER'), Store::getRequired('ERROR_ACTION'), $msgs);
            }
            BufferUtils::closeOutputBuffers(0, true);
        } catch (\Throwable $ex) {
            BufferUtils::closeOutputBuffers(0, false);
            $catchInternalErrors = Store::get('PHPYAM_CATCH_INTERNAL_PHP_ERRORS', false);
            if (($ex instanceof \Error && ! $catchInternalErrors) ||
            // No logger is defined, better throw the exception then loose it.
            Store::getConfiguration() === null) {
                throw $ex;
            }
            // Do not send this exception, simply log it.
            // We're on the error page, there's not much to do when the error
            // page itself contains errors!
            Store::logError($ex);
            if ($ex instanceof RouterException) {
                // Keep track of the original error and log some useful informations.
                Store::logError(implode(PHP_EOL, $msgs));
            }
        }
    }

    /**
     * When the router stops on a "fatal" error (i.e.
     * an exception that was not "catched" by the application),
     * we drop (for safety) all ODBC connections and the current PHP session.
     * We also take care to hide all E_NOTICE notifications.
     */
    private function cleanupOnFatalError()
    {
        $dropOdbcConnectionsOnError = Store::get('DROP_ALL_ODBC_CONNECTIONS_ON_FATAL_ERROR', true);
        $createSession = Store::get('CREATE_SESSION', true);
        $dropSessionOnError = Store::get('DROP_SESSION_ON_FATAL_ERROR', true);

        // Persistent connections created with odbc_connect() are never released,
        // even when they become unusable (after that a database was restarted, for example).
        // An exception would then be thrown at each database access attempt, preventing subsequent
        // browsing within this application until the web server is restarted!
        // To avoid this situation, we try to reset all ODBC connections after each "fatal" error.
        if ($dropOdbcConnectionsOnError && @function_exists('odbc_close_all')) {
            @odbc_close_all();
        }

        // Similarly, we destroy the session data, to avoid working on "corrupt" data.
        if ($createSession && $dropSessionOnError && @session_id() !== '') {
            if (@ini_get('session.use_cookies')) {
                $params = @session_get_cookie_params();
                if (is_array($params)) {
                    @setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                }
            }
            @session_unset();
            @session_destroy();
            @session_write_close();
        }
    }

    /**
     * Retrieves and cuts the URL of the current web request, to translate it as a call to
     * a controller + action + parameters.
     */
    private function splitUrl()
    {
        if (isset($_GET['route'])) {
            $useAssociativeParams = Store::get('URL_ASSOCIATIVE_PARAMS', true);

            // Split URL.
            $url = rtrim($_GET['route'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            // Note: the controller name and action name will also be decoded.
            foreach ($url as &$value) {
                $value = Core::decodeUrlParameter($value);
            }
            // IMPORTANT: release reference to array value because $value will be used later again.
            unset($value);

            // Put URL parts into according properties.
            // By the way, the syntax here is just a short form of if/else, called "Ternary Operators".
            // @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
            $this->urlController = isset($url[0]) && $url[0] !== '' ? $url[0] : Store::getRequired('DEFAULT_CONTROLLER');
            array_shift($url);
            $this->urlAction = isset($url[0]) && $url[0] !== '' ? $url[0] : Store::getRequired('DEFAULT_ACTION');
            array_shift($url);
            if ($useAssociativeParams) {
                $params = array();
                while (count($url)) {
                    $key = array_shift($url);
                    $value = count($url) ? array_shift($url) : '';
                    $params[$key] = $value;
                }
                $this->urlParameters = $params;
            } else {
                $this->urlParameters = $url;
            }
        } else {
            $this->urlController = Store::getRequired('DEFAULT_CONTROLLER');
            $this->urlAction = Store::getRequired('DEFAULT_ACTION');
            $this->urlParameters = array();
        }
    }

    /**
     *
     * @return \PHPYAM\core\interfaces\IAuthentication returns the object containing the application credentials
     */
    public final function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PHPYAM\core\interfaces\IRouter::loadResource()
     */
    public final function loadResource($type, $resourceName, $throwException)
    {
        $resourceFile = $this->getResourceFileName($type, $resourceName);
        // Resource file is optional.
        if ($resourceFile !== null) {
            $isReadable = is_readable($resourceFile);
            if ($throwException) {
                Assert::isTrue($isReadable, Core::gettext("The resource '%s' of type '%s' cannot be read as file '%s'."), $resourceName, $type, $resourceFile);
            }
            if ($isReadable) {
                require_once $resourceFile;
            } else {
                return null;
            }
        }
        $resourceClassName = $this->getClassName($type, $resourceName);
        if ($throwException) {
            Assert::isFalse($resourceClassName === null || ! class_exists($resourceClassName), Core::gettext("The resource '%s' of type '%s' cannot be found as class '%s'."), $resourceName, $type, $resourceClassName);
        } else {
            if ($resourceClassName === null || ! class_exists($resourceClassName)) {
                return null;
            }
        }
        return $resourceClassName;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PHPYAM\core\interfaces\IRouter::call()
     */
    public final function call($urlController, $urlAction, array $urlParameters = array())
    {
        Assert::isTrue(is_string($urlController), Core::gettext('The parameter %s is not of type string.'), $urlController);
        Assert::isTrue(is_string($urlAction), Core::gettext('The parameter %s is not of type string.'), $urlAction);

        // If so, then load this file and create this controller.
        // Example: if controller would be "car", then this line would translate into: $controller = new car($this);
        $controllerClassName = $this->loadResource('controllers', $urlController, false);

        if ($controllerClassName !== null) {
            $controller = null;

            // Check for class: does such a class exist (and does it have a callable constructor)?
            if (method_exists($controllerClassName, '__construct') && is_callable(array(
                $controllerClassName,
                '__construct'
            ), true)) {
                // Call the class constructor and pass the reference to this router object.
                $controller = new $controllerClassName($this);
            }

            // Check for method: does such a method exist (and is it callable) in the controller?
            if (is_object($controller) && method_exists($controller, $urlAction) && is_callable(array(
                $controller,
                $urlAction
            ))) {
                // Call the method and pass the arguments to it.
                $controller->{$urlAction}($urlParameters);
                return;
            }
        }

        // Invalid URL.
        throw new RouterException(Core::gettext('URL is invalid.'));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PHPYAM\core\interfaces\IRouter::forward()
     */
    public final function forward($urlController, $urlAction, array $urlParameters = array(), $clearOutputBuffersBeforeRedirect = true)
    {
        if ($clearOutputBuffersBeforeRedirect) {
            // We empty all buffers.
            BufferUtils::closeOutputBuffers(0, false);
        }

        // We check that the header() statements have been taken into account,
        // which is only possible if the HTTP headers have not been sent yet.
        Assert::isFalse(headers_sent(), Core::gettext('HTTP headers have already been sent.'));

        header('location:' . Core::url($urlController, $urlAction, $urlParameters));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PHPYAM\core\interfaces\IRouter::isAjaxCall()
     */
    public final function isAjaxCall()
    {
        return ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * \PHPYAM\core\Router's constructor.
     * Automatically starts the routing by analyzing the elements of the web request URL and by calling
     * the corresponding control + action. Non-catched exceptions redirect the user to an error page
     * (or return the full list of errors + HTTP code 500 when it's an Ajax request).
     *
     * @param $configuration \PHPYAM\core\interfaces\IConfiguration|null
     *            Router's configuration. When no configuration object is provided, PHYAM first
     *            loofs after global constants (for backward compatibility) before applying default hardcoded values.
     */
    public final function __construct($configuration = null)
    {
        try {
            if ($configuration !== null) {
                Store::setConfiguration($configuration);
            } else {
                Store::removeConfiguration();
            }

            $this->initRouter();

            if (! $this->authentication->authenticate($this->urlController, $this->urlAction, $this->urlParameters)) {
                throw new RouterException(Core::gettext('You are not authorized to access this page.'));
            }

            $this->call($this->urlController, $this->urlAction, $this->urlParameters);

            $this->endRouter();
        } catch (RouterException $ex) {
            $this->endRouterOnError(array(
                $ex instanceof IKeepRouterException ? $ex : $ex->getMessage()
            ));
        } catch (\Throwable $ex) {
            $catchInternalErrors = Store::get('PHPYAM_CATCH_INTERNAL_PHP_ERRORS', false);
            if ($ex instanceof \Error && ! $catchInternalErrors) {
                throw $ex;
            }
            Store::logError($ex);
            $this->endRouterOnError(array(
                Core::gettext('Internal error. Please restart the application.'),
                $ex
            ));
            $this->cleanupOnFatalError();
        }
    }
}
