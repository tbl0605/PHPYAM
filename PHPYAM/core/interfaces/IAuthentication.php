<?php
namespace PHPYAM\core\interfaces;

/**
 * Interface for all methods (needing any kind of authentication) managing:
 * <ul>
 * <li>database connections
 * <li>user identification
 * <li>user habilitations
 * <ul>
 *
 * @package PHPYAM.core
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014 Thierry BLIND
 */
interface IAuthentication
{

    /**
     * Method used by the router to parameterize the controller + action + parameters
     * that will called after each URL request.
     * This method should be used to make user authentication and parameter verifications & cleanups.
     *
     * @param string $controllerName
     *            controller name (passed by reference to be able to modify the controller to execute)
     * @param string $actionName
     *            action name (passed by reference to be able to modify the action to execute)
     * @param array $parameters
     *            array of parameters (passed by reference to be able to modify the parameters to use)
     * @return boolean true if the user is authorized to execute the controller + action + parameters, false otherwise
     *         (when false, the router will throw a \PHPYAM\libs\RouterException after call to this method)
     */
    public function authenticate(&$controllerName, &$actionName, array &$parameters);

    /**
     * Returns a resource, a list of resources or an object that will be passed as parameter
     * to the constructor of the models build by {@link \PHPYAM\core\Controller::loadModel($modelName)}.
     *
     * @return mixed a resource, a list of resources or an object that will be passed as parameter
     *         to the constructor of the models build by \PHPYAM\core\Controller::loadModel($modelName)
     */
    public function newDatabaseConnections();

    /**
     * Returns a resource, a list of resources or an object created by {@link \PHPYAM\core\interfaces\IAuthentication::newDatabaseConnections()}.
     *
     * @param mixed $db
     *            a resource, a list of resources or an object created by \PHPYAM\core\interfaces\IAuthentication::newDatabaseConnections()
     */
    public function destroyDatabaseConnections($db);
}
