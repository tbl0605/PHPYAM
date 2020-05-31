<?php
namespace PHPYAM\core\interfaces;

/**
 * Interface defining the methods required by the router to handle web requests.
 *
 * @package PHPYAM\core\interfaces
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014-2020 Thierry BLIND
 */
interface IRouter
{

    /**
     *
     * @return \PHPYAM\core\interfaces\IAuthentication returns the object having all authentication informations
     */
    public function getAuthentication();

    /**
     * Translates the resource $resourceName as its corresponding file name, depending on its type.
     *
     * @param string $type
     *            resource type, either 'controllers', 'models' or 'security'
     * @param string $resourceName
     *            resource name
     * @return string|null NULL when resource doesn't exist or doesn't need to be load later by the router, non-NULL otherwise
     */
    public function getResourceFileName($type, $resourceName);

    /**
     * Translates the resource $resourceName as its corresponding fully-qualified class name, depending on its type.
     *
     * @param string $type
     *            resource type, either 'controllers', 'models' or 'security'
     * @param string $resourceName
     *            resource name
     * @return string|null NULL when class name couldn't be computed, non-NULL otherwise
     */
    public function getClassName($type, $resourceName);

    /**
     * Used to load the resource $resourceName.
     * The resource name could either be a non-qualified class name or a PHP file name without the ".php" suffix
     * that will be translated to a fully-qualified class name using method IRouter::getClassName($type, $resourceName).
     * Note that this method first calls method IRouter::getResourceFileName($type, $resourceName) and tries to load
     * the required file (when the computed file name is non-null).
     *
     * @param string $type
     *            resource type, either 'controllers', 'models' or 'security'
     * @param string $resourceName
     *            resource name
     * @param boolean $throwException
     *            throw an exception on invalid file path (when non-null)
     *            or on invalid class name <b>only when $throwException is true</b>
     * @return string|null fully-qualified class name, null when something went wrong
     * @throws \PHPYAM\libs\AssertException exception thrown on invalid file name (when non-null) or on invalid class name
     *         <b>when parameter $throwException is true</b>
     */
    public function loadResource($type, $resourceName, $throwException);

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
