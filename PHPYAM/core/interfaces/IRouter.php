<?php
namespace PHPYAM\core\interfaces;

/**
 * Interface defining the methods required by the router to handle web requests.
 *
 * @package PHPYAM.core
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014 Thierry BLIND
 */
interface IRouter
{

    /**
     *
     * @return \PHPYAM\core\interfaces\IAuthentication returns the object having all authentication informations
     */
    public function getAuthentication();

    /**
     * Checks if the resource $resourceName exists in the directory $pathName.
     * The resource name should either be a class name or a PHP file name without the ".php" suffix
     * (useful when looking for a "view" file that contains no PHP class).
     *
     * @param string $pathName
     *            directory where to look for the resource
     * @param string $resourceName
     *            resource name
     * @return boolean TRUE when resource exists, FALSE otherwise
     */
    public function isResource($pathName, $resourceName);

    /**
     * Used to load the resource $resourceName from the directory $pathName.
     * The resource name should either be a class name or a PHP file name without the ".php" suffix
     * (useful when looking for a "view" file that contains no PHP class).
     *
     * @param string $pathName
     *            directory where to look for the resource
     * @param string $resourceName
     *            resource name
     * @throws \PHPYAM\libs\AssertException exception thrown on invalid resource (i.e. \PHPYAM\core\interfaces\IRouter::isResource($pathName, $resourceName) returns FALSE)
     */
    public function loadResource($pathName, $resourceName);

    /**
     * Used to call a controller + action (for example from another controller + action).
     * There are 2 ways to call this method:
     * <ul>
     * <li>either let the router call it as response to a web request
     * <li>either let the developer call it directly from inside another controller + action
     * (to act like a redirection on server side and NOT a redirection/forwarding on client side).<br>
     * Calling this method acts like doing <code>(new {$urlController}($this))->{$urlAction}($urlParameters)</code>.<br>
     * Be aware that calling this method from another controller + action doesn't reset the data already outputted,
     * the developer should use the ob_*() functions to control overall data output buffering if necessary.
     * </ul>
     *
     * In both case, the initial context will be retained ($_POST, $_GET, etc...), which means that
     * $urlParameters, $_POST, $_GET, etc... should be carefully "cleaned-up"
     * before calling this method. A good place to do that is in method {@link \PHPYAM\core\interfaces\IAuthentication::authenticate()},
     * because it's called (once) by the router at the beginning of a every new web request.
     *
     * @param string $urlController
     *            controller (case-insensitive)
     * @param string $urlAction
     *            action (case-insensitive)
     * @param array $urlParameters
     *            parameters (default: empty array)
     * @throws \PHPYAM\libs\AssertException exception thrown when $urlController or $urlAction are not strings
     * @throws \PHPYAM\libs\RouterException exception thrown on invalid url
     * @see \PHPYAM\core\interfaces\IAuthentication::authenticate($controllerName, $actionName, $parameters)
     */
    public function call($urlController, $urlAction, array $urlParameters = array());

    /**
     * Forwards current web request to another controller + action using the <code>header('location')</code> commando.
     * In order to make the "redirect" work, no data should have been outputted before calling this method
     * (by doing some early ob_end_flush() for example), or the <code>header('location')</code> commando will fail.
     * By default, the router always buffers data output and clears those output buffers when calling this method
     * with parameter $clearOutputBuffersBeforeRedirect set to true.
     *
     * Note: the current context will be lost ($_POST, $_GET, etc...) as we force the client to send a new web request
     * using the <code>header('location')</code> commando.<br>
     * Note 2: this method does not stop current script, developers should do (if necessary)
     * a return, die or exit after calling this method.
     *
     * @param string $urlController
     *            controller (case-insensitive)
     * @param string $urlAction
     *            action (case-insensitive)
     * @param array $urlParameters
     *            parameters (default: empty array)
     * @param boolean $clearOutputBuffersBeforeRedirect
     *            true if all output buffers must be cleared, false otherwise. Default value: true.
     * @throws \PHPYAM\libs\AssertException exception thrown when headers were already sent before calling <code>header('location')</code>
     */
    public function forward($urlController, $urlAction, array $urlParameters = array(), $clearOutputBuffersBeforeRedirect = true);

    /**
     * Checks if current web request is an Ajax call.
     *
     * @return boolean true if the current web request is an Ajax call, false otherwise
     * @see http://davidwalsh.name/detect-ajax
     */
    public function isAjaxCall();
}
